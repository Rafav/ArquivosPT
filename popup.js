function startContentScript() {
    let total= document.querySelectorAll('a[href^="javascript:__doPostBack"][class^="ViewerControl"]').length;
    console.log(total);
    document.querySelectorAll('a[href^="javascript:__doPostBack"][class^="ViewerControl"]').forEach((link, index) => {
        if (total >= 2 && index === 0) return; // Ignora el primer link (index 0) si hay varios, usa la primera imagen de portada       
        let button = document.createElement('button');
        button.innerHTML = link.innerHTML;

        // Copy classes, IDs, styles, etc.
        button.className = link.className;
        button.id = link.id;
        button.style.cssText = link.style.cssText;

        // Attach the JavaScript in href as an onclick event
        let jsCode = link.getAttribute('href').substring(11); // Remove 'javascript:' prefix
        button.setAttribute('onclick', jsCode);

        link.parentNode.replaceChild(button, link);
    });

    function clickButtonAndWait(button) {
        return new Promise(resolve => {
            button.click();
            // Wait for a specified time for the page to load
            setTimeout(resolve, 4000); // waits for 4 seconds
            //document.getElementById('ViewerControl1_HyperLinkDownload').click();


        });
    }

    async function processButtons() {
        const buttons = document.querySelectorAll('button');
        for (const button of buttons) {
            console.log(button);
            await clickButtonAndWait(button);
            document.getElementById('ViewerControl1_HyperLinkDownload').click();
        }
    }
    processButtons();
}



document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('clickLinks').addEventListener('click', () => {

        chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
            const activeTab = tabs[0];
            //const message = { action: 'scrapePage' };

            chrome.scripting.executeScript({
                target: { tabId: activeTab.id },
                function: startContentScript
            });
        });
    });
});
