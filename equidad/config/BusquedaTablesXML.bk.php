<?php
require_once ('conexion.php');

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname']:'';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder']:'';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

$dbadministracion=false;
$dbequidad=true;

$conect=new conexion();
$conect->queryequi("CREATE temp TABLE usuarios_tmp AS SELECT * FROM dblink('dbname=administracion', 'select usuario_cod, COALESCE(usuario_nombres,\'\')  || \' \' || 
		COALESCE(usuario_priape,\'\') || \' \' || COALESCE(usuario_segape,\'\')  from admusuario') as (usuario_cod text,  nombres text)");

$Detalles="'<a href=\"#\" class=\"Detalles\" title=\"Detalles tramite\" onClick=\"MuestraDetalles('|| cor.numtramite ||')\">'|| cor.numtramite ||'</a>', ";
if($_POST['consulta'] == 'ActualRadica'){	
        $consulta="SELECT $Detalles ('<a href=\"#\" onClick= \"MuestraCodeBar(' || cor.numtramite || ', ''".$_REQUEST['codusu']."'')\"><img src=\"images/barcode.png\" border=\"0\"></a>') as barcode, to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, (select (case when count(sr) > 0 then '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor(' || cor.numtramite || ')\">' || to_char(count(sr), '99') || ' <img src=\"images/clip.png\" width=\"12px\" border=\"0\"/></a>' else 'No'  end ) from correspondencia where srtodo=cor.numtramite) as adjuntos , cor.numfolios, '<a onClick=\"Eliminar(' || cor.numtramite || ')\" href=\"#\"><img src=\"images/delete.png\" border=\"0\"/></a>'";
        $where = " from trasacorrespondencia tra, radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where tra.numtramite=cor.numtramite and cor.tipodoc=doc.tipodocid and cor.area=are.areasid and are.agencia=ofi.codigo and tra.estado='RADICADO' AND cor.radicado='".$_REQUEST['codusu']."' and not exists(select * from trasacorrespondencia where numtramite=cor.numtramite and fechahora is not null and estado!='RADICADO')";
        $campoCount = "cor.numtramite";
}

if($_POST['consulta'] == 'LotesPlanillas'){
        $Detalles="'<a href=\"#\" class=\"Detalles\" title=\"Detalles tramite\" onClick=\"MuestraDetalles('|| tra.numtramite ||')\">'|| tra.numtramite ||'</a>', ";
        $consulta="select ('<input type=\"checkbox\" class=\"Tramites\" value=\"'||tra.numtramite||'\">'), $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end) as remitente, doc.tipo, cor.numfolios, max(tra.fechahora)";
        $where = " from trasacorrespondencia tra, radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where are.areasid=cor.area and cor.tipodoc=doc.tipodocid and cor.tipodoc=doc.tipodocid and are.agencia=ofi.codigo and cor.numtramite=tra.numtramite and tra.area='".$_REQUEST['areausu']."' and fechahora is not null and tra.estado!='CERRADO' and not exists (select * from trasacorrespondencia where numtramite=tra.numtramite and fechahora>tra.fechahora) group by tra.numtramite, cor.fecins, doc.tipo, cor.numfolios, cor.destinatario, cor.numtramite, cor.remitente, are.area, ofi.descrip";
        $campoCount = "tra.numtramite";
}

if($_POST['consulta'] == 'Escritorio'){	

	if($_REQUEST['FiltroEstado'] == 0){
		$FiltroEstado="and (tra.estado='RECIBIDO CORRESPONDENCIA' or tra.estado='RECIBIDO' or (tra.estado='RECIBIDO DESTINATARIO' and usuario='".$_REQUEST['uscod']."')) and tra.fechahora is null";
		$OpcionEscritorio="('<a title=\"Recibir tramite\" href=\"#\" onClick=RecibirIcono(' || cor.numtramite || ')><img src=\"images/recibir.png\" width=\"15px\" border=\"0\"/></a>'), ";
	}
	
	if($_REQUEST['FiltroEstado'] == 1){
		//$FiltroEstado="and tra.estado='CERRADO' and (cor.destinatario='".$_REQUEST['uscod']."' or exists(select corr.sr from trasacorrespondencia tras, radcorrespondencia corr where corr.numtramite=tras.numtramite and tras.estado='CERRADO' and tras.fechahora is null and tras.area='".$_REQUEST['areausu']."' and corr.destinatario='0' and tras.numtramite=cor.numtramite)) and tra.fechahora is null";
		$FiltroEstado="and tra.estado='CERRADO' and (case when tra.usuario is null then tra.area='".$_REQUEST['areausu']."' else cor.destinatario='".$_REQUEST['uscod']."' end) and tra.fechahora is null";
		$OpcionEscritorio="('<a title=\"Cerrar caso\" href=\"#\" onClick=CerrarIcono(' || cor.numtramite || ')><img src=\"images/good.png\" width=\"15px\" border=\"0\"/></a>'), ";
	}
	
	if($_REQUEST['FiltroEstado'] == 2){
		$FiltroEstado="and tra.estado='RADICADO'  and tra.usuario='".$_REQUEST['uscod']."'";
		$OpcionEscritorio="'', ";
	}
	
	if($_REQUEST['FiltroEstado'] == 3){
		$FiltroEstado="and tra.estado='CERRADO' and tra.usuario='".$_REQUEST['uscod']."' and tra.fechahora is not null";
		$OpcionEscritorio="'', ";
	}
	
	if($_REQUEST['FiltroEstado'] == 4){
		$FiltroEstado="and (tra.estado='DISTRIBUCION EXTERNA' or tra.estado='ENVIADO') and tra.area='".$_REQUEST['areausu']."' and tra.fechahora is null";
		$OpcionEscritorio="('<a title=\"Enviar correspondencia\" href=\"#\" onClick=CerrarIcono(' || cor.numtramite || ')><img src=\"images/envia.png\" width=\"15px\" border=\"0\"/></a>'), ";
	}

	$consulta="select $OpcionEscritorio $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, cor.numfolios, cor.identificacion ";

        $where = " from trasacorrespondencia tra, radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where cor.tipodoc=doc.tipodocid and cor.numtramite=tra.numtramite and cor.area=are.areasid and are.agencia=ofi.codigo $FiltroEstado and tra.area='".$_REQUEST['areausu']."'";
        $campoCount = "cor.numtramite";


        if($_REQUEST['FiltroEstado'] == 5){
                $consulta="select (select (case when count(sr) > 0 then '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor(' || cor.numtramite || ')\">' || to_char(count(sr), '99') || ' <img src=\"images/clip.png\" width=\"12px\" border=\"0\"/></a>' else 'No'  end ) from correspondencia where srtodo=cor.numtramite) as adjuntos , $OpcionEscritorio $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, cor.numfolios, cor.identificacion ";
                $where = " from trasacorrespondencia tra, radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where cor.tipodoc=doc.tipodocid and cor.numtramite=tra.numtramite and cor.area=are.areasid and are.agencia=ofi.codigo and cor.area='".$_REQUEST['areausu']."' and tra.estado='RADICADO' and not exists(select * from trasacorrespondencia where numtramite=cor.numtramite and fechahora is not null and area='".$_REQUEST['areausu']."') and not exists(select * from trasacorrespondencia where numtramite=cor.numtramite and ((fechahora is not null and area='".$_REQUEST['areausu']."') or estado='ELIMINADO'))";
        }

	
}

if($_POST['consulta'] == 'ConsultaTramite'){
	if($_REQUEST['NumTramite'] != null)
		$NumTramite="and cor.numtramite ='".$_REQUEST['NumTramite']."'";
		
	if($_REQUEST['Asunto'] != null)
		$NumTramite="and cor.asunto ilike '%".$_REQUEST['Asunto']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and to_char(cor.fecins,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and cor.fecins between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";
		
	if($_REQUEST['AreaOrigen'] != null)
		$AreaOrigen="and exists(select numtramite from trasacorrespondencia where estado='RADICADO' and numtramite=cor.numtramite and area='".$_REQUEST['AreaOrigen']."')";
	
	if($_REQUEST['AreaDestino'] != null)
		$AreaDestino="and cor.area ='".$_REQUEST['AreaDestino']."'";
		
	if($_REQUEST['Estado'] != null){
		if($_REQUEST['Estado'] == 1)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia where (estado='RECIBIDO CORRESPONDENCIA' or estado='RECIBIDO') and fechahora is null)";
		
		if($_REQUEST['Estado'] == 2)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia where estado='ENVIADO' and fechahora is null)";
			
		if($_REQUEST['Estado'] == 3)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia where estado='CERRADO' and fechahora is null)";
		
		if($_REQUEST['Estado'] == 4)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia tras where tras.estado='DEVUELTO' and tras.fechahora=(select max(fechahora) from trasacorrespondencia where numtramite=tras.numtramite))";
			
		if($_REQUEST['Estado'] == 5)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia tras where tras.estado='REDIRECCIONADO' and tras.fechahora=(select max(fechahora) from trasacorrespondencia where numtramite=tras.numtramite))";
			
		if($_REQUEST['Estado'] == 6)
			$Estado="and cor.numtramite in (select numtramite from trasacorrespondencia where estado='CERRADO' and fechahora is not null)";
	}
		
	$consulta="select $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, cor.numfolios, cor.identificacion ";
	$where = " from radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where cor.tipodoc=doc.tipodocid and cor.area=are.areasid and are.agencia=ofi.codigo $NumTramite $Fecha $Estado $AreaOrigen $AreaDestino";
	$campoCount = "cor.numtramite";
}

