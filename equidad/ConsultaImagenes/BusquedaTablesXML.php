<?php
require_once ('../config/conexion.php');
header('Content-Type: text/html; charset=iso-8859-1'); 


$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname']:'';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder']:'';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

$conect=new conexion();

if($_POST['consulta'] == 'OtrosPagos'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Transferencia'] != null)
		$Transferencia="and transferencia = '".$_REQUEST['Transferencia']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";
		
	if($_REQUEST['Cuenta'] != null)
		$Cuenta="and cuenta = '".$_REQUEST['Cuenta']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech,  transferencia, comprobante, banco, cuenta, to_char(valor, '999,999,999,999,999'), (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'ptesoreria\')\"> Ver ' || count(*) || '</a>' from planillastesoreria where srtodo=pla.srtodo)";
	$where = " from planillastesoreria pla where cab=54 and tipo='P' $Nombre $Fecha $Transferencia $Comprobante $Cuenta $Valor";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'Traslados'){	

	if($_REQUEST['Traslado'] != null)
		$Traslado="and cab='".$_REQUEST['Traslado']."'";
	else
		$Traslado="and (cab='55' or cab='56')";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and codint='".$_REQUEST['Comprobante']."'";

	if($_REQUEST['Banco'] != null)
		$Banco="and banco='".utf8_decode($_REQUEST['Banco'])."'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Compania="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Transferencia'] != null)
		$Transferencia="and transferencia = '".$_REQUEST['Transferencia']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";
		
	if($_REQUEST['Cuenta'] != null)
		$Cuenta="and cuenta = '".$_REQUEST['Cuenta']."'";	
		
	if($_REQUEST['NroComprobante'] != null)
		$NroComprobante="and comprobante = '".$_REQUEST['NroComprobante']."'";

	$consulta="select codint, banco, compañia, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, transferencia, to_char(valor, '999,999,999,999,999'), cuenta, comprobante,(select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'ptesoreria\')\"> Ver ' || count(*) || '</a>' from planillastesoreria where srtodo=pla.srtodo)";
	$where = " from planillastesoreria pla where tipo='P' $Traslado $Comprobante $Banco $Fecha $Compania $Transferencia $Valor $Cuenta $NroComprobante ";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'ComprobantesIngreso'){	
		if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";

	$consulta="select to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, agencia, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pcontabilidad\')\"> Ver ' || count(*) || '</a>' from planillascontabilidad where srtodo=pla.srtodo)";
	$where = " from planillascontabilidad pla where cab=49 and tipo='P' $Nombre $Fecha $Campañia $Agencia";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'ComprobantesEgreso'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";

	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";
		
	if($_REQUEST['Cheque'] != null)
		$Cheque="and cheque = '".$_REQUEST['Cheque']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, agencia, cheque, comprobante, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pcontabilidad\')\"> Ver ' || count(*) || '</a>' from planillascontabilidad where srtodo=pla.srtodo)";
	$where = " from planillascontabilidad pla where cab=48 and tipo='P' $Nombre $Fecha $Campañia $Agencia $Cheque $Comprobante";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'TalentoHumano'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";	
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, banco, to_char(valor, '999,999,999,999,999'), (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'ptesoreria\')\"> Ver ' || count(*) || '</a>' from planillastesoreria where srtodo=pla.srtodo)";
	$where = " from planillastesoreria pla where cab=53 and tipo='P' $Nombre $Fecha $Campañia $Valor";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'Reaseguros'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";	
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, transferencia, to_char(valor, '999,999,999,999,999'), (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'ptesoreria\')\"> Ver ' || count(*) || '</a>' from planillastesoreria where srtodo=pla.srtodo)";
	$where = " from planillastesoreria pla where cab=52 and tipo='P' $Nombre $Fecha $Campañia $Valor";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'ComprobantesInternos'){	

	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";

	$consulta="select to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, comprobante, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pcontabilidad\')\"> Ver ' || count(*) || '</a>' from planillascontabilidad where srtodo=pla.srtodo)";
	$where = " from planillascontabilidad pla where cab=50 and tipo='P' $Fecha $Campañia $Comprobante";
	$campoCount = "fecha";
}

if($_POST['consulta'] == 'HojasVida'){	
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
	
	if($_REQUEST['Documento'] != null)
		$Documento="and numid='".$_REQUEST['Documento']."'";
		
	if($_REQUEST['Estado'] != null)
		$Estado="and estado='".$_REQUEST['Estado']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia='".$_REQUEST['Agencia']."'";

	$result=$conect->query("select * from admusuario where usuario_cod='".$_REQUEST['Usuario']."'");
	$row = pg_fetch_array($result);	
	
	$Usudd = strtoupper($row['usuario_desc']);

	$Detalle = "(select '<a href=''#'' class=''Planillalote'' onClick=MuestraVisor('||pla.srtodo||',''phojasvida'',''$Usudd'','|| pla.numid ||')> Ver '|| count(*) ||'
			</a>' from planillashojasdevida where srtodo=pla.srtodo)";


	$consulta="select tipid, numid, nombre, estado, agencia, $Detalle";
	$where = " from planillashojasdevida pla where cab=42 and tipo='P' and pla.numid!='".$row['numerodoc']."' $Nombre $Documento $Estado $Agencia";
	$campoCount = "numid";
}

