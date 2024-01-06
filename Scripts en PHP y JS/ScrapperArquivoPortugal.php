<?php
//javascript:__doPostBack('ViewerControl1$TreeViewFiles','sR191093\\F5464602')

function descarga_index($id_pagina_web){

$url = "https://digitarq.arquivos.pt/viewer?id=".$id_pagina_web;


$headers = array(
    'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0',
    'Accept: */*',
    'Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3',
    'X-Requested-With: XMLHttpRequest',
    'X-MicrosoftAjax: Delta=true',
    'Cache-Control: no-cache',
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'Origin: https://digitarq.arquivos.pt',
    'Connection: keep-alive',
    'Referer: https://digitarq.arquivos.pt/viewer?id='.$id_pagina_web,
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: same-origin',
);


$handler = curl_init();
curl_setopt($handler, CURLOPT_URL, $url);
curl_setopt($handler,CURLOPT_RETURNTRANSFER,1);
curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
curl_setopt($handler,CURLOPT_FOLLOWLOCATION, 1);
$response = curl_exec ($handler); 

return $response;
}


function descarga($id,$legajo,$folio,$viewstate){
       
    $post_fields="ViewerControl1%24ToolkitScriptManager1=ViewerControl1%24UpdatePanelMain%7CViewerControl1%24TreeViewFiles&ViewerControl1_ToolkitScriptManager1_HiddenField=&__EVENTTARGET=ViewerControl1%24TreeViewFiles&__EVENTARGUMENT=";
    $post_fields.=$legajo;
    $post_fields.="%5CF";
    $suma = $folio;
    //$suma= ($suma+$i);
    $post_fields.=$suma;
    //$post_fields.="&__VIEWSTATE=%2FwEPDwUKMTM3MTEzNjA5Mg9kFgICAQ9kFgQCAw9kFgQCAQ8WAh4LXyFJdGVtQ291bnQCAhYEZg9kFgICAQ8PFggeBFRleHQFClBvcnR1Z3XDqnMeC05hdmlnYXRlVXJsBR1%2BL0NoYW5nZUN1bHR1cmVGb3JtLmFzcHg%2FbD1wdB4ISW1hZ2VVcmwFE34vaW1hZ2UvZmxhZy1wdC5wbmceB1Rvb2xUaXAFClBvcnR1Z3XDqnNkZAICD2QWAgIBDw8WCB8BBQdFbmdsaXNoHwIFG34vQ2hhbmdlQ3VsdHVyZUZvcm0uYXNweD9sPR8DBRF%2BL2ltYWdlL2ZsYWctLnBuZx8EBQdFbmdsaXNoZGQCBQ8PFgQfAQUFTG9naW4eB1Zpc2libGVoZGQCBQ8PFgYeEURlc2NyaXB0aW9uSXRlbUlEAv%2Bq9AIeBVRpdGxlBX1QVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOV9tMDAwMS50aWYgLSBQcm9jZXNzbyBkZSByZXF1ZXJpbWVudG8gZGUgcGFzc2Fwb3J0ZSBkZSBKb8OjbyBMb3VyZW7Dp28gRmVybmFuZGVzIGRlIEFndWlhch4TQ3VycmVudFNlbGVjdGVkTm9kZWVkFgICAg9kFgJmD2QWFAIBDw8WAh8BBU1Qcm9jZXNzbyBkZSByZXF1ZXJpbWVudG8gZGUgcGFzc2Fwb3J0ZSBkZSBKb8OjbyBMb3VyZW7Dp28gRmVybmFuZGVzIGRlIEFndWlhcmRkAgIPZBYCAgEQPCsACQIADxYGHg1OZXZlckV4cGFuZGVkZB4MU2VsZWN0ZWROb2RlZB4JTGFzdEluZGV4AgVkCBQrAAIFAzA6MBQrAAIWCh8BBSNQVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOR4FVmFsdWUFB1I3OTg0NTUfAwUafi9pbWFnZS9yZXByZXNlbnRhdGlvbi5wbmceDFNlbGVjdEFjdGlvbgsqLlN5c3RlbS5XZWIuVUkuV2ViQ29udHJvbHMuVHJlZU5vZGVTZWxlY3RBY3Rpb24BHghFeHBhbmRlZGcUKwAFBQ8wOjAsMDoxLDA6MiwwOjMUKwACFgwfAQUtUFQtQURMU0ItQUMtR0NMLUgtRC0wMDYtMDAwMTYtMDAxMDlfbTAwMDEudGlmHwwFCUYzMDAzNjUzMh8DBRB%2BL2ltYWdlL2ZpbGUucG5nHw0LKwQAHghTZWxlY3RlZGgfDmdkFCsAAhYKHwEFLVBULUFETFNCLUFDLUdDTC1ILUQtMDA2LTAwMDE2LTAwMTA5X20wMDAyLnRpZh8MBQlGMzAwMzY1MzMfAwUQfi9pbWFnZS9maWxlLnBuZx8NCysEAB8OZ2QUKwACFgofAQUtUFQtQURMU0ItQUMtR0NMLUgtRC0wMDYtMDAwMTYtMDAxMDlfbTAwMDMudGlmHwwFCUYzMDAzNjUzNB8DBRB%2BL2ltYWdlL2ZpbGUucG5nHw0LKwQAHw5nZBQrAAIWCh8BBS1QVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOV9tMDAwNC50aWYfDAUJRjMwMDM2NTM1HwMFEH4vaW1hZ2UvZmlsZS5wbmcfDQsrBAAfDmdkZGQCAw8WAh8FaGQCBA8PFgIfAgVTdmF1bHQvP2lkPURJU1NFTUlOQVRJT04vMzMxODI0NTA4Qzg3QkUwOTI2QkYwQUI4NkIzRDYyNzcmYT1UcnVlJm09aW1hZ2UvanBlZyZmZT1qcGdkZAIFDw8WAh8CBaIBamF2YXNjcmlwdDpwcmludEltYWdlKCd2YXVsdGltYWdlLz9pZD1ESVNTRU1JTkFUSU9OLzMzMTgyNDUwOEM4N0JFMDkyNkJGMEFCODZCM0Q2Mjc3JnI9MCZ3dz0xMDAwJndoPTYwMCZzPWZhbHNlJywnUFQtQURMU0ItQUMtR0NMLUgtRC0wMDYtMDAwMTYtMDAxMDlfbTAwMDEudGlmJyk7ZGQCDg8PFgIfBWhkZAIPDw8WAh8FaGRkAhAPDxYCHwMFPHRodW1iLz9mPVRIVU1CLzk4MTM1RTIwMjUyM0I1RUQ1QUUyQ0I1NTdDMjlEOUZEJm09aW1hZ2UvdGlmZmRkAhEPDxYCHwMFPHRodW1iLz9mPVRIVU1CLzc5QTcxMENGMDA0MTgwM0IwNzZCQjg1RTFEMzBFMUVDJm09aW1hZ2UvdGlmZmRkAhMPDxYCHwVoZGQYAgUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgYFHFZpZXdlckNvbnRyb2wxJFRyZWVWaWV3RmlsZXMFJFZpZXdlckNvbnRyb2wxJEltYWdlQnV0dG9uUm90YXRlTGVmdAUlVmlld2VyQ29udHJvbDEkSW1hZ2VCdXR0b25Sb3RhdGVSZXNldAUlVmlld2VyQ29udHJvbDEkSW1hZ2VCdXR0b25Sb3RhdGVSaWdodAUnVmlld2VyQ29udHJvbDEkSW1hZ2VCdXR0b25UaHVtYm5haWxOZXh0BSVWaWV3ZXJDb250cm9sMSRJbWFnZUJ1dHRvblNjcm9sbFJpZ2h0BTBWaWV3ZXJDb250cm9sMSRGcmFtZWRQYW5lbFZpZXdlciRNdWx0aVZpZXdWaWV3ZXIPD2RmZAghjj2yf2JyqK5MZQir5%2BCsBBNz";
    $post_fields.="&__VIEWSTATE=";
    $post_fields .= urlencode($viewstate);
    $post_fields.= "&__VIEWSTATEGENERATOR=&__SCROLLPOSITIONX=0&__SCROLLPOSITIONY=0&__EVENTVALIDATION=%2FwEWCwKXzNDwCgLMkrGPAgKD1trbCAKD1t7bCAKD1vLbCAKD1vbbCAK%2Bl%2FaEDwL2lMbkAQKSzY6IAwK3xejsBwLpwtg0kG3Z1fFn0000V61aHHUEvH%2FDWOvRVNQ%3D";
    $post_fields.= "&ViewerControl1_TreeViewFiles_ExpandState=&ViewerControl1_TreeViewFiles_SelectedNode=ViewerControl1_TreeViewFilesn1&ViewerControl1_TreeViewFiles_PopulateLog=&__ASYNCPOST=true&";

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://digitarq.arquivos.pt/ViewerForm.aspx?id='.$id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/109.0';
    $headers[] = 'Accept: */*';
    $headers[] = 'Accept-Language: es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br';
    $headers[] = 'X-Requested-With: XMLHttpRequest';
    $headers[] = 'X-Microsoftajax: Delta=true';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
    $headers[] = 'Origin: https://digitarq.arquivos.pt';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Referer: https://digitarq.arquivos.pt/viewer?id='.$id;
    $headers[] = 'Cookie: _ga_KMW1D94TLD=GS1.1.1702292205.8.1.1702292212.0.0.0; _ga=GA1.1.1263942295.1701532626; ASP.NET_SessionId=ehziw245fdek0cjcx0fkxm55';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

function legajo($html){
    $pattern = "/'([a-zA-Z0-9]+)\\\\\\\\([a-zA-Z0-9]+)/i";
    $encontrado = preg_match_all($pattern, $html, $coincidencias);
    return ($coincidencias[1][0]);
}

function hoja_inicial($html){
    $pattern = "/'([a-zA-Z0-9]+)\\\\\\\\([a-zA-Z0-9]+)/i";
    $encontrado = preg_match_all($pattern, $html, $coincidencias);
    return (ltrim($coincidencias[2][0], $coincidencias[2][0][0]));
}

function estado($html){
    $pattern = '/id="__VIEWSTATE" value="([^"]+)"/';
    $estado_encontrados = preg_match_all($pattern, $html, $coincidencias);
    return ($coincidencias[1][0]);
}

function num_hojas($html){
    $pattern = '/lastIndex = ([0-9]+);/';
    $estado_encontrados = preg_match_all($pattern, $html, $coincidencias);
    return ($coincidencias[1][0]);
}

function id_imagen($html){
    $pattern = '/DISSEMINATION\/([a-zA-Z0-9]*)/';
    $pattern_alternativo = '/vault\/\?id=([a-zA-Z0-9]*)/';
    
    $estado_encontrados = preg_match_all($pattern, $html, $coincidencias);
    if ($estado_encontrados ){
        return ($coincidencias[1][0]);
    }else {
        $estado_encontrados = preg_match_all($pattern_alternativo, $html, $coincidencias);
        return ($coincidencias[1][0]);
    }
}
//GLOBAL
//$id_parametro=6100351; //PRIMER EXITO  OJO CON ID_IMAGEN Y DISSEMINATION

$id_parametro=2307881;

$url_descarga="https://digitarq.arquivos.pt/Controls/vault/?id=";
$url_descarga_final="&a=True&m=image/jpeg&fe=jpg";

$pagina_principal= descarga_index($id_parametro);


$id_legajo=legajo($pagina_principal);
$id_hoja_inicial= hoja_inicial($pagina_principal);
$estado = estado($pagina_principal);
$numero_hojas= num_hojas($pagina_principal) -1; //Hay una más, la portada

for ($i=0;$i<$numero_hojas;$i++){
    $miedo=descarga($id_parametro,$id_legajo,$id_hoja_inicial+$i,$estado);
    //file_put_contents("miedo".$i, $miedo);
    $id_imagen =id_imagen($miedo);
    echo $url_descarga.$id_imagen.$url_descarga_final."\n";
    file_put_contents(($id_hoja_inicial+$i).".jpg",file_get_contents($url_descarga.$id_imagen.$url_descarga_final));
   
}


/*
for ($i=0;$i<3;$i++){
    $result=descarga(6100351,"sR798455",30036532+$i,"/wEPDwUKMTM3MTEzNjA5Mg9kFgICAQ9kFgQCAw9kFgQCAQ8WAh4LXyFJdGVtQ291bnQCAhYEZg9kFgICAQ8PFggeBFRleHQFClBvcnR1Z3XDqnMeC05hdmlnYXRlVXJsBR1+L0NoYW5nZUN1bHR1cmVGb3JtLmFzcHg/bD1wdB4ISW1hZ2VVcmwFE34vaW1hZ2UvZmxhZy1wdC5wbmceB1Rvb2xUaXAFClBvcnR1Z3XDqnNkZAICD2QWAgIBDw8WCB8BBQdFbmdsaXNoHwIFG34vQ2hhbmdlQ3VsdHVyZUZvcm0uYXNweD9sPR8DBRF+L2ltYWdlL2ZsYWctLnBuZx8EBQdFbmdsaXNoZGQCBQ8PFgQfAQUFTG9naW4eB1Zpc2libGVoZGQCBQ8PFgoeEURlc2NyaXB0aW9uSXRlbUlEAv+q9AIeBVRpdGxlBX1QVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOV9tMDAwMi50aWYgLSBQcm9jZXNzbyBkZSByZXF1ZXJpbWVudG8gZGUgcGFzc2Fwb3J0ZSBkZSBKb8OjbyBMb3VyZW7Dp28gRmVybmFuZGVzIGRlIEFndWlhch4TQ3VycmVudFNlbGVjdGVkTm9kZQURUjc5ODQ1NS9GMzAwMzY1MzMeEEN1cnJlbnRGaWxlSW5kZXgCAR4KUm90YXRlRmxpcAspcFN5c3RlbS5EcmF3aW5nLlJvdGF0ZUZsaXBUeXBlLCBTeXN0ZW0uRHJhd2luZywgVmVyc2lvbj0yLjAuMC4wLCBDdWx0dXJlPW5ldXRyYWwsIFB1YmxpY0tleVRva2VuPWIwM2Y1ZjdmMTFkNTBhM2EAZBYCAgIPZBYCZg9kFhgCAQ8PFgIfAQVNUHJvY2Vzc28gZGUgcmVxdWVyaW1lbnRvIGRlIHBhc3NhcG9ydGUgZGUgSm/Do28gTG91cmVuw6dvIEZlcm5hbmRlcyBkZSBBZ3VpYXJkZAICD2QWAgIBEDwrAAkCAA8WBh4NTmV2ZXJFeHBhbmRlZGQeDFNlbGVjdGVkTm9kZQUeVmlld2VyQ29udHJvbDFfVHJlZVZpZXdGaWxlc24yHglMYXN0SW5kZXgCBWQIFCsAAgUDMDowFCsAAhYMHwEFI1BULUFETFNCLUFDLUdDTC1ILUQtMDA2LTAwMDE2LTAwMTA5HgVWYWx1ZQUHUjc5ODQ1NR8DBRp+L2ltYWdlL3JlcHJlc2VudGF0aW9uLnBuZx4MU2VsZWN0QWN0aW9uCyouU3lzdGVtLldlYi5VSS5XZWJDb250cm9scy5UcmVlTm9kZVNlbGVjdEFjdGlvbgEeCEV4cGFuZGVkZx4IU2VsZWN0ZWRoFCsABQUPMDowLDA6MSwwOjIsMDozFCsAAhYMHwEFLVBULUFETFNCLUFDLUdDTC1ILUQtMDA2LTAwMDE2LTAwMTA5X20wMDAxLnRpZh8OBQlGMzAwMzY1MzIfAwUQfi9pbWFnZS9maWxlLnBuZx8PCysFAB8RaB8QZ2QUKwACFgwfAQUtUFQtQURMU0ItQUMtR0NMLUgtRC0wMDYtMDAwMTYtMDAxMDlfbTAwMDIudGlmHw4FCUYzMDAzNjUzMx8DBRB+L2ltYWdlL2ZpbGUucG5nHw8LKwUAHxBnHxFnZBQrAAIWDB8BBS1QVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOV9tMDAwMy50aWYfDgUJRjMwMDM2NTM0HwMFEH4vaW1hZ2UvZmlsZS5wbmcfDwsrBQAfEGcfEWhkFCsAAhYMHwEFLVBULUFETFNCLUFDLUdDTC1ILUQtMDA2LTAwMDE2LTAwMTA5X20wMDA0LnRpZh8OBQlGMzAwMzY1MzUfAwUQfi9pbWFnZS9maWxlLnBuZx8PCysFAB8QZx8RaGRkZAIDDxYCHwVoZAIEDw8WAh8CBVN2YXVsdC8/aWQ9RElTU0VNSU5BVElPTi84QTZCMkMxNDAwQkRGMDNDMTAyNzM5NUFFNzg5RTE2NiZhPVRydWUmbT1pbWFnZS9qcGVnJmZlPWpwZ2RkAgUPDxYCHwIFogFqYXZhc2NyaXB0OnByaW50SW1hZ2UoJ3ZhdWx0aW1hZ2UvP2lkPURJU1NFTUlOQVRJT04vOEE2QjJDMTQwMEJERjAzQzEwMjczOTVBRTc4OUUxNjYmcj0wJnd3PTEwMDAmd2g9NjAwJnM9ZmFsc2UnLCdQVC1BRExTQi1BQy1HQ0wtSC1ELTAwNi0wMDAxNi0wMDEwOV9tMDAwMi50aWYnKTtkZAIND2QWAmYPZBYCZg9kFgICAQ9kFgRmD2QWAgIBDxYCHgNzcmMFUHZhdWx0aW1hZ2UvP2lkPURJU1NFTUlOQVRJT04vOEE2QjJDMTQwMEJERjAzQzEwMjczOTVBRTc4OUUxNjYmcj0wJnd3PTEwMDAmd2g9NjAwZAIDD2QWAgIDDw8WAh8CBVN2YXVsdC8/aWQ9RElTU0VNSU5BVElPTi84QTZCMkMxNDAwQkRGMDNDMTAyNzM5NUFFNzg5RTE2NiZhPVRydWUmbT1pbWFnZS9qcGVnJmZlPWpwZ2RkAg4PDxYCHwVnZGQCDw8PFgQfBWcfAwU8dGh1bWIvP2Y9VEhVTUIvOTgxMzVFMjAyNTIzQjVFRDVBRTJDQjU1N0MyOUQ5RkQmbT1pbWFnZS90aWZmZGQCEA8PFgIfAwU8dGh1bWIvP2Y9VEhVTUIvNzlBNzEwQ0YwMDQxODAzQjA3NkJCODVFMUQzMEUxRUMmbT1pbWFnZS90aWZmZGQCEQ8PFgQfAwU8dGh1bWIvP2Y9VEhVTUIvNDQ5Q0Q4Rjk3MjA1NkNEQkQ1N0FGN0JBNDM0MDkxRUQmbT1pbWFnZS90aWZmHwVnZGQCEg8PFgIfBWdkZAITDw8WAh8FaGRkGAIFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYIBRxWaWV3ZXJDb250cm9sMSRUcmVlVmlld0ZpbGVzBSRWaWV3ZXJDb250cm9sMSRJbWFnZUJ1dHRvblJvdGF0ZUxlZnQFJVZpZXdlckNvbnRyb2wxJEltYWdlQnV0dG9uUm90YXRlUmVzZXQFJVZpZXdlckNvbnRyb2wxJEltYWdlQnV0dG9uUm90YXRlUmlnaHQFJFZpZXdlckNvbnRyb2wxJEltYWdlQnV0dG9uU2Nyb2xsTGVmdAUrVmlld2VyQ29udHJvbDEkSW1hZ2VCdXR0b25UaHVtYm5haWxQcmV2aW91cwUnVmlld2VyQ29udHJvbDEkSW1hZ2VCdXR0b25UaHVtYm5haWxOZXh0BSVWaWV3ZXJDb250cm9sMSRJbWFnZUJ1dHRvblNjcm9sbFJpZ2h0BTBWaWV3ZXJDb250cm9sMSRGcmFtZWRQYW5lbFZpZXdlciRNdWx0aVZpZXdWaWV3ZXIPD2RmZOyMq98LWTKYi/fyBpQf+2g5kfNy");
  file_put_contents($i,$result);    

}
*/
?>
