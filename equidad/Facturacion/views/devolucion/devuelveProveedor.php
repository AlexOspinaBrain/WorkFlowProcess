<?php
session_start();
require_once ("../../../config/conexion.php");
require_once ("../../../Correspondencia/Trazabilidad.php");
if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
	$_SESSION['EstadoSesion']="La sesion a terminado";
	echo "<script>location.reload()</script>";
	exit();
}

$codigo = queryQR("");

$TramiteCorrespondencia = GuardaRadicacionCorrespondencia($_REQUEST['Tramite'], "Tramite ".$_REQUEST['Tramite']." radicado por devolucion de factura y/o cuenta de cobro al proveedor", $row['desc_servicio'], $row['id_ciudad'], $row['nombre'], $row['telefono'], $row['direccion'], $row['id_compania']);
echo $TramiteCorrespondencia;
 
queryQR("update fac_radica set estado='Devuelta al proveedor' where serial_factura='".$_REQUEST['Tramite']."'");
queryQR("update fac_historial set actividad='Devuelta al proveedor', estado='Devoluci�n', usuario_cod=".$_SESSION['uscod'].", fecha_terminado=now() where fecha_terminado is null and id_radica=(select id_radica from fac_radica where serial_factura='".$_REQUEST['Tramite']."') ");


function GuardaRadicacionCorrespondencia($Tramite, $Observaciones, $TipoTramite, $Cuidad, $Nombre, $Telefono, $Direccion, $Compania){
	$AgenciaUsu = queryQR("select * from adm_usuario usu join tblareascorrespondencia are on are.areasid=usu.area join tblradofi age on are.agencia=age.codigo where usu.usuario_cod=1438");
	$AgenciaUsu = $AgenciaUsu->FetchRow();
	$AgenciaUsu = $AgenciaUsu['codigo'];


	$codigo = queryQR("select case when (substring(max(numtramite) from 12 for 4)='9999') 
						THEN 'false' when (substring(max(numtramite) from 12 for 4)!='9999') 
						THEN 'true' end as continua, to_number(max(numtramite),'000000000000000')+1 as 
						siguiente from  radcorrespondencia where numtramite like '".date ( "Ymd" ).$AgenciaUsu."%'");
	$codigo = $codigo->FetchRow();

	if($codigo['continua'] != 'false'){
		if(strlen($codigo['siguiente'])==15)
			$NumeroTramite=$codigo['siguiente'];
		else
			$NumeroTramite=date( "Ymd" ).$AgenciaUsu.'0001';
	}else{
		return "No se pueden radicar mas documentos el dia de hoy";
	}

	$Remitente=$_SESSION['uscod'];

	if($_REQUEST['Observaciones']!=null){
		$nombres = queryQR("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  as nombres from adm_usuario where usuario_cod='".$_SESSION['uscod']."'");		
		$nombres = $nombres->FetchRow();
		$nombres = $nombres['nombres'];
		$Observaciones="<b style=\'color: #00009B; font-size: 9px\'>".date("h:i:s A d-m-Y ").' - '.$nombres.":</b> <div style=\'margin-left:30px; width:500px\'>".str_replace(array("'", "\""), '',$Observaciones).'</div>';// Guarda nombre comentario
	}

	$result = queryQR("insert into radcorrespondencia (sr, area, tipodoc, asunto, observaciones, fecins, 
		remitente, ciudad, numtramite, numfolios, destinatario, radicado) values ((select max(sr)+1 from radcorrespondencia), ".'68'.", 
		994, upper('Devolucion de factura $Tramite al provedor'), '".$Observaciones."', now(), 
		$Remitente, 1, '$NumeroTramite', '1', '0', '".$_SESSION['uscod']."')");

	if(!$result)
		return "Error en la radicación";

	$proveedor= queryQR("select * from fac_radica join proveedor USING(id_proveedor) join wf_compania using(id_compania) where serial_factura='".$_REQUEST['Tramite']."'");
	$proveedor = $proveedor->FetchRow();

	$result = queryQR("insert into radcorresext (numtramite, destinatario, telefono, direccion,  prioridad, tipo) values ('$NumeroTramite' , 
		upper('".$proveedor['nombre']."'), '".$proveedor['telefono']."', upper('".$proveedor['direccion']."'), upper('ALTA'), '".$proveedor['des_compania']."')");

	if(!$result)
		return "Error en la radicación";
	
	TrazabilidadCorrespondencia($NumeroTramite);

	return $NumeroTramite;

}

function TrazabilidadCorrespondencia($NumeroTramite){	
	$inserts="('".$NumeroTramite."', now(), ".$_SESSION['uscod'].", 'RADICADO', ".$_SESSION['area']."), ";
	
	$inserts=substr( $inserts ,0,strlen( $inserts )-2);
	$consulta=queryQR("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values $inserts");
	
	Trazabilidad($NumeroTramite, $_SESSION['uscod'], null);
}