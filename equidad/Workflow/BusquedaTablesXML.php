<?php
require_once ('../config/conexion.php');

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname']:'';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder']:'';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;


$semaforoTramite="(case when (rad.estado!='Cerrado' and rad.estado!='Anulado') 
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

if($_POST['consulta'] == 'Admtipologias'){	
	if($_REQUEST['filtroCodTipologia'] != null)
		$CodTipologia="and tip.id_tipologia = '".utf8_decode($_REQUEST['filtroCodTipologia'])."'";
		
	if($_REQUEST['filtroTipologia'] != null)
		$Tipologia="and tip.desc_tipologia = '".utf8_decode($_REQUEST['filtroTipologia'])."'";
		
	if($_REQUEST['filtroCompania'] != null)
		$Compania="and com.id_compania = '".utf8_decode($_REQUEST['filtroCompania'])."'";
	
	if($_REQUEST['filtroAgencia'] != null)
		$Agencia="and age.codigo = '".utf8_decode($_REQUEST['filtroAgencia'])."'";
		
	if($_REQUEST['filtroProceso'] != null)
		$Proceso="and pro.id_proceso = '".utf8_decode($_REQUEST['filtroProceso'])."'";
		
	if($_REQUEST['filtroServicio'] != null)
		$Servicio="and ser.id_servicio = '".utf8_decode($_REQUEST['filtroServicio'])."'";

	if($_REQUEST['filtroTipotra'] != null)
		$TipoTramite="and tip.id_tipotramite = '".utf8_decode($_REQUEST['filtroTipotra'])."'";	
	else
		$TipoTramite="and tip.id_tipotramite is null";		
	
	$opcionEdita = '<img src="images/edit.png" width="15px" title="Editar" border="0" style="cursor: pointer" onClick="EditaTipologia(\'|| tip.id_tipologia||\')">';
	$opcionCopia = '<img src="images/copiar.png" width="15px" title="Copiar" border="0" style="cursor: pointer" onClick="CopiaTipologia(\'|| tip.id_tipologia||\')">';
	$consulta="select '".$opcionEdita."', '".$opcionCopia."', tip.id_tipologia,tip.desc_tipologia, com.des_compania, age.descrip, pro.proceso_desc, ser.desc_servicio, tip.codigo_entidad, tip.id_tipotramite";
	$where = " from wf_tipologia tip, wf_compania com, wf_proceso pro, wf_servicio ser, tblradofi age 
			   where tip.id_compania=com.id_compania and tip.id_proceso=pro.id_proceso and tip.id_servicio=ser.id_servicio
			   and age.codigo=tip.id_agencia $CodTipologia $Tipologia $Compania $Agencia $Proceso $Servicio 
			   $TipoTramite and eliminado_tipologia = false";
			   //and age.codigo=tip.id_agencia $CodTipologia $Tipologia $Compania $Agencia $Proceso $Servicio ";
			   
	$campoCount = "tip.id_tipologia";
}

