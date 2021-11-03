<?php
require_once ('../config/conexion.php');

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname']:'';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder']:'';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;


if($_POST['consulta'] == 'BuscaProductos'){	
	if(!empty($_POST['Producto']))
		$filtros = "and poliza ='".$_POST['Producto']."'";
	
	$consulta = "select 
				poliza,
				certif,
				decode( substr(sucur,1,1),'1','Seguros Generales','Seguros de Vida') as Compania,
				case estado when 0 then '<b class=\"Vencido\">No vigente</b>' when 1 then '<b class=\"Activo\">Vigente</b>' when 2 then '<b class=\"Vencido\">Cancelado</b>' when 3 then '<b class=\"Vencido\">Anulado</b>' end,
				orden,
				tipcer,
				osiris.fc_codpla(a.codpla) as Descripcion,	
				pmolano.fc_traer500(substr(a.sucur,2),'nombre') as Radicada,
				fecren as InicioTecnico,
				fecini as InicioCertificado,
				fecter as FinTecnico,
				Fecter as FinCertificado,
				'( ' || ltrim(rtrim(pmolano.fc_traer500(a.tomador,'nit'),' '),'0') || ' ) '||
				pmolano.fc_traer500(a.tomador,'nombre') as Tomador,
				'( ' ||ltrim(rtrim(pmolano.fc_traer500(a.asegurado,'nit'),' '),'0')  || ' ) '||
				pmolano.fc_traer500(a.asegurado,'nombre') as Asegurado,
				'( ' ||ltrim(rtrim(pmolano.fc_traer500(a.beneficiario,'nit'),' '),'0')  || ' ) '||
				pmolano.fc_traer500(a.beneficiario,'nombre') as Beneficiario,
				'( ' ||ltrim(rtrim(pmolano.fc_traer500(a.agente,'nit'),' '),'0')  || ' ) '||
				pmolano.fc_traer500(a.agente,'nombre') as Intermediario				
				FROM OSIRIS.S03020 A where a.".$_POST['TipoCliente']."='".$_POST['codigo']."' $filtros";
}

if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

if ($query) $where .= " and CAST($qtype as TEXT) ILIKE '%".pg_escape_string($query)."%' ";

$ordena = " ORDER BY $sortname $sortorder, poliza";
//$ordena ="";

$db = NewADOConnection("oci8");
$db->charSet = 'we8iso8859p1';
$ls=$db->Connect("(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.200.12)(PORT = 1521))) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME= osiris)))", "wfimagine", "wfimagine");
$rs = $db->Execute(   $consulta . $where);
$total = $rs->RecordCount();
$rs =$db->SelectLimit($consulta . $where.$ordena, $rp, $start);
$rows = array();
while ($row = $rs->FetchRow()) {
	$rows[] = $row;	
}

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