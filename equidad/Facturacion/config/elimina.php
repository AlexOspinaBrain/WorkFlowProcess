<?php
session_start();
include("../../config/conexion.php");
	
	$conect = new conexion();

	//echo $_POST['tramite'];


	$sql = "SELECT id_radica FROM fac_radica 
		WHERE serial_factura = '".$_POST['tramite']."' ";

	$result = pg_query($sql) or die('La consulta fallo: ' . pg_last_error());

	while ($rta = pg_fetch_array($result, null, PGSQL_ASSOC)){

		$sql2 = "UPDATE fac_radica SET estado = 'Eliminado' WHERE id_radica = '".$rta['id_radica']."' ";
		$update1 = pg_query($sql2);

		$sql3 = "UPDATE fac_historial SET actividad = 'Eliminado', usuario_cod = '".$_SESSION['uscod']."', fecha_terminado = now() 
				WHERE id_radica = '".$rta['id_radica']."' AND actividad = 'Recibir en el área' AND fecha_terminado IS NULL";
		$update2 = pg_query($sql3);

		echo "El tramite se ha eliminado con exito!";
	}


?>
