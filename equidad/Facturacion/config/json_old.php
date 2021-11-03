<?php
session_start();
require_once ('../../config/conexion.php');

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
$query = $_GET['query']; // get the query
$searchField = isset($_GET['searchField']) ? $_GET['searchField'] : false;
$searchOper = isset($_GET['searchOper']) ? $_GET['searchOper']: false;
$searchString = isset($_GET['searchString']) ? $_GET['searchString'] : false;

$usuario = "COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as usuario";

if($query == 'ConsultaOrdenGiro'){
	$joins="";
	$filters="";

	if($_REQUEST['estado'] != null){
		$consulta="";
				
		foreach ($_REQUEST['estado'] as $valor) 
			$consulta .= "rad.estado = '".utf8_decode($valor)."' or ";

		if($_REQUEST['IncluyeTramite'] != null)
		$consulta .= " (rad.estado = 'Corrección Comprobante de pago' and id_comprobante=(select id_comprobante from fac_comprobantepago where num_comprobante='".$_REQUEST['IncluyeTramite']."')) or";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta) ";
	}

	if($_REQUEST['aseguradora'] != null){
		$consulta="";
				
		foreach ($_REQUEST['aseguradora'] as $valor) 
			$consulta .= "ord.id_compania = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['IdentifiProveedor'] != null){
		$consulta="";
				
		foreach ($_REQUEST['IdentifiProveedor'] as $valor) 
			$consulta .= "per.documento = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['usuarioPend'] != null){
		$consulta="";
		if($_REQUEST['usuarioPend'] == 'login' &&  (is_int (array_search('Recibir contabilidad', $_REQUEST['estado'])) || is_int (array_search(utf8_encode('Causación'), $_REQUEST['estado'])) )){
			$consulta .="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.4') or ";	
		}	

		if($_REQUEST['usuarioPend'] == 'login' &&  (is_int (array_search(utf8_encode('Recibir tesorería'), $_REQUEST['estado'])) || is_int (array_search(utf8_encode('Generar CP'), $_REQUEST['estado'])))){
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.5') or ";	
			
		}

		if($_REQUEST['usuarioPend'] == 'login' &&  (is_int (array_search(utf8_encode('Enviar área contabilidad'), $_REQUEST['estado'])) || is_int (array_search(utf8_encode('Recibir corrección orden de giro'), $_REQUEST['estado'])))){
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.3') or ";	
			
		}
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['usuario_ord'] != null){
		$filters.="and (rad.estado = 'Corrección orden de giro' or rad.estado = 'Enviar área contabilidad') and 
			usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
			usu.usuario_cod=".$_SESSION['uscod']." and (jerarquia_opcion='3.4' or jerarquia_opcion='3.3'))";				
	}

	if($_REQUEST['num_ordengiro']!=null){
		$patron = "/^(gen|vid|GEN|VID)/";
		if(preg_match($patron, $_REQUEST['num_ordengiro']))
			$filters.=" and num_ordengiro ilike '%".$_REQUEST['num_ordengiro']."%'";

		$patron = "/^(fac|FAC)/";
		if(preg_match($patron, $_REQUEST['num_ordengiro']))
			$filtersRAD="where serial_factura ilike '%".$_REQUEST['num_ordengiro']."%'";
	}

	if($_REQUEST['proveedor']!=null){
		$filters.=" and (CAST( per.documento AS TEXT) ilike '%".$_REQUEST['proveedor']."%' or per.nombre ilike '%".$_REQUEST['proveedor']."%')";
		
	}

	$select = "select to_char(ord.fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins, num_ordengiro, 
				case when rad.estado is null then 'Anulado' else rad.estado end,
				case when rad.estado = 'Corrección orden de giro' then 'Corregir' end as corregir, 
				' ( ' || tipo_doc || ' ' || per.documento || ' ) ' || per.nombre as proveedor, des_compania,
  				(select count(*) from fac_radica where id_ordengiro=ord.id_ordengiro) as cant_fac, $usuario";
	$where ="from fac_ordengiro ord left join
   	 			(select  DISTINCT on (id_ordengiro) id_ordengiro, estado, usu_his, usuario_cod as usu_fac, id_proveedor from fac_radica rad join(
        			select DISTINCT on (id_radica) id_radica, usuario_cod as usu_his from fac_historial his order by id_radica, id_historial desc
    			) his USING (id_radica) $filtersRAD
  			)rad USING(id_ordengiro) 
			left join adm_usuario usu on usu.usuario_cod = usu_his 
			left join proveedor pro using(id_proveedor) 
			join persona per using(id_persona) 
			left join wf_compania com using(id_compania)
  			where id_ordengiro!='0' $filters";
	$id='num_ordengiro';
	$columns = array('num_ordengiro','fecha_ins', 'proveedor', 'usuario', 'estado', 'cant_fac');
	
	if($_REQUEST['columns'] != null)
		$columns = $_REQUEST['columns'];
}

