// Service worker: hace la descarga masiva en segundo plano para que NO se
// cancele si el usuario cierra el popup o pincha en cualquier parte.
//
// El visor de Digitarq (Next.js) expone una API JSON en el mismo origen:
//   - Lista:     GET /api/rdigital/{docId}?fromIndex=0&max=N
//                -> { results: [{ id, name, type, ... }], total }
//   - Imagen:    GET /api/rdigital/dissemination?fileId={id}
//                -> bytes de la imagen (¡con Content-Type: image/tiff aunque
//                   en realidad sean JPEG!)

const ORIGIN = 'https://digitarq.arquivos.pt';

// Descargas simultáneas limitadas: encolar cientos de golpe hace que el
// servidor corte conexiones y no es respetuoso con el archivo.
const CONCURRENCY = 3;
const MAX_RETRIES = 3;
const PAUSE_MS = 200; // cortesía con el servidor entre descargas

// Estado del trabajo en curso, consultable desde el popup.
let job = { running: false, done: 0, total: 0, failed: 0, message: 'Listo para descargar.' };

function sleep(ms) {
    return new Promise((r) => setTimeout(r, ms));
}

function setMessage(msg) {
    job.message = msg;
    broadcast();
}

// Avisa al popup (si está abierto) del progreso. Si está cerrado, se ignora.
function broadcast() {
    chrome.runtime.sendMessage({ type: 'progress', job }).catch(() => {});
}

// Carpeta destino: prefijo del código de referencia sin el sufijo _mNNNN.ext.
function folderFor(fileName, docId) {
    const base = (fileName || '').replace(/_m?\d+\.[^.]+$/i, '').trim();
    return base || docId;
}

// El servidor manda siempre Content-Type: image/tiff aunque los bytes sean
// JPEG, así que se detecta el formato real por los "magic bytes".
function sniffFormat(bytes) {
    const b = new Uint8Array(bytes);
    if (b[0] === 0xff && b[1] === 0xd8 && b[2] === 0xff) return { ext: 'jpg', mime: 'image/jpeg' };
    if (b[0] === 0x89 && b[1] === 0x50 && b[2] === 0x4e && b[3] === 0x47) return { ext: 'png', mime: 'image/png' };
    if ((b[0] === 0x49 && b[1] === 0x49 && b[2] === 0x2a) ||
        (b[0] === 0x4d && b[1] === 0x4d && b[2] === 0x00)) return { ext: 'tif', mime: 'image/tiff' };
    if (b[0] === 0x25 && b[1] === 0x50 && b[2] === 0x44 && b[3] === 0x46) return { ext: 'pdf', mime: 'application/pdf' };
    if (b[0] === 0x47 && b[1] === 0x49 && b[2] === 0x46) return { ext: 'gif', mime: 'image/gif' };
    return null; // desconocido: se conserva la extensión del nombre original
}

// Los service workers no tienen URL.createObjectURL, así que se descarga
// mediante una data: URL (con el MIME correcto) en vez de un blob URL.
function bytesToDataUrl(bytes, mime) {
    const b = new Uint8Array(bytes);
    let binary = '';
    const CHUNK = 0x8000;
    for (let i = 0; i < b.length; i += CHUNK) {
        binary += String.fromCharCode.apply(null, b.subarray(i, i + CHUNK));
    }
    return `data:${mime};base64,${btoa(binary)}`;
}

function withExtension(name, ext) {
    return name.replace(/\.[^./]*$/, '') + '.' + ext;
}

// Descarga los bytes del fichero (con reintentos). Devuelve un ArrayBuffer.
async function fetchBytes(url) {
    for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
        try {
            const res = await fetch(url, { credentials: 'include' });
            if (res.ok) return await res.arrayBuffer();
            console.error('HTTP', res.status, 'en', url);
        } catch (e) {
            console.error('Error de red en', url, e);
        }
        await sleep(500 * attempt); // espera creciente antes de reintentar
    }
    return null;
}

// Lanza la descarga de una data: URL y devuelve su downloadId.
function startDownload(url, filename) {
    return new Promise((resolve, reject) => {
        chrome.downloads.download({ url, filename, conflictAction: 'overwrite' }, (downloadId) => {
            if (chrome.runtime.lastError || downloadId === undefined) {
                reject(chrome.runtime.lastError || new Error('download failed'));
            } else {
                resolve(downloadId);
            }
        });
    });
}