if($_POST['consulta'] == 'LogHojasVida'){	

	if($_REQUEST['Usuario'] != null)
		$Nombre="and usuario = '".$_REQUEST['Usuario']."'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd') between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
	
	if($_REQUEST['DocumentoHV'] != null)
		$DocumentoHV="and reg.numid like '%".$_REQUEST['DocumentoHV']."%'";
		
	if($_REQUEST['NombreHV'] != null)
		$NombreHV="and reg.numid='".$_REQUEST['NombreHV']."'";

	$consulta="SELECT nombres, are.area, to_char(reg.feconsulta,'yyyy-MM-dd HH:MI:SS AM'), reg.numid, (select nombre from planillashojasdevida where numid=reg.numid limit 1)";
	$where = " FROM dblink('dbname=administracion', 'select usuario_desc, COALESCE(usuario_nombres,'' '')  || 
	'' '' || COALESCE(usuario_priape,'' '') || '' '' || COALESCE(usuario_segape,'' ''), area from admusuario') as 
		usu(usuario_desc text, nombres text, areausu integer), 
		registroconsultahv reg, tblareascorrespondencia are 
		where reg.usuario=upper(usu.usuario_desc) and are.areasid=areausu $Nombre $Fecha $DocumentoHV $NombreHV";	
	$campoCount = "feconsulta";
}

if($_POST['consulta'] == 'ActualizaHojasVida'){	
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
	
	if($_REQUEST['Documento'] != null)
		$Documento="and numid='".$_REQUEST['Documento']."'";
		
	if($_REQUEST['Estado'] != null)
		$Estado="and estado='".$_REQUEST['Estado']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia='".$_REQUEST['Agencia']."'";

	$result=$conect->query("select * from admusuario where usuario_cod='".$_REQUEST['Usuario']."'");
	$row = pg_fetch_array($result);	



	$Usudd = strtoupper($row['usuario_desc']);
	
	$consulta="select tipid, numid, nombre,  ('<select onChange=ActualizaHV(this) id='|| pla.numid ||'><option value=''Activo''>Activo</option><option value=''Retirado'' '|| case when estado='Retirado' then 'selected' else '' end||'>Retirado</option></select>'), agencia, (select '<a href=''#'' class=''Planillalote'' onClick=MuestraVisor('|| pla.srtodo ||', ''phojasvida'', ''$Usudd'', '|| pla.numid ||')> Ver ' || count(*) || '</a>' from planillashojasdevida where srtodo=pla.srtodo)";
	$where = " from planillashojasdevida pla where cab=42 and tipo='P' and pla.numid!='".$row['numerodoc']."' $Nombre $Documento $Estado $Agencia";
	$campoCount = "numid";
}

if($_POST['consulta'] == 'JuridicoContratos'){	
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Consecutivo'] != null)
		$Consecutivo="and codint = '".$_REQUEST['Consecutivo']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";
		
	if($_REQUEST['Objeto'] != null)
		$Objeto="and objeto ilike '%".$_REQUEST['Objeto']."%'";
	
	$consulta="select numid, nombre, codint, ( case when moneda='1' then 'USD $ ' else 'COP $ ' end || to_char(valor, '999,999,999,999,999')), objeto, fechaaut, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pintermediarios\')\"> Ver ' || count(*) || '</a>' from planillasintermediarios where srtodo=pla.srtodo)";
	$where = " from planillasintermediarios pla where cab=40 and tipo='P' $Nit $Nombre $Consecutivo $Valor $Objeto";
	$campoCount = "nombre";
}

if($_POST['consulta'] == 'TesoreriaTransferencia'){	
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Consecutivo'] != null)
		$Consecutivo="and codint='".$_REQUEST['Consecutivo']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor='".$_REQUEST['Valor']."'";
		
	if($_REQUEST['Objeto'] != null)
		$Objeto="and objeto ilike '%".$_REQUEST['Objeto']."%'";
	
	$consulta="select numid, nombre, to_char(fecha,'yyyy-MM-dd HH:MI:SS AM'), (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'ptesoreria\')\"> Ver ' || count(*) || '</a>' from planillastesoreria where srtodo=pla.srtodo)";
	$where = " from planillastesoreria pla where cab=45 and tipo='P' $Nit $Nombre $Consecutivo $Valor $Objeto";
	$campoCount = "numid";
}

if($_POST['consulta'] == 'Intermediarios'){	
	
	if($_REQUEST['CodIntermediario'] != null)
		$CodIntermediario="and codint = '".$_REQUEST['CodIntermediario']."'";
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";

	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";
	
	$consulta="select codint, numid, nombre, agencia, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pintermediarios\')\"> Ver ' || count(*) || '</a>' from planillasintermediarios where srtodo=pla.srtodo)";
	$where = " from planillasintermediarios pla where cab=21 and tipo='P' $CodIntermediario $Nit $Nombre $Agencia";
	$campoCount = "codint";
}

if($_POST['consulta'] == 'Proveedores'){	
	
	if($_REQUEST['NoIdentificacion'] != null)
		$NoIdentificacion="and numid = '".$_REQUEST['NoIdentificacion']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	$consulta="select tipid, numid, nombre, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pintermediarios\')\"> Ver ' || count(*) || '</a>' from planillasintermediarios where srtodo=pla.srtodo)";
	$where = " from planillasintermediarios pla where cab=39 and tipo='P' $NoIdentificacion $Nombre";
	$campoCount = "numid";
}

if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

if ($query) $where .= " and CAST($qtype as TEXT) ILIKE '%".pg_escape_string($query)."%' ";

$ordena = " ORDER BY ".utf8_decode($sortname)." $sortorder";
$limit = " LIMIT $rp OFFSET $start";


$result=$conect->querydb( "SELECT count($campoCount)" . $where);//obtiene numero de registros numero 
$row = pg_fetch_array($result);
$total=$row[0];

$result=$conect->querydb( $consulta . $where . $ordena . $limit);//obtiene registros consulta
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