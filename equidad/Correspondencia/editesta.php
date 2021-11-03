<?php
session_start();
require_once ('../config/conexion.php');
$conect=new conexion();
$upda=$conect->queryequi("update cor_estados set tiempo_estado='".$_REQUEST['tiempo_estado']."' where id_estados='".$_REQUEST["id"]."' ");
?>