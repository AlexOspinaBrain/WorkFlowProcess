<?php
header ('Content-type: text/html; charset=ISO-8859-1');
include ("../config/conexion.php");

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";
$basedb = new conexion();

if($opcion=='ActualizaHV'){
	$consulta=$basedb->querydb("update planillashojasdevida set estado='".$_POST['Estado']."' where numid='$qAjax'");
	$rows=pg_affected_rows($consulta);
	$salida.='{"value": "'.$rows.'"}, ';
}

if(strlen( $salida )>0)
	$salida=substr( $salida ,0,strlen( $salida )-2);
?>

<?="[ $salida ]"?>

<?php
$basedb->cierracon();
?>