<?php 
include ("../config/conexion.php");
$result=queryQR("select * from wf_adjuntos where id_adjunto=".$_REQUEST['Id']);
$row = $result->FetchRow();



if (file_exists($row["ruta_adjunto"])) {

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$row["desc_adjunto"]);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($row["ruta_adjunto"]));
    ob_clean();
    flush();
    readfile($row["ruta_adjunto"]);
    exit;
}
?> 