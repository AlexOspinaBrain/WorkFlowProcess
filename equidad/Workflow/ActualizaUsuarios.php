<?php
require_once ('/var/www/equidad/config/conexion.php');

$result = queryQR("select * from adm_usuario");
while($row = $result->FetchRow()){
	$result2 = queryADM("select * from admusuario where usuario_cod=".$row['usuario_cod']);
	if($row2 = $result2->FetchRow()){
		if(strlen($row['area']))
			$area="area='".$row['area']."',";
		else
			$area="";
			
		queryADM ("update admusuario set usuario_desc='".$row['usuario_desc']."', usuario_contrasena='1', usuario_nombres='".$row['usuario_nombres']."', usuario_priape='".$row['usuario_priape']."', 
				usuario_segape='".$row['usuario_segape']."', usuario_correo='".$row['usuario_correo']."',
			    usuario_bloqueado='".$row['usuario_bloqueado']."', $area tipodoc='".$row['tipodoc']."', numerodoc='".$row['numerodoc']."' 
			    where usuario_cod=".$row['usuario_cod']);
	}else{
		//echo $row['usuario_desc']."<br>";
			
		queryADM ("insert into admusuario (usuario_cod, usuario_desc, usuario_contrasena, usuario_nombres, usuario_priape, usuario_segape, usuario_correo,  usuario_bloqueado, tipodoc, numerodoc, area) values 
			('".$row['usuario_cod']."', '".$row['usuario_desc']."', '1', '".$row['usuario_nombres']."', '".$row['usuario_priape']."', '".$row['usuario_segape']."', '".$row['usuario_correo']."',
			'".$row['usuario_bloqueado']."', '".$row['tipodoc']."', '".$row['numerodoc']."', '".$row['area']."')");
	}
}
?>
