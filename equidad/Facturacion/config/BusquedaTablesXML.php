<?php
require_once ('../../config/conexion.php');

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname']:'';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder']:'';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;


if($_POST['consulta'] == 'ConsultaRadicacion'){	
	$consulta = "select serial_factura, to_char(fechahora_ins,'yyyy-MM-dd HH:MI:SS AM') as fechahora_ins, no_factura, area, 
			'Destinatario', desc_documento from fac_radica rad join proveedor pro using(id_proveedor) join 
			tblareascorrespondencia are on are.areasid=rad.id_area join wf_compania using(id_compania) join 
			fac_documento doc using(id_documento)";
}


if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

if ($query) $where .= " and CAST($qtype as TEXT) ILIKE '%".pg_escape_string($query)."%' ";

$ordena = " ORDER BY $sortname $sortorder";
$limit = " LIMIT $rp OFFSET $start";

$result=queryQR( $consulta. $where);//obtiene numero de registros numero 
	
$total = $result->RecordCount();

$result=queryQR( $consulta . $where . $ordena .$limit);//obtiene registros consulta

$rows = array();
while ($row = $result->FetchRow()) {
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