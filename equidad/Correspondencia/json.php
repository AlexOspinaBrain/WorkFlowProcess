<?php
session_start();
require_once ('../config/conexion.php');

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
$query = $_GET['query']; // get the query


if($query == 'consuli'){
	$select="select * from cor_estados ";
	$id='id_estados';
	$columns = array('nom_estado','tiempo_estado');
}
	
	
$result=queryQR( "$select $where");
$count = $result->RecordCount();

if(!$sidx) $sidx =1;

if($limit == -1) 
	$limit = $count;
	
if( $count >0 )
	$total_pages = ceil($count/$limit);
else
	$total_pages = 0;

if ($page > $total_pages) 
	$page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if($start < 0)$start =0;
	
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$ordena = " ORDER BY $sidx $sord";

$result=queryQR( "$select $where $ordena LIMIT $limit offset $start");

$i=0;
while($row = $result->FetchRow()) {
    $responce->rows[$i]['id']=$row[$id];
	$array = array();
	foreach($columns as $valor)
		$array[]=utf8_encode($row[$valor]);
		
	$responce->rows[$i]['cell']=$array;
    $i++;
} 

echo json_encode($responce);