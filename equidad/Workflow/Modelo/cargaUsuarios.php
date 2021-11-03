<?php
session_start();
include("../../config/conexion.php");
	
	$salida="<option value=''></option>";

	$usuarios = queryQR("SELECT u.usuario_cod, u.usuario_nombres, u.usuario_priape, u.usuario_segape FROM wf_workflowusuarios AS wu 
	INNER JOIN adm_usuario AS u ON u.usuario_cod = wu.usuario_cod
	INNER JOIN (SELECT id_workflow FROM wf_workflow 
			WHERE id_tipologia = '".$_POST['id']."' 
            AND inicio_workflow IS true 
            AND id_Actividad = '6') AS w ON w.id_workflow = wu.id_workflow");

	while ($usu = $usuarios->FetchRow()){
		$salida.="<option value='".$usu["usuario_cod"]."'>".$usu["usuario_nombres"].' '.$usu["usuario_priape"].' '.$usu["usuario_segape"]."</option>";
	}
	echo $salida;
?>
