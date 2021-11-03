<?php
require_once ('../config/conexion.php');

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  
header ("Content-type: application/x-msexcel");  

if($_REQUEST['consulta'] == 'ConsultaTramitesSeg')
	header ("Content-Disposition: attachment; filename=Informeseguimiento.xls" );
else
	header ("Content-Disposition: attachment; filename=Informeespecifico.xls" );

echo "<table border='1'>";
$semaforoTramite="(case when (rad.estado!='Cerrado') 
						then( case when (EXTRACT(EPOCH FROM(rad.fechahora_limite-now()))/3600)>(tit.tiempo_tipotramite/2) 
							then '1' 
							ELSE(case when (EXTRACT(EPOCH FROM(rad.fechahora_limite-now()))/3600)>0 
								then '2' 
								else '3' 
							end) 
						end ) 
					else '0' end)";
					
$semaforoActividad="( case when (EXTRACT(EPOCH FROM(his.fechahora_limite-now()))/3600)>(his.tiempo_actividad/2) 
						then '1' 
							ELSE(case when (EXTRACT(EPOCH FROM(his.fechahora_limite-now()))/3600)>0 
								then '2' 
								else '3' 
							end) 
						end )";

	$Filtros="";
		
	if($_REQUEST['FiltroTramite'] != null)
		$Filtros.="and rad.id_radicacion = '".$_REQUEST['FiltroTramite']."'";
		
	if($_REQUEST['FiltroPreasignado'] != null)
		$Filtros.="and rad.preasignado = '".$_REQUEST['FiltroPreasignado']."'";
		
	if($_REQUEST['FiltroEstado'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroEstado']);
		
		foreach ($items as $valor) {
			$consulta .= "rad.estado = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroSemaforoTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroSemaforoTramite']);
		
		foreach ($items as $valor) {
			$consulta .= "$semaforoTramite = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroAgenciaTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroAgenciaTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_agencia = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroAgenciaReclamante'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroAgenciaReclamante']);
		
		 foreach ($items as $valor) {
			$consulta .= "rad.id_agencia = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroDesdeFecReal'] != null || $_REQUEST['FiltroHastaFecReal'] != null)
		$Fecha="and to_char(rad.fechareal,'yyyyMMdd')='".$_REQUEST['FiltroDesdeFecReal'].$_REQUEST['FiltroHastaFecReal']."'";
				
	if($_REQUEST['FiltroDesdeFecReal'] != null && $_REQUEST['FiltroHastaFecReal'] != null)
		$Fecha="and rad.fechareal between '".$_REQUEST['FiltroDesdeFecReal'] ."' and '".$_REQUEST['FiltroHastaFecReal'] ."'";
		
	$Filtros.=$Fecha;
	$Fecha="";
	
	if($_REQUEST['FiltroDesdeSistema'] != null || $_REQUEST['FiltroHastaSistema'] != null)
		$Fecha="and to_char(rad.fechahora,'yyyyMMdd')='".$_REQUEST['FiltroDesdeSistema'].$_REQUEST['FiltroHastaSistema']."'";
				
	if($_REQUEST['FiltroDesdeSistema'] != null && $_REQUEST['FiltroHastaSistema'] != null)
		$Fecha="and rad.fechahora between '".$_REQUEST['FiltroDesdeSistema'] ."' and (date '".$_REQUEST['FiltroHastaSistema'] ."'+ interval '1 day')";
		
	$Filtros.=$Fecha;
	$Fecha="";
	
	if($_REQUEST['FiltroDesdeLimite'] != null || $_REQUEST['FiltroHastaLimite'] != null)
		$Fecha="and to_char(rad.fechahora_limite,'yyyyMMdd')='".$_REQUEST['FiltroDesdeLimite'].$_REQUEST['FiltroHastaLimite']."'";
				
	if($_REQUEST['FiltroDesdeLimite'] != null && $_REQUEST['FiltroHastaLimite'] != null)
		$Fecha="and rad.fechahora_limite between '".$_REQUEST['FiltroDesdeLimite'] ."' and (date '".$_REQUEST['FiltroHastaLimite'] ."'+ interval '1 day')";
		
	$Filtros.=$Fecha;
	$Fecha="";
	
	if($_REQUEST['FiltroDesdeCierre'] != null || $_REQUEST['FiltroHastaCierre'] != null)
		$Fecha="and to_char(rad.fechahora_estado,'yyyyMMdd')='".$_REQUEST['FiltroDesdeCierre'].$_REQUEST['FiltroHastaCierre']."' and rad.estado='Cerrado'";
				
	if($_REQUEST['FiltroDesdeCierre'] != null && $_REQUEST['FiltroHastaCierre'] != null)
		$Fecha="and rad.fechahora_estado between '".$_REQUEST['FiltroDesdeCierre'] ."' and (date '".$_REQUEST['FiltroHastaCierre'] ."'+ interval '1 day') and rad.estado='Cerrado'";
		
	$Filtros.=$Fecha;
	$Fecha="";
	
	if($_REQUEST['FiltroAseguradoraTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroAseguradoraTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_compania = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroProcesoTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroProcesoTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_proceso = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroTipoTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroTipoTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tit.desc_tipotramite = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}

	if($_REQUEST['FiltroServicioTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroServicioTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_servicio= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroTipologiaTramite'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroTipologiaTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.desc_tipologia= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroNomReclamante'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroNomReclamante']);
		
		 foreach ($items as $valor) {
			$consulta .= "upper(rad.nombre)= upper('".utf8_decode($valor)."') or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroDocReclamante'] != null)
		$Filtros.="and rad.numero_doc = '".$_REQUEST['FiltroDocReclamante']."'";
		
	if($_REQUEST['FiltroCiuReclamante'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroCiuReclamante']);
		
		 foreach ($items as $valor) {
			$consulta .= "rad.id_ciudad= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroNombreProducto'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroNombreProducto']);
		
		 foreach ($items as $valor) {
			$consulta .= "lower(prd.descripcion)= lower('".utf8_decode($valor)."') or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroAseguradoraProducto'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroAseguradoraProducto']);
		
		 foreach ($items as $valor) {
			$consulta .= "prd.compania= '".utf8_decode($valor)."'  or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroPendienteActividad'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroPendienteActividad']);
		
		 foreach ($items as $valor) {
			$consulta .= "his.actividad= '".utf8_decode($valor)."'  or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroUsuarioActividad'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroUsuarioActividad']);
		
		 foreach ($items as $valor) {
			$consulta .= "his.usuario_cod= '".$valor."'  or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroPolizaProducto'] != null)
		$Filtros.="and lower(prd.poliza) = lower('".$_REQUEST['FiltroPolizaProducto']."')";
		
	if($_REQUEST['FiltroSemaforoActividad'] != null){
		$consulta="";
		$items = json_decode($_REQUEST['FiltroSemaforoActividad']);
		
		 foreach ($items as $valor) {
			$consulta .= "$semaforoActividad = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}

	if($_REQUEST['consulta'] == 'ConsultaTramitesSeg'){
		$consulta="select  rad.id_radicacion, tit.desc_tipotramite, ser.desc_servicio, com.des_compania, prd.descripcion, pro.proceso_desc,
			to_char(his.fechahora_limite,'yyyy-MM-dd HH:MI:SS AM')	";


		$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null 
			LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod ,
			

			wf_tipotramite tit, wf_proceso pro, wf_servicio ser, wf_compania com, wf_producto prd, wf_tipologia tip

			where prd.id_producto=rad.id_producto and rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and 
			ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia

			and actividad = 'Seguimiento'
			$Filtros ";

		echo "<tr><th>No TRAMITE</th><th>TIPO TRAMITE</th><th>SERVICIO</th><th>ASEGURADORA</th><th>NOMBRE PRODUCTO</th><th>PROCESO</th>
		<th>FECHA DE SEGUIMIENTO</th></tr>";
		
		$columnas=7;

	}else{
	
		$consulta="select  rad.id_radicacion, ciu.ciudad, depto.desc_departamento, tit.desc_tipotramite, ser.desc_servicio, rad.preasignado,
		(select case when count(actividad)>0 then 'SI' else '' end from wf_historial where id_radicacion = rad.id_radicacion and actividad = 'Re-abierto'),
		(select desc_tipotramite from wf_tipotramite where id_tipotramite = rad.marced),
		rad.casociado, rad.estado, to_char(rad.fechahora_estado,'yyyy-MM-dd HH:MI:SS AM'), to_char(rad.fechahora,'yyyy-MM-dd HH:MI:SS AM'),
		rad.fechareal, com.des_compania, pro.proceso_desc, prd.descripcion, rad.respuesta_favor, 
		(select proceso_desc || ' - ' || respuesta from wf_historial hhs, wf_tiporespuesta tprr, wf_proceso pr 
			where id_radicacion = rad.id_radicacion and actividad = 'Generar respuesta' and hhs.cod_respuesta = tprr.cod_respuesta and tprr.id_proceso = pr.id_proceso
			order by id_historial desc limit 1),
		(select mvv.motivo from wf_historial hhs, wf_tiporespuesta tprr, wf_motivo mvv
			where id_radicacion = rad.id_radicacion and actividad = 'Generar respuesta' and hhs.cod_respuesta = tprr.cod_respuesta
			and mvv.id_motivo = tprr.id_motivo order by id_historial desc limit 1)	";


		$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null 
		LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod  left join wf_tipocliente tpcliente on rad.id_tipocliente = tpcliente.id_tipocliente
		LEFT JOIN tblciudades ciu on ciu.idciudad=rad.id_ciu_queja
		LEFT JOIN tbldepartamentos depto on ciu.id_departamento=depto.id_departamento,

		wf_tipotramite tit, wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com, wf_producto prd, wf_recepcion rec, wf_respuesta rta, tblradofi ofi

		where ofi.codigo=rad.id_agencia and rta.id_respuesta=rad.id_respuesta and rec.id_recepcion=rad.id_recepcion and 
			prd.id_producto=rad.id_producto and rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and 
			pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia
			$Filtros ";

		echo "<tr><th>No TRAMITE</th><th>CIUDAD DEL EVENTO QUE GENERO LA QUEJA</th><th>DEPARTAMENTO</th><th>TIPO DE TRAMITE</th>
		<th>SERVICIO</th><th>No. PRE-ASIGNADO</th><th>MARCA REABIERTO</th><th>TIPO DE TRAMITE DE REAPERTURA</th><th>CASO ASOCIADO</th>
		<th>ESTADO DE TRAMITE</th><th>FECHA ESTADO</th><th>FECHA SISTEMA</th><th>FECHA REAL RADICACION</th><th>ASEGURADORA</th><th>PROCESO</th>
		<th>NOMBRE PRODUCTO</th><th>RESPUESTA FAVOR</th><th>TIPOLOGIA CIERRE</th><th>MOTIVO</th>
		</tr>";
		
		$columnas=19;
	}

$result=queryQR( $consulta . $where );

while ($row = $result->FetchRow()) {
	echo "<tr>";
	
	for( $i=0; $i<$columnas; $i++)
		echo "<td>".$row[$i]."</td>";
	echo "</tr>";	
}
echo "</table>";
?>