if($_POST['consulta'] == 'BuscaProductos'){	
	/*$opcion = '<a  href="#" onClick=\"RadicaProducto(\'|| id_producto ||\', \'\'\'|| compania ||\'\'\')\">\'|| poliza ||\'</a>';
	
	if($_REQUEST['Compania'] =='1')
		$Compania='Generales';
	else
		$Compania='Vida';
	
	if($_REQUEST['Producto'] != null)
		$Producto="and poliza = '".$_REQUEST['Producto']."'";
	
	$consulta="select (case when '$Compania'=compania then '".$opcion."' else poliza end), (case when now() BETWEEN iniciotecnico AND fintecnico then '<b class=\"Activo\">Vigente</b>' else '<b class=\"Vencido\">Cancelada</b>' end) as estado,compania, descripcion, radicada, nombretomador, nombreasegurado, nombrebeneficiario ";
	$where = " from wf_producto where (nittomador='".$_REQUEST['Identificadion']."' or nitasegurado='".$_REQUEST['Identificadion']."' or nitbeneficiario='".$_REQUEST['Identificadion']."') $Producto";
	$campoCount = "poliza";*/

	$consulta = "select 
				fecren as InicioTecnico,
				fecini as InicioCertificado,
				osiris.fc_codpla(a.codpla) as Descripcion,
				poliza,
				certif, 
				orden,
				SUCUR,
				pmolano.fc_traer500(substr(a.sucur,2),'nombre') as Radicada,
				ltrim(rtrim(pmolano.fc_traer500(a.tomador,'nit'),' '),'0') as NitTomador,
				pmolano.fc_traer500(a.tomador,'nombre') as NombreTomador,
				ltrim(rtrim(pmolano.fc_traer500(a.asegurado,'nit'),' '),'0') as NitAsegurado,
				pmolano.fc_traer500(a.asegurado,'nombre') as NombreAsegurado,
				ltrim(rtrim(pmolano.fc_traer500(a.beneficiario,'nit'),' '),'0') as NitBeneficiario,
				pmolano.fc_traer500(a.beneficiario,'nombre') as NombreBeneficiario,
				ltrim(rtrim(pmolano.fc_traer500(a.agente,'nit'),' '),'0') as NitIntermediario,
				pmolano.fc_traer500(a.agente,'nombre') as NombreIntermediario,
				CODPLA AS PRODUCTO,
				pmolano.fc_traer500(substr(a.sucur,2),'nombre') as DescripcionPlan,
				fecter as FinTecnico,
				Fecter as FinCertificado,
				decode( substr(sucur,1,1),'1','Generales','Vida') as Compania,
				tipcer,
				estado,
				osiris.fc_traerdet502(a.tomador, '00000060') as cod_ciudad,
				osiris.fc_traer105('00000060', osiris.fc_traerdet502(a.tomador, '00000060')) as Ciudad,
				osiris.fc_traer105('00000065', osiris.fc_traerdet502(a.tomador, '00000065')) as Departamento,
				osiris.fc_traerdet502(a.tomador, '630') as Direccion,
				osiris.fc_traerdet502(a.tomador, '700') as Telefono,
				osiris.fc_traerdet502(a.tomador, '831') as CorreoElectronico,
				osiris.fc_traerdet502(a.beneficiario, '630') as Direccionben,
				osiris.fc_traerdet502(a.beneficiario, '700') as Telefonoben,
				osiris.fc_traerdet502(a.beneficiario, '831') as CorreoElectronicoben,
				osiris.fc_traerdet502(a.asegurado, '630') as Direccionasegu,
				osiris.fc_traerdet502(a.asegurado, '700') as Telefonoasegu,
				osiris.fc_traerdet502(a.asegurado, '831') as CorreoElectronicoasegu
				FROM OSIRIS.S03020 A";
   	$where = "where a.asegurado='000052553339'";
   	$campoCount = "poliza";
}

if($_POST['consulta'] == 'ClasificacionProductos'){		
	$consulta="select DISTINCT(descripcion) as producto, (select desc_clasificacion || ' - ' || com.des_compania from wf_clasificacion_producto pr, wf_compania com where com.id_compania=pr.id_compania and id_clasificacion=pro.id_clasificacion)  as clasificacion";
	$where = " from wf_producto pro where id_producto !=0";
	$campoCount = "producto";
}

