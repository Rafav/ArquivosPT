// El popup solo lanza el trabajo y muestra el progreso. La descarga en sí
// corre en el service worker (background.js), de modo que NO se cancela si el
// usuario cierra el popup o pincha en cualquier parte de la página.

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

function render(job) {
    if (!job) return;
    setStatus(job.message || '');
    document.getElementById('clickLinks').disabled = !!job.running;
}

// Refleja en el popup el progreso que emite el background.
chrome.runtime.onMessage.addListener((msg) => {
    if (msg && msg.type === 'progress') render(msg.job);
});

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('clickLinks');

    // Si ya hay una descarga en marcha (o recién terminada), muestra su estado.
    chrome.runtime.sendMessage({ action: 'getProgress' }, (res) => {
        if (!chrome.runtime.lastError && res) render(res.job);
    });

    button.addEventListener('click', () => {
        chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
            const tab = tabs[0];
            const docId = getDocId(tab && tab.url);

            if (!docId) {
                setStatus('Abre primero un documento en digitarq.arquivos.pt/fileViewer/…');
                return;
            }

            button.disabled = true;
            chrome.runtime.sendMessage({ action: 'start', docId }, (res) => {
                if (chrome.runtime.lastError) {
                    setStatus(`Error: ${chrome.runtime.lastError.message}`);
                    button.disabled = false;
                } else if (res) {
                    render(res.job);
                }
            });
        });
    });
});
