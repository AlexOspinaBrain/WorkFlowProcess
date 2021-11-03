<?php
session_start();
header('Content-Type: text/plain; charset=ISO-8859-1');
require_once ("../../config/conexion.php");
require_once ("Workflow.php");

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";

$usuario = "COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as usuario";

function utf8_encode_all($dat){ // -- It returns $dat encoded to UTF8 
  if (is_string($dat)) return utf8_encode($dat); 
  if (!is_array($dat)) return $dat; 
  $ret = array(); 
  foreach($dat as $i=>$d) $ret[$i] = utf8_encode_all($d); 
  return $ret; 
} 


if ($opcion=="statusWorkflow") {
	$estadoCopyUsu = array();
	$tipo_tramite =  substr($_REQUEST['tramite'], 0, 3);
	$actividad = new StdClass();
	

	if($_REQUEST['tipo_doc'] == 'Orden de giro'){
		$result = queryQR("select * from fac_ordengiro ord join fac_radica rad USING(id_ordengiro) where ord.num_ordengiro='".$_REQUEST['tramite']."' limit 1");
		$result=$result->FetchRow();
		$_REQUEST['tramite'] = $result['serial_factura'];
	}

	if($_REQUEST['tipo_doc'] == 'Comprobante de pago'){
		$result = queryQR("select * from fac_ordengiro join fac_radica USING(id_ordengiro) join fac_comprobantepago com USING(id_comprobante)
 			where com.num_comprobante ='".$_REQUEST['tramite']."' limit 1");
		$result=$result->FetchRow();
		$_REQUEST['tramite'] = $result['serial_factura'];
	}

	$nuevoFlujo = new Workflow( $_REQUEST['tramite'], utf8_decode('Devolución'));
	$actividad->proxima= $nuevoFlujo -> getProximo();

	if($actividad->proxima == ('Recibir corrección radicación'))
		$estadoCopyUsu=array('Radicado', 'Correción radicación');

	if($actividad->proxima == ('Recibir corrección orden de giro'))
		$estadoCopyUsu=array('Generar orden de giro', 'Corrección orden de giro');

	if($actividad->proxima == ('Recibir corrección causación'))
		$estadoCopyUsu=array('Causación', 'Corrección causación');

	if($actividad->proxima == ('Recibir corrección Comprobante de pago'))
		$estadoCopyUsu=array('Generar CP', 'Corrección Comprobante de pago');

	$filtro="";			
	foreach ($estadoCopyUsu as $valor) 
		$filtro .= "his.actividad = '$valor' or ";
		
	$filtro=substr( $filtro ,0,strlen( $filtro )-3);
	
	$result = queryQR(" select his.usuario_cod from fac_radica fac join fac_historial his USING(id_radica) where 
			fac.serial_factura='".$_REQUEST['tramite']."' and ($filtro) order by his.id_historial desc limit 1");
	$result=$result->FetchRow();
	$actividad->usuario=$result['usuario_cod'];

	$actividad = utf8_encode_all( $actividad);
	echo json_encode($actividad);
}
?>