if($query == 'ConsultaCP'){
	if($_REQUEST['estado'] != null){
		$consulta="";
				
		foreach ($_REQUEST['estado'] as $valor) 
			$consulta .= "ord.estado = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['usuarioPend'] != null){
		$consulta="";
		if($_REQUEST['usuarioPend'] == 'login' &&  (is_int (array_search('Recibir Auditoria', $_REQUEST['estado'])) || is_int (array_search('Auditoria', $_REQUEST['estado']))))
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.6') or ";	

		if($_REQUEST['usuarioPend'] == 'login' &&  (is_int (array_search('Recibir para cierre', $_REQUEST['estado'])) || is_int (array_search('Cerrar tramite', $_REQUEST['estado']))))
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.7') or";	

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['num_comprobante'] != null){
		$filters.="and num_comprobante='".$_REQUEST['num_comprobante']."'";
	}

	if($_REQUEST['proveedor']!=null){
		$filters.=" and (CAST( per.documento AS TEXT) ilike '%".$_REQUEST['proveedor']."%' or per.nombre ilike '%".$_REQUEST['proveedor']."%')";
		
	}

	$select = "select num_comprobante, to_char(fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_ins, 
			(select count(*) from fac_ordengiro where id_comprobante=ord.id_comprobante) as cant_ord,
			' ( ' || tipo_doc || ' ' || per.documento || ' ) ' || per.nombre as proveedor,
			case when ord.estado = 'Corrección Comprobante de pago' then 'Corregir' end as corregir,
			ord.estado, medio_pago, to_char(valor_cp,'LFM 999,999,999') as valor_cp, $usuario";
	$where = "from fac_comprobantepago com left join
				(select  DISTINCT on (id_comprobante) id_comprobante, estado, usu_his, id_proveedor from fac_ordengiro ord left join
			   	 	(select  DISTINCT on (id_ordengiro) id_ordengiro, estado, usu_his, usuario_cod as usu_fac, id_proveedor from fac_radica rad join
			        	(select DISTINCT on (id_radica) id_radica, usuario_cod as usu_his from fac_historial his order by id_radica, id_historial desc
			    		) his USING (id_radica)
			  		)rad USING(id_ordengiro) 
			    )ord USING(id_comprobante)
			join adm_usuario usu on usu.usuario_cod=usu_his
			left join proveedor pro using(id_proveedor)
			join persona per using(id_persona)
			where id_comprobante!=0 $filters";

	$id='num_comprobante';
	$columns = array('num_comprobante','fecha_ins', 'proveedor', 'usuario', 'estado', 'medio_pago', 'cant_ord');
	
	if($_REQUEST['columns'] != null)
		$columns = $_REQUEST['columns'];
}

