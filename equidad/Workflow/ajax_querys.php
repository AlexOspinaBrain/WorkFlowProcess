<?php
session_start();
header ('Content-type: text/html; charset=utf-8');
include ("../config/conexion.php");


function CalculaTiempoLimite($tramite, $TiempoHoras){
	$TiempoHoras = strtotime( date('Y-m-d H:i:s', 0)."+$TiempoHoras hour");
	$TiempoCalculado= strtotime( "now");
	$TiempoSumado= 0;
	
	$result = queryQR("select to_char(fechahora_limite, 'yyyy-mm-dd')|| ' ' ||(select max(horario_hasta) from wf_horarios) 
						as limite from wf_radicacion where id_radicacion=$tramite");
	$row = $result->FetchRow();
	$fechalimite = strtotime($row['limite']);

	$result = queryQR("select * from wf_horarios");
	$horario = $result->GetArray();
	
	for($i=0; $TiempoSumado < $TiempoHoras && $TiempoCalculado < $fechalimite; $i++){
		$nuevafecha = date ( 'Y-m-d' , strtotime( date('Y-m-d')."+$i day") );
		$result = queryQR("select * from wf_festivo where festivo='$nuevafecha'");
		if(!($row = $result->FetchRow())){
			foreach ($horario as $valor){
				$desde = strtotime($nuevafecha.' '.$valor['horario_desde']);
				$hasta = strtotime($nuevafecha.' '.$valor['horario_hasta']);
				
				if($TiempoCalculado < $desde && $TiempoSumado < $TiempoHoras){
					if($TiempoSumado+($hasta - $desde) > $TiempoHoras){		
						$TiempoCalculado = $desde+($TiempoHoras-$TiempoSumado);
						$TiempoSumado += ($TiempoHoras-$TiempoSumado) ;
					}else{
						$TiempoSumado += ($hasta - $desde);						
						$TiempoCalculado = $hasta;
					}
				}
				
				if($TiempoCalculado >= $desde && $TiempoCalculado < $hasta && $TiempoSumado < $TiempoHoras){
					if($TiempoSumado+($hasta - $TiempoCalculado) > $TiempoHoras){		
						$TiempoCalculado = $TiempoCalculado+($TiempoHoras-$TiempoSumado);
						$TiempoSumado += ($TiempoHoras-$TiempoSumado) ;						
					}else{
						$TiempoSumado += ($hasta - $TiempoCalculado);
						$TiempoCalculado = $hasta;
					}
				}
				
				if($TiempoCalculado > $fechalimite)
					$TiempoCalculado = $fechalimite;
			}
		}
	}
	return date( 'Y-m-d H:i:s', $TiempoCalculado);
}

function TiempoHoras($tramite, $idActividad){
	echo "<br>$tramite<br>";
	echo "<br>$idActividad<br>";
	$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) where id_radicacion=$tramite");
	$row = $result->FetchRow();
	
	if($row['ente_de_control']=='t'){//Es o no es ente de control
		$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) join wf_tiemposactividad 
					USING(id_tipotramite) where id_actividad = $idActividad and id_radicacion=$tramite");
		$row = $result->FetchRow();
		$tiempo = ($row['tiempo']*$row['tiempo_tramite'])/8;
		$tiempo =  round($tiempo, 0, PHP_ROUND_HALF_DOWN);
		return $tiempo;
	}else{
		$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) join wf_tiemposactividad 
					USING(id_tipotramite) where id_actividad = $idActividad and id_radicacion=$tramite");
		$row = $result->FetchRow();
		return $row['tiempo'];
	}
}


$opcion = $_REQUEST['op'];	
$qAjax = $_REQUEST['term'];	
$salida="";
/*?>
<script>
alert("e"+.$_REQUEST['codigo']+$_REQUEST['id_compania']+$_REQUEST['id_proceso']+$_REQUEST['id_servicio']+"r")
</script>

<?php
*/
if($opcion=='buscaActividades'){
	$result=queryQR("select id_actividad, desc_actividad from wf_actividad where desc_actividad ilike '%$qAjax%' order by desc_actividad");

	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_actividad"].'", "value": "'.$row["desc_actividad"].'" }, ';
	}
}

if($opcion=='existRespuesta'){
	$result=queryQR("select carta_respuesta is not null as exists from wf_radicacion where id_radicacion=$qAjax");
	$row = $result->FetchRow();
	$salida.='{ "exists": "'.$row['exists'].'"}, ';
}

if($opcion=='DatosClasificacion'){
	$result=queryQR("select id_clasificacion, desc_clasificacion || ' - ' || com.des_compania as clasificacion from wf_clasificacion_producto pr, 
					wf_compania com where com.id_compania=pr.id_compania order by clasificacion");

	while ($row = $result->FetchRow()){
		$salida.='{ "id_clasificacion": "'.$row["id_clasificacion"].'", "clasificacion": "'.$row["clasificacion"].'" }, ';
	}
}

if($opcion=='GuardaClasificacion'){
	$result=queryQR("update wf_producto set id_clasificacion='".$_REQUEST['clasificacion']."' where descripcion='".utf8_decode($_REQUEST['producto'])."'");
}

if($opcion=='buscaInput'){
	$result=queryQR("select id_input, desc_input from wf_inputs");

	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_input"].'", "value": "'.$row["desc_input"].'" }, ';
	}
}

