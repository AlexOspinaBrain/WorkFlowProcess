<?php
header ('Content-type: text/html; charset=ISO-8859-1');
include ("conexion.php");
require_once ('../Correspondencia/Trazabilidad.php');

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";
$basedb = new conexion();

$basedb->queryequi("CREATE temp TABLE usuarios_tmp AS SELECT * FROM dblink('dbname=administracion', 'select usuario_cod, 
		COALESCE(usuario_nombres,'' '')  || '' '' || COALESCE(usuario_priape,'' '') || '' '' || COALESCE(usuario_segape,'' ''), usuario_bloqueado  from admusuario') as (usuario_cod text, 
		nombres text, usuario_bloqueado boolean)");

if($opcion=='buscaNombreYUsuario'){
	$rsus = $basedb->queryequi("select usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from adm_usuario where usuario_nombres ilike '".$qAjax."%' order by usuario_nombres");
	$rsus2 = $basedb->queryequi("select usuario_cod, usuario_desc from adm_usuario where usuario_desc ilike '".$qAjax."%' order by usuario_desc");
	$rsus3 = $basedb->queryequi("select usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from adm_usuario where (usuario_nombres ilike '%".$qAjax."%' and usuario_nombres not ilike '".$qAjax."%') or usuario_priape ilike '%".$qAjax."%' or usuario_segape ilike '%".$qAjax."%' order by usuario_nombres");
	$rsus4 = $basedb->queryequi("select usuario_cod, usuario_desc from adm_usuario where usuario_desc ilike '%".$qAjax."%' and usuario_desc not ilike '".$qAjax."%' order by usuario_desc");
	
	while ($row = pg_fetch_array($rsus)){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["nombres"]."\" }, ";
	}

	while ($row = pg_fetch_array($rsus2)){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["usuario_desc"]."\" }, ";
	}
	
	while ($row = pg_fetch_array($rsus3)){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["nombres"]."\" }, ";
	}
	
	while ($row = pg_fetch_array($rsus4)){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["usuario_desc"]."\" }, ";
	}
}

if($opcion=='buscausuario'){
	$rsus = $basedb->query("select usuario_cod, usuario_nombres, usuario_priape, usuario_segape from admusuario where area='$qAjax' and usuario_bloqueado=false order by usuario_nombres");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row["usuario_cod"].'", "value": "'.$row["usuario_nombres"].' '.$row["usuario_priape"].' '.$row["usuario_segape"].'" }, ';
	}
}


if($opcion=='buscausuariotramite'){
	$rsus = $basedb->queryequi("select usuario_cod, usuario_nombres, usuario_priape, usuario_segape from adm_usuario join radcorrespondencia  using (area) where numtramite='$qAjax' and usuario_bloqueado=false order by usuario_nombres");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row["usuario_cod"].'", "value": "'.$row["usuario_nombres"].' '.$row["usuario_priape"].' '.$row["usuario_segape"].'" }, ';
	}
}

if($opcion=='buscausuariotramite1'){
	$rsus = $basedb->queryequi("select * from trasacorrespondencia where numtramite='$qAjax' and fechahora is null ");
		while ($row = pg_fetch_array($rsus))
		{
			if($row["estado"] == "CERRADO" or $row["estado"] == "RECIBIDO DESTINATARIO" )
			{
				$rsus = $basedb->query("select usuario_cod, usuario_nombres, usuario_priape, usuario_segape from admusuario where area='".$row["area"]."' and usuario_bloqueado=false order by usuario_nombres");
				$usuari = array();
				while ($row = pg_fetch_array($rsus))
				{
					$usuari[]=  array("id"=>$row['usuario_cod'],"value"=>utf8_encode($row['usuario_nombres'].' '.$row["usuario_priape"].' '.$row["usuario_segape"]));
					
				}
				$salida.='{ "usuari": '.json_encode($usuari).'}, ';
			}
			else
			{
			$rsus = $basedb->queryequi("select l.agencia from tblareascorrespondencia as l join radcorrespondencia as e on e.area=l.areasid WHERE e.numtramite='$qAjax'  ");
				while ($ro = pg_fetch_array($rsus))
				{
					$rsus1 = $basedb->queryequi("select area,areasid FROM tblareascorrespondencia where agencia='".$ro['agencia']."'");
					$ar = array();
						while ($row = pg_fetch_array($rsus1))
						{
							$ar[]=  array("id"=>$row['areasid'],"value"=>utf8_encode($row['area']));
						}
						$salida.='{ "ar": '.json_encode($ar).'}, ';

				}

			}
		}
	}



if($opcion=='buscaarea'){
	$rsus = $basedb->queryequi("select distinct(are.areasid), are.area from tblareascorrespondencia are".$_REQUEST['addtablas']." where ".$_REQUEST['where']." are.agencia='$qAjax' order by are.area");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row["areasid"].'", "value": "'.$row["area"].'" }, ';
	}
}


if($opcion=='buscatiposdocu'){
	$rsus = $basedb->queryequi("select tipodocid, tipo, prioridad from tbltiposdoccorresp where area='$qAjax' and activo order by tipo asc");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row["tipodocid"].'", "value": "'.trim($row["tipo"]).'", "prioridad": "'.$row["prioridad"].'"}, ';
	}
}

if($opcion=='buscaciudades'){
	$rsus = $basedb->queryequi("select idciudad, ciudad from tblciudades order by ciudad");

	while ($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "'.$row["idciudad"].'", "value": "'.trim($row["ciudad"]).'"}, ';
	}
}

