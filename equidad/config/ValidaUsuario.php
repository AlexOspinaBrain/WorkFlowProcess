<?php
session_start(); 
require_once ('config/conexion.php');
require_once ('config/LDAPhp.class.php');



if($_REQUEST['EnviaLogin'] && basename($_SERVER['PHP_SELF'])=='default.php'){
	echo IniciaSesion();
}

if( isset($_SESSION['uscod'])  && basename($_SERVER['PHP_SELF'])=='default.php'){
	header("Location: principal.php");
}

if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){//valida variable de sesion
	$_SESSION['EstadoSesion']="La sesión ha terminado";
	header("Location:./default.php"); 
	exit();
}

function IniciaSesion(){
	
		$conect=new conexion();
		$consulta=$conect->query("select usuario_cod, usuario_bloqueado, usuario_inactivo, usuario_ultfecha, usuario_numentradas, usuario_ofipos, area, usuario_desc
								  from admusuario where lower(usuario_desc) = lower('".$_REQUEST['Usuario']."') and usuario_bloqueado = false");
		
		if($row = pg_fetch_array($consulta)){	
			//if($_SERVER['SERVER_NAME']=='imagine.laequidadseguros.coop')
				$acceso=AutenticaEquidad($_POST['Usuario'], $_POST['Password']);
			//else
			//	$acceso=AutenticaBD($_POST['Usuario'], $_POST['Password']);
				
		}else{		
			$_SESSION['EstadoSesion']="El usuario no esta registrado en la aplicación imagine";
		}

		if($acceso==23)
			$_SESSION['EstadoSesion']="El usuario no esta registrado en el sistema equidad";
			
		if($acceso==24)
			$_SESSION['EstadoSesion']="Contraseña incorrecta";


		if($acceso==1){
			$conect=new conexion();
			$conect->query("update admusuario set usuario_numentradas = usuario_numentradas+1 ,usuario_ultfecha = now() where usuario_desc = '".$_POST['Usuario']."'");
			$conect->cierracon();

			$_SESSION['usuario_desc']=$row['usuario_desc'];
			$_SESSION['area']=$row['area'];
			$_SESSION['EstadoSesion']="Sesion Abierta";
			$_SESSION['uscod']=$row['usuario_cod'];
			$_SESSION['agencia']=$row['usuario_ofipos'];
			
			header("Location: principal.php");		
		}
	

}

function AutenticaEquidad($usu, $pw){
	$dns = array();
	$dns[] ="OU=LaEquidad,DC=laequidad,DC=com,DC=co";
	$dns[] ="OU=IT,DC=laequidad,DC=com,DC=co";
	$servidor="192.168.200.1";
	$puerto="389";	
	$cadenaConexionUsuario = "userphp@laequidad.com.co";	
	$password = "UserPhp2011";	
	$dn = "OU=LaEquidad,DC=laequidad,DC=com,DC=co";
	$filtro = "(&(objectClass=user)(samAccountName=$usu))";	
	$PHPLdap = new PHPLdap($servidor, $puerto);
	$PHPLdap->enlazarPHPLdap($cadenaConexionUsuario, $password);	
	foreach ($dns as $dn) {
		$autenticar = $PHPLdap->autenticarUsuario($dn, $filtro, $pw);
		if($autenticar===true)
			return 1;
		//else
		if($autenticar===false)
			return $PHPLdap->swld;
	}
	
	$PHPLdap->cerrarConexion();
}

function AutenticaBD($usu, $pw){
	$conect=new conexion();
	$consulta=$conect->query("select usuario_cod from admusuario where usuario_desc='$usu' and usuario_contrasena='$pw'");
	$conect->cierracon();
	if($row = pg_fetch_array($consulta))
		return 1;
	else
		return 24;
}
