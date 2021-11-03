<?php
session_start();
require_once ("../../config/conexion.php");
require_once ("../../config/EnLetras.php");
require_once ("Workflow.php");

$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";

$usuario = "COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as usuario";

function utf8_encode_all($dat){ // -- It returns $dat encoded to UTF8 
  if (is_string($dat)) return utf8_encode($dat); 
  if (!is_array($dat)) return $dat; 
  $ret = array(); 
  foreach($dat as $i=>$d) $ret[$i] = utf8_encode_all($d); 
  return $ret; 
} 

function getConsecutivoFactura(){
	$fecha = substr(date('Y'), -2).date('md');
	$result = queryQR("select * from fac_radica where serial_factura like 'FAC$fecha%' order by id_radica desc limit 1");
	if($row = $result->FetchRow()){
		$consecutivo = $row['serial_factura'];
		$consecutivo = substr($consecutivo, -4);
		$consecutivo = intval($consecutivo) + 1;
		$consecutivo = str_pad($consecutivo, 4, "0", STR_PAD_LEFT);	
		return "FAC$fecha".$consecutivo;
	}else
		return "FAC$fecha"."0001";
}

function getConsecutivoOrdenGiro($aseguradora){
	$año = substr(date('Y'), -2);
	if($aseguradora == 1)
		$consec = "GEN".$año;
		
	if($aseguradora == 2)
		$consec = "VID".$año;
	
	$result = queryQR("select * from fac_ordengiro where num_ordengiro like '$consec%' order by id_ordengiro desc limit 1");
	if($row = $result->FetchRow()){
		$consecutivo = $row['num_ordengiro'];
		$consecutivo = substr($consecutivo, -5);
		$consecutivo = intval($consecutivo) + 1;
		$consecutivo = str_pad($consecutivo, 5, "0", STR_PAD_LEFT);	
		return $consec.$consecutivo;
	}else
		return $consec."00001";
}

function GuardaRadicacion($datos){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		return $save;
	}

	if($_GET['Consecutivo']==null){
		$_GET['ValorFactura']=str_replace(array("$", ".", " "), '',$_GET['ValorFactura']);
		$_GET['Consecutivo']=getConsecutivoFactura();
			
		$result=queryQR("insert into fac_radica (serial_factura, no_factura, valor_factura, fecha_expedicion, fecha_vencimiento, 
				id_proveedor, id_compania, id_area, fechahora_ins, id_documento, estado, id_ordengiro, usuario_cod) values 
				('".$_GET['Consecutivo']."', '".$_GET['NoFactura']."', '".$_GET['ValorFactura']."', to_date('".$_GET['fecha_expedicion']."', 'DD/MM/YYYY'), 
				to_date('".$_GET['fecha_vencimiento']."', 'DD/MM/YYYY'), '".$_GET['id_proveedor']."', '".$_GET['Aseguradora']."', 
				'".$_GET['Area']."', now(), '".$_GET['TipoDocumento']."', 'En tramite', 0,  ".$_SESSION['uscod'].")");
		
		if(!$result)
			return;
			
		$nuevoFlujo = new Workflow($_GET['Consecutivo'], "Normal");
		$nuevoFlujo -> iniciaWf($_SESSION['uscod']);
		$nuevoFlujo -> ingresaSiguiente($_GET['Destinatario']);
	}else{
		$result=queryQR("update fac_radica set no_factura='".$_GET['NoFactura']."', valor_factura='".$_GET['ValorFactura']."', 
				fecha_expedicion=to_date('".$_GET['fecha_expedicion']."', 'DD/MM/YYYY'), fecha_vencimiento=
				to_date('".$_GET['fecha_vencimiento']."', 'DD/MM/YYYY'), id_proveedor='".$_GET['id_proveedor']."', 
				id_compania='".$_GET['Aseguradora']."', id_area='".$_GET['Area']."', 
				id_documento='".$_GET['TipoDocumento']."',  usuario_cod='".$_SESSION['uscod']."' where serial_factura='".$_GET['Consecutivo']."'");

		$nuevoFlujo = new Workflow($_GET['Consecutivo'], "Normal");
		$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
		$nuevoFlujo -> ingresaSiguiente($_GET['Destinatario']);
		
	}
	return $_GET['Consecutivo'];
}