if($_POST['consulta'] == 'TramitesArea'){	
if($_REQUEST['NumTramite'] != null)
		$NumTramite="and cor.numtramite ='".$_REQUEST['NumTramite']."'";
		if($_REQUEST['Area'] != null)
		$Area="and cor.area ='".$_REQUEST['Area']."'";
		
		
	$OpcionEscritorio="('<a title=\"Redireccionar tramite\" href=\"#\" onClick=RedireccionaIcono(' || cor.numtramite || ')><img src=\"images/redire.png\" width=\"15px\" border=\"0\"/></a>'), ";
	$consulta="select $OpcionEscritorio $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, cor.numfolios, cor.identificacion ";
	$where = " from radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where cor.tipodoc=doc.tipodocid and cor.area=are.areasid and are.agencia=ofi.codigo and exists(select * from trasacorrespondencia where numtramite=cor.numtramite and fechahora is null and (estado='CERRADO' or estado='RECIBIDO DESTINATARIO') $Area $NumTramite)";
	$campoCount = "cor.numtramite";
}

if($_POST['consulta'] == 'Informe'){
	
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
	
	if ($NumTramite || $Fecha || $AreaOrigen || $AreaDestino){
	
		$consulta="select $Detalles to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecha, ((select (case when usuario_cod=cor.remitente then nombres else cor.remitente end) as remitente from usuarios_tmp order by usuario_cod=cor.remitente desc limit 1)), ofi.descrip, are.area, (case when cor.destinatario='0' then (select destinatario from radcorresext where numtramite=cor.numtramite ) else (select nombres from usuarios_tmp where usuario_cod=cor.destinatario) end), doc.tipo, cor.numfolios, cor.identificacion ";
		$where = " from radcorrespondencia cor, tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp doc where cor.tipodoc=doc.tipodocid and cor.area=are.areasid and are.agencia=ofi.codigo and LENGTH(cor.numtramite)=15 $NumTramite $Fecha $AreaOrigen $AreaDestino limit 5";
		$campoCount = "cor.numtramite";
	}
}

