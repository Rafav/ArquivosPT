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

// Descarga de un fichero devuelve una promesa que resuelve al terminar.
function download(url, filename) {
    return new Promise((resolve, reject) => {
        chrome.downloads.download({ url, filename, conflictAction: 'uniquify' }, (downloadId) => {
            if (chrome.runtime.lastError || downloadId === undefined) {
                reject(chrome.runtime.lastError || new Error('download failed'));
            } else {
                resolve(downloadId);
            }
        });
    });
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

    for (const file of files) {
        const url = `${ORIGIN}/api/rdigital/dissemination?fileId=${encodeURIComponent(file.id)}&download=true`;
        const name = file.name || `${file.id}.jpg`;
        const filename = `${folder}/${name}`.replace(/[\\:*?"<>|]/g, '_');
        try {
            await download(url, filename);
        } catch (e) {
            failed++;
            console.error('Fallo al descargar', name, e);
        }
        done++;
        setStatus(`Descargando ${done} / ${files.length}${failed ? ` (${failed} con error)` : ''}…`);
    }

    setStatus(`Listo: ${files.length - failed} de ${files.length} imágenes enviadas a descargas.` +
        (failed ? ` ${failed} fallaron.` : ''));
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