if($opcion=="BuscaFactura"){
	$factura = new StdClass();
	$result=queryQR("select *, to_char(fechahora_ins,'yyyy-MM-dd HH:MI:SS AM') as fechahora_ins,
				to_char(fecha_expedicion,'dd/MM/yyyy') as fecha_expedicion,
				to_char(fecha_vencimiento,'dd/MM/yyyy') as fecha_vencimiento, rad.id_compania as id_compania_rad, valor_factura as valor_fac,
				rad.observaciones as observaciones_fac
				from 
				fac_radica rad 
				join proveedor pro using(id_proveedor) 
				join persona per using(id_persona) 
				join tblareascorrespondencia are on are.areasid=rad.id_area 
				join wf_compania using(id_compania) 
				join fac_documento doc using(id_documento) 
				join tblradofi age on agencia=codigo
				join fac_ordengiro ord USING(id_ordengiro)
				join fac_comprobantepago com USING(id_comprobante)
				where 
				serial_factura='".$_REQUEST['tramite']."'");

	if($factura = $result->FetchRow()){
		$factura['valor_factura']="$ ".number_format($factura['valor_factura'], 0,'.','.');
		$historial=queryQR("select $usuario, to_char(fecha_asignado,'yyyy-MM-dd HH:MI AM') as fecha_asig, to_char(fecha_terminado,'yyyy-MM-dd HH:MI AM') as fecha_term, * from fac_historial his join adm_usuario usu using(usuario_cod) 
							where id_radica=".$factura['id_radica']." order by his.fecha_asignado");
		$historial = $historial->GetArray();
		$factura['historial']=$historial;
		$factura = utf8_encode_all( $factura);
	}else{
		$factura->fecha_vencimiento=date("d/m/Y", strtotime(date("Y/m/d")."+1 month"));
	}
	
	echo json_encode($factura);	
}

if($opcion=="BuscaOrdenGiro"){
	$result=queryQR("select num_ordengiro,  to_char(ord.fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins, count(id_radica) as cant_fac, 
			rad.estado, ord.observaciones, des_compania, $usuario, id_ordengiro
			from fac_radica rad join 
			fac_ordengiro ord USING(id_ordengiro) join 
			adm_usuario usu on usu.usuario_cod=ord.usuario_cod join 
			wf_compania com on ord.id_compania=com.id_compania
			where num_ordengiro='".$_REQUEST['tramite']."' group by rad.estado, num_ordengiro, ord.fecha_ins, usuario_nombres, usuario_priape, usuario_segape, ord.observaciones, des_compania, id_ordengiro");
	$ordenGiro = $result->FetchRow();
	$ordenGiro = utf8_encode_all( $ordenGiro);	
	echo json_encode($ordenGiro);	
}

if($opcion=="FormatoOrdenGiro"){
	$result=queryQR("select *, to_char(ord.fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins,
			to_char(rad.valor_factura,'LFM 999,999,999') as valor_fac
			from 
			fac_ordengiro ord 
			join fac_radica rad USING(id_ordengiro) 
			join tblareascorrespondencia are on are.areasid=rad.id_area
			join proveedor pro USING(id_proveedor)
			join persona per USING(id_persona)
			where num_ordengiro='".$_REQUEST['tramite']."' limit 1");
	$ordenGiro = $result->FetchRow();

	$result=queryQR(" select *, 
			(select array_to_string(array_agg(desc_documento), ',') 
					from provee_rel_doc join proveedor_doc USING(id_documento) where id_proveedor=pro.id_proveedor) as soportes
			from fac_radica rad 
			join proveedor pro on rad.id_proveedor=pro.id_proveedor
 			where rad.id_ordengiro='".$ordenGiro['id_ordengiro']."' ");
	$ordenGiro['facturas'] = $result->GetArray();

	$result = queryQR("select to_char(sum(valor_factura),'LFM 999,999,999') as valor_total, sum(valor_factura) as valor_tot from fac_radica rad where  rad.id_ordengiro='".$ordenGiro['id_ordengiro']."'");
	$result = $result->FetchRow();
	$ordenGiro['valor_total'] = $result['valor_total'];
	$ordenGiro['valor_tot'] = $result['valor_tot'];

 	$valor_letras=new EnLetras();
 	$ordenGiro['valor_letras'] = utf8_decode($valor_letras->ValorEnLetras($ordenGiro['valor_tot'], "pesos"));
	$ordenGiro = utf8_encode_all( $ordenGiro);	
	echo json_encode($ordenGiro);	
}

if($opcion=="BuscaOrdenGiro2"){
	$result=queryQR("select * from fac_ordengiro ord
			join adm_usuario usu USING(usuario_cod) where num_ordengiro='".$_REQUEST['tramite']."'");
	if(!$ordenGiro = $result->FetchRow())
		exit();

	$result=queryQR("select * from fac_radica rad join proveedor using(id_proveedor) join persona using(id_persona) where id_ordengiro='".$ordenGiro['id_ordengiro']."'");
	$ordenGiro['ListOrdenes'] = $result->GetArray();
	$ordenGiro = utf8_encode_all( $ordenGiro);	
	echo json_encode($ordenGiro);	
}

if($opcion=="BuscaCP"){
	$result=queryQR("select num_comprobante, count(id_comprobante) cant_ord, to_char(fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins, 
		estado, medio_pago, valor_cp, observaciones, $usuario from fac_comprobantepago com  join (select DISTINCT(id_ordengiro), 
		id_comprobante, rad.estado from fac_radica rad join fac_ordengiro ord USING(id_ordengiro) group by id_ordengiro, id_comprobante, rad.estado) ord 
		USING(id_comprobante) join adm_usuario usu using(usuario_cod) where id_comprobante!=0 and 
		num_comprobante='".$_REQUEST['tramite']."' group by ord.id_comprobante, num_comprobante, 
		fecha_ins, estado, medio_pago, valor_cp, observaciones, usuario_nombres, usuario_priape, usuario_segape");

	$Comprobante = $result->FetchRow();
	$Comprobante['valor_cp']= $Comprobante['valor_cp'] != null ? "$ ".number_format($Comprobante['valor_cp'], 0,'.','.') : null;

	$Comprobante = utf8_encode_all( $Comprobante);	
	echo json_encode($Comprobante);	
}

if($opcion=="BuscaCP2"){
	$result=queryQR("select *
				from fac_comprobantepago com left join
				(select  DISTINCT on (id_comprobante) id_comprobante, id_proveedor, id_compania from fac_ordengiro ord left join
					(select  DISTINCT on (id_ordengiro) id_ordengiro, id_proveedor from fac_radica rad       	
 					)rad USING(id_ordengiro) 
   				)ord USING(id_comprobante)
   				join proveedor pro USING(id_proveedor)
   				join persona per USING(id_persona)
   				where num_comprobante='".$_REQUEST['tramite']."'");

	if(!$Comprobante = $result->FetchRow())
		exit();

	$result=queryQR("select * from fac_ordengiro where id_comprobante='".$Comprobante['id_comprobante']."'");
	$Comprobante['ListOrdenes'] = $result->GetArray();
	
	$Comprobante = utf8_encode_all( $Comprobante);	
	echo json_encode($Comprobante);	
}

if($opcion=="ConfirmaRecibeFactura"){
	$result=queryQR("select *, to_char(fechahora_ins,'yyyy-MM-dd HH:MI:SS AM') as fechahora_ins,
					case when (his.actividad='Recibir en el área' or his.actividad='Recibir corrección radicación') then true else false end as recibe
					from 
					fac_radica rad join proveedor pro using(id_proveedor) join 
					tblareascorrespondencia are on are.areasid=rad.id_area join 
					wf_compania using(id_compania) join 
					fac_documento doc using(id_documento) join
					tblradofi age on agencia=codigo JOIN
                    fac_historial his USING(id_radica)
					where serial_factura='".$_REQUEST['tramite']."' and his.fecha_terminado is null");
				
	$factura = $result->FetchRow();
	$factura = utf8_encode_all( $factura);
	echo json_encode($factura);	
}

if($opcion=="ConfirmaGenCausacion"){
	$result=queryQR("select num_ordengiro,  to_char(ord.fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins, count(id_radica) as cant_fac, 
			rad.estado, ord.observaciones, des_compania, $usuario, id_ordengiro
			from fac_radica rad join 
			fac_ordengiro ord USING(id_ordengiro) join 
			adm_usuario usu on usu.usuario_cod=ord.usuario_cod join 
			wf_compania com on ord.id_compania=com.id_compania
			where num_ordengiro='".$_REQUEST['tramite']."' and (rad.estado='Causación' or rad.estado='Corrección causación') group by rad.estado, num_ordengiro, ord.fecha_ins, usuario_nombres, usuario_priape, usuario_segape, ord.observaciones, des_compania, id_ordengiro");
	$ordenGiro = $result->FetchRow();
	$ordenGiro = utf8_encode_all( $ordenGiro);	
	echo json_encode($ordenGiro);
}

if($opcion=="ConfirmaGenAuditoria"){
	$result=queryQR("select serial_factura from fac_comprobantepago com join fac_ordengiro USING (id_comprobante) 
					join fac_radica rad USING(id_ordengiro) where com.num_comprobante='".$_REQUEST['tramite']."'");

	$comprobantePago = $result->FetchRow();
	$comprobantePago = utf8_encode_all( $comprobantePago);	
	echo json_encode($comprobantePago);
}

if($opcion=="RecibeFactura"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}
	
	$nuevoFlujo = new Workflow($_REQUEST['tramite'], "Normal");
	if( $nuevoFlujo->getActual() === utf8_encode('Recibir en el área') || $nuevoFlujo->getActual() === utf8_encode('Recibir corrección radicación')){
		$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
		$nuevoFlujo -> ingresaSiguiente($_SESSION['uscod']);
	}

	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="GuardaOrdenGiro"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}

	if($_REQUEST['OrdenGiro']==null)
		$consec = getConsecutivoOrdenGiro($_POST['Aseguradora']);
	else
		$consec=$_REQUEST['OrdenGiro'];
	
	
	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_POST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Observaciones']));
	$_POST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Observaciones']);
	$_POST['Observaciones']=str_replace("\r", '',$_POST['Observaciones']);
	$_POST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_POST['Observaciones'].'</span></p>';
							 
	$_POST['Concepto']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Concepto']));
	$_POST['Concepto']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Concepto']);
	$_POST['Concepto']=str_replace("\r", '',$_POST['Concepto']);
	
	if($_REQUEST['OrdenGiro']==null){
		$result=queryQR("insert into fac_ordengiro (num_ordengiro, fecha_ins, usuario_cod, observaciones, id_comprobante, id_compania, concepto) 
				values ('$consec', now(), ".$_SESSION['uscod'].", '".$_POST['Observaciones']."', 0, ".$_POST['Aseguradora'].", '".$_POST['Concepto']."')");
	}else{
		$result=queryQR("update fac_ordengiro set observaciones=COALESCE(observaciones,'') || 
				'".$_POST['Observaciones']."', concepto='".$_POST['Concepto']."' where num_ordengiro='$consec'");

		$result=queryQR("update fac_radica set id_ordengiro=0 where id_ordengiro=(select id_ordengiro from fac_ordengiro where num_ordengiro='$consec')");
	}

	if(!$result)
		exit();
	
	$result=queryQR("select * from fac_ordengiro where num_ordengiro='$consec'");
	$idOrden = $result->FetchRow();
	$idOrden = $idOrden['id_ordengiro'];
	
	foreach($_POST['ListFactura'] as $valor){
		queryQR ("update fac_radica set id_ordengiro=$idOrden where serial_factura='$valor'");
		$nuevoFlujo = new Workflow($valor, "Normal");
		if( $nuevoFlujo->getActual() === utf8_encode('Generar orden de giro') || $nuevoFlujo->getActual() === utf8_encode('Corrección orden de giro')){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($_SESSION['uscod']);
		}		
	}

	$result=queryQR("update fac_historial his set actividad='Generar orden de giro' where id_radica 
		IN(select id_radica from fac_radica where estado='Corrección orden de giro' and id_ordengiro=0) and fecha_terminado is null;
		update fac_radica set estado='Generar orden de giro' where estado='Corrección orden de giro' and id_ordengiro=0");
	
	$save->guardado = true;
	$save->orden_giro = $consec;
	echo json_encode($save);	
}

if($opcion=="EnviaOrdenGiro"){
	$save = new StdClass();
	$usuarioDestino = isset($_REQUEST['usuario']) ? $_REQUEST['usuario'] : $_SESSION['uscod'];

	$result=queryQR("select * from fac_ordengiro join fac_radica using(id_ordengiro) where num_ordengiro='".$_REQUEST['tramite']."'");$result=queryQR("select * from fac_ordengiro join fac_radica using(id_ordengiro) where num_ordengiro='".$_REQUEST['tramite']."'");
	while ( $row = $result->FetchRow()) {
		$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
		if( ($nuevoFlujo->getActual() === utf8_encode('Enviar área contabilidad') && $_REQUEST['usuario']!=null) || $nuevoFlujo->getActual() === utf8_encode('Recibir contabilidad')|| $nuevoFlujo->getActual() === utf8_encode('Recibir tesorería') || $nuevoFlujo->getActual() === utf8_encode('Recibir corrección orden de giro')){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($usuarioDestino);
		}		
	}

	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="BuscaProveedor"){
	$result=queryQR("select * from proveedor join persona USING(id_persona) where documento='".$_REQUEST['identificacion']."'");
	if(!$proveedor = $result->FetchRow())
		exit();

	$result=queryQR("select * from  provee_rel_doc join proveedor_doc USING(id_documento) where id_proveedor=".$proveedor['id_proveedor']);
	$proveedor["requeridos"] = $result->GetArray();

	$proveedor = utf8_encode_all( $proveedor);

	echo json_encode($proveedor);	
}

if($opcion=="CargaAseguradora"){
	$result=queryQR("select * from wf_compania where eliminado_compania=false and id_compania!=0");
	$aseguradora = $result->GetArray();
	$aseguradora = utf8_encode_all( $aseguradora);
	echo json_encode($aseguradora);	
}

if($opcion=="CargaAgenciaRadicaFac"){
	$result=queryQR("select age.codigo, age.descrip from adm_usumenu um join adm_usuario usu on usu.usuario_cod=um.usuario_cod join 
			tblareascorrespondencia are on are.areasid=usu.area join tblradofi age on age.codigo=are.agencia where 
			um.jerarquia_opcion = '3.3' group by age.codigo, age.descrip");
	$Agencia = $result->GetArray();
	$Agencia = utf8_encode_all( $Agencia);
	echo json_encode($Agencia);	
}

if($opcion=="CargaAreasRadicaFac"){
	$result=queryQR("select are.areasid, are.area from adm_usumenu um join adm_usuario usu on usu.usuario_cod=um.usuario_cod join tblareascorrespondencia 
			are on are.areasid=usu.area where um.jerarquia_opcion = '3.3' and are.agencia='".$_REQUEST['id_agencia']."' GROUP by are.areasid, are.area");
	$Agencia = $result->GetArray();
	$Agencia = utf8_encode_all( $Agencia);
	echo json_encode($Agencia);	
}

if($opcion=="CargaDestinatariosRadicaFac"){
	$result=queryQR("select * from adm_usumenu um join adm_usuario usu on usu.usuario_cod=um.usuario_cod where usu.area=".$_REQUEST['id_area']." and um.jerarquia_opcion = '3.3'");
	$Agencia = $result->GetArray();
	$Agencia = utf8_encode_all( $Agencia);
	echo json_encode($Agencia);	
}

if($opcion=="GetCheckList"){
	$result=queryQR("select * from fac_checklist where id_documento='".$_REQUEST['id_documento']."' order by desc_list");
	$Checklist = $result->GetArray();
	$Checklist = utf8_encode_all( $Checklist);
	echo json_encode($Checklist);	
}

if($opcion=="BuscaTipoDoc"){
	$result=queryQR("select * from fac_documento");
	$Documento = $result->GetArray();
	$Documento = utf8_encode_all( $Documento);
	echo json_encode($Documento);	
}

if($opcion=="GuardaRadicacion"){
	$respuesta = GuardaRadicacion ($_POST['datos']);
	echo json_encode($respuesta);	
}

if($opcion=="CargaTramitesAseg"){
	$result=queryQR("select * from fac_radica where estado='Generar orden de giro' and id_compania=".$_POST['id_compania']);
	$Tramites = $result->GetArray();
	$Tramites = utf8_encode_all( $Tramites);
	echo json_encode($Tramites);	
}

if($opcion == "UsuarioRecibeOrdenGiro"){	
	$result=queryQR("select usuario_cod, $usuario from adm_usuario join adm_usumenu USING(usuario_cod) where jerarquia_opcion ='3.4'");
	$usuarios = $result->GetArray();
	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}

if($opcion == "UsuarioRecibeCausacion"){	
	$result=queryQR("select usuario_cod, $usuario from adm_usuario join adm_usumenu USING(usuario_cod) where jerarquia_opcion ='3.5'");
	$usuarios = $result->GetArray();
	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}

if($opcion == "UsuarioRecibeCierre"){	
	$result=queryQR("select usuario_cod, $usuario from adm_usuario join adm_usumenu USING(usuario_cod) where jerarquia_opcion ='3.7'");
	$usuarios = $result->GetArray();
	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}


if($opcion == "UsuarioRecibeAuditoria"){	
	$result=queryQR("select usuario_cod, $usuario from adm_usuario join adm_usumenu USING(usuario_cod) where jerarquia_opcion ='3.6'");
	$usuarios = $result->GetArray();
	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}

if($opcion == "UsuarioRecibeRadicacion"){	
	$result=queryQR("select usuario_cod, $usuario from adm_usuario join adm_usumenu USING(usuario_cod) where jerarquia_opcion ='3.1'");
	$usuarios = $result->GetArray();
	$usuarios = utf8_encode_all( $usuarios);
	echo json_encode($usuarios);	
}


if($opcion=="GenCausacion"){
	$save = new StdClass();
	$usuarioDestino = isset($_REQUEST['usuario']) ? $_REQUEST['usuario'] : $_SESSION['uscod'];

	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_POST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Observaciones']));
	$_POST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Observaciones']);
	$_POST['Observaciones']=str_replace("\r", '',$_POST['Observaciones']);
	$_POST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_POST['Observaciones'].'</span></p>';

	$result=queryQR("update fac_ordengiro set observaciones= COALESCE(observaciones,'') || 
					'".$_POST['Observaciones']."' where num_ordengiro='".$_REQUEST['tramite']."'");
	$result=queryQR("select * from fac_ordengiro join fac_radica using(id_ordengiro) where num_ordengiro='".$_REQUEST['tramite']."'");
	while ( $row = $result->FetchRow()) {
		$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
		if( $nuevoFlujo->getActual() === utf8_encode('Causación')){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($usuarioDestino);
		}	
	}

	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="GuardaCP"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}

	if ($_REQUEST['id_comprobante'] == null) {
		$num_comprobante=queryQR("select * from fac_comprobantepago where num_comprobante='".$_POST['Comprobante']."'");
		if($num_comprobante = $num_comprobante->FetchRow()){
			$save->error = utf8_encode("El número de comprobante ya existe.");
			echo json_encode($save);	
			exit();
		}
	}

	
	
	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_POST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Observaciones']));
	$_POST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Observaciones']);
	$_POST['Observaciones']=str_replace("\r", '',$_POST['Observaciones']);
	$_POST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_POST['Observaciones'].'</span></p>';
	
	$_POST['ValorCP']=str_replace(array("$", ".", " "), '',$_POST['ValorCP']);

	if($_REQUEST['id_comprobante']==null){
		$result=queryQR("insert into fac_comprobantepago (num_comprobante, fecha_ins, usuario_cod, medio_pago, observaciones, valor_cp) values 
				   ('".$_POST['Comprobante']."', now(), ".$_SESSION['uscod'].", '".$_POST['MedioPago']."', '".$_POST['Observaciones']."', ".$_POST['ValorCP'].")");
	}else{
		$result=queryQR("update fac_comprobantepago set observaciones=COALESCE(observaciones,'') || 
				'".$_POST['Observaciones']."', medio_pago='".$_POST['MedioPago']."', valor_cp=".$_POST['ValorCP']." where num_comprobante='".$_POST['Comprobante']."'");

		$result=queryQR("update fac_ordengiro set id_comprobante=0 where id_comprobante='".$_REQUEST['id_comprobante']."'");
	}

	if(!$result)
		exit();
	
	$result=queryQR("select * from fac_comprobantepago where num_comprobante='".$_POST['Comprobante']."'");
	$idComprobante = $result->FetchRow();
	$idComprobante = $idComprobante['id_comprobante'];

	foreach($_POST['ListOrdenes'] as $valor){
		queryQR("update fac_ordengiro set id_comprobante=$idComprobante where num_ordengiro='$valor'");
		$result=queryQR("select * from fac_ordengiro ord join fac_radica rad using (id_ordengiro) where ord.num_ordengiro='$valor'");
		while ($row = $result->FetchRow()) {
			$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
			if( $nuevoFlujo->getActual() === utf8_encode('Generar CP') || $nuevoFlujo->getActual() === utf8_encode('Corrección Comprobante de pago')){
				$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
				$nuevoFlujo -> ingresaSiguiente($_REQUEST['Usuario']);
			}			
		}
	}

	$result=queryQR("update fac_historial his set actividad='Generar CP' where id_radica 
		IN(select id_radica from fac_radica rad join fac_ordengiro ord USING(id_ordengiro) 
		where estado='Corrección Comprobante de pago' and id_comprobante=0) and fecha_terminado is null;
		update fac_radica as rad set estado='Generar CP' from fac_ordengiro ord where 
		ord.id_ordengiro=rad.id_ordengiro and estado='Corrección Comprobante de pago' and id_comprobante=0");

	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="RecibirCP"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}

	$result=queryQR("select serial_factura from fac_comprobantepago com join fac_ordengiro USING (id_comprobante) 
					join fac_radica rad USING(id_ordengiro) where com.num_comprobante='".$_REQUEST['tramite']."'");
	while ($row = $result->FetchRow()) {
		$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
		if( $nuevoFlujo->getActual() === utf8_encode('Recibir Auditoria') ||  $nuevoFlujo->getActual() === utf8_encode('Recibir para cierre') ||  $nuevoFlujo->getActual() === utf8_encode('Recibir corrección Comprobante de pago')){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($_SESSION['uscod']);
		}	
	}

	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="GuardaAuditoria"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}
	
	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_POST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Observaciones']));
	$_POST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Observaciones']);
	$_POST['Observaciones']=str_replace("\r", '',$_POST['Observaciones']);
	$_POST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_POST['Observaciones'].'</span></p>';
	
	$result=queryQR("update fac_comprobantepago set observaciones=observaciones || '".$_POST['Observaciones']."' where 
					num_comprobante='".$_POST['Comprobante']."'");
	if(!$result)
		exit();
	
	$result=queryQR("select serial_factura from fac_radica fac join fac_ordengiro ord USING(id_ordengiro) join fac_comprobantepago 
				com USING(id_comprobante) where num_comprobante='".$_POST['Comprobante']."'");
	while ($row = $result->FetchRow()) {
		$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
		
		if( $nuevoFlujo->getActual() === utf8_encode('Auditoria') ){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($_REQUEST['Usuario']);
		}	
	}
	
	$save->guardado = true;
	echo json_encode($save);	
}

if($opcion=="GuardaCierre"){
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}
	
	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_POST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_POST['Observaciones']));
	$_POST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_POST['Observaciones']);
	$_POST['Observaciones']=str_replace("\r", '',$_POST['Observaciones']);
	$_POST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_POST['Observaciones'].'</span></p>';
	
	$result=queryQR("update fac_comprobantepago set observaciones=observaciones || '".$_POST['Observaciones']."' where 
					num_comprobante='".$_POST['Comprobante']."'");
	if(!$result)
		exit();
	
	$result=queryQR("select serial_factura from fac_radica fac join fac_ordengiro ord USING(id_ordengiro) join fac_comprobantepago 
				com USING(id_comprobante) where num_comprobante='".$_POST['Comprobante']."'");
	while ($row = $result->FetchRow()) {
		$nuevoFlujo = new Workflow( $row['serial_factura'], "Normal");
		if( $nuevoFlujo->getActual() === utf8_encode('Cerrar tramite') ){
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			queryQR("update fac_radica set estado='Aprobada' where serial_factura='". $row['serial_factura']."'");
		}		
	}
	
	$save->guardado = true;
	echo json_encode($save);	
}

if ($opcion=="statusWorkflow") {
	$estadoCopyUsu = array();
	$tipo_tramite =  substr($_REQUEST['tramite'], 0, 3);
	$actividad = new StdClass();

	if($_REQUEST['tipo_doc'] == 'Orden de giro'){
		$result = queryQR("select * from fac_ordengiro ord join fac_radica rad USING(id_ordengiro) where ord.num_ordengiro='".$_REQUEST['tramite']."' limit 1");
		$result=$result->FetchRow();
		$_REQUEST['tramite'] = $result['serial_factura'];
	}

	if($_REQUEST['tipo_doc'] == 'Comprobante de pago'){
		$result = queryQR("select * from fac_ordengiro join fac_radica USING(id_ordengiro) join fac_comprobantepago com USING(id_comprobante)
 			where com.num_comprobante ='".$_REQUEST['tramite']."' limit 1");
		$result=$result->FetchRow();
		$_REQUEST['tramite'] = $result['serial_factura'];
	}

	$nuevoFlujo = new Workflow( $_REQUEST['tramite'], utf8_decode($_REQUEST['workflow']));
	$actividad->proxima= $nuevoFlujo -> getProximo();

	if($actividad->proxima == utf8_encode('Recibir corrección radicación'))
		$estadoCopyUsu=array('Radicado', 'Correción radicación');

	if($actividad->proxima == utf8_encode('Recibir corrección orden de giro'))
		$estadoCopyUsu=array('Generar orden de giro', 'Corrección orden de giro');

	if($actividad->proxima == utf8_encode('Recibir corrección causación'))
		$estadoCopyUsu=array('Causación', 'Corrección causación');

	if($actividad->proxima == utf8_encode('Recibir corrección Comprobante de pago'))
		$estadoCopyUsu=array('Generar CP', 'Corrección Comprobante de pago');

	$filtro="";			
	foreach ($estadoCopyUsu as $valor) 
		$filtro .= "his.actividad = '$valor' or ";
		
	$filtro=substr( $filtro ,0,strlen( $filtro )-3);

	$result = queryQR(" select his.usuario_cod from fac_radica fac join fac_historial his USING(id_radica) where 
			fac.serial_factura='".$_REQUEST['tramite']."' and ($filtro) order by his.id_historial desc limit 1");
	$result=$result->FetchRow();
	$actividad->usuario=$result['usuario_cod'];

	$actividad = utf8_encode_all( $actividad);
	echo json_encode($actividad);		
}

if ($opcion=="ContinuaDevuelve") {
	$save = new StdClass();
	if(!isset($_SESSION['uscod'])){
		$save->error = 'La sesion a caducado';
		echo json_encode($save);	
		exit();
	}

	$nombre_usu=queryQR("select $usuario from adm_usuario where usuario_cod=".$_SESSION['uscod']);
	$nombre_usu=$nombre_usu->FetchRow();
	$nombre_usu=$nombre_usu['usuario'];
	$_REQUEST['Observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_REQUEST['Observaciones']));
	$_REQUEST['Observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_REQUEST['Observaciones']);
	$_REQUEST['Observaciones']=str_replace("\r", '',$_REQUEST['Observaciones']);
	$_REQUEST['Observaciones']='<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">'.$nombre_usu.' - '.date('Y/m/d h:i A').'</span><br>
							 <span style="margin:2px 0px 0px 15px; display: block;">'.$_REQUEST['Observaciones'].'</span></p>';


	if($_REQUEST['tipo_doc']=='Factura' || $_REQUEST['tipo_doc']=='Cuenta de cobro'){		
		queryQR("update fac_radica set observaciones=  COALESCE( observaciones , '')  || '".$_REQUEST['Observaciones'] ."' where serial_factura='". $_REQUEST['tramite']."'");
		$nuevoFlujo = new Workflow( $_REQUEST['tramite'], utf8_decode($_REQUEST['workflow']));
		$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
		$nuevoFlujo -> ingresaSiguiente($_REQUEST['usuario']);				
	}

	if($_REQUEST['tipo_doc']=='Orden de giro'){		
		queryQR("update fac_ordengiro set observaciones=  COALESCE( observaciones , '')  || '".$_REQUEST['Observaciones'] ."' where num_ordengiro='". $_REQUEST['tramite']."'");

		$result=queryQR("select serial_factura from fac_radica fac join fac_ordengiro ord USING(id_ordengiro) where num_ordengiro='". $_REQUEST['tramite']."'");
		while ($row = $result->FetchRow()) {
			$nuevoFlujo = new Workflow( $row['serial_factura'], utf8_decode($_REQUEST['workflow']));
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($_REQUEST['usuario']);	
		}		
	}

	if($_REQUEST['tipo_doc']=='Comprobante de pago'){		
		queryQR("update fac_comprobantepago set observaciones=  COALESCE( observaciones , '')  || '".$_REQUEST['Observaciones'] ."' where num_comprobante='". $_REQUEST['tramite']."'");
		$result=queryQR("select serial_factura from fac_radica fac join fac_ordengiro ord USING(id_ordengiro) join fac_comprobantepago 
				com USING(id_comprobante) where num_comprobante='". $_REQUEST['tramite']."'");
		while ($row = $result->FetchRow()) {
			$nuevoFlujo = new Workflow( $row['serial_factura'], utf8_decode($_REQUEST['workflow']));
			$nuevoFlujo -> actualizaActual($_SESSION['uscod']);
			$nuevoFlujo -> ingresaSiguiente($_REQUEST['usuario']);	
		}	
	}

	$save->guardado = true;
	echo json_encode($save);
}
?>