if($_POST['consulta'] == 'TodosLosPermisos'){
	if($_REQUEST['usuario_cod'] != null)
		$CodUsuario="and usu.usuario_cod ='".$_REQUEST['usuario_cod']."'";

	if($_REQUEST['Permiso'] != null)
		$Permiso="and usu.usuario_cod in (select usuario_cod from adm_usumenu where jerarquia_opcion = '".$_REQUEST['Permiso']."')";
	
	$dbadministracion=false;
	$dbequidad=true;
	
	$consulta=queryQR("select jerarquia_opcion from adm_menu order by jerarquia_opcion asc");
	while ($row = $consulta->FetchRow()){
		$permisos.=", '<input type=\"checkbox\" onClick=\"GuardaPermiso(\'' || usu.usuario_cod || '\', \'".$row['jerarquia_opcion']."\', this)\" ' || (case when exists(select * from adm_usumenu where usuario_cod=usu.usuario_cod and jerarquia_opcion='".$row['jerarquia_opcion']."') then 'checked' else '' end) || '/>' ";
	}
	
	$consulta="select usu.usuario_desc, (COALESCE(usu.usuario_nombres,'')  || ' ' || COALESCE(usu.usuario_priape,'') || ' ' || COALESCE(usu.usuario_segape,'')) $permisos ";
	$where = " from adm_usuario usu where 1=1 $CodUsuario $Permiso";
	$campoCount = "usu.usuario_desc";
}

