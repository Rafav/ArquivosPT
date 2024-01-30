async function clickAndWait(enlace) {
    return new Promise(resolve => {
        Sys.Application.add_load(function() {
            // Buscar enlaces con el patrón especificado
	let enlace_descarga = document.getElementById('ViewerControl1_HyperLinkDownload');
			enlace_descarga.click();
            resolve();
        });
        enlace.click(); // Simular clic
    });
}


// Seleccionar todos los enlaces
 enlaces = document.querySelectorAll('a[href^="javascript:__doPostBack"][class^="ViewerControl"]');


//let regex = /\\F(\d+)\)/;
// Ejecutar en secuencia
(async () => {
	
	let regex = /F([0-9]+)\'/;
	
    for (let enlace of enlaces) {

	match = enlace.getAttribute('href').match(regex);

    if (match) {
        await clickAndWait(enlace);
    } else {
        console.log('No se encontró el patrón deseado.');   //Enlace a la imagen de la portada por ejemplo  que solo tiene el legajo
    }

    }
})();
