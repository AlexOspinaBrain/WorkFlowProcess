<?php
require_once ('../config/conexion.php');

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  
header ("Content-type: application/x-msexcel");  
header ("Content-Disposition: attachment; filename=\"".$_REQUEST['consulta'].".xls\"" );

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

if($_REQUEST['consulta'] == 'ConsultaTramites'){	
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
	/*					
	$tiempoRestante="(case when rad.estado!='Cerrado' then cast((EXTRACT(EPOCH FROM (rad.fechahora_limite-now()))/3600) as bigint)||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',rad.fechahora_limite-now()))) || ' sec' else '' end) as tiemporestante";
	
	$consulta="select  rad.id_radicacion, rad.preasignado, rad.estado, to_char(rad.fechahora_estado,'yyyy-MM-dd HH:MI:SS AM'), to_char(rad.fechahora,'yyyy-MM-dd HH:MI:SS AM'), rad.fechareal, rad.fechahora_limite, his2.fecha_envio,$tiempoRestante, com.des_compania, pro.proceso_desc, tit.desc_tipotramite, ser.desc_servicio, rec.desc_recepcion, rta.desc_respuesta, (select descrip from tblradofi where codigo=tip.id_agencia), tip.desc_tipologia, rad.respuesta_favor, rad.descripcion, rad.tipo_doc ||' '|| rad.numero_doc, rad.nombre, rad.email, rad.telefono, rad.direccion, ciu.ciudad, ofi.descrip, prd.poliza, prd.descripcion, prd.radicada, (case when now() BETWEEN iniciotecnico AND fintecnico then 'Vigente' else 'Cancelada' end), prd.iniciotecnico, prd.fintecnico, prd.compania, prd.ciudad, prd.nittomador, prd.nombretomador, prd.nitasegurado, prd.nombreasegurado, prd.nitbeneficiario, prd.nombrebeneficiario, prd.nitintermediario, prd.nombreintermediario, actividad,(COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')), $semaforoTramite as semaforo";
	$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod left join (select id_radicacion, to_char(fechahora,'yyyy-MM-dd HH:MI:SS AM') as fecha_envio from wf_historial where actividad='Enviar respuesta' and fechahora is not null order by fechahora desc limit 1) his2 on his2.id_radicacion=rad.id_radicacion, wf_tipotramite tit,wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com, wf_producto prd, wf_recepcion rec, wf_respuesta rta, tblradofi ofi, tblciudades ciu where ciu.idciudad=rad.id_ciudad and ofi.codigo=rad.id_agencia and rta.id_respuesta=rad.id_respuesta and rec.id_recepcion=rad.id_recepcion and prd.id_producto=rad.id_producto and rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia $Filtros order by semaforo desc";
	
	echo "<tr><th colspan='19'>DATOS TRAMITE</th><th colspan='7'>DATOS RECLAMANTE</th><th colspan='16'>DATOS POLIZA</th><th colspan='2'>DATOS LINEA PROCESO</th></tr>";
	echo "<tr><th>No TRAMITE</th><th>No. PRE-ASIGNADO</th><th>ESTADO TRAMITE</th><th>ESTADO ESTADO</th><th>FECHA SISTEMA</th><th>FECHA REAL RADICACION</th><th>FECHA HORA LIMITE</th><th>FECHA HORA ENVIO RESPUESTA</th><th>TIEMPO RESTANTE</th><th>ASEGURADORA</th><th>PROCESO</th><th>TIPO TRAMITE</th><th>SERVICIO</th><th>CANAL DE RECEPCIÓN</th><th>MEDIO DE RESPUESTA</th><th>AGENCIA QUE TRAMITA</th><th>TIPOLOGIA</th><th>RESPUESTA FAVOR</th><th>DESCRIPCIÓN DE LA QUEJA</th><th>IDENTIFICACIÓN</th><th>NOMBRE</th><th>E-MAIL</th><th>TELEFONO</th><th>DIRECCIÓN</th><th>CIUDAD</th><th>AGENCIA QUE RECIBIO</th><th>POLIZA</th><th>NOMBRE PRODUCTO</th><th>AGENCIA</th><th>ESTADO</th><th>INICIO POLIZA</th><th>VENCIMIENTO POLIZA</th><th>ASEGURADORA</th><th>CIUDAD</th><th>NIT TOMADOR</th><th>NOMBRE TOMADOR</th><th>NIT ASEGURADO</th><th>NOMBRE ASEGURADO</th><th>NIT BENEFICIARIO</th><th>NOMBRE BENEFICIARIO</th><th>NIT INTERMEDIARIO</th><th>NOMBRE INTERMEDIARIO</th><th>ACTIVIDAD PENDIENTE</th><th>USUARIO ACTIVIDAD</th></tr>";
	*/
	$tiempoRestante="(case when rad.estado!='Cerrado' then cast((EXTRACT(EPOCH FROM (rad.fechahora_limite-now()))/3600) as bigint)||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',rad.fechahora_limite-now()))) || ' sec' else '' end) as tiemporestante";
	
//	(select observacion from wf_historial where id_radicacion = rad.id_radicacion and actividad = 
//		'Generar respuesta' order by id_historial desc limit 1),
	$consulta="select  rad.id_radicacion, rad.preasignado, rad.estado, to_char(rad.fechahora_estado,'yyyy-MM-dd HH:MI:SS AM'), 
	(select case when count(actividad)>0 then 'SI' else '' end from wf_historial where id_radicacion = rad.id_radicacion and actividad = 'Re-abierto'),
	rad.casociado,
	to_char(rad.fechahora,'yyyy-MM-dd HH:MI:SS AM'), 
	rad.fechareal, rad.fechahora_limite, 
	
	(select (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nomres from wf_historial, adm_usuario where id_radicacion = rad.id_radicacion and actividad = 'Generar respuesta' and adm_usuario.usuario_cod = wf_historial.usuario_cod order by id_historial desc limit 1),

	(select to_char(fechahora,'yyyy-MM-dd HH:MI:SS AM') as fecha_envio from wf_historial where id_radicacion = rad.id_radicacion and actividad = 'Enviar respuesta' order by id_historial limit 1),

	(select case when cc0 > 1 then to_char(fechahora,'yyyy-MM-dd HH:MI:SS AM') else null end from wf_historial , (select 
		count(*) as cc0 from wf_historial where id_radicacion = rad.id_radicacion and actividad = 'Enviar respuesta' ) as qq
		where id_radicacion = rad.id_radicacion and actividad = 'Enviar respuesta' order by id_historial desc limit 1),

	(select REGEXP_REPLACE(observacion ,'<br>',' ','g') from wf_historial where id_radicacion = rad.id_radicacion and actividad = 'Enviar respuesta' order by id_historial desc limit 1),


	$tiempoRestante, com.des_compania, pro.proceso_desc, tit.desc_tipotramite, ser.desc_servicio, rec.desc_recepcion, 
	rta.desc_respuesta, (select descrip from tblradofi where codigo=tip.id_agencia), tip.desc_tipologiaalterna, tip.desc_tipologia, rad.respuesta_favor, 
(select proceso_desc || ' - ' || respuesta from wf_historial hhs, wf_tiporespuesta tprr, wf_proceso pr 
		where id_radicacion = rad.id_radicacion and actividad = 'Generar respuesta' and hhs.cod_respuesta = tprr.cod_respuesta and tprr.id_proceso = pr.id_proceso
		order by id_historial desc limit 1),
	replace(replace(replace(replace(replace(replace(replace(replace(rad.descripcion,chr(10),' '),chr(11),' '),chr(13),' '),chr(27),' '),chr(32),' '),chr(39),' '),chr(9),' '),'<br>',' ') as descripcion,

	rad.tipo_doc ||' '|| rad.numero_doc, rad.nombre, rad.email, rad.telefono, rad.direccion, ciu.ciudad, depto.desc_departamento as deptocliente, tpcliente.descripcion, ofi.descrip, prd.poliza, prd.descripcion, prd.radicada, (case when now() BETWEEN iniciotecnico AND fintecnico then 'Vigente' else 'Cancelada' end), prd.iniciotecnico, prd.fintecnico, prd.compania, prd.ciudad, prd.nittomador, prd.nombretomador, prd.nitasegurado, prd.nombreasegurado, prd.nitbeneficiario, prd.nombrebeneficiario, prd.nitintermediario, prd.nombreintermediario, actividad,(COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')), 
		$semaforoTramite as semaforo";

//left join (select id_radicacion, to_char(max(fechahora),'yyyy-MM-dd HH:MI:SS AM') as fecha_envio, observacion from wf_historial where actividad='Enviar respuesta' group by id_radicacion) his2 on his2.id_radicacion=rad.id_radicacion

	$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod  left join wf_tipocliente tpcliente on rad.id_tipocliente = tpcliente.id_tipocliente, wf_tipotramite tit,wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com, wf_producto prd, wf_recepcion rec, wf_respuesta rta, tblradofi ofi, tblciudades ciu, tbldepartamentos depto

		where ciu.idciudad=rad.id_ciudad and ofi.codigo=rad.id_agencia and rta.id_respuesta=rad.id_respuesta and rec.id_recepcion=rad.id_recepcion and prd.id_producto=rad.id_producto and rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia and ciu.id_departamento=depto.id_departamento $Filtros order by semaforo desc";

	echo "<tr><th colspan='26'>DATOS TRAMITE</th><th colspan='9'>DATOS RECLAMANTE</th><th colspan='16'>DATOS POLIZA</th><th colspan='2'>DATOS LINEA PROCESO</th></tr>";
	echo "<tr><th>No TRAMITE</th><th>No. PRE-ASIGNADO</th><th>ESTADO TRAMITE</th><th>FECHA ESTADO</th><th>MARCA RE-ABIERTO</th><th>CASO ASOCIADO A</th><th>FECHA SISTEMA</th><th>FECHA REAL RADICACION</th><th>FECHA HORA LIMITE</th><th>QUIEN GENERA RESPUESTA</th><th>FECHA HORA ENVIO PRIMER RESPUESTA</th><th>FECHA HORA ENVIO ULTIMA RESPUESTA</th><th>COMENTARIO ENVIO RESPUESTA</th><th>TIEMPO RESTANTE</th><th>ASEGURADORA</th><th>PROCESO</th><th>TIPO TRAMITE</th><th>SERVICIO</th><th>CANAL DE RECEPCIÓN</th><th>MEDIO DE RESPUESTA</th><th>AGENCIA QUE TRAMITA</th>
	<th>TIPOLOGIA</th><th>SUB-TIPOLOGIA</th><th>RESPUESTA FAVOR</th><th>TIPOLOGIA CIERRE</th> <th>DESCRIPCIÓN DE LA QUEJA</th><th>IDENTIFICACIÓN</th><th>NOMBRE</th><th>E-MAIL</th><th>TELEFONO</th><th>DIRECCIÓN</th><th>CIUDAD</th><th>DEPARTAMENTO</th><th>TIPO CLIENTE</th><th>AGENCIA QUE RECIBIO</th><th>POLIZA</th><th>NOMBRE PRODUCTO</th><th>AGENCIA</th><th>ESTADO</th><th>INICIO POLIZA</th><th>VENCIMIENTO POLIZA</th><th>ASEGURADORA</th><th>CIUDAD</th><th>NIT TOMADOR</th><th>NOMBRE TOMADOR</th><th>NIT ASEGURADO</th><th>NOMBRE ASEGURADO</th><th>NIT BENEFICIARIO</th><th>NOMBRE BENEFICIARIO</th><th>NIT INTERMEDIARIO</th><th>NOMBRE INTERMEDIARIO</th><th>ACTIVIDAD PENDIENTE</th><th>USUARIO ACTIVIDAD</th></tr>";
	
	$columnas=54;
}

$result=queryQR( $consulta . $where );

while ($row = $result->FetchRow()) {
	if($row['semaforo'] == '3')
		echo "<tr style='color: red;'>";
		
	if($row['semaforo'] == '2')
		echo "<tr style='color: orange;'>";
		
	if($row['semaforo'] == '1')
		echo "<tr style='color: green;'>";
		
	if($row['semaforo'] == '0')
		echo "<tr>";
	
	for( $i=0; $i<$columnas; $i++)
		echo "<td>".$row[$i]."</td>";
	echo "</tr>";	
}
echo "</table>";
?>
