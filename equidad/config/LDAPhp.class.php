<?php

/*** Título: Enlace PHP con LDAP para autenticar usuarios en aplicaciónes web
**** Autor: Andrés Pérez-Albela H.** Web: http://www.seguridadinformatica.org/
**** Blog: http://develsec.blogspot.com
**** Versión: 1.0
**** Lenguaje: PHP
**** Licencia: Copyright © SeguridadInformatica.Org
**** Creative Commons Atribución-Sin Obras Derivadas 3.0 Unported License
****/

class PHPLdap{
private $ds;
private $bind;
public $swld;

public function PHPLdap($host, $puerto){

   if(!isset($this->ds)){
	if($this->ds = (ldap_connect($host, $puerto))){
		//echo "- LDAP conectado a: ".$host."";
	}
	else
	{
		$this->swld = '25';
		//die("- No me puedo conectar con servidor LDAP");
	}
   }
}

public function enlazarPHPLdap($cadenaConexionUsuario, $password){
	//$this->bind = @ldap_bind($this->ds,'jospina','789alexander789');
	$this->bind = @ldap_bind($this->ds, $cadenaConexionUsuario, $password);

	if($this->bind){
		//echo "- Se realizó el enlace principal satisfactoriamente con el usuario PHP";
	}
	else
	{
		$this->swld = '26';
		//die("- El enlace principal no se pudo llevar a cabo");
	}
}

public function autenticarUsuario($dn, $filtro, $clave){
	$busqueda=ldap_search($this->ds, $dn, $filtro);
	$resultados = ldap_get_entries($this->ds, $busqueda);
		//var_dump($resultados);

//Habiendo buscado el usuario a autenticar con "ldap_search" y obteniendo resultados en
//un arreglo $resultados, con "ldap_get_entries", pasamos a condicionar la existencia
//de resultados positivos.

	if($resultados["count"]>0){
//Ingreso a la condicional, verificando con el "count" que el usuario exista
//Quiere decir que si encontró al usuario
//Luego obtenemos el DN (Nombre Distinguido) del usuario
		$dnUsuario = trim($resultados[0]["distinguishedname"][0]);

//Para poder finalmente realizar el enlace final, siendo "enlace final" el login correcto.
//Si y solo si... la clave es correcta

		if(@ldap_bind($this->ds, $dnUsuario, $clave)){
//Se realiza el bind, siendo la clave correcta, y se retorna un valor VERDADERO (true)
			return true;
		}
		else
		{
			$this->swld = '24';
			return false;
		}
	}else{
		$this->swld = '23';
	}
}

public function usuarioActivo($dn, $filtro){
	$busqueda=ldap_search($this->ds, $dn, $filtro);
	$resultados = ldap_get_entries($this->ds, $busqueda);
	var_dump($resultados);

}

public function cerrarConexion(){
//Cerramos la conexion a LDAP
ldap_close($this->ds);
}
}
?>