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
		"Radicado" => Array ( "transition" => "Recibir en el área" ) ,      
		"Recibir en el área" => Array ( "transition" => "Generar orden de giro" ) ,      
		"Generar orden de giro" => Array ( "transition" => "Enviar área contabilidad" ), 
		"Enviar área contabilidad" => Array ( "transition" => "Recibir contabilidad" ), 
		"Recibir contabilidad" => Array ( "transition" => "Causación" ), 
		"Causación" => Array ( "transition" => "Recibir tesorería" ), 
		"Recibir tesorería" => Array ( "transition" => "Generar CP" ), 
		"Generar CP" => Array ( "transition" => "Recibir Auditoria" ), 
		"Recibir Auditoria" => Array ( "transition" => "Auditoria" ), 
		"Auditoria" => Array ( "transition" => "Recibir para cierre" ), 
		"Recibir para cierre" => Array ( "transition" => "Cerrar tramite" ), 
		"Recibir corrección radicación" => Array ( "transition" => "Correción radicación" ), 
		"Recibir corrección orden de giro" => Array ( "transition" => "Corrección orden de giro" ), 
		"Recibir corrección causación" => Array ( "transition" => "Corrección causación" ), 
		"Recibir corrección Comprobante de pago" => Array ( "transition" => "Corrección Comprobante de pago" ), 
		"Recibir corrección auditoria" => Array ( "transition" => "Corrección auditoria" ), 
		"Correción radicación" => Array ( "transition" => "Recibir en el área" ), 
		"Corrección orden de giro" => Array ( "transition" => "Enviar área contabilidad" ), 
		"Corrección causación" => Array ( "transition" => "Recibir tesorería" ), 
		"Corrección Comprobante de pago" => Array ( "transition" => "Recibir Auditoria" ), 
		"Corrección auditoria" => Array ( "transition" => "Recibir para cierre" ), 
    )
);

$workflow=Array(
    "node" => Array  ( 
		"Generar orden de giro" => Array ( "transition" => "Recibir corrección radicación" ) ,
		"Causación" => Array ( "transition" => "Recibir corrección orden de giro" ) ,
		"Generar CP" => Array ( "transition" => "Recibir corrección causación" ) ,
		"Auditoria" => Array ( "transition" => "Recibir corrección Comprobante de pago" ) ,
		"Corrección causación" => Array ( "transition" => "Recibir corrección orden de giro" ), 
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

