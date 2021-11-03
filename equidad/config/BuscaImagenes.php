<?php
header ('Content-type: text/html; charset=ISO-8859-1');
include ("conexion.php");

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";
$basedb = new conexion();

if($opcion=='ImagenesTramite'){
	$rsus = $basedb->queryequi("select * from correspondencia where srtodo='$qAjax'");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row['nombre'].'", "value": "'.$row['path'].'", "sr": "'.$row['sr'].'"}, ';
	}
}

if($opcion=='ptesoreria'){
	$rsus = $basedb->querydb("select * from planillastesoreria where srtodo='$qAjax' order by sr");

	while ($row = pg_fetch_array($rsus)){
		$str=str_replace("\\", "/", $row['path']);
		$salida.='{ "id": "'.$row['descripcion'].'", "value": "'.substr($str, strrpos($str,'/vol'),strlen($str)).'", "sr": "'.$row['sr'].'"}, ';
	}
}

if($opcion=='pcontabilidad'){
	$rsus = $basedb->querydb("select * from planillascontabilidad where srtodo='$qAjax' order by sr");

	while ($row = pg_fetch_array($rsus)){
		$str=str_replace("\\", "/", $row['path']);
		$salida.='{ "id": "'.$row['descripcion'].'", "value": "'.substr($str, strrpos($str,'/vol'),strlen($str)).'", "sr": "'.$row['sr'].'"}, ';
	}
}

if($opcion=='phojasvida'){
	$rsus = $basedb->querydb("insert into registroconsultahv(usuario, feconsulta, numid) values('".$_REQUEST['usuario']."', now(), '".$_REQUEST['consultaHV']."'); select * from planillashojasdevida where srtodo='$qAjax' order by sr");

	while ($row = pg_fetch_array($rsus)){
		$str=str_replace("\\", "/", $row['path']);
		$salida.='{ "id": "'.$row['descripcion'].'", "value": "'.substr($str, strrpos($str,'/vol'),strlen($str)).'", "sr": "'.$row['sr'].'"}, ';
	}
}

if($opcion=='pintermediarios'){
	$rsus = $basedb->querydb("select * from planillasintermediarios where srtodo='$qAjax' order by sr");

	while ($row = pg_fetch_array($rsus)){
		$str=str_replace("\\", "/", $row['path']);
		$salida.='{ "id": "'.$row['descripcion'].'", "value": "'.substr($str, strrpos($str,'/vol'),strlen($str)).'", "sr": "'.$row['sr'].'"}, ';
	}
}


if(strlen( $salida )>0)
	$salida=substr( $salida ,0,strlen( $salida )-2);
?>

<?="[ $salida ]"?>

<?php
$basedb->cierracon();
?>