if($opcion=='ReasignaTramite'){
	$result=queryQR("select usu.usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres 
					from wf_radicacion rad, wf_historial his, wf_workflow wor, wf_workflowusuarios wfu, adm_usuario usu where usu.usuario_cod=wfu.usuario_cod and 
					wfu.id_workflow=wor.id_workflow and wor.id_workflow=his.id_workflow AND	his.id_radicacion=rad.id_radicacion and his.fechahora is null and 
					usu.usuario_cod!=".$_REQUEST['UsuarioActual']." and rad.id_radicacion=".$_REQUEST['Tramite']);
					
	while ($row = $result->FetchRow())
		$salida.='{ "usuario_cod": "'.$row['usuario_cod'].'", "nombres": "'.$row['nombres'].'"}, ';
}

if($opcion=='flujoreabrirentes'){

	$procesose = array();

	$result = queryQR("select * from wf_radicacion radd, wf_tipologia as tip, wf_historial as his 
		where radd.id_radicacion='$qAjax' and radd.estado = 'Re-abierto' and radd.id_tipotramite = 3 and (tip.id_proceso in (3,29,21,23,24) or tip.id_compania in (4)) and his.fechahora is null and his.actividad in ('Aprobacion','Revision','Generar respuesta')
		and radd.id_radicacion = his.id_radicacion and radd.id_tipologia = tip.id_tipologia");

	if($result->RecordCount() > 0){

		$salida.='{ "id": "s", "value": "1" }, ';
	}
	else
		$salida.='{ "id": "n", "value": "999" }, ';


}

if($opcion=='ipmprocesores'){

	
	$procesose = array();

	$result = queryQR("select * from wf_radicacion radd,wf_historial his, wf_tipologia tipol
		 where his.id_radicacion='$qAjax' and his.fechahora is null and his.actividad = 'Generar respuesta' and radd.id_radicacion = his.id_radicacion and radd.id_tipologia = tipol.id_tipologia");
	if($result->RecordCount() > 0){
		

		$row = $result->FetchRow();

		if ($row['id_compania']==4){
		 	$result2 = queryQR("select pr.id_proceso,pr.proceso_desc from wf_proceso pr, wf_tiporespuesta tprr where pr.id_proceso = tprr.id_proceso and proceso_eliminado = false and pr.id_proceso in (10,27) 
		 		group by pr.id_proceso, pr.proceso_desc ");
		 }else{
		 	$result2 = queryQR("select pr.id_proceso,pr.proceso_desc from wf_proceso pr, wf_tiporespuesta tprr where pr.id_proceso = tprr.id_proceso and proceso_eliminado = false 
		 		group by pr.id_proceso, pr.proceso_desc ");
		}

		while ($row2 = $result2->FetchRow()){		
			$procesose[]=  array("id_proceso"=>$row2['id_proceso'],"proceso_desc"=>utf8_encode($row2['proceso_desc']));
		} 

		$salida.='{ "id": "1", "value": "1" , "procesose":'. json_encode($procesose).'}, ';
	}
	else
		$salida.='{ "id": "999", "value": "999" , "procesose":'. json_encode($procesose).'}, ';

	
}

if($opcion=='muestrasu'){

	/*$result = queryQR("select * from wf_radicacion radd, wf_tipologia tipol
		 where radd.id_radicacion='$qAjax' and tipol.id_tipotramite = 3 and radd.estado <> 'Re-abierto' and
		 tipol.id_compania <> 4 and 
		 radd.id_tipologia = tipol.id_tipologia");*/

	$result = queryQR("select * from wf_radicacion radd, wf_tipologia tipol
		 where radd.id_radicacion='$qAjax' and tipol.id_tipotramite = 3  and
		 tipol.id_compania <> 4 and 
		 radd.id_tipologia = tipol.id_tipologia");

	$result1 = queryQR("select * from wf_radicacion radd,wf_historial his, wf_tipologia tipol
		 where his.id_radicacion='$qAjax' and his.fechahora is null and (tipol.id_tipotramite <> 3 or tipol.id_tipotramite is null) 
		 and
		 radd.id_radicacion = his.id_radicacion and radd.id_tipologia = tipol.id_tipologia");
// se quita de la consulta que evite seleccionar el usuario al generar respuesta, es decir ahora si se puede seleccionar a quien.
/*$result1 = queryQR("select * from wf_radicacion radd,wf_historial his, wf_tipologia tipol
		 where his.id_radicacion='$qAjax' and his.fechahora is null and (tipol.id_tipotramite <> 3 or tipol.id_tipotramite is null) 
		 and his.actividad <> 'Generar respuesta' and
		 radd.id_radicacion = his.id_radicacion and radd.id_tipologia = tipol.id_tipologia");
*/
	if($result->RecordCount() > 0 || $result1->RecordCount() > 0)
		$salida.='{ "simuestra": "S"}, ';
	else
		$salida.='{ "simuestra": "N"}, ';
}



if($opcion=='TipoRsp'){

	$result = queryQR("select * from wf_radicacion radd,wf_historial his, wf_tipologia tipol
		 where his.id_radicacion='$qAjax' and his.fechahora is null and his.actividad = 'Generar respuesta' and 
		 radd.id_radicacion = his.id_radicacion and radd.id_tipologia = tipol.id_tipologia");
	if($result->RecordCount() > 0){
			$rowp = $result->FetchRow();

			$result2=queryQR("select cod_respuesta, respuesta from wf_tiporespuesta where eliminado_tprespuesta=false and id_proceso = ".$_REQUEST['proce']);
							
			while ($row = $result2->FetchRow()){
				$salida.='{ "id": "'.$row['cod_respuesta'].'", "value": "'.$row['respuesta'].'"}, ';
			}
	}else
		$salida.='{ "id": "999", "value": "999"}, ';
}

if($opcion=='ageusu'){


		$result=queryQR("select ag.codigo,ag.descrip from tblradofi  ag, tblareascorrespondencia are
				where are.areasid = '".$_REQUEST['Areaa']."' and are.agencia=ag.codigo");

		$row = $result->FetchRow();
		$salida.='{ "id": "'.$row['codigo'].'", "value": "'.$row['descrip'].'"}, ';
		
}

if($opcion=='GuardaReasignaTramite'){
	if($_REQUEST['UsuarioReasignar'] == null || $_REQUEST['ObservacionesReasigna'] == null)
		exit();
		
	$result = queryQR("select * from wf_radicacion rad, wf_historial his where his.id_radicacion=rad.id_radicacion and his.fechahora is null and rad.id_radicacion=".$_REQUEST['Tramite']);
	if($result->RecordCount() > 0){
		$row = $result->FetchRow();
		
		queryQR("update wf_historial set fechahora=now(),usuario_cod='".$_SESSION['uscod']."', observacion='Reasignación: ".$_REQUEST['ObservacionesReasigna']."' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		
		$result2 = queryQR("select * from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
				act.id_actividad=wor.id_actividad and wor.id_workflow='".$row['id_workflow']."' and tie.id_tipotramite='".$row['id_tipotramite']."'");
		$row2 = $result2->FetchRow();
		
		$TiempoHoras = TiempoHoras($_REQUEST['Tramite'], $row2['id_actividad']);
		$Limite = CalculaTiempoLimite($_REQUEST['Tramite'], $TiempoHoras);

		queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) 
					values (".$_REQUEST['Tramite'].", '".$row['actividad']."',
						'".$_REQUEST['UsuarioReasignar']."', '$Limite', ".$row['id_workflow'].", '$TiempoHoras')");
	}
}

if($opcion=='DevolucionTramite'){
	$actividades = array();
	$causales = array();
	
	//se cambia para devolver a radicación
	$resqi=queryQR("select * from wf_historial his where  his.id_radicacion=".$_REQUEST['Tramite']." 
					 and his.actividad = 'Clasificar'");
	if($resqi->RecordCount()==0){
		$result=queryQR("select * from wf_historial his, wf_radicacion rad where his.id_radicacion=rad.id_radicacion and his.id_radicacion=".$_REQUEST['Tramite']." 
						 and  (rad.estado='En tramite' or rad.estado='Re-abierto') and his.fechahora is not null and  his.actividad!=(select actividad from wf_historial 
						 where fechahora is null and id_radicacion=his.id_radicacion)  order by his.id_historial asc");
	}else{
		$result=queryQR("select * from wf_historial his, wf_radicacion rad where his.id_radicacion=rad.id_radicacion and his.id_radicacion=".$_REQUEST['Tramite']." 
						 and his.actividad!='Radicar' and (rad.estado='En tramite' or rad.estado='Re-abierto') and his.fechahora is not null and  his.actividad!=(select actividad from wf_historial 
						 where fechahora is null and id_radicacion=his.id_radicacion)  order by his.id_historial asc");		
	}

	$result1=queryQR("select * from wf_radicacion rad where id_radicacion=".$_REQUEST['Tramite']);

	$prevrow = $result1->FetchRow();
	if ($prevrow['estado']=='Re-abierto'){
		while ($row = $result->FetchRow()){		
			if ($row['actividad']=='Generar respuesta'){
				$actividades[]=  array("id_workflow"=>$row['id_workflow'],"actividad"=>utf8_encode($row['actividad']));
			}
		}
	}else{
		if($result->RecordCount()!=0){
			while ($row = $result->FetchRow()){		
				$actividades[]=  array("id_workflow"=>$row['id_workflow'],"actividad"=>utf8_encode($row['actividad']));
			}
		}
	}
	$result=queryQR("select * from wf_causaldevolucion");
					
	while ($row = $result->FetchRow()){		
		$causales[]=  array("id_causal_devolucion"=>$row['id_causal_devolucion'],"desc_causal_devolucion"=>utf8_encode($row['desc_causal_devolucion']));
	}
	
	$salida.='{ "actividades": '.json_encode($actividades).', "causales": '.json_encode($causales).'}, ';
}

if($opcion=='buscaTipologias'){
	$compania=(($_REQUEST['compania']=='Generales')?"1":"0");
	$result=queryQR("select id_tipologia, (id_tipologia || '-->'|| desc_tipologia || '-->'|| proceso_desc || '-->'|| descrip) as tipologia from wf_tipologia tip, tblradofi ofi, wf_proceso pro where 
					 ofi.codigo=tip.id_agencia and pro.id_proceso=tip.id_proceso and tip.id_servicio='".$_REQUEST['serv']."' and 
					 desc_tipologia ilike '$qAjax%' and id_compania='$compania' and id_tipotramite is null
					 order by id_tipologia");

	while ($row = $result->FetchRow()){
		$proximos = array();
		$result2=queryQR("select id_flujo from  wf_workflow wor, wf_flujo flu where wor.id_workflow=flu.id_workflow and wor.id_tipologia=".$row["id_tipologia"]." and wor.inicio_workflow=true");
		while ($row2 = $result2->FetchRow()){
			$result3=queryQR("select id_workflow, desc_actividad from  wf_workflow wor, wf_actividad act where wor.id_actividad=act.id_actividad and  wor.id_workflow=".$row2['id_flujo']);
			$row3 = $result3->FetchRow();
			$proximos[]=  array("id"=>$row3['id_workflow'],"value"=>$row3['desc_actividad']);
		}
		$salida.='{ "id": "'.$row["id_tipologia"].'", "value": "'.$row["tipologia"].'" , "proximos":'. json_encode($proximos).'}, ';
	}
}

if($opcion=='copiaTipologia'){
	$result=queryQR("select * from wf_tipologia tip, tblradofi ofi, wf_proceso pro, wf_compania com, wf_servicio ser where 
					ofi.codigo=tip.id_agencia and pro.id_proceso=tip.id_proceso and com.id_compania=tip.id_compania and 
					ser.id_servicio=tip.id_servicio and	tip.id_tipologia=$qAjax order by id_tipologia");

	while ($row = $result->FetchRow()){
		$actividades = array();
		$result2=queryQR("select * from wf_workflow wor, wf_actividad act where wor.id_actividad=act.id_actividad and wor.id_tipologia=".$row['id_tipologia']." order by id_workflow");
				
		while ($row2 = $result2->FetchRow()){
			$usuarios = array();
			$flujos = array();
			
			$result3=queryQR("select *, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres 
						from wf_workflowusuarios wfu, adm_usuario usu where usu.usuario_cod=wfu.usuario_cod and wfu.id_workflow=".$row2['id_workflow']);
			while ($row3 = $result3->FetchRow()){
				$usuarios[]=  array("id_usuario"=>$row3['usuario_cod'],"desc_usuario"=>utf8_encode($row3['nombres']));
			}
			
			$result3=queryQR("select * from wf_flujo flu, wf_workflow wor, wf_actividad act where flu.id_flujo=wor.id_workflow AND
						act.id_actividad=wor.id_actividad and flu.id_workflow=".$row2['id_workflow']);
			while ($row3 = $result3->FetchRow()){
				$flujos[]=  array("id_actividad"=>$row3['id_actividad'],"desc_actividad"=>utf8_encode($row3['desc_actividad']));
			}
			
			$actividades[]=  array("id_actividad"=>$row2['id_actividad'],"desc_actividad"=>utf8_encode($row2['desc_actividad']), usuarios=>($usuarios), flujos=>($flujos));
			if($row2['inicio_workflow']=='t')
				$inicio=$row2['id_actividad'];
				
			if($row2['fin_workflow']=='t')
				$fin=$row2['id_actividad'];
		}
	
		$salida.='{ "nombre": "'.$row["desc_tipologia"].'", 
					"alterna": "'.$row["desc_tipologiaalterna"].'", 
					"compania": "'.$row["id_compania"].'", 
					"codigosuper": "'.$row["codigo_entidad"].'", 
					"agencia": "'.$row["id_agencia"].'", 
					"idtipologia": "'.$row["id_tipologia"].'", 
					"proceso": "'.$row["id_proceso"].'", 
					"servicio": "'.$row["id_servicio"].'",
					"tipotra": "'.$row["id_tipotramite"].'",
					 
					"habilitada": "'.$row["eliminado_tipologia"].'", 
					"inicio": "'.$inicio.'", 
					"fin": "'.$fin.'", 
					"actividades": '.json_encode($actividades).'}, ';
	}
}

if($opcion=='buscaTipoTramite'){
	$result=queryQR("select id_tipotramite, desc_tipotramite from wf_tipotramite tra, wf_servicio ser where ser.id_servicio=tra.id_servicio and ser.id_servicio=$qAjax order by id_tipotramite");

	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_tipotramite"].'", "value": "'.$row["desc_tipotramite"].'" }, ';
	}
}

if($opcion=='BuscaTipologia'){
	$tipologias = array();

	if ($_REQUEST['tptra']==3)
		$tptra= " and id_tipotramite = 3";
	else
		$tptra= " and id_tipotramite is null";

	$result=queryQR("select * from wf_tipologia where id_servicio = " . $_REQUEST['id_servicio'] . " and id_proceso = 
		" .$_REQUEST['id_proceso']. " and id_agencia = '" .$_REQUEST['codigo']. "' 
		 and id_compania = " . $_REQUEST['id_compania'] . " and not eliminado_tipologia 
		 $tptra ");
	while ($row = $result->FetchRow()){		
		$tipologias[]=  array("id_tipologia"=>$row['id_tipologia'],"desc_tipologia"=>utf8_encode($row['desc_tipologia']));
		//$salida.='{ "id": "'.$row["id_tipologia"].'", "value": "'.$row["desc_tipologia"].'" }, ';
	}
	
	$salida.='{ "tipologias": '.json_encode($tipologias).'}, ';
}

if($opcion=='BuscaTipologiaR'){
        $tipologias = array();
		$comlpemtip="";

        if ($_REQUEST['tpreclamo'] == 3 )
        	$comlpemtip = " and id_tipotramite = 3";


        $result=queryQR("select * from wf_tipologia where id_servicio = " . $_REQUEST['id_servicio'] . " and id_proceso = " .
        		$_REQUEST['id_proceso']. " and id_agencia = '" .$_REQUEST['codigo']. "'
                 and id_compania = " . $_REQUEST['id_compania'] . " " . $comlpemtip . " and not eliminado_tipologia");
        while ($row = $result->FetchRow()){
                //$tipologias[]=  array("id_tipologia"=>$row['id_tipologia'],"desc_tipologia"=>utf8_encode($row['desc_tipologia']));
                $salida.='{ "id": "'.$row["id_tipologia"].'", "value": "'.$row["desc_tipologia"].'" }, ';
        }

        //$salida.='{ "tipologias": '.json_encode($tipologias).'}, ';
}

if($opcion=='buscaProcesoEC'){
        
        $result=queryQR("select distinct pr.id_proceso, pr.proceso_desc from wf_tipologia tp
        	 inner join wf_proceso pr on tp.id_proceso = pr.id_proceso  
        	 inner join wf_servicio srv on tp.id_servicio = srv.id_servicio
        	 where id_agencia = '$qAjax' and srv.id_servicio = " . $_REQUEST['servee'] . "
                 and id_compania = " . $_REQUEST['compan'] . " and not eliminado_tipologia");

        while ($row = $result->FetchRow()){

                $salida.='{ "id": "'.$row["id_proceso"].'", 
                			"value": "'.$row["proceso_desc"].'" }, ';
        }

        
}

if($opcion=='BuscaUsuResp'){
        
    /*$result=queryQR("select distinct usu.usuario_cod, usu.usuario_nombres || ' ' || usu.usuario_priape || ' ' || usu.usuario_segape as nombre
    	from wf_workflow wor, wf_workflowusuarios wfusu, adm_usuario usu
    	where wor.id_tipologia = ".$_REQUEST['id_tipologia']. " and wor.id_actividad = 4 and 
    		wor.id_workflow = wfusu.id_workflow and wfusu.usuario_cod = usu.usuario_cod
    	order by nombre");*/

	$result=queryQR("select distinct usu.usuario_cod, usu.usuario_nombres || ' ' || usu.usuario_priape || ' ' || usu.usuario_segape as nombre

    	from wf_workflowusuarios wfusu inner join adm_usuario usu using (usuario_cod) inner join wf_workflow wor using (id_workflow) 
    	inner join (select wf_flujo.id_flujo from wf_workflow, wf_flujo  where wf_flujo.id_workflow = wf_workflow.id_workflow 
			and wf_workflow.id_tipologia = ".$_REQUEST['id_tipologia']. " 
			order by wf_workflow.id_workflow limit 1) as ffl on wor.id_workflow = ffl.id_flujo");
	
        while ($row = $result->FetchRow()){

                $salida.='{ "id": "'.$row["usuario_cod"].'", 
                			"value": "'.$row["nombre"].'" }, ';
        }

        
}

if($opcion=='buscaCambioTipologias'){
	$compania = array();
	$agencia = array();
	$servicio = array();
	$procesos = array();
	$tipologias = array();
	
	$result=queryQR("select * from wf_compania where id_compania!='0'  and eliminado_compania=false order by id_compania");
	
	while ($row = $result->FetchRow()){		
		$compania[]=  array("id_compania"=>$row['id_compania'],"des_compania"=>$row['des_compania']);
	}
	
	$result=queryQR("select * from tblradofi where codigo!='0' and codigo!='094' and codigo!='999' order by descrip");
	
	while ($row = $result->FetchRow()){		
		$agencia[]=  array("codigo"=>$row['codigo'],"descrip"=>utf8_encode($row['descrip']));
	}
	
	$result=queryQR("select * from wf_servicio where eliminado_servicio=false");
	
	while ($row = $result->FetchRow()){		
		$servicio[]=  array("id_servicio"=>$row['id_servicio'],"desc_servicio"=>utf8_encode($row['desc_servicio']));
	}

	$result=queryQR("select tip.id_tipologia, tip.id_agencia, tip.id_compania, tip.id_proceso, tip.id_servicio, tip.id_tipotramite from wf_radicacion rad, wf_tipologia tip where rad.id_tipologia=tip.id_tipologia and rad.id_radicacion='$qAjax'");
	$row = $result->FetchRow();

	if($row['id_tipotramite']==3)
		$tptr=" and tp.id_tipotramite = 3 and rad.id_tipotramite = tt.id_tipotramite ";
	else
		$tptr=" and tp.id_tipotramite is null ";

	$resultp=queryQR("select distinct pr.* from wf_proceso pr, wf_tipologia tp, wf_radicacion rad, 
		wf_tipotramite tt, wf_servicio  sr where rad.id_radicacion = $qAjax and pr.id_proceso!='0' and
		proceso_eliminado=false $tptr and tp.eliminado_tipologia = false
		and tt.id_servicio = sr.id_servicio 
		and tp.id_servicio = sr.id_servicio and tp.id_proceso = pr.id_proceso");
	
	while ($rowp = $resultp->FetchRow()){		
		$procesos[]=  array("id_proceso"=>$rowp['id_proceso'],"desc_proceso"=>utf8_encode($rowp['proceso_desc']));
	}
	
	
	$result2=queryQR("select * from wf_tipologia tp where id_agencia='".$row['id_agencia']."' and id_compania='".$row['id_compania']."' and id_proceso='".$row['id_proceso']."' and id_servicio='".$row['id_servicio']."' $tptr and not eliminado_tipologia");
	
	while ($row2 = $result2->FetchRow()){		
		$tipologias[]=  array("id_tipologia"=>$row2['id_tipologia'],"desc_tipologia"=>utf8_encode($row2['desc_tipologia']));
	}
	
	$salida.='{ "companias": '.json_encode($compania).',
				"agencias": '.json_encode($agencia).', 
				"servicios": '.json_encode($servicio).', 
				"procesos": '.json_encode($procesos).', 
				"tipologias": '.json_encode($tipologias).', 
				"tipologia":"'.$row['id_tipologia'].'", 
				"agencia":"'.$row['id_agencia'].'", 
				"compania":"'.$row['id_compania'].'", 
				"proceso":"'.$row['id_proceso'].'", 
				"servicio":"'.$row['id_servicio'].'",
				"tptramite":"'.$row['id_tipotramite'].'"}, ';
	
}

if($opcion=='buscaNombre'){
	/*$result=queryQR("select usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from adm_usuario 
			where usuario_nombres ilike '".$qAjax."%'  order by nombres", null, null);
	
	$salida.="{ \"id\": \"0\", \"value\": \"TODOS\" }, ";
	while ($row = $result->FetchRow()){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["nombres"]."\" }, ";
	}
	
	$result=queryQR("select usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from adm_usuario 
			where  usuario_priape ilike '".$qAjax."%' order by nombres", null, null);
	
	while ($row = $result->FetchRow()){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["nombres"]."\" }, ";
	}
	*/
	$result=queryQR("select usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from adm_usuario 
			where (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) ilike '%".$qAjax."%' and usuario_bloqueado = false order by nombres");
	
	while ($row = $result->FetchRow()){
		$salida.="{ \"id\": \"".$row["usuario_cod"]."\", \"value\": \"".$row["nombres"]."\" }, ";
	}
}

if($opcion=='GuardaTipologia'){
	$tipologia=$_REQUEST['Tipologia'];
	
	if (!$tipologia['tptramite'])

		$result=queryQR("insert into wf_tipologia (desc_tipologia, desc_tipologiaalterna, eliminado_tipologia, id_compania, id_proceso, id_servicio, id_agencia, codigo_entidad) values
					('".utf8_decode($tipologia['nombre'])."','".utf8_decode($tipologia['alterna'])."', '".(($tipologia['habilitada']=='true')?"0":"1")."', '".$tipologia['compania']."', '".$tipologia['proceso']."', '"
					.$tipologia['servicio']."', '".$tipologia['agencia']."', '".$tipologia['codigosuper']."')");
	else{
		$result=queryQR("insert into wf_tipologia (desc_tipologia, desc_tipologiaalterna, eliminado_tipologia, id_compania, id_proceso, id_servicio, id_agencia, codigo_entidad,id_tipotramite) values
					('".utf8_decode($tipologia['nombre'])."','".utf8_decode($tipologia['alterna'])."', '".(($tipologia['habilitada']=='true')?"0":"1")."', '".$tipologia['compania']."', '".$tipologia['proceso']."', '"
					.$tipologia['servicio']."', '".$tipologia['agencia']."', 
					'".$tipologia['codigosuper']."', '".$tipologia['tptramite']."')");

	}	
	if($result == true){
		$result = queryQR("select id_tipologia from wf_tipologia order by id_tipologia desc limit 1");
		$row = $result->FetchRow();

		foreach ($tipologia['actividades'] as $actividad){
			$result=queryQR("insert into wf_workflow (id_tipologia, id_actividad, inicio_workflow, fin_workflow) values
				('".$row['id_tipologia']."', '".$actividad['id_actividad']."', false, false)");
				
			if($result == true){
				$result2 = queryQR("select id_workflow from wf_workflow order by id_workflow desc limit 1");
				$row2 = $result2->FetchRow();
				
				if($actividad['usuarios'])
				foreach ($actividad['usuarios'] as $usuario){
					queryQR("insert into wf_workflowusuarios (id_workflow, usuario_cod) values
						('".$row2['id_workflow']."', '".$usuario['id_usuario']."')");
				}
			}
		}
		
		foreach ($tipologia['actividades'] as $actividad){
			$result2=queryQR("select * from wf_workflow where id_tipologia='".$row['id_tipologia']."' and id_actividad='".$actividad['id_actividad']."'");
			$row2 = $result2->FetchRow();

			if($actividad['flujos'])
			foreach ($actividad['flujos'] as $flujos){
				$result3=queryQR("select * from wf_workflow where id_tipologia='".$row['id_tipologia']."' and id_actividad='".$flujos['id_actividad']."'");
				$row3 = $result3->FetchRow();
				queryQR ("insert into wf_flujo (id_workflow, id_flujo) values('".$row2['id_workflow']."', '".$row3['id_workflow']."')");
			}
			
		}
		
		$result2=queryQR("select * from wf_workflow where id_tipologia='".$row['id_tipologia']."' and id_actividad='".$tipologia['inicio']."'");
		$row2 = $result2->FetchRow();
		queryQR("update wf_workflow set inicio_workflow=true where id_workflow='".$row2['id_workflow']."'");
		
		$result2=queryQR("select * from wf_workflow where id_tipologia='".$row['id_tipologia']."' and id_actividad='".$tipologia['fin']."'");
		$row2 = $result2->FetchRow();
		queryQR("update wf_workflow set fin_workflow=true where id_workflow='".$row2['id_workflow']."'");
	}
	
	$salida.='{ "termino": "si"}, ';
}

if($opcion=='EditaTipologia'){
	$tipologia=$_REQUEST['Tipologia'];
	
	$updtiptipol="";
	if($tipologia['tptramite']!=null){
		$updtiptipol = ", id_tipotramite='".$tipologia['tptramite']."' ";
	}

	$result=queryQR("update wf_tipologia set desc_tipologia='".utf8_decode($tipologia['nombre'])."', 
		desc_tipologiaalterna='".utf8_decode($tipologia['alterna'])."',
		eliminado_tipologia='".(($tipologia['habilitada']=='true')?"0":"1")."', 
		id_compania='".$tipologia['compania']."', id_proceso='".$tipologia['proceso']."', 
		id_servicio='".$tipologia['servicio']."', id_agencia='".$tipologia['agencia']."', 
		codigo_entidad='".$tipologia['codigosuper']."' $updtiptipol
		where id_tipologia='".$tipologia['idtipologia']."'");
	
	if($result == true){
		foreach ($tipologia['actividades'] as $actividad){
			$result=queryQR("select * from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$actividad['id_actividad']."'");
			
			if($result->RecordCount()==0){
				$result=queryQR("insert into wf_workflow (id_tipologia, id_actividad, inicio_workflow, fin_workflow) values
				('".$tipologia['idtipologia']."', '".$actividad['id_actividad']."', false, false)");
			}
			
			$result2 = queryQR("select id_workflow from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$actividad['id_actividad']."'");
			$row2 = $result2->FetchRow();
			
			queryQR("delete from wf_flujo where id_workflow='".$row2['id_workflow']."'");
			queryQR("delete from wf_workflowusuarios where id_workflow='".$row2['id_workflow']."'");

			if($actividad['usuarios'])
				foreach ($actividad['usuarios'] as $usuario){
					queryQR("insert into wf_workflowusuarios (id_workflow, usuario_cod) values
						('".$row2['id_workflow']."', '".$usuario['id_usuario']."')");
				}
		}
		
		foreach ($tipologia['actividades'] as $actividad){
			$result2=queryQR("select * from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$actividad['id_actividad']."'");
			$row2 = $result2->FetchRow();

			if($actividad['flujos'])
			foreach ($actividad['flujos'] as $flujos){
				$result3=queryQR("select * from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$flujos['id_actividad']."'");
				$row3 = $result3->FetchRow();
				queryQR ("insert into wf_flujo (id_workflow, id_flujo) values('".$row2['id_workflow']."', '".$row3['id_workflow']."')");
			}
			
		}
		queryQR("update wf_workflow set inicio_workflow=false, fin_workflow=false where id_tipologia='".$tipologia['idtipologia']."'");
		
		$result2=queryQR("select * from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$tipologia['inicio']."'");
		$row2 = $result2->FetchRow();
		queryQR("update wf_workflow set inicio_workflow=true where id_workflow='".$row2['id_workflow']."'");
		
		$result2=queryQR("select * from wf_workflow where id_tipologia='".$tipologia['idtipologia']."' and id_actividad='".$tipologia['fin']."'");
		$row2 = $result2->FetchRow();
		queryQR("update wf_workflow set fin_workflow=true where id_workflow='".$row2['id_workflow']."'");
	}
	$salida.='{ "termino": "si"}, ';
}

if($opcion=='ProximaActividad'){

		//consulta para saber si es ente de control reabierto
	$qreabreente = queryQR("select * from wf_radicacion radd, wf_tipologia as tip, wf_historial as his 
		where radd.id_radicacion='$qAjax' and radd.estado = 'Re-abierto' and radd.id_tipotramite = 3 and 
			(tip.id_proceso in (3,29,21,23,24) or tip.id_compania in (4)) and his.actividad in ('Generar respuesta','Revision','Aprobacion') 
			and his.fechahora is null
			and radd.id_tipologia = tip.id_tipologia and radd.id_radicacion = his.id_radicacion");


	if($qreabreente->RecordCount()>0){


		//busca usuarios a aprobar en entes de control reabiertos
		$result2 = queryQR ("select * from adm_usuario usu, adm_usumenu usm where 
					usm.jerarquia_opcion = '4.1.5.5' and
					usu.usuario_cod = usm.usuario_cod ");
				
				if($result2->RecordCount() > 0){
					$rowU = $result2->FetchRow();
					$usuaprueba = $rowU['usuario_cod'];
					$usuapruebanom = utf8_encode($rowU['usuario_nombres'].' '.$rowU['usuario_priape'].' '.$rowU['usuario_segape']);
				}
				else{
					$usuaprueba = 1810;
					$usuapruebanom = "Luisa Velandia";
				}

		//busca usuarios a revisar en entes de control reabiertos
		$usuariorev=queryQR("select his.usuario_cod, usu.usuario_nombres, usu.usuario_priape, usu.usuario_segape 
			from wf_historial his, wf_workflow wor, adm_usuario usu where
			wor.id_workflow=his.id_workflow and his.usuario_cod = usu.usuario_cod and wor.id_actividad = 9 and his.id_radicacion='$qAjax' order by id_historial");
		

				if($usuariorev->RecordCount() > 0){
					
					$rowusurev = $usuariorev->FetchRow();
					$usurevisa = $rowusurev['usuario_cod'];
					$usurevisanom = utf8_encode($rowusurev['usuario_nombres'].' '.$rowusurev['usuario_priape'].' '.$rowusurev['usuario_segape']);
				}
				else{
					$usurevisa = 1810;
					$usurevisanom = "Luisa Velandia";
				}		


		//busca flujo para actividad enviar respuesta
		$flujoqe=queryQR("select wor.id_workflow from wf_radicacion rad, wf_workflow wor where 
			wor.id_tipologia=rad.id_tipologia and wor.id_actividad = 5 and rad.id_radicacion='$qAjax'");
		$rowfqe = $flujoqe->FetchRow();


		//busca usuarios a enviar respuesta segun flujo

		$usuariorev=queryQR("select wus.usuario_cod, usu.usuario_nombres, usu.usuario_priape, usu.usuario_segape 
			from wf_workflowusuarios wus, adm_usuario usu where
			wus.usuario_cod = usu.usuario_cod and wus.id_workflow = ". $rowfqe['id_workflow']);
		

				if($usuariorev->RecordCount() > 0){
					
					$rowusurev = $usuariorev->FetchRow();
					$usuenvia = $rowusurev['usuario_cod'];
					$usuenvianom = utf8_encode($rowusurev['usuario_nombres'].' '.$rowusurev['usuario_priape'].' '.$rowusurev['usuario_segape']);
				}
				else{
					$usuenvia = 1810;
					$usuenvianom = "Luisa Velandia";
				}

		//busca actividad actual para proceder el siguiente flujo... solo entes de control reabiertos
		$resultqe=queryQR("select id_actividad, his.actividad from wf_historial his, wf_workflow wor where
			wor.id_workflow=his.id_workflow and his.fechahora is null and his.id_radicacion='$qAjax'");



		while ($rowqe = $resultqe->FetchRow()){
			//se identifica 98 para Revision y 99 para aprobacion, todo esto para el requerimiento de actividades diferentes en el flujo al reabrir casos.
			$usuarios = array();
			switch($rowqe['id_actividad']){
				case "4": //Generar Respuesta
					$usuarios[]=  array("usuario_cod"=>$usurevisa,"nombres"=>$usurevisanom);
					$salida.='{ "id_workflow": "98", "desc_actividad": "Revision", "usuarios": '.json_encode($usuarios).' }, ';			
				break;				
				case "8": //Revision
					$usuarios[]=  array("usuario_cod"=>$usuaprueba,"nombres"=>$usuapruebanom);
					$salida.='{ "id_workflow": "99", "desc_actividad": "Aprobacion", "usuarios": '.json_encode($usuarios).' }, ';			
					
				break;
				case "9": //Aprobacion
				
					$usuarios[]=  array("usuario_cod"=>$usuenvia,"nombres"=>$usuenvianom);
					$salida.='{ "id_workflow": "'.$rowfqe['id_workflow'].'", "desc_actividad": "Enviar respuesta", "usuarios": '.json_encode($usuarios).' }, ';			
				break;
				case "98": //flujo reabrir entes
				case "99": //flujo reabrir entes
					switch($rowqe['actividad']){
						case "Revision":
							$usuarios[]=  array("usuario_cod"=>$usuaprueba,"nombres"=>$usuapruebanom);
							$salida.='{ "id_workflow": "99", "desc_actividad": "Aprobacion", "usuarios": '.json_encode($usuarios).' }, ';							
						break;
						case "Aprobacion":
							$usuarios[]=  array("usuario_cod"=>$usuenvia,"nombres"=>$usuenvianom);
							$salida.='{ "id_workflow": "'.$rowfqe['id_workflow'].'", "desc_actividad": "Enviar respuesta", "usuarios": '.json_encode($usuarios).' }, ';
						break;

					}
							
				break;		
			}	
		}

	}else{
		$qrseguimiento = queryQR("select seguimiento, id_tipotramite from wf_radicacion rad, wf_historial his where rad.id_radicacion='$qAjax' and seguimiento = true
			and his.id_radicacion = rad.id_radicacion and actividad = 'Seguimiento' and his.fechahora is null ");


		if($qrseguimiento->RecordCount()>0){
			$rowqrs = $qrseguimiento->FetchRow();
			if ($rowqrs['id_tipotramite'] != 3){
				$result=queryQR("select id_flujo,id_historial from wf_historial his, wf_workflow wor, wf_flujo flu where his.id_radicacion='$qAjax' and
				flu.id_workflow=wor.id_workflow and
				wor.id_workflow=his.id_workflow and actividad = 'Enviar respuesta'  order by id_historial desc");		
			}else{
				$result=queryQR("select id_flujo,id_historial from wf_historial his, wf_workflow wor, wf_flujo flu where his.id_radicacion='$qAjax' and
				flu.id_workflow=wor.id_workflow and
				wor.id_workflow=his.id_workflow and actividad = 'Cerrado'  order by id_historial desc");
			}
		}else{
			$result=queryQR("select id_flujo from wf_historial his, wf_workflow wor, wf_flujo flu where his.id_radicacion='$qAjax' and 
				flu.id_workflow=wor.id_workflow and
				wor.id_workflow=his.id_workflow and his.fechahora is null  order by id_flujo");
		}

		while ($row = $result->FetchRow()){
			$usuarios = array();
			$result2=queryQR("select * from wf_workflow wor, wf_actividad act where wor.id_actividad=act.id_actividad and wor.id_workflow='".$row['id_flujo']."'");
			$row2 = $result2->FetchRow();
			
			$result3=queryQR("select * from wf_workflowusuarios wfu, adm_usuario usu where wfu.usuario_cod=usu.usuario_cod and wfu.id_workflow='".$row2['id_workflow']."' and usu.usuario_bloqueado = false");
			while ($row3 = $result3->FetchRow()){		
				$usuarios[]=  array("usuario_cod"=>$row3['usuario_cod'],"nombres"=>utf8_encode($row3['usuario_nombres'].' '.$row3['usuario_priape'].' '.$row3['usuario_segape']));
			}
			
			$salida.='{ "id_workflow": "'.$row2['id_workflow'].'", "desc_actividad": "'.$row2['desc_actividad'].'", "usuarios": '.json_encode($usuarios).' }, ';
		}
		
	}
}

if($opcion=='CambiaProximaActividad'){
	$result=queryQR("select id_actividad from wf_radicacion rad, wf_historial his, wf_workflow wor where rad.id_radicacion=his.id_radicacion and his.id_workflow=wor.id_workflow
			and his.fechahora is null and rad.id_radicacion=$qAjax");

	$row = $result->FetchRow();
	if($result->RecordCount()>0)
		$id_actividad=$row['id_actividad'];
	else
		$id_actividad='1';
	
	$result=queryQR("select id_flujo from wf_workflow wor, wf_tipologia tip, wf_flujo flu where flu.id_workflow=wor.id_workflow and tip.id_tipologia=wor.id_tipologia and 
					tip.id_tipologia=".$_REQUEST['Tipologia']." and wor.id_actividad=".$id_actividad);
	
	while ($row = $result->FetchRow()){
		$usuarios = array();
		$result2=queryQR("select * from wf_workflow wor, wf_actividad act where wor.id_actividad=act.id_actividad and wor.id_workflow='".$row['id_flujo']."'");
		$row2 = $result2->FetchRow();
		
		$result3=queryQR("select * from wf_workflowusuarios wfu, adm_usuario usu where wfu.usuario_cod=usu.usuario_cod and wfu.id_workflow='".$row2['id_workflow']."' and usu.usuario_bloqueado = false");
		while ($row3 = $result3->FetchRow()){		
			$usuarios[]=  array("usuario_cod"=>$row3['usuario_cod'],"nombres"=>utf8_encode($row3['usuario_nombres'].' '.$row3['usuario_priape'].' '.$row3['usuario_segape']));
		}

		$salida.='{ "id_workflow": "'.$row2['id_workflow'].'", "desc_actividad": "'.$row2['desc_actividad'].'", "usuarios": '.json_encode($usuarios).' }, ';
	}
}

if($opcion=='CartaRespuesta'){

	$result=queryQR("SELECT *,t.id_tipotramite as tptp FROM wf_radicacion AS r 
		INNER JOIN wf_tipologia AS t ON t.id_tipologia = r.id_tipologia
		WHERE id_radicacion=$qAjax");

	$row = $result->FetchRow();

	$plantilla = "2";
	if ($row['tptp'] == 3)
		$plantilla = "1";

	if($row['id_compania'] == "4"){
		$qryInfo = queryQR("SELECT * FROM wf_radarl WHERE id_radicacion = '".$row['id_radicacion']."'");
		$infoExt = $qryInfo->FetchRow();
		$plantilla = "2";
	}

	if($row['carta_respuesta'] != null){
		$Carta= $row['carta_respuesta'];
		//$Carta="alex";
	}else{
		$result=queryQR("SELECT * FROM wf_plantillas WHERE id_plantilla = ".$plantilla);
		$row = $result->FetchRow();
		$Carta= $row['desc_plantilla'];

		//$result=queryQR("select *, ciu.ciudad as ciudadclie, rad.descripcion as descqueja from wf_radicacion rad, tblciudades ciu, wf_producto pro where pro.id_producto=rad.id_producto and ciu.idciudad=rad.id_ciudad and rad.id_radicacion=$qAjax");
		$result=queryQR("SELECT *, ciu.ciudad AS ciudadclie, rad.descripcion AS descqueja 
						 FROM wf_radicacion AS rad
						 INNER JOIN tblciudades AS ciu ON ciu.idciudad = rad.id_ciudad
						 INNER JOIN wf_producto AS pro ON pro.id_producto = rad.id_producto
						 WHERE rad.id_radicacion=$qAjax");

		$row = $result->FetchRow();
		
		setlocale(LC_ALL,"es_ES");
		$Carta = str_replace ("{id_radicado}", $row['id_radicacion'], $Carta);
		$Carta = str_replace ("{fecha_espanol}",  strftime("%A %d de %B del %Y"), $Carta);
		$Carta = str_replace ("{direccion_cliente}",  $row['direccion'], $Carta);
		$Carta = str_replace ("{telefono}",  $row['telefono'], $Carta);
		$Carta = str_replace ("{nombre_cliente}",  $row['nombre'], $Carta);
		$Carta = str_replace ("{numero_doc}",  $row['numero_doc'], $Carta);
		$Carta = str_replace ("{tipo_doc}",  $row['tipo_doc'], $Carta);
		$Carta = str_replace ("{ciudad_cliente}",  $row['ciudadclie'], $Carta);
		$Carta = str_replace ("{no_poliza}",  (($row['poliza'] != null)?"Poliza: ".$row['poliza']:""), $Carta);
		$Carta = str_replace ("{asegurado_poliza}",  "Asegurado: ".$row['nombreasegurado'], $Carta);
		$Carta = str_replace ("{descripcion_queja}",  $row['descqueja'], $Carta);
		$Carta = str_replace ("{fecha_espanol_radicacion}",  strftime("%A %d de %B del %Y", strtotime($row['fechahora'])), $Carta);
	}
	$Carta=str_replace('"', "'", $Carta);
	$salida.='{ "termino": "'.str_replace (array("\r", "\r\n", "\n"), " ", $Carta).'"}, ';

}

if($opcion=='MedioRespuesta'){
	
	$result=queryQR("select * from wf_radicacion rad, wf_respuesta res where rad.id_respuesta=res.id_respuesta and rad.id_radicacion=$qAjax");
	$row = $result->FetchRow();
	
	$salida.='{ "MedioRespuesta": "'.$row['desc_respuesta'].'"}, ';
}

if($opcion=='TipoServicio'){
	
	$result=queryQR("select * from wf_radicacion rad, wf_tipotramite tip where rad.id_tipotramite=tip.id_tipotramite and rad.id_radicacion=$qAjax");
	$row = $result->FetchRow();
	
	$salida.='{ "IdTipoServicio": "'.$row['id_servicio'].'"}, ';
}

if($opcion=='CambiaTipoAdj'){
	if($_REQUEST['TipoAdjunto'] == 'DocRequeridos')
		$TipoDocumento="Requerido";
		
	if($_REQUEST['TipoAdjunto'] == 'DocAdicionales')
		$TipoDocumento="Adicional";
		
	if($_REQUEST['TipoAdjunto'] == 'DocRespuesta')
		$TipoDocumento="Respuesta";
	
	$IdAdjunto= str_replace("Adj", "", $qAjax);	
	queryQR("update wf_adjuntos set tipo_adjunto='".$TipoDocumento."' where id_adjunto=$IdAdjunto");
	
}

if($opcion=='CambiaNombreAdj'){	
	$IdAdjunto= str_replace("Adj", "", $qAjax);	
	queryQR("update wf_adjuntos set desc_adjunto='".$_REQUEST['Nombre']."' where id_adjunto=$IdAdjunto");	
}

if($opcion=='MediosRespuesta'){
	$MediosRespuesta = array();
	$DatosRespuesta = array();

	$result=queryQR("select * from wf_respuesta");
	while ($row = $result->FetchRow()){
		$MediosRespuesta[]=  array("id_respuesta"=>$row['id_respuesta'],"desc_respuesta"=>utf8_encode($row['desc_respuesta']));
	}	
	
	$result=queryQR("select * from wf_radicacion where id_radicacion=$qAjax");
	$row = $result->FetchRow();
	
	if($row['id_respuesta'] == 1)
		$DatoMedio=$row['direccion'];
		
	if($row['id_respuesta'] == 2)
		$DatoMedio=$row['email'];
		
	if($row['id_respuesta'] == 3)
		$DatoMedio=$row['telefono'];
		
	
	$salida.='{ "MediosRespuesta": '.json_encode($MediosRespuesta).', "DatoSeleccionado": "'.$row['id_respuesta'].'", "DatoMedio": "'.$DatoMedio.'"}, ';
}

if($opcion=='CambiaMedioRespuesta'){

	$result=queryQR("select * from wf_radicacion where id_radicacion=$qAjax");
	$row = $result->FetchRow();
	
	if($_REQUEST['DatoSeleccionado'] == 1)
		$DatoMedio=$row['direccion'];
		
	if($_REQUEST['DatoSeleccionado'] == 2)
		$DatoMedio=$row['email'];
		
	if($_REQUEST['DatoSeleccionado'] == 3)
		$DatoMedio=$row['telefono'];
		
	$salida.='{ "DatoMedio": "'.$DatoMedio.'"}, ';
}

if($opcion=='InformacionProducto'){
	
	$result=queryQR("select *, (case when now() BETWEEN iniciotecnico AND fintecnico then 'Vigente' else 'Cancelada' end) as estado from wf_producto where id_producto=$qAjax");
	$row = $result->FetchRow();
	
	$salida.='{ "NoPoliza": "'.$row['poliza'].
			 '", "FechaInicio": "'.$row['iniciotecnico'].
			 '","FechaCancelacion": "'.$row['fintecnico'].
			 '","NombreProducto": "'.$row['descripcion'].
			 '","Radicada": "'.$row['radicada'].
			 '","Tomador": "'.$row['nombretomador'].
			 '","Asegurado": "'.$row['nombreasegurado'].
			 '","Beneficiario": "'.$row['nombrebeneficiario'].
			 '","Intermediario": "'.$row['nombreintermediario'].
			 '","Estado": "'.$row['estado'].'"}, ';
}

if($opcion=='CausalesCierre'){	
	$result=queryQR("select * from wf_causalcierre where id_causalcierre!=0");
	
	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_causalcierre"].'", "value": "'.$row["desc_causalcierre"].'" }, ';
	}
}

if($opcion=='CausalesSuspender'){	
	$result=queryQR("select * from wf_causalsuspender where id_causal_suspender!=0");
	
	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_causal_suspender"].'", "value": "'.$row["desc_causal_suspender"].'" }, ';
	}
}

if($opcion=='CausalesAnular'){	
	$result=queryQR("select * from wf_causalanular where id_causalanular!=0");
	
	while ($row = $result->FetchRow()){
		$salida.='{ "id": "'.$row["id_causalanular"].'", "value": "'.$row["desc_causalanular"].'" }, ';
	}
}

if($opcion=='ReAbrirTramite'){
	$actividades = array();
	$causales = array();

	//Reabre tramite para ente de control
	$result3=queryQR("select his.id_workflow, act.desc_actividad, his.usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' 
				|| COALESCE(usuario_segape,'')) as nombres

		from wf_historial his, wf_workflow wor, wf_actividad act, wf_tipologia tip, wf_radicacion rad, adm_usuario usu 
		where 
					his.id_radicacion=".$_REQUEST['Tramite']." 
					and tip.id_tipotramite = 3 and act.id_actividad = 4 
					and rad.estado='Cerrado' and his.usuario_cod = usu.usuario_cod
					and his.id_workflow=wor.id_workflow and wor.id_actividad=act.id_actividad and 
					rad.id_radicacion=his.id_radicacion and
					rad.id_tipologia = tip.id_tipologia
					order by id_historial desc");
							
	if ($row3 = $result3->FetchRow()){	

		$usuariosFlujo = array();
		$usuariosFlujo[]=  array("usuario_cod"=>$row3['usuario_cod'],"usuario"=>utf8_encode($row3['nombres']));
		$actividades[]=  array("id_workflow"=>$row3['id_workflow'],"actividad"=>utf8_encode($row3['desc_actividad']), 
			"usuarios"=>($usuariosFlujo));


	}else{	// Reabre resto de tramite con las vaildaciones preestablecidas

		$result=queryQR("select * from wf_radicacion where id_radicacion=".$_REQUEST['Tramite']);
		$row = $result->FetchRow();
		
		if($row['estado'] == 'Cerrado'){
			$result2=queryQR("select * from wf_historial his, wf_radicacion rad where his.id_radicacion=rad.id_radicacion and his.id_radicacion=".$_REQUEST['Tramite']." 
							 and his.actividad!='Radicar' and his.actividad!='Suspendido' and his.actividad!='Re-abierto' and his.actividad!='Cerrado' and his.fechahora is not null order by his.id_historial asc");
								
			while ($row2 = $result2->FetchRow()){		
			
				$usuarios=queryQR("select usu.usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' 
				|| COALESCE(usuario_segape,'')) as nombres from wf_workflowusuarios wu join adm_usuario usu USING(usuario_cod) 
				where wu.id_workflow='".$row2['id_workflow']."'");

				$usuariosFlujo = array();
				while ($usuario = $usuarios->FetchRow()){	
					$usuariosFlujo[]=  array("usuario_cod"=>$usuario['usuario_cod'],"usuario"=>utf8_encode($usuario['nombres']));
				}

				$actividades[]=  array("id_workflow"=>$row2['id_workflow'],"actividad"=>utf8_encode($row2['actividad']), "usuarios"=>($usuariosFlujo));
			}
		}
		
		$result2=queryQR("select his.id_workflow, act.desc_actividad from wf_historial his, wf_workflow wor, wf_actividad act where 
						his.id_workflow=wor.id_workflow and wor.id_actividad=act.id_actividad and not exists (select * from wf_historial where id_radicacion=".$_REQUEST['Tramite']." and fechahora is null) and 
						id_radicacion=".$_REQUEST['Tramite']." order by id_historial desc");
								
		if ($row2 = $result2->FetchRow()){	
			$usuarios=queryQR("select usu.usuario_cod, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres from wf_workflowusuarios wu join adm_usuario usu USING(usuario_cod) 
				where wu.id_workflow='".$row2['id_workflow']."'");

			$usuariosFlujo = array();
			while ($usuario = $usuarios->FetchRow()){	
				$usuariosFlujo[]=  array("usuario_cod"=>$usuario['usuario_cod'],"usuario"=>utf8_encode($usuario['nombres']));
			}

			$actividades[]=  array("id_workflow"=>$row2['id_workflow'],"actividad"=>utf8_encode($row2['desc_actividad']), "usuarios"=>($usuariosFlujo));
		}

	}

		$result3=queryQR("select id_tipotramite,desc_tipotramite from wf_tipotramite tpt where 
						id_servicio = 1 ");
		$marcaedtd = array();
		while ($marcaed = $result3->FetchRow()){	
			$marcaedtd[]=  array("idd"=>$marcaed['id_tipotramite'],"desc"=>utf8_encode($marcaed['desc_tipotramite']));
		}

	//$salida.='{ "actividades": '.json_encode($actividades).', "estado": "'.$row['estado'].'"}, ';
	$salida.='{ "actividades": '.json_encode($actividades).', "marcaed": '.json_encode($marcaedtd).', "estado": "'.$row['estado'].'"}, ';


}

if($opcion=='InformeSuper'){
	$Informe = array();
	$Tipologias = array();
	$Totales = array();
		
	$result=queryQR("select * from wf_clasificacion_producto where id_compania=".$_REQUEST['id_compania']);
	while ($row = $result->FetchRow()){		
		$Tipologias = array();
		$PendienteAnt_Subtotal=0;
		$PendienteAhora_Subtotal=0;
		$PendienteTot_Subtotal=0;
		$FavorCliente_Subtotal=0;
		$FavorCompania_Subtotal=0;
		$Concluidos_Subtotal=0;
		$EnTramite_Subtotal=0;
		
		$result2=queryQR("select * from wf_superfinanciera where id_clasificacion=".$row['id_clasificacion']." order by cod_super");
		while ($row2 = $result2->FetchRow()){				
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and(rad.estado='En tramite' or rad.estado='Re-abierto' or rad.estado='Suspendido') 
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora < '".$_REQUEST['Desde']."' 
						and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$PendienteAnt=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and(rad.estado='En tramite' or rad.estado='Re-abierto' or rad.estado='Suspendido' or rad.estado='Cerrado') 
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora BETWEEN '".$_REQUEST['Desde']."' 
						and '".$_REQUEST['Hasta']."' and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$PendienteAhora=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and(rad.estado='En tramite' or rad.estado='Re-abierto' or rad.estado='Suspendido') 
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora < '".$_REQUEST['Hasta']."'
						and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$PendienteTot=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and rad.estado='Cerrado' and respuesta_favor='Cliente'
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora BETWEEN '".$_REQUEST['Desde']."' and 
						'".$_REQUEST['Hasta']."' and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$FavorCliente=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and rad.estado='Cerrado' and respuesta_favor='Compañia'
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora BETWEEN '".$_REQUEST['Desde']."' and 
						'".$_REQUEST['Hasta']."' and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$FavorCompania=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and rad.estado='Cerrado'
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora BETWEEN '".$_REQUEST['Desde']."' and 
						'".$_REQUEST['Hasta']."' and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$Concluidos=$row3[0];
			
			$result3=queryQR("select count(*) from wf_radicacion rad, wf_tipologia tip, wf_producto pro where pro.id_producto=rad.id_producto and 
						rad.id_tipologia=tip.id_tipologia and (rad.estado='En tramite' or rad.estado='Re-abierto' or rad.estado='Suspendido') 
						and tip.codigo_entidad='".$row2['cod_super']."' and pro.id_clasificacion='".$row['id_clasificacion']."' and rad.fechahora BETWEEN '".$_REQUEST['Desde']."' and 
						'".$_REQUEST['Hasta']."' and id_tipotramite=".$_REQUEST['TipoTramite']);
			$row3 = $result3->FetchRow();
			$EnTramite=$row3[0];
			
			$Tipologias[] = array("codigo_entidad"=>$row2['cod_super'],"desc_tipologia"=>utf8_encode($row2['desc_super']), "PendienteAnt"=>$PendienteAnt, 
								  "PendienteAhora"=>$PendienteAhora, "PendienteTot"=>$PendienteTot, "FavorCliente"=>$FavorCliente, "FavorCompania"=>$FavorCompania,
								  "Concluidos"=>$Concluidos, "EnTramite"=>$EnTramite);
			
			$PendienteAnt_Subtotal+=$PendienteAnt;
			$PendienteAhora_Subtotal+=$PendienteAhora;
			$PendienteTot_Subtotal+=$PendienteTot;
			$FavorCliente_Subtotal+=$FavorCliente;
			$FavorCompania_Subtotal+=$FavorCompania;
			$Concluidos_Subtotal+=$Concluidos;
			$EnTramite_Subtotal+=$EnTramite;
		}
		
		
		$Tipologias[] = array("codigo_entidad"=>"<b>999</b>","desc_tipologia"=>"<b>Subtotal</b>", "PendienteAnt"=>"<b>".$PendienteAnt_Subtotal."</b>", "PendienteAhora"=>"<b>".$PendienteAhora_Subtotal.
							  "</b>", "PendienteTot"=>"<b>".$PendienteTot_Subtotal."</b>", "FavorCliente"=>"<b>".$FavorCliente_Subtotal."</b>", "FavorCompania"=>"<b>".$FavorCompania_Subtotal."</b>",
							  "Concluidos"=>"<b>".$Concluidos_Subtotal."</b>", "EnTramite"=>"<b>".$EnTramite_Subtotal."</b>");
		
		$Informe[]=  array("id_clasificacion"=>$row['id_clasificacion'],"desc_clasificacion"=>utf8_encode($row['desc_clasificacion']), "unidad_captura"=>$row['unidad_captura'], "tipologias"=>$Tipologias);
		
		$PendienteAnt_Total+=$PendienteAnt_Subtotal;
		$PendienteAhora_Total+=$PendienteAhora_Subtotal;
		$PendienteTot_Total+=$PendienteTot_Subtotal;
		$FavorCliente_Total+=$FavorCliente_Subtotal;
		$FavorCompania_Total+=$FavorCompania_Subtotal;
		$Concluidos_Total+=$Concluidos_Subtotal;
		$EnTramite_Total+=$EnTramite_Subtotal;
	}
	
	$Totales[] = array("codigo_entidad"=>"<b>999</b>","desc_tipologia"=>"<b>Total</b>", "PendienteAnt"=>"<b>".$PendienteAnt_Total."</b>", "PendienteAhora"=>"<b>".$PendienteAhora_Total."</b>", 
					   "PendienteTot"=>"<b>".$PendienteTot_Total."</b>", "FavorCliente"=>"<b>".$FavorCliente_Total."</b>", "FavorCompania"=>"<b>".$FavorCompania_Total."</b>",
					   "Concluidos"=>"<b>".$Concluidos_Total."</b>", "EnTramite"=>"<b>".$EnTramite_Total."</b>");
	
	$salida.='{ "Informe": '.json_encode($Informe).',  "Totales": '.json_encode($Totales).'}, ';
}

if($opcion=='getCompanias'){
	$compania = array();
	$result=queryQR("select * from wf_compania where id_compania!=0");
	while($row = $result->FetchRow()){
		$compania[]=$row;
	}
	$salida.='{ "companias": '.json_encode( $compania , true).'}, ';
}

if($opcion=='getEncuesta'){
	$encuesta = array();
	$result=queryQR("select * from wf_encuesta where id_radicacion='$qAjax'");
	while($row = $result->FetchRow()){
		$encuesta[]= array("pregunta"=>utf8_encode($row['pregunta']), "respuesta"=>utf8_encode($row['respuesta']));
	}
	$salida.='{ "encuesta": '.json_encode( $encuesta , true).'}, ';
}

if(strlen( $salida )>0)
	$salida=substr( $salida ,0,strlen( $salida )-2);
?>

<?=utf8_encode("[ $salida ]")?>