if($_POST['consulta'] == 'InformeOportunidad'){		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and to_char(rad.fechahora,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and rad.fechahora between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";



	if($_REQUEST['TipoInforme'] == 'Proceso')
		$consulta="select *, 
				case when total!=0 then  round(CAST(total-sin_solucion  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end ,
				case when total!=0 then  round(CAST(in_estandar  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end 
				from (
					select pro.proceso_desc as informe,
					count(case when rad.estado!='Anulado' $Fecha then 1 else NULL end) as total, 
					count(case when rad.estado='Cerrado' and fechahora_estado <= fechahora_limite $Fecha then 1 else NULL end) as in_estandar,
					count(case when rad.estado='Cerrado' and fechahora_estado > fechahora_limite $Fecha then 1 else NULL end) as out_estandar,
					count(case when rad.estado!='Cerrado' $Fecha then 1 else NULL end) as sin_solucion
					from  wf_proceso pro
					JOIN  wf_tipologia tip  USING(id_proceso) 
					LEFT JOIN  wf_radicacion rad  USING(id_tipologia)
					GROUP BY pro.id_proceso, informe order by informe) 
				as result";

	if($_REQUEST['TipoInforme'] == 'Respuestas'){
		if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
			$Fechaa=" to_char(rad.fechahora,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
		if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
			$Fechaa=" rad.fechahora between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";

		if($_REQUEST['Desde'] == null && $_REQUEST['Hasta'] == null)
			$Fechaa=" rad.fechahora >= to_timestamp(to_char(now(),'yyyymmdd'),'yyyymmdd') + '-30 day' " ;

			$precqq="select proceso_desc, estado,
       		case when extract(day from (to_timestamp(to_char(bb.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd') - to_timestamp(to_char(dd.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd')))-
                (select count(*) from wf_festivo where festivo between
            	    dd.fecha_inicio and bb.fecha_inicio) <= 4 then 1 else 0 end as estandar,
       		extract(day from (to_timestamp(to_char(bb.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd') - to_timestamp(to_char(dd.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd')))-
                (select count(*) from wf_festivo where festivo between
                	dd.fecha_inicio and bb.fecha_inicio)  dias,
       		dd.fecha_inicio clas, bb.fecha_inicio genr, rad.id_radicacion
			into temp table tmp_informe
			from (((((wf_radicacion rad inner join wf_historial his using (id_radicacion))
					left join wf_tipologia using (id_tipologia))
					left join wf_proceso using (id_proceso))
					left join (select id_radicacion, min(fechahora) as fecha_inicio from wf_historial where actividad = 'Clasificar' 
						group by id_radicacion) as dd on his.id_radicacion = dd.id_radicacion) 
					left join (select id_radicacion, min(fechahora) as fecha_inicio from wf_historial where actividad in ('Generar respuesta','Cerrado' )
						group by id_radicacion) as bb on his.id_radicacion = bb.id_radicacion) 

			where $Fechaa and rad.estado!='Anulado' and his.actividad = 'Radicar'";



		$consulta="select distinct proceso_desc as informe,
			(select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc ) as total,
			(select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc and estandar = 1) as in_estandar,
			(select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc and estandar = 0) as out_estandar,
			(select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc and estado <> 'Cerrado' ) as sin_solucion,
			round(CAST((select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc )-(select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc and estado <> 'Cerrado' )  as real) /CAST( (select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc ) as real)*100)||' %',
			round(CAST((select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc and estandar = 1)  as real) /CAST( (select count(*) from tmp_informe where proceso_desc=tmpinf.proceso_desc ) as real)*100)||' %'

			from tmp_informe tmpinf ";
	}
	if($_REQUEST['TipoInforme'] == 'Agencia')
		$consulta="select *, 
				case when total!=0 then  round(CAST(total-sin_solucion  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end ,
				case when total!=0 then  round(CAST(in_estandar  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end 
				from (
					select ofi.descrip as informe,
					count(case when rad.estado!='Anulado' $Fecha then 1 else NULL end) as total, 
					count(case when rad.estado='Cerrado' and fechahora_estado <= fechahora_limite $Fecha then 1 else NULL end) as in_estandar,
					count(case when rad.estado='Cerrado' and fechahora_estado > fechahora_limite $Fecha then 1 else NULL end) as out_estandar,
					count(case when rad.estado!='Cerrado' $Fecha then 1 else NULL end) as sin_solucion
					from  tblradofi ofi 
					JOIN  wf_tipologia tip  ON ofi.codigo=tip.id_agencia 
					LEFT JOIN  wf_radicacion rad  USING(id_tipologia)
					GROUP BY ofi.descrip, informe order by informe) 
				as result";

	if($_REQUEST['TipoInforme'] == 'Compania'){


		if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
			$Fechaa=" to_char(rad.fechahora,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
		if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
			$Fechaa=" rad.fechahora between '".$_REQUEST['Desde'] ."' and (date '".$_REQUEST['Hasta'] ."'+ interval '1 day')";

		if($_REQUEST['Desde'] == null && $_REQUEST['Hasta'] == null)
			$Fechaa=" rad.fechahora >= to_timestamp(to_char(now(),'yyyymmdd'),'yyyymmdd') + '-30 day' " ;

		$precqq="select des_compania, estado,
		       case when extract(day from (to_timestamp(to_char(bb.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd') - to_timestamp(to_char(dd.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd')))-
		                (select count(*) from wf_festivo where festivo between
		                dd.fecha_inicio and bb.fecha_inicio) <= 8 then 1 else 0 end as estandar,

		       extract(day from (to_timestamp(to_char(bb.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd') - to_timestamp(to_char(dd.fecha_inicio,'yyyy-mm-dd'),'yyyy-mm-dd')))-
		                (select count(*) from wf_festivo where festivo between
		                dd.fecha_inicio and bb.fecha_inicio)  dias,
		       dd.fecha_inicio clas, bb.fecha_inicio genr, rad.id_radicacion
		into temp table tmp_informe
		from (((((wf_radicacion rad inner join wf_historial his using (id_radicacion))
				left join wf_tipologia using (id_tipologia))
				left join wf_compania using (id_compania))
				left join (select id_radicacion, min(fechahora) as fecha_inicio from wf_historial where actividad = 'Radicar' 
					group by id_radicacion) as dd on his.id_radicacion = dd.id_radicacion) 
				left join (select id_radicacion, min(fechahora) as fecha_inicio from wf_historial where actividad in ('Generar respuesta','Cerrado' )
					group by id_radicacion) as bb on his.id_radicacion = bb.id_radicacion) 
		where $Fechaa and rad.estado!='Anulado' and his.actividad = 'Radicar'";

		$consulta="select distinct des_compania as informe,
			(select count(*) from tmp_informe where des_compania=tmpinf.des_compania ) as total,
			(select count(*) from tmp_informe where des_compania=tmpinf.des_compania and estandar = 1) as in_estandar,
			(select count(*) from tmp_informe where des_compania=tmpinf.des_compania and estandar = 0) as out_estandar,
			(select count(*) from tmp_informe where des_compania=tmpinf.des_compania and estado <> 'Cerrado' ) as sin_solucion,
			round(CAST((select count(*) from tmp_informe where des_compania=tmpinf.des_compania )-(select count(*) from tmp_informe where des_compania=tmpinf.des_compania and estado <> 'Cerrado' )  as real) /CAST( (select count(*) from tmp_informe where des_compania=tmpinf.des_compania ) as real)*100)||' %',
			round(CAST((select count(*) from tmp_informe where des_compania=tmpinf.des_compania and estandar = 1)  as real) /CAST( (select count(*) from tmp_informe where des_compania=tmpinf.des_compania ) as real)*100)||' %'

			from tmp_informe tmpinf ";


		/*$consulta="select *, 
				case when total!=0 then  round(CAST(total-sin_solucion  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end ,
				case when total!=0 then  round(CAST(in_estandar  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end 
				from (
					select com.des_compania as informe,
					count(case when rad.estado!='Anulado' $Fecha then 1 else NULL end) as total, 
					count(case when rad.estado='Cerrado' and fechahora_estado <= fechahora_limite $Fecha then 1 else NULL end) as in_estandar,
					count(case when rad.estado='Cerrado' and fechahora_estado > fechahora_limite $Fecha then 1 else NULL end) as out_estandar,
					count(case when rad.estado!='Cerrado' $Fecha then 1 else NULL end) as sin_solucion
					from  wf_compania com 
					JOIN  wf_tipologia tip USING(id_compania)
					LEFT JOIN  wf_radicacion rad  USING(id_tipologia)
					GROUP BY com.des_compania, informe order by informe) 
				as result";*/
		}
	if($_REQUEST['TipoInforme'] == 'Tipologia')
		$consulta="select *, 
				case when total!=0 then  round(CAST(total-sin_solucion  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end ,
				case when total!=0 then  round(CAST(in_estandar  as real) /CAST( total as real)*100)||' %' else 'Sin datos' end 
				from (
					select tip.desc_tipologia as informe,
					count(case when rad.estado!='Anulado' $Fecha then 1 else NULL end) as total, 
					count(case when rad.estado='Cerrado' and fechahora_estado <= fechahora_limite $Fecha then 1 else NULL end) as in_estandar,
					count(case when rad.estado='Cerrado' and fechahora_estado > fechahora_limite $Fecha then 1 else NULL end) as out_estandar,
					count(case when rad.estado!='Cerrado' $Fecha then 1 else NULL end) as sin_solucion
					from  wf_tipologia tip 
					LEFT JOIN  wf_radicacion rad  USING(id_tipologia)
					GROUP BY tip.desc_tipologia, informe order by informe) 
				as result";

}

if($_POST['consulta'] == 'BuscaServiciosAnteriores'){	
	$opcion = '<a  href="#" onClick="MuestraTramite(\'|| rad.id_radicacion ||\')">\'|| rad.id_radicacion ||\'</a>';
	$tiempoRestante="(case when (rad.estado='En tramite' or rad.estado='Re-abierto') then (cast((EXTRACT(day FROM (rad.fechahora_limite-now()))) as bigint)||' dias '|| abs(date_part('hours',rad.fechahora_limite-now()))||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min ') else '' end)as tiemporestante";
	
	$consulta="select  '".$opcion."', rad.estado, ser.desc_servicio, com.des_compania,tip.desc_tipologia, rad.fechareal, to_char(rad.fechahora,'yyyy-MM-dd HH:MI:SS AM') as fechahor, $tiempoRestante, $semaforoTramite as semaforo";
	$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod, wf_tipotramite tit,wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com where rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia and rad.numero_doc='".$_REQUEST['codigo']."'";
	$campoCount = "rad.id_radicacion";
}

if($_POST['consulta'] == 'TramitesPendientes'){	


	$opcion = '<a  href="#" onClick="MuestraTramite(\'|| rad.id_radicacion ||\')">\'|| rad.id_radicacion ||\'</a>';
	$tiempoRestante="cast((EXTRACT(EPOCH FROM (his.fechahora_limite-now()))/3600) as bigint)||' hrs '|| abs(date_part('minutes',his.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',his.fechahora_limite-now()))) || ' sec' as tiemporestante";
	
	$consulta="select '".$opcion."', his.fechahora_limite,(case when his.actividad='Suspendido' then his.actividad else act.desc_actividad end) as actividad, $tiempoRestante, rad.nombre, pro.proceso_desc, ser.desc_servicio, com.des_compania, tit.desc_tipotramite, $semaforoActividad as semaforo  ";
	$where = " from wf_radicacion rad, wf_historial his, wf_workflow wor, wf_actividad act, wf_tiemposactividad tac, wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com, wf_tipotramite tit where tit.id_tipotramite=rad.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and tip.id_tipologia=wor.id_tipologia and tac.id_tipotramite=rad.id_tipotramite and tac.id_actividad=act.id_actividad and his.id_workflow=wor.id_workflow and wor.id_actividad=act.id_actividad and rad.id_radicacion=his.id_radicacion and his.fechahora is null and his.usuario_cod='".$_POST['usuario']."'";
	$campoCount = "rad.id_radicacion";
}

if($_POST['consulta'] == 'ConsultaTramites'){	
	$Filtros="";
		
	if($_REQUEST['FiltroTramite'] != null)
		$Filtros.="and rad.id_radicacion = '".$_REQUEST['FiltroTramite']."'";
		
	if($_REQUEST['FiltroPreasignado'] != null)
		$Filtros.="and rad.preasignado = '".$_REQUEST['FiltroPreasignado']."'";
		
	if($_REQUEST['FiltroEstado'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroEstado']);
		
		 foreach ($items as $valor) {
			$consulta .= "rad.estado = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroSemaforoTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroSemaforoTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "$semaforoTramite = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroAgenciaTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroAgenciaTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_agencia = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroAgenciaReclamante'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroAgenciaReclamante']);
		
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
		$Fecha="and to_char(rad.fechahora_estado,'yyyyMMdd')='".$_REQUEST['FiltroDesdeCierre'].$_REQUEST['FiltroHastaCierre']."' and  rad.estado='Cerrado'";
				
	if($_REQUEST['FiltroDesdeCierre'] != null && $_REQUEST['FiltroHastaCierre'] != null)
		$Fecha="and rad.fechahora_estado between '".$_REQUEST['FiltroDesdeCierre'] ."' and (date '".$_REQUEST['FiltroHastaCierre'] ."'+ interval '1 day') and  rad.estado='Cerrado'";
		
	$Filtros.=$Fecha;
	$Fecha="";
	
	if($_REQUEST['FiltroAseguradoraTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroAseguradoraTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_compania = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroProcesoTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroProcesoTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_proceso = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroTipoTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroTipoTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tit.desc_tipotramite = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}

	if($_REQUEST['FiltroServicioTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroServicioTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.id_servicio= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroTipologiaTramite'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroTipologiaTramite']);
		
		 foreach ($items as $valor) {
			$consulta .= "tip.desc_tipologia= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroNomReclamante'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroNomReclamante']);
		
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
		$items = json_decode($_POST['FiltroCiuReclamante']);
		
		 foreach ($items as $valor) {
			$consulta .= "rad.id_ciudad= '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroNombreProducto'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroNombreProducto']);
		
		 foreach ($items as $valor) {
			$consulta .= "lower(prd.descripcion)= lower('".utf8_decode($valor)."') or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroAseguradoraProducto'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroAseguradoraProducto']);
		
		 foreach ($items as $valor) {
			$consulta .= "prd.compania= '".utf8_decode($valor)."'  or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
		
	if($_REQUEST['FiltroPendienteActividad'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroPendienteActividad']);
		
		 foreach ($items as $valor) {
			$consulta .= "his.actividad= '".utf8_decode($valor)."'  or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	
	if($_REQUEST['FiltroUsuarioActividad'] != null){
		$consulta="";
		$items = json_decode($_POST['FiltroUsuarioActividad']);
		
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
		$items = json_decode($_POST['FiltroSemaforoActividad']);
		
		 foreach ($items as $valor) {
			$consulta .= "$semaforoActividad = '".utf8_decode($valor)."' or ";
		}

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$Filtros.="and ($consulta)";
	}
	

	$opcion = '<a href="#" onClick="MuestraTramite(\'|| rad.id_radicacion ||\')">\'|| rad.id_radicacion ||\'</a>';	
					
	$tiempoRestante="(case when rad.estado!='Cerrado' then cast((EXTRACT(EPOCH FROM (rad.fechahora_limite-now()))/3600) as bigint)||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',rad.fechahora_limite-now()))) || ' sec' else '' end) as tiemporestante";
	
	$consulta="select  '".$opcion."', rad.preasignado,rad.fechahora_limite, actividad, $tiempoRestante, (COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres, pro.proceso_desc, ser.desc_servicio, com.des_compania, tit.desc_tipotramite,$semaforoTramite as semaforo";
	$where = " from wf_radicacion rad LEFT JOIN wf_historial his ON rad.id_radicacion=his.id_radicacion and his.fechahora is null LEFT JOIN adm_usuario usu on his.usuario_cod=usu.usuario_cod, wf_tipotramite tit,wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_compania com, wf_producto prd where prd.id_producto=rad.id_producto and rad.id_tipotramite=tit.id_tipotramite and com.id_compania=tip.id_compania and ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso and tip.id_tipologia=rad.id_tipologia $Filtros";
	$campoCount = "rad.id_radicacion";
}


if (!$page) $page = 1;
if (!$rp) $rp = 10;
$start = (($page-1) * $rp);

if ($query) $where .= " and CAST($qtype as TEXT) ILIKE '%".pg_escape_string($query)."%' ";

$ordena = " ORDER BY $sortname $sortorder";
$limit = " LIMIT $rp OFFSET $start";


//$result=queryQR( $precqq );


if( $_POST['consulta'] == 'InformeOportunidad' && 
		($_REQUEST['TipoInforme'] == 'Respuestas' || $_REQUEST['TipoInforme'] == 'Compania')){

	$result=queryQR( $precqq );

	$result=queryQR( $consulta );//obtiene numero de registros numero 
	
	$total = $result->RecordCount();

	$result=queryQR( $consulta );//obtiene registros consulta

}else{
	$result=queryQR( $consulta. $where);//obtiene numero de registros numero 
	
	$total = $result->RecordCount();

	$result=queryQR( $consulta . $where . $ordena .$limit);//obtiene registros consulta
}

$rows = array();
while ($row = $result->FetchRow()) {
	$rows[] = $row;	
}

header("Content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
$xml .= "<rows>";
$xml .= "<page>$page</page>";
$xml .= "<total>$total</total>";
foreach($rows AS $row){
	$xml .= "<row >";
	for($i=0; $i<sizeof($row); $i++){
		$xml .= "<cell><![CDATA[".$row[$i]."]]></cell>";
	}
	$xml .= "</row>";
}
$xml .= "</rows>";
echo $xml;