// Espera a que una descarga concreta termine; resuelve true si se completó.
function waitForCompletion(downloadId) {
    return new Promise((resolve) => {
        let settled = false;
        const finish = (ok) => {
            if (settled) return;
            settled = true;
            chrome.downloads.onChanged.removeListener(listener);
            resolve(ok);
        };
        const listener = (delta) => {
            if (delta.id !== downloadId || !delta.state) return;
            if (delta.state.current === 'complete') finish(true);
            else if (delta.state.current === 'interrupted') finish(false);
        };
        chrome.downloads.onChanged.addListener(listener);
        // Por si terminó antes de registrar el listener.
        chrome.downloads.search({ id: downloadId }, (items) => {
            const state = items && items[0] && items[0].state;
            if (state === 'complete') finish(true);
            else if (state === 'interrupted') finish(false);
        });
    });
}

// Descarga un fichero: baja los bytes, corrige la extensión según el formato
// real y lo guarda. Devuelve true si se completó.
async function downloadFile(url, folder, rawName) {
    const bytes = await fetchBytes(url);
    if (!bytes) return false;

    const fmt = sniffFormat(bytes);
    const name = fmt ? withExtension(rawName, fmt.ext) : rawName;
    const dataUrl = bytesToDataUrl(bytes, fmt ? fmt.mime : 'application/octet-stream');
    try {
        const id = await startDownload(dataUrl, `${folder}/${name}`);
        return await waitForCompletion(id);
    } catch (e) {
        console.error('Error al guardar', name, e);
        return false;
    }
}

async function downloadAll(docId) {
    setMessage('Obteniendo lista de imágenes…');

    const listUrl = `${ORIGIN}/api/rdigital/${docId}?fromIndex=0&max=100000`;
    const res = await fetch(listUrl, { credentials: 'include' });
    if (!res.ok) throw new Error(`No se pudo obtener la lista (HTTP ${res.status})`);

    const data = await res.json();
    const files = (data.results || []).filter((f) => f && f.id);
    if (files.length === 0) {
        setMessage('No se encontraron imágenes en este documento.');
        return;
    }

    const folder = folderFor(files[0].name, docId);
    job.total = files.length;
    job.done = 0;
    job.failed = 0;

    const tick = () => setMessage(
        `Descargando ${job.done} / ${job.total}${job.failed ? ` (${job.failed} con error)` : ''}…`);
    tick();

    // Cola procesada por varios "workers" en paralelo (concurrencia limitada).
    let next = 0;
    async function worker() {
        while (next < files.length) {
            const file = files[next++];
            const url = `${ORIGIN}/api/rdigital/dissemination?fileId=${encodeURIComponent(file.id)}`;
            const name = (file.name || `${file.id}.jpg`).replace(/[\\/:*?"<>|]/g, '_');
            const ok = await downloadFile(url, folder, name);
            if (!ok) {
                job.failed++;
                console.error('Fallo al descargar', name);
            }
            job.done++;
            tick();
            await sleep(PAUSE_MS);
        }
    }

    await Promise.all(Array.from({ length: Math.min(CONCURRENCY, files.length) }, worker));

    setMessage(`Listo: ${job.total - job.failed} de ${job.total} imágenes descargadas.` +
        (job.failed ? ` ${job.failed} fallaron (revisa la consola).` : ''));
}

async function runJob(docId) {
    if (job.running) return;
    job = { running: true, done: 0, total: 0, failed: 0, message: 'Iniciando…' };
    try {
        await downloadAll(docId);
    } catch (e) {
        setMessage(`Error: ${e.message}`);
        console.error(e);
    } finally {
        job.running = false;
        broadcast();
    }
}

chrome.runtime.onMessage.addListener((msg, sender, sendResponse) => {
    if (msg && msg.action === 'start') {
        runJob(msg.docId); // corre en segundo plano; no se espera aquí
        sendResponse({ job });
    } else if (msg && msg.action === 'getProgress') {
        sendResponse({ job });
    }
    return false;
});