if($query == 'ConsultaFactura'){
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
		$filters.="and serial_factura ilike '%".$_REQUEST['serial_factura']."%'";
	}

	if($_REQUEST['num_ordengiro'] != null){
		$filters.="and num_ordengiro='".$_REQUEST['num_ordengiro']."'";
	}

	if($_REQUEST['num_comprobante'] != null){
		$filters.="and num_comprobante='".$_REQUEST['num_comprobante']."'";
	}

	if($_REQUEST['no_factura'] != null){
		$filters.="and no_factura ilike '%".$_REQUEST['no_factura']."%'";
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
		$filters.="and ($consulta)";
	}	

	if($_REQUEST['IdentifiProveedor'] != null){
		$consulta="";
				
		foreach ($_REQUEST['IdentifiProveedor'] as $valor) 
			$consulta .= "per.documento = '".utf8_decode($valor)."' or ";
		
		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['usuarioPend'] != null){
		$consulta="";
		if($_REQUEST['usuarioPend'] == 'login' && (is_int (array_search(utf8_encode('Recibir en el área'), $_REQUEST['estado'])) || is_int (array_search(utf8_encode('Generar orden de giro'), $_REQUEST['estado']))))
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.3') or ";		

		if($_REQUEST['usuarioPend'] == 'login' && is_int (array_search(utf8_encode('Recibir corrección radicación'), $_REQUEST['estado'])) )
			$consulta.="usu.area=(select area from adm_usuario usu join adm_usumenu USING(usuario_cod) where 
				usu.usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='3.1') or ";		

		$consulta=substr( $consulta ,0,strlen( $consulta )-3);
		$filters.="and ($consulta)";
	}

	if($_REQUEST['proveedor']!=null){
		$filters.=" and (CAST( per.documento AS TEXT) ilike '%".$_REQUEST['proveedor']."%' or per.nombre ilike '%".$_REQUEST['proveedor']."%')";	
	}

	$select = "select (case when rad.estado='Correción radicación' then 'Corregir' end) as update,
				(case when rad.estado='Recibir en el área' and rad.usuario_cod = ".$_SESSION['uscod']." then 'Eliminar' end) as elimina, 
				to_char(fechahora_ins,'yyyy-MM-dd HH:MI AM') as fecha_insfac, are.area as area_desc, rad.estado as status,  
				' ( ' || tipo_doc || ' ' || per.documento || ' ) ' || per.nombre as proveedor, to_char(rad.valor_factura,'LFM 999,999,999') as valor_fac, *";
	$where ="from fac_radica rad join(
				select DISTINCT on (id_radica) id_radica, usuario_cod as usu_his from fac_historial his order by id_radica, id_historial desc
			) his USING (id_radica)
			join proveedor pro using(id_proveedor)
			join persona per using(id_persona)
			join tblareascorrespondencia are on are.areasid=rad.id_area 
			join wf_compania using(id_compania) 
			join fac_documento doc using(id_documento)
			join adm_usuario usu on usu.usuario_cod=his.usu_his 
			join fac_ordengiro ord USING(id_ordengiro)
			join fac_comprobantepago com USING(id_comprobante)
			where 1=1 $filters";
			  
	$id='serial_factura';
	$columns = array('serial_factura','fecha_insfac', 'status', 'proveedor', 'no_factura', 'area_desc', 'desc_documento');

	if($_REQUEST['columns'] != null)
		$columns = $_REQUEST['columns'];
}

if($query == 'FacturaInOrdenGiro'){
	if($_REQUEST['id_ordengiro'] != null)
		$filters="where id_ordengiro = '".$_REQUEST['id_ordengiro']."'";

	$select = "select  to_char(fechahora_ins,'yyyy-MM-dd HH:MI:SS AM') as fecha_ins, ' ( ' || tipo_doc || ' ' || per.documento || ' ) ' || per.nombre as proveedor, *";
	$where = "from fac_radica rad join proveedor pro using(id_proveedor) join tblareascorrespondencia are 
			  on are.areasid=rad.id_area join wf_compania using(id_compania) join fac_documento doc 
			  using(id_documento) join persona per using(id_persona) $filters";
	$id='serial_factura';
	$columns = array('serial_factura', 'no_factura', 'proveedor', 'desc_documento');
}

if($query == 'OrdenGiroInCP'){
	if($_REQUEST['num_comprobante'] != null)
		$filters="where num_comprobante = '".$_REQUEST['num_comprobante']."'";

	$select = "select num_ordengiro, to_char(ord.fecha_ins,'yyyy-MM-dd HH:MI AM') as fecha_insord, count(id_ordengiro) as cant_fac, $usuario";
	$where = "from fac_ordengiro ord join fac_comprobantepago com using(id_comprobante) join fac_radica using(id_ordengiro) 
			join adm_usuario usu on ord.usuario_cod=usu.usuario_cod	$filters group by num_ordengiro, ord.fecha_ins, usuario_nombres, usuario_priape, usuario_segape";
	$id='num_ordengiro';
	$columns = array('num_ordengiro', 'fecha_insord', 'usuario', 'cant_fac');
}
	
	
$result=queryQR( "$select $where");
$count = $result->RecordCount();

if(!$sidx) $sidx =1;

if($limit == -1) 
	$limit = $count;
	
if( $count >0 )
	$total_pages = ceil($count/$limit);
else
	$total_pages = 0;

if ($page > $total_pages) 
	$page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if($start < 0)$start =0;
	
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$ordena = " ORDER BY $sidx $sord";

$result=queryQR( "$select $where $ordena LIMIT $limit offset $start");

$i=0;
while($row = $result->FetchRow()) {
    $responce->rows[$i]['id']=$row[$id];
	$array = array();
	foreach($columns as $valor)
		$array[]=utf8_encode($row[$valor]);
		
	$responce->rows[$i]['cell']=$array;
    $i++;
} 

echo json_encode($responce);