if($_POST['consulta'] == 'AdmAgencias'){
	if($_REQUEST['Agencia'] != null)
		$Agencia="where codigo ='".$_REQUEST['Agencia']."'";
		
	$opcion = '<img src=\"images/edit.png\" width="15px" border="0" style="cursor: pointer" onClick="editaAgencia(\'\'\'|| ofi.codigo||\'\'\', \'\'\'|| ofi.descrip||\'\'\')">';
	$consulta="select codigo, descrip , '".$opcion."'";
	$where = " from tblradofi ofi $Agencia";
	$campoCount = "ofi.codigo";
}

if($_POST['consulta'] == 'AdmAreas'){
	if($_REQUEST['Area'] != null)
		$Area="and are.areasid ='".$_REQUEST['Area']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and are.agencia ='".$_REQUEST['Agencia']."'";
	
	if($_REQUEST['Corres'] != null)
		$Corres="and ".(($_REQUEST['Corres'] == 'SI')?"correspondencia ='t'":"correspondencia is null");

	$opcion = '<img src=\"images/edit.png\" width="15px" border="0" style="cursor: pointer" onClick="editaArea(\'\'Edita\'\', \'\'\'|| are.areasid||\'\'\', \'\'\'|| are.area||\'\'\', \'\'\'|| are.agencia||\'\'\', \'\'\'|| (case when are.correspondencia=\'t\' then \'SI\' else \'NO\' end)||\'\'\')">';
	$consulta="select are.area, ofi.descrip, (case when are.correspondencia='t' then 'SI' else 'NO' end) , '".$opcion."'";
	$where = " from tblareascorrespondencia are, tblradofi ofi where are.agencia=ofi.codigo $Area $Agencia $Corres";
	$campoCount = "are.areasid";
}

if($_POST['consulta'] == 'AdmDocumentos'){
	if($_REQUEST['Doc'] != null)
		$Doc="and doc.tipo ilike '".utf8_decode($_REQUEST['Doc'])."%'";
		
	if($_REQUEST['Area'] != null)
		$Area="and doc.area ='".$_REQUEST['Area']."'";
	
	if($_REQUEST['Prio'] != null)
		$Prio="and ".(($_REQUEST['Prio'] == 'SI')?"prioridad ='t'":"prioridad is null");
		
	$opcion = '<img src=\"images/edit.png\" width="15px" border="0" style="cursor: pointer" onClick=\"editaDocumento(\'\'Edita\'\', \'\'\'|| doc.tipodocid||\'\'\', \'\'\'|| doc.tipo||\'\'\', \'\'\'|| doc.area ||\'\'\', \'\'\'|| (case when doc.prioridad=\'t\' then \'SI\' else \'NO\' end)||\'\'\')\">';
	$consulta="select doc.tipo, are.area, (case when doc.prioridad='t' then 'SI' else 'NO' end), '".$opcion."'";
	$where = " from tblareascorrespondencia are, tbltiposdoccorresp doc where doc.area=are.areasid $Doc $Area $Prio";
	$campoCount = "doc.tipodocid";
}

