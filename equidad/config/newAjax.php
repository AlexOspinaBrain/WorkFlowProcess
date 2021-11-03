<?php
session_start();
include ("conexion.php");

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	

$usuario = "COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as usuario";

if($opcion=='buscaNombreYUsuario'){
	$usuarios = array();
	$result = queryQR("select *, $usuario from adm_usuario usu where trim(usu.usuario_nombres) || ' ' || trim(usu.usuario_priape) 
						|| ' ' || trim(usu.usuario_segape) ILIKE '%$qAjax%'");
	while ($row = $result->FetchRow()){
		$usuarios[] = array("id"=>$row['usuario_cod'], "value"=>$row['usuario']);
	}

	$result = queryQR("select * from adm_usuario usu where usuario_desc ILIKE '%$qAjax%'");
	while ($row = $result->FetchRow()){
		$usuarios[] = array("id"=>$row['usuario_cod'], "value"=>$row['usuario_desc']);
	}

	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}

function utf8_encode_all($dat){ // -- It returns $dat encoded to UTF8 
  if (is_string($dat)) return utf8_encode($dat); 
  if (!is_array($dat)) return $dat; 
  $ret = array(); 
  foreach($dat as $i=>$d) $ret[$i] = utf8_encode_all($d); 
  return $ret; 
} 
?>