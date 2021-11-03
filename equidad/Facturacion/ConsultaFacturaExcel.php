
<?php
require_once ('../config/conexion.php');
header ("Content-type: application/x-msexcel");  
header ("Content-Disposition: attachment; filename=Reporte_facturacion.xls" );
?>

<?php
echo "<table style='font-family:Arial, sans-serif;border-collapse:collapse;'>";

	if($_REQUEST['estado'] != null){
		$consulta="";
				
		foreach ($_REQUEST['estado'] as $valor) 
			$consulta .= "rad.estado = '".utf8_decode($valor)."' or ";

		if($_REQUEST['IncluyeTramite'] != null)
			$consulta .= "(rad.estado = 'Corrección orden de giro' and rad.id_ordengiro='".$_REQUEST['IncluyeTramite']."') or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['aseguradora'] != null){
		$consulta="";
				
		foreach ($_REQUEST['aseguradora'] as $valor) 
			$consulta .= "rad.id_compania = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['FiltroTipoDoc'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['FiltroTipoDoc']))
		foreach ($_REQUEST['FiltroTipoDoc'] as $valor) 
			$consulta .= "rad.id_documento = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['FiltroTipoDoc']))
			$consulta .= "rad.id_documento = '".utf8_decode($_REQUEST['FiltroTipoDoc'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['serial_factura'] != null){
		$filters.="and serial_factura='".$_REQUEST['serial_factura']."'";
	}

	if($_REQUEST['num_ordengiro'] != null){
		$filters.="and num_ordengiro='".$_REQUEST['num_ordengiro']."'";
	}

	if($_REQUEST['num_comprobante'] != null){
		$filters.="and num_comprobante='".$_REQUEST['num_comprobante']."'";
	}

	if($_REQUEST['no_factura'] != null){
		$filters.="and no_factura='".$_REQUEST['no_factura']."'";
	}

	$Fecha="";	
	if($_REQUEST['FiltroDesdeFecRad'] != null || $_REQUEST['FiltroHastaFecRad'] != null)
		$Fecha="and to_char(rad.fechahora_ins,'yyyyMMdd')='".$_REQUEST['FiltroDesdeFecRad'].$_REQUEST['FiltroHastaFecRad']."'";
				
	if($_REQUEST['FiltroDesdeFecRad'] != null && $_REQUEST['FiltroHastaFecRad'] != null)
		$Fecha="and rad.fechahora_ins between '".$_REQUEST['FiltroDesdeFecRad'] ."' and (date '".$_REQUEST['FiltroHastaFecRad'] ."'+ interval '1 day')";		
	$filters.=$Fecha;

	$Fecha="";	
	if($_REQUEST['FiltroDesdeExp'] != null || $_REQUEST['FiltroHastaExp'] != null)
		$Fecha="and to_char(rad.fecha_expedicion,'yyyyMMdd')='".$_REQUEST['FiltroDesdeExp'].$_REQUEST['FiltroHastaExp']."'";
				
	if($_REQUEST['FiltroDesdeExp'] != null && $_REQUEST['FiltroHastaExp'] != null)
		$Fecha="and rad.fecha_expedicion between '".$_REQUEST['FiltroDesdeExp'] ."' and (date '".$_REQUEST['FiltroHastaExp'] ."'+ interval '1 day')";		
	$filters.=$Fecha;

	$Fecha="";	
	if($_REQUEST['FiltroDesdeVen'] != null || $_REQUEST['FiltroHastaVen'] != null)
		$Fecha="and to_char(rad.fecha_vencimiento,'yyyyMMdd')='".$_REQUEST['FiltroDesdeVen'].$_REQUEST['FiltroHastaVen']."'";
				
	if($_REQUEST['FiltroDesdeVen'] != null && $_REQUEST['FiltroHastaVen'] != null)
		$Fecha="and rad.fecha_vencimiento between '".$_REQUEST['FiltroDesdeVen'] ."' and (date '".$_REQUEST['FiltroHastaVen'] ."'+ interval '1 day')";		
	$filters.=$Fecha;

	$Valor="";	
	if($_REQUEST['ValorFacturaDesde'] != null || $_REQUEST['ValorFacturaHasta'] != null)
		$Valor="and valor_factura='".$_REQUEST['ValorFacturaDesde'].$_REQUEST['ValorFacturaHasta']."'";
				
	if($_REQUEST['ValorFacturaDesde'] != null && $_REQUEST['ValorFacturaHasta'] != null)
		$Valor="and valor_factura between '".$_REQUEST['ValorFacturaDesde'] ."' and '".$_REQUEST['ValorFacturaHasta'] ."'";		
	$filters.=$Valor;

	if($_REQUEST['identificacion'] != null){
		$filters.="and per.documento=".intval($_REQUEST['identificacion']);
	}

	if($_REQUEST['NombrePro'] != null){
		$filters.="and per.nombre ilike '%".$_REQUEST['NombrePro']."%'";
	}

	if($_REQUEST['Aseguradora'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['Aseguradora']))
		foreach ($_REQUEST['Aseguradora'] as $valor) 
			$consulta .= "rad.id_compania = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['Aseguradora']))
			$consulta .= "rad.id_compania = '".utf8_decode($_REQUEST['Aseguradora'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['Aseguradora'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['Aseguradora']))
		foreach ($_REQUEST['Aseguradora'] as $valor) 
			$consulta .= "rad.id_compania = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['Aseguradora']))
			$consulta .= "rad.id_compania = '".utf8_decode($_REQUEST['Aseguradora'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['Agencia'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['Agencia']))
		foreach ($_REQUEST['Agencia'] as $valor) 
			$consulta .= "are.agencia = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['Agencia']))
			$consulta .= "are.agencia = '".utf8_decode($_REQUEST['Agencia'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['Area'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['Area']))
		foreach ($_REQUEST['Area'] as $valor) 
			$consulta .= "rad.id_area = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['Area']))
			$consulta .= "rad.id_area = '".utf8_decode($_REQUEST['Area'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['Actividad'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['Actividad']))
		foreach ($_REQUEST['Actividad'] as $valor) 
			$consulta .= "rad.estado = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['Actividad']))
			$consulta .= "rad.estado = '".utf8_decode($_REQUEST['Actividad'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['UsuarioPen'] != null){
		$consulta="";
		
		if(is_array ($_REQUEST['UsuarioPen']))
		foreach ($_REQUEST['UsuarioPen'] as $valor) 
			$consulta .= "his.usu_his = '".utf8_decode($valor)."' or ";
		
		if(!is_array($_REQUEST['UsuarioPen']))
			$consulta .= "his.usu_his = '".utf8_decode($_REQUEST['UsuarioPen'])."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters2.="and ($consulta)";
	}	

	if($_REQUEST['IdentifiProveedor'] != null){
		$consulta="";
				
		foreach ($_REQUEST['IdentifiProveedor'] as $valor) 
			$consulta .= "per.documento = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

$select = "select (case when rad.estado='Correción radicación' then 'Corregir' end) as update, 
		to_char(fechahora_ins,'yyyy-MM-dd HH:MI AM') as fecha_insfac, to_char(fecha_terminado,'yyyy-MM-dd HH:MI AM') as fecha_terminado2, 
		are.area as area_desc, rad.estado as status,  
		' ( ' || tipo_doc || ' ' || per.documento || ' ) ' || per.nombre as proveedor, 
		EXTRACT(DAY FROM age(date(fecha_terminado) ,date(fechahora_ins) ) ) || ' días' tiempo_tramite, 
		case 
			when rad.estado='Recibir en el área' then are.area
		    when rad.estado='Generar orden de giro' then are.area
		    when rad.estado='Enviar área contabilidad' then are.area
		    when rad.estado='Recibir contabilidad' then 'Contabilidad' 
		    when rad.estado='Causación' then 'Contabilidad' 
		    when rad.estado='Recibir tesorería' then 'Tesorería' 
		    when rad.estado='Generar CP' then 'Tesorería' 
		    when rad.estado='Recibir Auditoria' then 'Auditoria' 
		    when rad.estado='Auditoria' then 'Auditoria' 
		    when rad.estado='Recibir para cierre' then 'Tesorería' 
		    when rad.estado='Recibir corrección radicación' then 'Correspondencia' 
		    when rad.estado='Recibir corrección orden de giro' then are.area 
		    when rad.estado='Recibir corrección causación' then 'Contabilidad' 
		    when rad.estado='Recibir corrección Comprobante de pago' then 'Tesorería' 
		    when rad.estado='Recibir corrección auditoria' then 'Auditoria' 
		    when rad.estado='Correción radicación' then 'Correspondencia' 
		    when rad.estado='Corrección orden de giro' then are.area 
		    when rad.estado='Corrección causación' then 'Contabilidad' 
		    when rad.estado='Corrección Comprobante de pago' then 'Tesorería' 
		    when rad.estado='Corrección auditoria' then 'Auditoria' 
		    when rad.estado='Cerrar tramite' then 'Tesorería'    
		end
		 as desc_area,*";
$where ="from fac_radica rad join(
			select DISTINCT on (id_radica) id_radica, usuario_cod as usu_his from fac_historial his order by id_radica, id_historial desc
		) his USING (id_radica)
		left join(
			select DISTINCT on (id_radica) id_radica, to_char(fecha_terminado,'yyyy-MM-dd HH:MI AM') as fecha_devuelta from fac_historial his where actividad='Devuelta al proveedor' order by id_radica, id_historial desc
		) historial_devolucion USING (id_radica)
		join proveedor pro using(id_proveedor)
		join persona per using(id_persona)
		join tblareascorrespondencia are on are.areasid=rad.id_area 
		join wf_compania using(id_compania) 
		join fac_documento doc using(id_documento)
		join adm_usuario usu on usu.usuario_cod=his.usu_his 
		join fac_ordengiro ord USING(id_ordengiro)
		join fac_comprobantepago com USING(id_comprobante)
		left join fac_historial his2 on rad.id_radica=his2.id_radica and (his2.actividad='Cerrar tramite' or his2.actividad='Anulado') and his2.fecha_terminado is not null
		where 1=1 $filters $filters2";

$columnsFacturas = array('serial_factura','fecha_insfac', 'fecha_terminado2', 'fecha_devuelta', 'status', 'desc_area', 'proveedor', 'no_factura', 'area_desc', 'desc_documento', 'num_ordengiro', 'num_comprobante', 'tiempo_tramite');
$result=queryQR( $select . $where );

$Facturas = $result->getArray();
echo "<tr><th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Tramite</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Fecha radicado</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Fecha aprobada</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Fecha devuelta al proveedor</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Estado pendiente</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Area actual</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Proveedor</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>No factura</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Area destino</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Tipo documento</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Orden de giro</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Comprobante de pago</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Tiempo tramite</th><td></td>";

$select ="select serial_factura,  to_char(fecha_asignado,'yyyy-MM-dd HH:MI AM') as fecha_asignado , 
		to_char(fecha_terminado,'yyyy-MM-dd HH:MI AM') as fecha_terminado, 
		COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as usuario, 
		actividad, his.estado ";
$where ="from fac_historial his 
		join fac_radica rad USING(id_radica)
		join proveedor pro using(id_proveedor)
		join persona per using(id_persona)
		join tblareascorrespondencia are on are.areasid=rad.id_area 
		join wf_compania using(id_compania) 
		join fac_documento doc using(id_documento)
		join adm_usuario usu on usu.usuario_cod= his.usuario_cod
		join fac_ordengiro ord USING(id_ordengiro)
		join fac_comprobantepago com USING(id_comprobante)
		where 1=1 $filters order by his.id_radica,  id_historial";

$columnsHistorial = array('serial_factura', 'actividad','fecha_asignado', 'fecha_terminado', 'usuario', 'estado');
$result=queryQR( $select . $where );
$Historial = $result->getArray();
echo "<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Tramite</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Actividad</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Fecha Asignado</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Fecha Terminado</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Usuario</th>
		<th style='font-size:10px;padding:5px;background-color:#327E04;color:#fff;text-align:center;'>Estado</th></tr>";

$filas= max(count($Facturas), count($Historial));

for($i=0; $i<$filas; $i++){
	$alt="";
	if($i%2==1)
		$alt="color:#000;background-color:#EAF2D3";
	
	echo "<tr>";
	
		foreach($columnsFacturas as $valor){
			if($Facturas[$i])
				echo "<td style='font-size:10px;padding:3px 7px 2px 7px;border:1px solid #98bf21;$alt'>".$Facturas[$i][$valor]."</td>";	
			else
				echo "<td></td>";
		}
	
	echo"<td style='width:80px'></td>";
	
		foreach($columnsHistorial as $valor){
			if($Historial[$i])
				echo "<td style='font-size:10px;border:1px solid #98bf21;padding:3px 7px 2px 7px;$alt'>".$Historial[$i][$valor]."</td>";
			else
				echo "<td></td>";	
		}
	echo "</tr>";
}
echo "</table>";
?>
