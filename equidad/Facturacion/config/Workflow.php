<?php

class Workflow
{
	private $tramite = null;
	private $estado = null;
	private $workflow = null;
	private $id = null;
	
	public function __construct($tramite, $workflow)
    {
        include '../../config/adodb5/adodb.inc.php';  
		$this->tramite = $tramite;
		$this->workflow = $workflow;
		$this->getId();
    }
	
	private function getId(){
		$result = $this->query("select * from fac_radica where serial_factura='".$this->tramite."'");
		$row = $result->FetchRow();
		$this->id = $row['id_radica'];
	}
	
	private function setEstado(){
		$result = $this->query("select * from fac_radica where serial_factura='".$this->tramite."'");
		if( $row = $result->FetchRow())
			$this->estado = $row['estado'];
	}
		
	public function actualizaActual($usuario){
		$this->query("update fac_historial set usuario_cod=$usuario, fecha_terminado=now() where id_radica=".$this->id." and fecha_terminado is null");
	}
	
	public function iniciaWf($usuario){
		$workflow = $this->getWorkflow();
		$transition = $workflow['initial'];
		
		$this->query("insert into fac_historial (actividad, fecha_asignado, fecha_terminado, usuario_cod, estado, id_radica) values 
						( '$transition', now(), now(), $usuario, '".$this->workflow."', '".$this->id."')");
		$this->query("update fac_radica set estado='$transition' where id_radica=".$this->id);
	}
	
	public function ingresaSiguiente($usuario){
		$workflow = $this->getWorkflow();
		$result = $this->query("select * from fac_radica where id_radica='".$this->id."'");
		$tramite = $result->FetchRow();
		if(isset($workflow['node'][$tramite['estado']])){
		
			$transition = $workflow['node'][$tramite['estado']]['transition'];

			$this->query("insert into fac_historial (actividad, fecha_asignado, usuario_cod, estado, id_radica) values 
						( '$transition', now()+ '1 second', $usuario, '".$this->workflow."', '".$this->id."')");

			$this->query("update fac_radica set estado='$transition' where id_radica=".$this->id);
		}
	}
	
	public function getWorkflow(){
		$result = $this->query("select * from fac_workflow where desc_workflow='".$this->workflow."'");
		$workflow = $result->FetchRow();
		$workflow = $workflow['workflow'];
		$workflow = unserialize ( $workflow );
		return $workflow;
	}
		
	public function query($q){
		$db = NewADOConnection('postgres');
		$db->Connect("localhost", "postgres", "QulebrA", "equidad");
		$rs = $db->Execute($q);
		if (!$rs) 
			print $db->ErrorMsg();
		return $rs;
	}

	public function getProximo(){
		$workflow = $this->getWorkflow();
		$this->setEstado();
		if(isset($workflow['node'][$this->estado])){		
			$transition = $workflow['node'][$this->estado]['transition'];		
			return utf8_encode($transition);			
		}
	}
	public function getActual(){
		$workflow = $this->getWorkflow();
		$this->setEstado();
		return utf8_encode($this->estado);	
	}
}
/*
$nuevoFlujo = new Workflow("FAC1307110003", "Normal");
$workflow = $nuevoFlujo -> getWorkflow();*/
/*
$workflow=Array(
    "initial" => "Radicado",
    "node" => Array  ( 
		"Radicado" => Array ( "transition" => "Recibir en el �rea" ) ,      
		"Recibir en el �rea" => Array ( "transition" => "Generar orden de giro" ) ,      
		"Generar orden de giro" => Array ( "transition" => "Enviar �rea contabilidad" ), 
		"Enviar �rea contabilidad" => Array ( "transition" => "Recibir contabilidad" ), 
		"Recibir contabilidad" => Array ( "transition" => "Causaci�n" ), 
		"Causaci�n" => Array ( "transition" => "Recibir tesorer�a" ), 
		"Recibir tesorer�a" => Array ( "transition" => "Generar CP" ), 
		"Generar CP" => Array ( "transition" => "Recibir Auditoria" ), 
		"Recibir Auditoria" => Array ( "transition" => "Auditoria" ), 
		"Auditoria" => Array ( "transition" => "Recibir para cierre" ), 
		"Recibir para cierre" => Array ( "transition" => "Cerrar tramite" ), 
		"Recibir correcci�n radicaci�n" => Array ( "transition" => "Correci�n radicaci�n" ), 
		"Recibir correcci�n orden de giro" => Array ( "transition" => "Correcci�n orden de giro" ), 
		"Recibir correcci�n causaci�n" => Array ( "transition" => "Correcci�n causaci�n" ), 
		"Recibir correcci�n Comprobante de pago" => Array ( "transition" => "Correcci�n Comprobante de pago" ), 
		"Recibir correcci�n auditoria" => Array ( "transition" => "Correcci�n auditoria" ), 
		"Correci�n radicaci�n" => Array ( "transition" => "Recibir en el �rea" ), 
		"Correcci�n orden de giro" => Array ( "transition" => "Enviar �rea contabilidad" ), 
		"Correcci�n causaci�n" => Array ( "transition" => "Recibir tesorer�a" ), 
		"Correcci�n Comprobante de pago" => Array ( "transition" => "Recibir Auditoria" ), 
		"Correcci�n auditoria" => Array ( "transition" => "Recibir para cierre" ), 
    )
);

$workflow=Array(
    "node" => Array  ( 
		"Generar orden de giro" => Array ( "transition" => "Recibir correcci�n radicaci�n" ) ,
		"Causaci�n" => Array ( "transition" => "Recibir correcci�n orden de giro" ) ,
		"Generar CP" => Array ( "transition" => "Recibir correcci�n causaci�n" ) ,
		"Auditoria" => Array ( "transition" => "Recibir correcci�n Comprobante de pago" ) ,
		"Correcci�n causaci�n" => Array ( "transition" => "Recibir correcci�n orden de giro" ), 
    )
);

$workflow = serialize ( $workflow );
echo "<pre>";
print_r($workflow);
echo "</pre>";

*/
 //$consulta = new Workflow("FAC1307190006", "Normal");
 //$result = $consulta->query("select * from fac_radica");
 //while($row = $result->FetchRow()){
	// echo "<br>".$row['serial_factura'];
	/* $nuevoFlujo = new Workflow("FAC1309110001", "Normal");
	 $nuevoFlujo -> iniciaWf(1438);
	 $nuevoFlujo -> ingresaSiguiente(1438);*/
// }

