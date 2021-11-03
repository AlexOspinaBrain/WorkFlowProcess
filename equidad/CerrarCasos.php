<?php
/*require_once ('config/conexion.php');
$conect=new conexion(); 
$consulta=$conect->queryequi(" select ra.id_radica from fac_radica ra join fac_historial hi
using (id_radica) where hi.fecha_terminado is null
and ra.fechahora_ins BETWEEN '2013/09/01' and '2014/08/31' and hi.usuario_cod='1353' ");

while ($row = pg_fetch_array($consulta))
{
echo $row['serial_factura']."<br>";
$editar=$conect->queryequi("update fac_radica set observaciones='<p style=\"color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px\"> <span style=\"color: #327E04;font-weight: bold\">Cerrado autom&aacute;tico por antig&uuml;edad de los casos</span> </p>', estado='Anulado' where id_radica='".$row['id_radica']."' ");
$edita=$conect->queryequi("update fac_historial set fecha_terminado='now()' where id_radica='".$row['id_radica']."' and fecha_terminado is null ");
$inse=$conect->queryequi("insert INTO fac_historial( actividad,fecha_asignado,fecha_terminado,usuario_cod,estado,id_radica) VALUES('Anulado','now()','now()','0', 'normal','".$row['id_radica']."') ");
echo $row['serial_factura']."<br>";
}*/

include ("config/conexion.php");


$result=queryQR("SELECT t.sr, r.numtramite, r.fecins, t.estado, t.fechahora, t.area FROM radcorrespondencia AS r
		INNER JOIN trasacorrespondencia AS t ON t.numtramite = r.numtramite
		WHERE r.fecins >= '2015/03/31' AND t.fechahora IS null AND t.area IN ('4','85','108')
		ORDER BY r.numtramite ASC");

//$row = $result->FetchRow();

while ($row = $result->FetchRow()){

	echo $row['sr']." ".$row['numtramite']." ".$row['area'];


	queryQR("UPDATE trasacorrespondencia SET area = '110' WHERE sr = '".$row['sr']."' ");

	
	echo "<br>";
}


?>