if($_POST['consulta'] == 'InformeOportunidad'){		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and to_char(fecins,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecins between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";
	
	
		$consulta="select proceso, catn, ord ";
		$where = " from (
SELECT 'SINIESTROS VIDA' as proceso, count(*) as catn, 1 as ord from arpplanillassiniestros where 1=1 $Fecha  union
SELECT 'INTERMEDIARIOS' as proceso, count(*) as catn, 2 from planillasintermediarios  where 1=1 $Fecha and cab = 21 union
SELECT 'PROVEEDORES' as proceso, count(*) as catn, 3 from planillasintermediarios  where 1=1 $Fecha and cab = 39 union
SELECT 'HOJAS DE VIDA' as proceso, count(*) as catn, 4 from planillashojasdevida  where 1=1 $Fecha union
SELECT 'CONTRATOS' as proceso, count(*) as catn, 5 from planillasintermediarios  where 1=1 $Fecha and cab = 40 union
SELECT 'TESORERIA' as proceso, count(*) as catn, 6 from planillastesoreria  where 1=1 $Fecha and cab = 45 union
select 'CONTABILIDAD', sum(cont), 7 from(
	SELECT  count(*) cont from planillastesoreria  where 1=1 $Fecha and (cab = 53 or cab = 54 or cab = 52 or cab = 55 or cab = 56)union
	SELECT count(*) cont from planillascontabilidad where 1=1 $Fecha and (cab = 48 or cab = 49 or cab = 50)
) as a UNION
select 'TOTAL', sum(cont), 8 from(

SELECT  count(*) AS cont  from arpplanillassiniestros  where 1=1 $Fecha union
SELECT  count(*)  from planillasintermediarios  where 1=1 $Fecha  union
SELECT count(*) from planillashojasdevida  where 1=1 $Fecha union
SELECT count(*) from planillastesoreria  where 1=1 $Fecha union
SELECT count(*) from planillascontabilidad  where 1=1 $Fecha
) AS C)as b";
	$campoCount = "proceso";

}

if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

if ($query) $where .= " and CAST($qtype as TEXT) ILIKE '%".pg_escape_string($query)."%' ";

$ordena = " ORDER BY $sortname $sortorder";
$limit = " LIMIT $rp OFFSET $start";

if($dbequidad)
	$result=$conect->queryequi( "SELECT count($campoCount)" . $where);//obtiene numero de registros numero 

if($dbadministracion)
	$result=$conect->query( "SELECT count($campoCount)" . $where);//obtiene numero de registros numero 

$row = pg_fetch_array($result);
$total=$row[0];

if($dbequidad)
	$result=$conect->queryequi( $consulta . $where . $ordena . $limit);//obtiene registros consulta

if($dbadministracion)
	$result=$conect->query( $consulta . $where . $ordena . $limit);//obtiene registros consulta


$rows = array();
while ($row = pg_fetch_array($result)) {
	$rows[] = $row;	
}

$conect->cierracon();

header("Content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
$xml .= "<rows>";
$xml .= "<page>$page</page>";
$xml .= "<total>$total</total>";
foreach($rows AS $row){
	$xml .= "<row >";
	for($i=0; $i<sizeof($row); $i++){
		$xml .= "<cell><![CDATA[".$row[$i]."]]></cell>";
	}
	$xml .= "</row>";
}
$xml .= "</rows>";
echo $xml;