if($opcion=='buscacodbar'){
	$rsus=$basedb->queryequi("select count(*), (select usuario from trasacorrespondencia where numtramite='$qAjax' order by sr limit 1) as remitente from trasacorrespondencia where numtramite='$qAjax' and usuario is not null");
	$row = pg_fetch_array($rsus);
	if($row[0] == '1' && $row[1]==$_REQUEST['Usuario'])
		$salida.='{ "id": "Nuevo", "value": "true"}, ';
	else
		$salida.='{ "id": "Nuevo", "value": "false"}, ';

	$rsus=$basedb->queryequi("select to_char(rad.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, rad.numfolios, rad.asunto,  are.area, ofi.descrip, rad.destinatario, rad.siniestro, rad.tipodoc,
			(case when rad.destinatario='0' then (select destinatario from radcorresext where numtramite=rad.numtramite ) else 
			(select nombres from usuarios_tmp where usuario_cod=rad.destinatario) end) as destinatario, rad.numtramite, rad.ciudad ,
			((select (case when usuario_cod=rad.remitente then nombres else rad.remitente end) as remitente from usuarios_tmp order by 
			usuario_cod=rad.remitente desc limit 1)) as remitente from radcorrespondencia rad, tblareascorrespondencia are, tblradofi ofi 
			where rad.numtramite='$qAjax' and are.agencia=ofi.codigo and rad.area=are.areasid ");

	if($row = pg_fetch_array($rsus)){
		$salida.='{ "id": "FechaHora", "value": "'.$row["fecha"].'"}, 
				  { "id": "Folios", "value": "'.$row["numfolios"].'"},
				  { "id": "Area", "value": "'.$row["area"].'"},
				  { "id": "Destinatario", "value": "'.$row["destinatario"].'"}, 
				  { "id": "Agencia", "value": "'.$row["descrip"].'"}, 
				  { "id": "Asunto", "value": "'.$row["asunto"].'"},
				  { "id": "Remitente", "value": "'.$row["remitente"].'"}, ';
	}
	
	if($row["area"] == 'CORRESPONDENCIA EXTERNA'){
		$rsus=$basedb->queryequi("select ext.*, ciu.ciudad from radcorrespondencia cor, radcorresext ext, tblciudades ciu where 
					cor.numtramite=ext.numtramite and cor.ciudad=ciu.idciudad and cor.numtramite='".$row["numtramite"]."'");
		if($row = pg_fetch_array($rsus)){
			$salida.='{ "id": "Ciudad", "value": "'.$row["ciudad"].'"}, 
					  { "id": "Direccion", "value": "'.$row["direccion"].'"}, 
					  { "id": "Telefono", "value": "'.$row["telefono"].'"}, ';
			$salida.='{ "id": "Siniestro", "value": "'.$row["siniestro"].'"}, ';
				$rssin=$basedb->queryequi("select * from radsiniestro where numtramite='".$row["numtramite"]."'");
				if($rowsin = pg_fetch_array($rssin)){
                   	   $salida.='{ "id": "Producto", "value": "'.$rowsin["producto"].'"}, ';
                }
		}
	}else{
			$salida.='{ "id": "Siniestro", "value": "'.$row["siniestro"].'"}, ';
				$rssin=$basedb->queryequi("select * from radsiniestro where numtramite='".$row["numtramite"]."'");
				if($rowsin = pg_fetch_array($rssin))
                   	$salida.='{ "id": "Producto", "value": "'.$rowsin["producto"].'"}, ';
                else
                	$salida.='{ "id": "Producto", "value": " "}, ';
	}
	
}

if($opcion=='CierraLote'){
	$row=$basedb->queryequi("update radlote set fechacierre=now(), usuariocerro=".$_REQUEST['codusuario']." where lote='$qAjax'");
	$rows=pg_affected_rows($row);
	$salida.='{"value": "'.$rows.'"}, ';	
}

if($opcion=='GuardaNumGuia'){

	/*$consulta=$basedb->queryequi("select estado from trasacorrespondencia where numtramite='$qAjax' and fechahora is not null order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	if($row['estado'] != 'DEVUELTO'){	
		$consulta=$basedb->queryequi("select tra.sr, tra.estado, (select areasid from tblareascorrespondencia where agencia=(select agencia from tblareascorrespondencia 
					where areasid=cor.area) and correspondencia='t') as area from trasacorrespondencia tra, radcorrespondencia cor where cor.numtramite=tra.numtramite 
					and tra.numtramite='$qAjax' and (tra.estado='ENVIADO' or tra.estado='DISTRIBUCION EXTERNA') and tra.fechahora is null");
		if($row = pg_fetch_array($consulta)){
			$basedb->queryequi("update trasacorrespondencia set fechahora=now(), usuario=".$_REQUEST['codusuario']." where sr='".$row['sr']."'");
			
			$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', ".(($row['estado'] == 'ENVIADO') ? "'RECIBIDO CORRESPONDENCIA', '".$row['area']. "')":"'CERRADO' , '".$_REQUEST['areausu']."')"));
		
			$basedb->queryequi("insert into radenvio (numguia, id_empresa, sr) values ('".$_REQUEST['NumGuia']."', ".$_REQUEST['EmpresaMsj'].", ".$row['sr'].")");
		}	
	}else{
		$consulta=$basedb->queryequi("select tra.sr, tra.estado	from trasacorrespondencia tra, radcorrespondencia cor where cor.numtramite=tra.numtramite and tra.numtramite='$qAjax' and 
			(tra.estado='ENVIADO' or tra.estado='DISTRIBUCION EXTERNA') and tra.fechahora is null");
		if($row = pg_fetch_array($consulta)){
			$basedb->queryequi("update trasacorrespondencia set fechahora=now(), usuario=".$_REQUEST['codusuario']." where sr='".$row['sr']."'");
			$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', 'REDIRECCIONADO', (select (select areasid from tblareascorrespondencia where agencia=are.agencia and correspondencia='t') from trasacorrespondencia tra, tblareascorrespondencia are where are.areasid=tra.area and tra.numtramite='$qAjax' and estado='RADICADO'))");
			$basedb->queryequi("insert into radenvio (numguia, id_empresa, sr) values ('".$_REQUEST['NumGuia']."', ".$_REQUEST['EmpresaMsj'].", ".$row['sr'].")");
		}
	}*/
	
	$consulta=$basedb->queryequi("select tra.sr, tra.estado	from trasacorrespondencia tra, radcorrespondencia cor where cor.numtramite=tra.numtramite and tra.numtramite='$qAjax' and 
			(tra.estado='ENVIADO' or tra.estado='DISTRIBUCION EXTERNA') and tra.fechahora is null");
	if($row = pg_fetch_array($consulta)){
		$basedb->queryequi("insert into radenvio (numguia, id_empresa, sr) values ('".$_REQUEST['NumGuia']."', ".$_REQUEST['EmpresaMsj'].", ".$row['sr'].")");
	}
	
	$basedb->queryequi("update trasacorrespondencia set fechahora=now(), usuario='".$_REQUEST['codusuario']."' where numtramite='$qAjax' and area='".$_REQUEST['areausu']."' 
					and fechahora is null and (estado='ENVIADO' OR estado='DISTRIBUCION EXTERNA')");
	Trazabilidad($qAjax, $_REQUEST['codusuario'], null);
	$salida.='{"value": "t"}, ';	
}

if($opcion=='GeneraPlanilla'){
	$consulta=$basedb->query("select * from admusuario where usuario_cod='".$_REQUEST['codusuario']."'");
	$info = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select are.area, ofi.descrip from tblareascorrespondencia are, tblradofi ofi where are.agencia=ofi.codigo and are.areasid='".$info['area']."'");
	$info2 = pg_fetch_array($consulta);

	$rsus=$basedb->queryequi("select are.agencia, ofi.descrip as agenciadesc, cor.area, are.area as areadesc from 
					radcorrespondencia cor, tblareascorrespondencia are, tblradofi ofi where cor.area=are.areasid 
					and are.agencia=ofi.codigo $qAjax GROUP BY are.agencia, ofi.descrip, are.area, 
					cor.area order by agenciadesc");
					
	$cont=0;
	while ($row = pg_fetch_array($rsus)){
		$rsus2 = $basedb->queryequi("select cor.numtramite, (select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1) as remitente, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end) as destinatario, tip.tipo, to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecharadicacion from radcorrespondencia cor, tbltiposdoccorresp tip where cor.tipodoc=tip.tipodocid and cor.area='".$row['area']."' $qAjax order by cor.numtramite asc");
		$rsus3 = $basedb->queryequi ("select count(cor.numtramite) from radcorrespondencia cor, tbltiposdoccorresp tip where cor.area='".$row['area']."' $qAjax and cor.tipodoc=tip.tipodocid ");
		$row3 = pg_fetch_array($rsus3);
		$tabla.="<fieldset><legend align='right'><a href='#' onClick='Imprimir(".++$cont.", this)'>Imprimir planilla <img src='images/print.png' border='0' width='20px'/></a></legend><table border='0' width='100%' id='planilla".$cont."'>".
				"<tr><th><img src='images/equidad.jpg' width='150px'></th>".
				"<td class='formular' align='left'>".
				"<fieldset><legend>DESTINO CORRESPONDENCIA</legend>".
				"<label><span>Agencia: </span>".$row['agenciadesc']."</label>".
				"<label><span>Area: </span>".$row['areadesc']."</label></fieldset>".
				"</td></tr>".
				"<tr><td colspan='2'>".
				"<table style='text-align:left'><tr><th>Generado por: </th><td width='400px'>".$info['usuario_nombres']." ".$info['usuario_priape']." ".$info['usuario_segape']."</td>".
				"<th></th><td></td></tr>".
				"<tr><th>Agencia: </th><td>".$info2['descrip']."</td>".
				"<th>Generado el </th><td>".date("d/m/Y")." a las ".date("h:i:s")."</td></tr>".
				"</table></td></tr>".
				"<tr><td colspan='2'>".
				"<table border='1' width='100%' class='radplanilla' cellspacing='0px'><tr><th style='background-color: #F0F0F0'>Num tramite</th><th>Remitente</th><th>Destinatario</th><th>Tipo doc</th><th>Fecha radicación</th><th>Observaciones</th><th>Recibido por</th></tr><tbody>";
		while ($row2 = pg_fetch_array($rsus2)){
			$tabla.="<tr><td>".$row2['numtramite']."</td><td>".$row2['remitente']."</td><td>".$row2['destinatario']."</td><td>".$row2['tipo']."</td><td>".$row2['fecharadicacion']."</td><td>.</td><td class='recibido'>.</td></tr>";
		}
		$tabla.="</tbody></table><br><b style='margin-left: 85%;'>Total tramites: ".$row3[0]."</b>".
				"</td></tr>".
				"</table></fieldset><br><br>";
	}
	$tabla=str_replace(array("\n", "\r", "\n\r"), '', $tabla);
	$salida.='{"value": "'.$tabla.'"}, ';	
}

if($opcion=='SiRecibirCor'){
	corrigeTramite($qAjax);
	/*$consulta=$basedb->queryequi("select tra.estado, cor.destinatario  from trasacorrespondencia tra, radcorrespondencia cor where cor.numtramite=tra.numtramite and tra.numtramite='$qAjax' and tra.area='".$_REQUEST['areausu']."' 
					and fechahora is null and (tra.estado='RECIBIDO CORRESPONDENCIA' OR tra.estado='DISTRIBUCION' or tra.estado='REDIRECCIONADO' 
					or (tra.estado='RECIBIDO' AND 'DISTRIBUCION'=(select estado from trasacorrespondencia where 
					numtramite='$qAjax' and fechahora is not null order by fechahora desc limit 1)))");
	if($row = pg_fetch_array($consulta)){
		if( $row['destinatario'] == '0')
			$tipo='externo';
		else
			$tipo='interno';
		
		$resp=$row['estado'];
	}else
		$resp='f';*/
		
	$consulta=$basedb->queryequi("select rad.fecins, (case when rad.destinatario='0' then (select destinatario from radcorresext where numtramite=rad.numtramite ) else 
			(select nombres from usuarios_tmp where usuario_cod=rad.destinatario) end) as destinatario, (select nombres from usuarios_tmp, trasacorrespondencia where 
			estado='RADICADO' AND numtramite='$qAjax' and usuario_cod=usuario LIMIT 1) as remitente, to_char(rad.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, asunto from 
			radcorrespondencia rad, trasacorrespondencia tra where tra.numtramite=rad.numtramite and  rad.numtramite='$qAjax' and (tra.estado='RECIBIDO' or 
			tra.estado='RECIBIDO CORRESPONDENCIA' or (tra.estado='RECIBIDO DESTINATARIO' and tra.usuario='".$_REQUEST['usucod']."')) and tra.fechahora is null 
			and tra.area='".$_REQUEST['areausu']."'");	
	if($row = pg_fetch_array($consulta)){	
		$resp="<table align='center'>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Remitente: </b>".$row['remitente']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Destinatario: </b>".$row['destinatario']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Fecha y hora de radicación: </b><br>".$row['fecha']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Asunto: </b>".$row['asunto']."</td></tr></table>";
		//$resp='t';
	}else{
		$resp='f';
	}
	
	$consulta=$basedb->queryequi("select destinatario from radcorrespondencia where numtramite='$qAjax'");
	$row = pg_fetch_array($consulta);
	if($row['destinatario'] == 0){
		$tipo='externo';
	}else{
		$tipo='interno';
	}
	
	$consulta=$basedb->queryequi("select estado from trasacorrespondencia where numtramite='$qAjax' and (estado='REDIRECCIONADO' OR estado='DEVUELTO') order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	$resp2 = $row['estado'];
	
	$consulta=$basedb->queryequi("select area from trasacorrespondencia where numtramite='$qAjax' and estado='RADICADO'");
	$row = pg_fetch_array($consulta);
	$area = $row['area'];
	
	$salida.='{"value": "'.$resp.'"}, {"value": "'.$tipo.'"}, {"value": "'.$resp2.'"}, {"value": "'.$area.'"}, ';
}

if($opcion=='SiEnviarCor'){
	corrigeTramite($qAjax);
	$resp='f';
	
	$consulta=$basedb->queryequi("select * from trasacorrespondencia where numtramite='$qAjax' and estado='ENVIADO' and area='".$_REQUEST['areausu']."' and fechahora is null");
	if($row = pg_fetch_array($consulta)){
		$resp='t';
	}	
	$salida.='{"value": "'.$resp.'"}, ';	
}

if($opcion=='SiCerrarCor'){
	corrigeTramite($qAjax);
		$resp='f';
	
	$consulta=$basedb->queryequi("select * from radcorrespondencia where numtramite='$qAjax'");
	if($row = pg_fetch_array($consulta)){
		if($row['area'] == 68){
			$consulta2=$basedb->queryequi("select numtramite from trasacorrespondencia where numtramite='$qAjax' and area='".$_REQUEST['areausu']."' and estado='CERRADO' and fechahora is null");
			if($row2 = pg_fetch_array($consulta2))
				$resp='externo';
		}else{
			$consulta2=$basedb->queryequi("select numtramite from trasacorrespondencia where numtramite='$qAjax' and usuario='".$_REQUEST['uscod']."' and estado='CERRADO' and fechahora is null");
			if($row2 = pg_fetch_array($consulta2))
				$resp='interno';
		}		
		$detalles="<table align='center'>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Remitente: </b>".$row['remitente']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Destinatario: </b>".$row['destinatario']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Fecha y hora de radicación: </b><br>".$row['fecha']."</td></tr>".
				"<tr><td style='border-bottom: 1px solid gray;'><b>Asunto: </b>".$row['asunto']."</td></tr></table>";
	}
	
	$salida.='{"value": "'.$resp.'"}, {"value": "'.$detalles.'"}, ';	
}
if($opcion=='SiRedirecionarCor'){
	corrigeTramite($qAjax);
	$consulta=$basedb->queryequi("select * from trasacorrespondencia where numtramite='$qAjax' and area=".$_REQUEST['areausu']." and fechahora is null 
			and (estado='CERRADO' or estado='RECIBIDO DESTINATARIO')");
	if($row = pg_fetch_array($consulta)){
		$resp='t';
	}else{
		$resp='f';
	}
	
	$salida.='{"value": "'.$resp.'"}, ';	
}


if($opcion=='CerrarTramite'){
	$consulta=$basedb->queryequi("UPDATE trasacorrespondencia set fechahora=now(), usuario='".$_REQUEST['uscod']."' where numtramite='$qAjax' and estado='CERRADO'");
	$rows=pg_affected_rows($consulta);
	if($_REQUEST['TipoCor'] == 'externo')
		EnviaCorreo($qAjax);
	$salida.='{"value": "'.$_REQUEST['TipoCor'].'"}, ';	
}

if($opcion=='RecibirTramite'){	
	/*$consulta2=$basedb->queryequi("select estado from trasacorrespondencia where numtramite='$qAjax' and 
			area='".$_REQUEST['areausu']."' and fechahora is null and (estado='RECIBIDO CORRESPONDENCIA' OR estado='DISTRIBUCION' or 
			(estado='RECIBIDO' AND EXISTS(select numtramite from trasacorrespondencia where numtramite='$qAjax' 
			and estado='DISTRIBUCION' and fechahora is not null)))");
	$row2 = pg_fetch_array($consulta2);
	
	if($row2['estado'] == 'RECIBIDO CORRESPONDENCIA'){
		$consulta3=$basedb->queryequi("select cor.destinatario, are.agencia from trasacorrespondencia tra, radcorrespondencia cor, tblareascorrespondencia are where 
			cor.area=are.areasid and cor.numtramite=tra.numtramite and tra.numtramite='$qAjax' and estado ='".$row2['estado']."'");
		$row3 = pg_fetch_array($consulta3);
			
		if($row3['destinatario'] == '0'){
			$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', 'DISTRIBUCION EXTERNA', '".$_REQUEST['areausu']."')");	
		}else{
			$consulta=$basedb->queryequi("select agencia from tblareascorrespondencia where areasid='".$_REQUEST['areausu']."'");
			$row = pg_fetch_array($consulta);
			
			if($row['agencia'] == $row3['agencia']){
				$basedb->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values ('$qAjax', now()+'00:00:02', '".$_REQUEST['usu']."', 'DISTRIBUCION', '".$_REQUEST['areausu']."')");	
			}else{
				$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', 'ENVIADO', '".$_REQUEST['areausu']."')");	
			}
		}
	}

	if($row2['estado'] == 'RECIBIDO'){
		$basedb->queryequi("insert into trasacorrespondencia (numtramite, usuario, estado, area) values ('$qAjax', (select destinatario from radcorrespondencia where numtramite='$qAjax'), 'CERRADO', '".$_REQUEST['areausu']."')");
	}*/
	
	$basedb->queryequi("update trasacorrespondencia set fechahora=now(), usuario='".$_REQUEST['usu']."' where numtramite='$qAjax' and area='".$_REQUEST['areausu']."' 
					and fechahora is null and (estado='RECIBIDO CORRESPONDENCIA' OR estado='RECIBIDO' or estado='RECIBIDO DESTINATARIO')");
		
	Trazabilidad($qAjax, $_REQUEST['usu'], null);
	
	$salida.='{"value": "t"}, ';		
}

if($opcion=='EliminaTramite'){
	$row=$basedb->queryequi("DELETE FROM trasacorrespondencia WHERE numtramite='$qAjax' and fechahora is null;
					insert into trasacorrespondencia (numtramite, estado, fechahora, usuario, area) VALUES ('$qAjax', 'ELIMINADO', NOW(), '".$_REQUEST['codusuario']."', '".$_REQUEST['areausuario']."');");
	$rows=pg_affected_rows($row);
	$salida.='{"value": "'.$rows.'"}, ';	
}

if($opcion=='DetallesTramite'){	
	corrigeTramite($qAjax);
	$consulta=$basedb->queryequi("select usuario from trasacorrespondencia where (estado='RADICADO' OR estado='REDIRECCIONADO') 
			AND fechahora is not null and numtramite='$qAjax' order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	if($row['usuario'] == $_REQUEST['usu'])
		$codebar="<a href='#' onClick= 'MuestraCodeBar($qAjax)'><img src='images/barcode.png' border='0' style='padding-left: 20px;' width='20px'></a>";
		
	$consulta=$basedb->queryequi("select cor.destinatario, tra.usuario, cor.destinatario from trasacorrespondencia tra, radcorrespondencia cor where 
			tra.numtramite=cor.numtramite and cor.numtramite='$qAjax' and tra.estado='RADICADO'");
	$row = pg_fetch_array($consulta);
	if($row[0] == $_REQUEST['usu'] || $row[1] == $_REQUEST['usu']){
		$consulta=$basedb->queryequi("select count(sr) from correspondencia where srtodo='$qAjax'");
		$row = pg_fetch_array($consulta);
		if($row[0])
			$adjuntos="<th>Adjuntos: </th><td><a href='#' class='Planillalote' onClick='MuestraVisor($qAjax)'>".$row[0]."<img src='images/clip.png' width='12px' border='0'/></a></td>";
		else		
			$adjuntos="<th>Adjuntos: </th><td> No </td>";
	}
	
	//if($row['destinatario'] == '0'){
		//$consulta=$basedb->queryequi("select correspondencia from tblareascorrespondencia where areasid='".$_REQUEST['areausu']."'");
		//$row = pg_fetch_array($consulta);
		//if($row[0] == 't')
			$codebar="<a href='#' onClick= 'MuestraCodeBar($qAjax)'><img src='images/barcode.png' border='0' style='padding-left: 20px;' width='20px'></a>";
	//}
		
	$consulta=$basedb->queryequi("select (select nombres from usuarios_tmp where usuario_cod=tra.usuario) as radicado, 
			(select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) from usuarios_tmp order 
			by usuario_cod=cor.remitente desc limit 1) as remitente, (case when cor.destinatario='0' then (select 
			destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where 
			usuario_cod=cor.destinatario) end) as destinatario, cor.numguia, ciu.ciudad, doc.tipo, are.area, 
			ofi.descrip, (select area from tblareascorrespondencia where areasid=cor.area) as areadestino, (select descrip 
			from tblradofi ofi, tblareascorrespondencia are where are.agencia=ofi.codigo and  are.areasid=cor.area) as 
			agenciadestino, cor.observaciones, cor.numfolios, cor.asunto, cor.siniestro from radcorrespondencia cor, tblciudades ciu, 
			tbltiposdoccorresp doc, trasacorrespondencia tra, tblareascorrespondencia are, tblradofi ofi where 
			ofi.codigo=are.agencia and are.areasid=tra.area and  tra.numtramite=cor.numtramite and cor.ciudad=ciu.idciudad 
			and doc.tipodocid=cor.tipodoc and cor.numtramite='$qAjax' and tra.estado='RADICADO'");
	$row = pg_fetch_array($consulta);

	if(strlen($row['numguia'])>0)
		$DocExterno="<tr><td colspan='4'><fieldset style='width:90%;' class='filtros'><legend style='color:#08298A'><b>Correspondencia origen externo </b></legend>".
		"<table class='TableResult'><tr><th>Documento externo:</th><td style='padding-right:30px'> Si </td><th>Ciudad: </th><td>".$row['ciudad']."</td></tr>".
		"<tr><th>Numero guia: </th><td colspan='3'>".$row['numguia']."</td></tr>".
		"</table></fieldset></td></tr>";
		
	if($row['areadestino']=='CORRESPONDENCIA EXTERNA'){
		$consulta=$basedb->queryequi("select * from radcorresext where numtramite='$qAjax'");
		$row2 = pg_fetch_array($consulta);
		
		$DocExterno="<tr><td colspan='4'><fieldset style='width:90%;' class='filtros'><legend style='color:#08298A'><b>Correspondencia destino externo </b></legend>".
		"<table class='TableResult'><tr><th>Prioridad: </th><td colspan='3' style='padding-right:30px'> ".$row2['prioridad']." </td></tr>".
		"<tr><th>Ciudad:</th><td style='padding-right:30px'> ".$row['ciudad']." </td><th>Dirección: </th><td>".$row2['direccion']."</td></tr>".
		"<tr><th>Teléfono:</th><td colspan='3' style='padding-right:30px'> ".$row2['telefono']." </td></table></fieldset></td></tr>";
	}
	
	$tabla="<table align='center'>".
		   "<tr><td><fieldset style='width:100%;' class='filtros'><legend style='color:#08298A'><b>Detalles tramite </b></legend>".
		   "<table border='0' class='TableResult'>".
		   "<tr><th>Numero tramite: </th><td> $qAjax $codebar</td><th></th><td>  </td></tr>".
		   "<tr><th>Tipo doc: </th><td> ".$row['tipo']." </td>$adjuntos</tr>".
		   "<tr><th>Asunto: </th><td> ".$row['asunto']." </td><th>Num folios: </th><td> ".$row['numfolios']." </td></tr>".
		   "<tr><th>Radicado por: </th><td colspan='3'> ".$row['radicado']." </td></tr>".
		   "<tr><th>Remitente: </th><td> ".$row['remitente']." </td><th>Destinatario: </th><td> ".$row['destinatario']." </td></tr>$DocExterno".
		   "<tr><th>Area origen: </th><td> ".$row['area']." </td><th>Area Destino: </th><td> ".$row['areadestino']." </td></tr>".
		   "<tr><th>Agencia origen: </th><td> ".$row['descrip']." </td><th>Agencia Destino: </th><td> ".$row['agenciadestino']." </td></tr>";

		   if($row['siniestro']!=''){

		        $conssin=$basedb->queryequi("select * from radsiniestro where numtramite='$qAjax'");
				$rowsin = pg_fetch_array($conssin);

				
		   		$tabla .= "<tr><th>Siniestro: </th><td> ".$row['siniestro']." </td><th>Producto: </th><td> ".$rowsin['producto']." </td></tr>";

		   }
		   		   
	$tabla .=  "<tr><th style='vertical-align: text-top'>Observaciones: </th><td colspan='3'> ".(($row['observaciones'] == null)?"No hay observaciones":str_replace(array("\n", "\r", "\t"), ' ', $row['observaciones']))." </td></tr>".
		   "</table></fieldset></td></tr>".
		   "<tr><td><fieldset style='width:100%;' class='filtros'><legend style='color:#08298A'><b>Historial: </b></legend><table border='1' cellspacing='0' class='TableResult'><tr><th>Fecha y hora</th><th>Estado</th><th>Usuario</th><th>Area</th><th>Envio</th></tr>";
	
	
	$consulta=$basedb->queryequi("select (select nombres from usuarios_tmp where usuario_cod=tra.usuario), 
			to_char(tra.fechahora,'yyyy-MM-dd HH:MI:SS AM') as fecha, tra.estado, are.area, (select (empresa || '<br>' || numguia) from radenvio env, tblempresamsj msj where env.id_empresa=msj.id_empresa and sr=tra.sr order by idenvio desc limit 1) as envio from trasacorrespondencia tra, 
			tblareascorrespondencia are, radcorrespondencia cor where cor.numtramite=tra.numtramite and are.areasid=tra.area 
			and cor.numtramite='$qAjax' order by tra.fechahora");
			
	while ($row = pg_fetch_array($consulta)){
		$tabla.="<tr ".(($row['fecha'] == null) ? "style='color:red'" : '') ."><td>".(($row['fecha'] == null) ? 'PENDIENTE' : $row['fecha']) ."</td><td>".$row['estado']."</td><td>".$row[0]."</td><td>".$row['area']."</td><td>".$row['envio']."</td></tr>";
	}
	$tabla.="</table></fieldset></td></tr></table>";
	$tabla=str_replace(array("\n", "\r", "\t"), ' ', $tabla);
	
	$salida.='{"value": "'.$tabla.'"}, ';	
}

if($opcion=='AreaDevolucion'){	
	$consulta=$basedb->queryequi("select are.areasid, are.agencia, are.correspondencia, are.area from trasacorrespondencia tra, 
				tblareascorrespondencia are where tra.area=are.areasid and tra.estado='RADICADO' and numtramite='$qAjax'");
	$row = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select agencia, area, correspondencia from tblareascorrespondencia where 
			areasid='".$_REQUEST['areausu']."'");
	$row2 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select areasid,area from tblareascorrespondencia where agencia='".$row['agencia']."' and correspondencia='t'");
	$row3 = pg_fetch_array($consulta);
	
	if($_REQUEST['areausu'] != $row['areasid']){
		if($row['agencia'] == $row2['agencia']){
			if($row2['correspondencia'] != 't'){
				$consulta=$basedb->queryequi("select areasid, area from tblareascorrespondencia where agencia='".$row['agencia']."' and correspondencia='t'");
				$row4 = pg_fetch_array($consulta);
				$id=$row4['areasid'];
				$valor=$row4['area'];
			}else{
				$id=$row['areasid'];
				$valor=$row['area'];
			}
		}else{
			if($row2['correspondencia'] != 't'){
				$consulta=$basedb->queryequi("select areasid, area from tblareascorrespondencia where agencia='".$row2['agencia']."' and correspondencia='t'");
				$row4 = pg_fetch_array($consulta);
				$id=$row4['areasid'];
				$valor=$row4['area'];
			}else{
				$id=$row3['areasid'];
				$valor=$row3['area'];
			}
		}
	}else{
		$id="f";
		$valor="f";
	}
	
	$salida.='{"value": "'.$id.'"}, {"value": "'.$valor.'"}, ';	
}

if($opcion == "BuscaGuia"){
	$rsus = $basedb->queryequi("select numguia from radenvio where numguia ilike '".$qAjax."%' group by numguia");
	
	while ($row = pg_fetch_array($rsus)){
		$salida.="{ \"id\": \"".$row["idenvio"]."\", \"value\": \"".$row["numguia"]."\" }, ";
	}
}

if($opcion == "BuscaEmpresaMsj"){
	$rsus = $basedb->queryequi("select empresa from radenvio where empresa ilike '".$qAjax."%' group by empresa order by empresa ");
	
	while ($row = pg_fetch_array($rsus)){
		$salida.="{ \"id\": \"".$row["idenvio"]."\", \"value\": \"".$row["empresa"]."\" }, ";
	}
}
if($opcion=='DevolucionTramite'){
		$consulta = $basedb->query("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  from admusuario where usuario_cod='".$_REQUEST['usu']."'");		
		$row = pg_fetch_array($consulta);
		$Observaciones="<b style=''color: #00009B; font-size: 9px''>".date("h:i:s A d-m-Y ").' - '.$row[0].":</b> <div style=''margin-left:30px; width:500px''>CAUSA DE DEVOLUCION: ".$_REQUEST['Causa'].'<br>'.$_REQUEST['Observacion'].'</div>';// Guarda nombre comentario

	$consulta=$basedb->queryequi("update radcorrespondencia set observaciones = COALESCE(observaciones, '') ||'$Observaciones' where numtramite='$qAjax';
					delete from trasacorrespondencia where fechahora is null and numtramite='$qAjax';
					insert into trasacorrespondencia (numtramite, estado, fechahora, usuario, area) values ('$qAjax', 
					'DEVUELTO', NOW(), '".$_REQUEST['usu']."', ".$_REQUEST['areausu'].");");
					
	$consulta=$basedb->queryequi("SELECT (select agencia from tblareascorrespondencia where areasid='".$_REQUEST['areausu']."') as origen,
					(select agencia from tblareascorrespondencia where areasid='".$_REQUEST['AreaRedi']."') as destino");
					
	$row = pg_fetch_array($consulta);
	
	if($row['origen'] != $row['destino']){
		$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', 'ENVIADO', '".$_REQUEST['areausu']."')");
	}else{
		$basedb->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$qAjax', 'RECIBIDO', '".$_REQUEST['AreaRedi']."')");
	}
	
	$salida.='{"value": "t"}, ';
}

if($opcion=='DatosExterno'){
	$consulta=$basedb->queryequi("select cor.ciudad, ext.direccion, ext.destinatario, ext.telefono from radcorrespondencia cor, 
			radcorresext ext where ext.numtramite=cor.numtramite and cor.numtramite='$qAjax' ");
	$row = pg_fetch_array($consulta);
	$salida.='{"value": "'.$row['ciudad'].'"}, {"value": "'.$row['direccion'].'"}, {"value": "'.$row['destinatario'].'"}, {"value": "'.$row['telefono'].'"}, ';
}

if($opcion=='Informes'){
	if($_REQUEST['NumTramite'] != null)
		$NumTramite="and cor.numtramite ='".$_REQUEST['NumTramite']."'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and to_char(cor.fecins,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and cor.fecins between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";
		
	if($_REQUEST['AreaOrigen'] != null)
		$AreaOrigen="and exists(select numtramite from trasacorrespondencia where estado='RADICADO' and numtramite=cor.numtramite and area='".$_REQUEST['AreaOrigen']."')";
	
	if($_REQUEST['AreaDestino'] != null)
		$AreaDestino="and cor.area ='".$_REQUEST['AreaDestino']."'";


	$wheretrasa = " from radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc, trasacorrespondencia tra where tra.numtramite=cor.numtramite and cor.tipodoc=doc.tipodocid and cor.area=are.areasid and are.agencia=ofi.codigo and LENGTH(cor.numtramite)=15 $NumTramite $Fecha $AreaOrigen $AreaDestino";
	
	$consulta=$basedb->queryequi("select to_char(min(cor.fecins),'yyyy-MM-dd HH:MI:SS AM') as periododesde, to_char(max(cor.fecins),'yyyy-MM-dd HH:MI:SS AM') as periodohasta, count(distinct(cor.numtramite)) as total $wheretrasa");
	$row = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("CREATE temp TABLE promediodia_tmp AS select count(distinct(cor.numtramite)) $wheretrasa group by to_char(cor.fecins,'dd'); select CAST(avg(count) as decimal(4,1)) as promediodia from promediodia_tmp");
	$row2 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) as docinternos $wheretrasa and cor.area!='68'");
	$row3 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select count(distinct(cor.numtramite)) as docexternos $wheretrasa and cor.area='68'");
	$row4 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area!='68' and tra.estado='RECIBIDO' and fechahora is not null");
	$row5 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area!='68' and tra.estado='CERRADO' and fechahora is not null");
	$row6 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area!='68' and tra.estado='DEVUELTO' and fechahora is not null");
	$row7 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area!='68' and tra.estado='REDIRECCIONADO' and fechahora is not null");
	$row8 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area='68' and tra.estado='DISTRIBUCION EXTERNA' and fechahora is not null");
	$row9 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area='68' and tra.estado='CERRADO' and fechahora is not null");
	$row10 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area='68' and tra.estado='DEVUELTO' and fechahora is not null");
	$row11 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("select  count(distinct(cor.numtramite)) $wheretrasa and cor.area='68' and tra.estado='REDIRECCIONADO' and fechahora is not null");
	$row12 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("CREATE temp TABLE promediointerno_tmp AS select age(tra.fechahora,cor.fecins) $wheretrasa and cor.area!='68' AND tra.estado='CERRADO' and fechahora is not null; select to_char(avg(age),  'dd \"dia(s)\" HH:MI:SS') as promediointerno from promediointerno_tmp");
	$row13 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("CREATE temp TABLE promedioexterno_tmp AS select age(tra.fechahora,cor.fecins) $wheretrasa and cor.area='68' AND tra.estado='CERRADO' and fechahora is not null; select to_char(avg(age),  'dd \"dia(s)\" HH:MI:SS') as promedioexterno from promedioexterno_tmp");
	$row14 = pg_fetch_array($consulta);
	
	$consulta=$basedb->queryequi("CREATE temp TABLE promediototal_tmp AS select age(tra.fechahora,cor.fecins) $wheretrasa AND tra.estado='CERRADO' and fechahora is not null; select to_char(avg(age),  'dd \"dia(s)\" HH:MI:SS') as promediototal from promediototal_tmp");
	$row15 = pg_fetch_array($consulta);
	
	$salida.='{ "PeriodoDesde": "'.$row['periododesde'].'", "PeriodoHasta": "'.$row['periodohasta'].'", "TotalTramites": "'.$row['total'].'", "PromedioDia": "'.
			$row2['promediodia'].'", "DocInternos": "'.$row3['docinternos'].'", "DocExternos": "'.$row4['docexternos'].'", "InternoRecibido": "'.$row5[0].
			'", "InternoCerrado": "'.$row6[0].'", "InternoDevuelto": "'.$row7[0].'", "InternoRedireccionado": "'.$row8[0].'", "ExternoDistribucion": "'.$row9[0].
			'", "ExternoCerrado": "'.$row10[0].'", "ExternoDevuelto": "'.$row11[0].'", "ExternoRedireccionado": "'.$row12[0].'", "PromedioInterno": "'.$row13[0].
			'", "PromedioExterno": "'.$row14[0].'", "PromedioTotal": "'.$row15[0].'" }, ';
}

if($opcion=='menuwq'){
	$consulta=queryQR("select jerarquia_opcion from adm_menu order by jerarquia_opcion asc");
	while ($row = $consulta->FetchRow()){
		$salida.='{"value": "'.$row['jerarquia_opcion'].'"}, ';
	}	
}

if($opcion=='GuardaPermisos'){
	if($_REQUEST['accion'] == 'Habilita')
		$result = queryQR("insert into adm_usumenu (usuario_cod, jerarquia_opcion) values ('".$_REQUEST['usuario']."', '".$_REQUEST['opcion']."')");
		
	if($_REQUEST['accion'] == 'Deshabilita')
		$result = queryQR("delete from adm_usumenu where usuario_cod='".$_REQUEST['usuario']."' and jerarquia_opcion='".$_REQUEST['opcion']."'");
	$rows=1;
	$salida.='{"value": "'.$rows.'"}, ';
}

if($opcion=='GuardaAgencia'){
	if($_REQUEST['nuevo'] == 'Nueva agencia'){
		$rsus = $basedb->queryequi("select * from tblradofi where codigo='".$_REQUEST['id']."' or descrip='".$_REQUEST['data']."'");
		if($row = pg_fetch_array($rsus)){
			$salida.='{"value": "Duplicado"}, ';
		}else{
			$consulta = $basedb->queryequi("insert into tblradofi (codigo, descrip) values ('".$_REQUEST['id']."', '".$_REQUEST['data']."')");
			$rows=pg_affected_rows($consulta);
			$salida.='{"value": "'.$rows.'"}, ';
		}
	}else{
		$consulta = $basedb->queryequi("update tblradofi set descrip=upper('".$_REQUEST['data']."') where codigo='".$_REQUEST['nuevo']."'");
		$rows=pg_affected_rows($consulta);
		$salida.='{"value": "'.$rows.'"}, ';
	}	
}

if($opcion=='GuardaArea'){
	if($_REQUEST['accion'] == 'Nueva area'){
		$consulta = $basedb->queryequi ("insert into tblareascorrespondencia (area, agencia, correspondencia) values (upper('".$_REQUEST['area']."'), '".$_REQUEST['agencia']."', ".(($_REQUEST['corres'] =='SI')?'\'t\'':'null').")");
	}else{
		$consulta = $basedb->queryequi ("update tblareascorrespondencia set area=upper('".$_REQUEST['area']."'), agencia='".$_REQUEST['agencia']."', correspondencia=".(($_REQUEST['corres'] =='SI')?'\'t\'':'null')." where areasid='".$_REQUEST['id']."'");
	}
	$rows=pg_affected_rows($consulta);
	$salida.='{"value": "'.$rows.'"}, ';
}

if($opcion=='GuardaDocu'){
	if($_REQUEST['accion'] == 'Nuevo documento'){
		$consulta = $basedb->queryequi ("insert into tbltiposdoccorresp (tipo, area, prioridad, activo) values (upper('".utf8_decode($_REQUEST['doc'])."'), '".$_REQUEST['areaDocu']."', ".(($_REQUEST['Prioritario'] =='SI')?'\'t\'':'null').", true)");
	}else{
		$consulta = $basedb->queryequi ("update tbltiposdoccorresp set tipo=upper('".utf8_decode($_REQUEST['doc'])."'), area='".$_REQUEST['areaDocu']."', prioridad=".(($_REQUEST['Prioritario'] =='SI')?'\'t\'':'null').", activo=true where tipodocid=".$_REQUEST['id']);
	}
	//$rows=pg_affected_rows($consulta);
	$salida.='{"value": "'.$consulta.'"}, ';
}

if($opcion=='CerrarTramiteDev'){	
	$consulta = $basedb->query("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  from admusuario where usuario_cod='".$_REQUEST['idusu']."'");		
		$row = pg_fetch_array($consulta);
	$Observaciones="<b style=''color: #00009B; font-size: 9px''>".date("h:i:s A d-m-Y ").' - '.$row[0].":</b> <div style=''margin-left:30px; width:500px''>OBSERVACIÓN DE CIERRE: <br>".$_REQUEST['observacion'].'</div>';// Guarda nombre comentario

	$consulta=$basedb->queryequi("update radcorrespondencia set observaciones = COALESCE(observaciones, '') ||'$Observaciones' where numtramite='$qAjax';
					update trasacorrespondencia set usuario=".$_REQUEST['idusu'].", area=".$_REQUEST['areausu'].", fechahora=now() where fechahora is null and numtramite='$qAjax';
					insert into trasacorrespondencia (numtramite, estado, fechahora, usuario, area) values ('$qAjax', 
					'CERRADO', NOW()+'00:00:01', '".$_REQUEST['idusu']."', ".$_REQUEST['areausu'].");");
					
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
<?php
function corrigeTramite($tramite){
	$conecta=new conexion();
	$consulta=$conecta->queryequi("select *, rad.area areadestino from radcorrespondencia rad join trasacorrespondencia tra using(numtramite) where numtramite='$tramite' and fechahora is null");
	$result = pg_fetch_all($consulta);
	if(count($result) > 1){
		$row = $result[0];
		if($row['areadestino'] == '68'){
			$area=$conecta->queryequi("select * from trasacorrespondencia tra where numtramite='$tramite' and estado='ENVIADO'");
			$area = pg_fetch_array($area);			
			$area = $area['area'];
			$usuario = "null";
		}else{
			$usuario = $row['destinatario'];
			$area = $row['areadestino'];
		}
		
		$conecta->queryequi("delete from trasacorrespondencia where numtramite='$tramite' and fechahora is null");
		$conecta->queryequi("insert into trasacorrespondencia (numtramite, estado, usuario, area) values('$tramite', 'CERRADO', $usuario, $area)");		
	}		
}

function EnviaCorreo($NumTramite){
	require 'phpmailer/class.phpmailer.php';
	$conecta=new conexion();
	$consulta=$conecta->queryequi("select to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecharad, ofi.descrip, doc.tipo, cor.destinatario, 
			tra.usuario from radcorrespondencia cor, tblradofi ofi, tbltiposdoccorresp doc, trasacorrespondencia tra, tblareascorrespondencia are
			where cor.area=are.areasid and ofi.codigo=are.agencia and tra.numtramite=cor.numtramite and tra.estado='RADICADO' AND
			cor.tipodoc=doc.tipodocid and cor.numtramite='$NumTramite'");
	$row = pg_fetch_array($consulta);
	$consulta=$conecta->query("select * from admusuario where usuario_cod='".$row['cor.destinatario']."'");
	$row2 = pg_fetch_array($consulta);
	$consulta=$conecta->queryequi("select destinatario from radcorresext where numtramite='".$NumTramite."'");
	$row3 = pg_fetch_array($consulta);

	$correos =array(strtolower($row2['usuario_correo']));
	//$correos =array("dxmefisto@gmail.com");
	
	$body	= file_get_contents('phpmailer/contentsconfirmation.html');
	$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
	$body   = str_replace('<HoraRadicado>',$row['fecharad'], $body);
	$body   = str_replace('<Agencia>',$row['descrip'], $body);
	$body   = str_replace('<Desti>',$row3['destinatario'], $body);
	$body   = str_replace('<TipoDocumento>',$row['tipo'], $body);
	$body   = str_replace('<NumTramite>',$NumTramite, $body);

try {
	$mail = new PHPMailer(true); 
	$body = preg_replace('/\\\\/','', $body);
	
	$mail->IsSMTP();                           
	$mail->SMTPAuth   = false;             
	$mail->Port       = 25;                    
	$mail->Host       = "192.168.241.63"; 
	//$mail->Host       = "outlook.laequidad.com.co";
	$mail->From       = "correspondencia@laequidadseguros.coop";
	$mail->FromName   = "Correspondencia Equidad";
	$mail->Subject  = "Alerta de correspondencia";	
	$mail->MsgHTML($body);
	$mail->IsHTML(true); 
	$intentos=0;
	
	foreach( $correos as $destino ) {
		$mail->addAddress( $destino );
	} 
	
	while ((!$mail->Send()) && ($intentos < 5)) {
		sleep(2);
		$intentos=$intentos+1;
	}
	//echo 'Message has been sent.'.$intentos;
} catch (phpmailerException $e) {
	echo "<script>alert('No se ha podido enviar el e-mail al destinatario debido a un error ');</script>";//echo $e->errorMessage();
}
}
?>
