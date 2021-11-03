<?php
session_start(); 

require_once ('LDAPhp.class.php');

$usu = "imagine";
$pw = "Megara/2019";

	$dns = array();
	$dns[] ="OU=LaEquidad,DC=laequidad,DC=com,DC=co";
	$dns[] ="OU=IT,DC=laequidad,DC=com,DC=co";
	$servidor="192.168.200.1";
	$puerto="389";	
	$cadenaConexionUsuario = "userphp@laequidad.com.co";	
	$password = "UserPhp2011";	
	$dn = "OU=LaEquidad,DC=laequidad,DC=com,DC=co";
	$filtro = "(&(objectClass=user)(samAccountName=$usu))";	
	
echo "ee \r\n";
	$PHPLdap = new PHPLdap($servidor, $puerto);
	
	$PHPLdap->enlazarPHPLdap($cadenaConexionUsuario, $password);



	foreach ($dns as $dn) {
		$autenticar = $PHPLdap->autenticarUsuario($dn, $filtro, $pw);
		echo $autenticar . " rr \r\n";
		if($autenticar===true)
			echo 1;
		else
			echo $PHPLdap->swld . " rr";
	}
	
	$PHPLdap->cerrarConexion();
?>