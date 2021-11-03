<?php
$tiempo_inicio = microtime(true);
require("../config/adodb5/adodb.inc.php");
require("../config/conexion.php");
$db = NewADOConnection("oci8");
	$db->charSet = 'we8iso8859p1';
	$ls=$db->Connect("(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.200.12)(PORT = 1521))) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME= osiris)))", "wfimagine", "wfimagine");
	$ADODB_CACHE_DIR = "/tmp/adodb_cache/";
	$query= "select 
       fecren as InicioTecnico,
       fecini as InicioCertificado,
      osiris.fc_codpla(a.codpla) as Descripcion,
       poliza,
       certif, 
       orden,
       SUCUR,
       a.tomador,
       pmolano.fc_traer500(substr(a.sucur,2),'nombre') as Radicada,
       ltrim(rtrim(pmolano.fc_traer500(a.tomador,'nit'),' '),'0') as NitTomador,
       pmolano.fc_traer500(a.tomador,'nombre') as NombreTomador,
       ltrim(rtrim(pmolano.fc_traer500(a.asegurado,'nit'),' '),'0') as NitAsegurado,
       pmolano.fc_traer500(a.asegurado,'nombre') as NombreAsegurado,
       ltrim(rtrim(pmolano.fc_traer500(a.beneficiario,'nit'),' '),'0') as NitBeneficiario,
       pmolano.fc_traer500(a.beneficiario,'nombre') as NombreBeneficiario,
       ltrim(rtrim(pmolano.fc_traer500(a.agente,'nit'),' '),'0') as NitIntermediario,
       pmolano.fc_traer500(a.agente,'nombre') as NombreIntermediario,
       CODPLA AS PRODUCTO,
       pmolano.fc_traer500(substr(a.sucur,2),'nombre') as DescripcionPlan,
       fecter as FinTecnico,
       Fecter as FinCertificado,
       decode( substr(sucur,1,1),'1','Generales','Vida') as Compania,
       tipcer,
       estado,
       osiris.fc_traerdet502(a.tomador, '00000060') as cod_ciudad,
       osiris.fc_traer105('00000060', osiris.fc_traerdet502(a.tomador, '00000060')) as Ciudad,
       osiris.fc_traer105('00000065', osiris.fc_traerdet502(a.tomador, '00000065')) as Departamento,
       osiris.fc_traerdet502(a.tomador, '630') as Direccion,
       osiris.fc_traerdet502(a.tomador, '700') as Telefono,
       osiris.fc_traerdet502(a.tomador, '831') as CorreoElectronico,
       osiris.fc_traerdet502(a.beneficiario, '630') as Direccionben,
       osiris.fc_traerdet502(a.beneficiario, '700') as Telefonoben,
       osiris.fc_traerdet502(a.beneficiario, '831') as CorreoElectronicoben,
       osiris.fc_traerdet502(a.asegurado, '630') as Direccionasegu,
       osiris.fc_traerdet502(a.asegurado, '700') as Telefonoasegu,
       osiris.fc_traerdet502(a.asegurado, '831') as CorreoElectronicoasegu
FROM OSIRIS.S03020 A";

	$conn->cacheSecs = 3600*24;
	$rs = $db->SelectLimit($query, 5000,75000);
	$count=0;
	if (!$rs) {
		print $db->ErrorMsg(); // Displays the error message if no results could be returned
	} else {
		while ($row = $rs->FetchRow()) {
			$result=queryQR("select * from wf_producto where poliza='".$row['POLIZA']."' AND nittomador='".$row['NITTOMADOR']."' 
						and nitasegurado='".$row['NITASEGURADO']."' and nitbeneficiario='".$row['NITBENEFICIARIO']."'");
			if($result->RecordCount() == 0){
				$count++;
				queryQR("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
						nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, ciudad, departamento, iniciocertificado, fincertificado) 
						values ('".$row['POLIZA']."', 
						'".$row['INICIOTECNICO']."', '".$row['FINTECNICO']."', '".$row['DESCRIPCION']."', '".$row['RADICADA']."', '".$row['NITTOMADOR']."', '".$row['NOMBRETOMADOR']."', 
						'".$row['NITASEGURADO']."', '".$row['NOMBREASEGURADO']."', '".$row['NITBENEFICIARIO']."', '".$row['NOMBREBENEFICIARIO']."', '".$row['NITINTERMEDIARIO']."', 
						'".$row['NOMBREINTERMEDIARIO']."', '".$row['COMPANIA']."', '".$row['TIPOCER']."', '".$row['ESTADO']."', '".$row['CIUDAD']."', '".$row['DEPARTAMENTO']."', '".
						$row['INICIOTECNICO']."', '".$row['FINTECNICO']."')");
						
			
				echo("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
						nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, ciudad, departamento, iniciocertificado, fincertificado) 
						values ('".$row['POLIZA']."', 
						'".$row['INICIOTECNICO']."', '".$row['FINTECNICO']."', '".$row['DESCRIPCION']."', '".$row['RADICADA']."', '".$row['NITTOMADOR']."', '".$row['NOMBRETOMADOR']."', 
						'".$row['NITASEGURADO']."', '".$row['NOMBREASEGURADO']."', '".$row['NITBENEFICIARIO']."', '".$row['NOMBREBENEFICIARIO']."', '".$row['NITINTERMEDIARIO']."', 
						'".$row['NOMBREINTERMEDIARIO']."', '".$row['COMPANIA']."', '".$row['TIPOCER']."', '".$row['ESTADO']."', '".$row['CIUDAD']."', '".$row['DEPARTAMENTO']."', '".
						$row['INICIOTECNICO']."', '".$row['FINTECNICO']."')<br><br>");
					
					
			}
		}  // end while
		
		
		
	} // end else


/*while ($row = $rs->FetchRow()) {
	echo $row['POLIZA']."<br>";
}*/


/*while ($row = $result->FetchRow()) {
	$result2=queryOci("select * from OSIRIS.produccionclientes where poliza='".$row['POLIZA']."' order by orden desc ");
	$row2 = $result2->FetchRow();
	queryQR("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
			nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, ciudad, departamento) values ('".$row2['POLIZA']."', 
			'".$row2['INICIOTECNICO']."', '".$row2['FINTECNICO']."', '".$row2['DESCRIPCION']."', '".$row2['RADICADA']."', '".$row2['NITTOMADOR']."', '".$row2['NOMBRETOMADOR']."', 
			'".$row2['NITASEGURADO']."', '".$row2['NOMBREASEGURADO']."', '".$row2['NITBENEFICIARIO']."', '".$row2['NOMBREBENEFICIARIO']."', '".$row2['NITINTERMEDIARIO']."', 
			'".$row2['NOMBREINTERMEDIARIO']."', '".$row2['COMPANIA']."', '".$row2['TIPOCER']."', '".$row2['ESTADO']."', '".$row2['CIUDAD']."', '".$row2['DEPARTAMENTO']."')");
	echo "<br><br><br><br>";

}
*/
$tiempo_fin = microtime(true);
echo "<br>total de insercciones: " . $count;
echo "<br>Tiempo de ejecución redondeado: " . round($tiempo_fin - $tiempo_inicio, 4);

function microtime_float()
{
list($useg, $seg) = explode(" ", microtime());
return ((float)$useg + (float)$seg);
}