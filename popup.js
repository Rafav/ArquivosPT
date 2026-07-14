// Descarga masiva de imágenes de Digitarq (Arquivos de Portugal).
//
// El visor antiguo (ASP.NET WebForms con __doPostBack) fue reemplazado por un
// SPA de Next.js que expone una API JSON en el mismo origen:
//   - Lista de ficheros:  GET /api/rdigital/{docId}?fromIndex=0&max=N
//                         -> { results: [{ id, name, type, ... }], total }
//   - Descarga de imagen: GET /api/rdigital/dissemination?fileId={id}&download=true
//                         -> bytes de la imagen
// El docId aparece en la URL del visor: /fileViewer/{docId}

const ORIGIN = 'https://digitarq.arquivos.pt';

function setStatus(msg) {
    document.getElementById('status').textContent = msg;
}

// Extrae el docId de una URL del visor (/fileViewer/{docId}).
function getDocId(url) {
    try {
        const match = new URL(url).pathname.match(/\/fileViewer\/([a-f0-9]+)/i);
        return match ? match[1] : null;
    } catch (e) {
        return null;
    }
}

// Carpeta destino: prefijo del código de referencia sin el sufijo _mNNNN.ext.
function folderFor(fileName, docId) {
    const base = (fileName || '').replace(/_m?\d+\.[^.]+$/i, '').trim();
    return base || docId;
}

// Nº de descargas simultáneas. Encolar cientos de golpe hace que el servidor
// corte las conexiones y no es respetuoso con el archivo, así que se limita,
// se espera a que cada una termine y se deja una pequeña pausa entre ellas.
const CONCURRENCY = 3;
const MAX_RETRIES = 3;
const PAUSE_MS = 200; // cortesía con el servidor entre descargas

function sleep(ms) {
    return new Promise((r) => setTimeout(r, ms));
}

// Lanza una descarga y devuelve su downloadId (o error de encolado).
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

// Descarga un fichero esperando a que se complete, con reintentos.
async function downloadFile(url, filename) {
    for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
        try {
            const id = await startDownload(url, filename);
            if (await waitForCompletion(id)) return true;
        } catch (e) {
            console.error('Error al encolar', filename, e);
        }
        await sleep(500 * attempt); // espera creciente antes de reintentar
    }
    return false;
}

async function downloadAll(docId) {
    setStatus('Obteniendo lista de imágenes…');

    const listUrl = `${ORIGIN}/api/rdigital/${docId}?fromIndex=0&max=100000`;
    const res = await fetch(listUrl, { credentials: 'include' });
    if (!res.ok) throw new Error(`No se pudo obtener la lista (HTTP ${res.status})`);

    const data = await res.json();
    const files = (data.results || []).filter((f) => f && f.id);
    if (files.length === 0) {
        setStatus('No se encontraron imágenes en este documento.');
        return;
    }

    const folder = folderFor(files[0].name, docId);
    let done = 0;
    let failed = 0;

    const tick = () => setStatus(
        `Descargando ${done} / ${files.length}${failed ? ` (${failed} con error)` : ''}…`);
    tick();

    // Cola procesada por varios "workers" en paralelo (concurrencia limitada).
    let next = 0;
    async function worker() {
        while (next < files.length) {
            const file = files[next++];
            const url = `${ORIGIN}/api/rdigital/dissemination?fileId=${encodeURIComponent(file.id)}&download=true`;
            const name = (file.name || `${file.id}.jpg`).replace(/[\\/:*?"<>|]/g, '_');
            const ok = await downloadFile(url, `${folder}/${name}`);
            if (!ok) {
                failed++;
                console.error('Fallo al descargar', name);
            }
            done++;
            tick();
            await sleep(PAUSE_MS);
        }
    }

    await Promise.all(Array.from({ length: Math.min(CONCURRENCY, files.length) }, worker));

    setStatus(`Listo: ${files.length - failed} de ${files.length} imágenes descargadas.` +
        (failed ? ` ${failed} fallaron (revisa la consola).` : ''));
}

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('clickLinks');

    button.addEventListener('click', () => {
        chrome.tabs.query({ active: true, currentWindow: true }, async (tabs) => {
            const tab = tabs[0];
            const docId = getDocId(tab && tab.url);

            if (!docId) {
                setStatus('Abre primero un documento en digitarq.arquivos.pt/fileViewer/…');
                return;
            }

            button.disabled = true;
            try {
                await downloadAll(docId);
            } catch (e) {
                setStatus(`Error: ${e.message}`);
                console.error(e);
            } finally {
                button.disabled = false;
            }
        });
    });
});
