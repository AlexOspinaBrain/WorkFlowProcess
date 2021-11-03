<?
require_once ('../config/conexion.php');
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");  
header ("Cache-Control: no-cache, must-revalidate");  
header ("Pragma: no-cache");  
header ("Content-type: application/x-msexcel");  
header ("Content-Disposition: attachment; filename=\"".$_REQUEST['consulta'].".xls\"" );
$columnas=0;
 
$conect=new conexion();
echo "<table border='1'>";

if($_REQUEST['consulta'] == 'OtrosPagos'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Transferencia'] != null)
		$Transferencia="and transferencia = '".$_REQUEST['Transferencia']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";
		
	if($_REQUEST['Cuenta'] != null)
		$Cuenta="and cuenta = '".$_REQUEST['Cuenta']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech,  transferencia, comprobante, banco, cuenta, to_char(valor, '999,999,999,999,999')";
	$where = " from planillastesoreria pla where cab=54 and tipo='P' $Nombre $Fecha $Transferencia $Comprobante $Cuenta $Valor order by fecha desc";
	$columnas=7;
	echo "<tr><th>NOMBRE</th><th>FECHA</th><th>TRANSFERENCIA</th><th>COMPROBANTE</th><th>BANCO</th><th>CUENTA</th><th>VALOR</th></tr>";
}

if($_REQUEST['consulta'] == 'Traslados'){	
	if($_REQUEST['Traslado'] != null)
		$Traslado="and cab='".$_REQUEST['Traslado']."'";
	else
		$Traslado="and (cab='55' or cab='56')";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and codint='".$_REQUEST['Comprobante']."'";

	if($_REQUEST['Banco'] != null)
		$Banco="and banco='".$_REQUEST['Banco']."'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Compania="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Transferencia'] != null)
		$Transferencia="and transferencia = '".$_REQUEST['Transferencia']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";
		
	if($_REQUEST['Cuenta'] != null)
		$Cuenta="and cuenta = '".$_REQUEST['Cuenta']."'";	
		
	if($_REQUEST['NroComprobante'] != null)
		$NroComprobante="and comprobante = '".$_REQUEST['NroComprobante']."'";
	
	$consulta="select codint, banco, compañia, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, transferencia, to_char(valor, '999,999,999,999,999'), cuenta, comprobante";
	$where = " from planillastesoreria pla where tipo='P' $Traslado $Comprobante $Banco $Fecha $Compania $Transferencia $Valor $Cuenta $NroComprobante order by fecha desc";
	
	$columnas=8;
	echo "<tr><th>COMPROBANTE</th><th>BANCO</th><th>CAMPAÑIA</th><th>FECHA</th><th>TRANSFERENCIA</th><th>VALOR</th><th>CUENTA</th><th>NRO COMPROBANTE</th></tr>";
}

if($_REQUEST['consulta'] == 'ComprobantesIngreso'){	
		if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";

	$consulta="select to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, agencia";
	$where = " from planillascontabilidad pla where cab=49 and tipo='P' $Nombre $Fecha $Campañia $Agencia order by fecha desc";
	$columnas=3;
	echo "<tr><th>FECHA</th><th>COMPAÑIA</th><th>AGENCIA</th></tr>";
}

if($_REQUEST['consulta'] == 'ComprobantesEgreso'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";

	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";
		
	if($_REQUEST['Cheque'] != null)
		$Cheque="and cheque = '".$_REQUEST['Cheque']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, agencia, cheque, comprobante";
	$where = " from planillascontabilidad pla where cab=48 and tipo='P' $Nombre $Fecha $Campañia $Agencia $Cheque $Comprobante order by fecha desc";
	$columnas=6;
	echo "<tr><th>NOMBRE</th><th>FECHA</th><th>COMPAÑIA</th><th>AGENCIA</th><th>CHEQUE</th><th>COMPROBANTE</th></tr>";
}

if($_REQUEST['consulta'] == 'TalentoHumano'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";	
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, banco, to_char(valor, '999,999,999,999,999')";
	$where = " from planillastesoreria pla where cab=53 and tipo='P' $Nombre $Fecha $Campañia $Valor order by fecha desc";
	$columnas=5;
	echo "<tr><th>NOMBRE</th><th>FECHA</th><th>COMPAÑIA</th><th>BANCO</th><th>VALOR</th></tr>";
}

if($_REQUEST['consulta'] == 'Reaseguros'){	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";	
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor = '".$_REQUEST['Valor']."'";

	$consulta="select nombre, to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, transferencia, to_char(valor, '999,999,999,999,999')";
	$where = " from planillastesoreria pla where cab=52 and tipo='P' $Nombre $Fecha $Campañia $Valor order by fecha desc";
	$columnas=5;
	echo "<tr><th>NOMBRE</th><th>FECHA</th><th>COMPAÑIA</th><th>DATOS BANCARIOS</th><th>VALOR</th></tr>";
}

if($_REQUEST['consulta'] == 'ComprobantesInternos'){	

	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and fecha='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and fecha between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
		
	if($_REQUEST['Compania'] != null)
		$Campañia="and compañia = '".$_REQUEST['Compania']."'";
		
	if($_REQUEST['Comprobante'] != null)
		$Comprobante="and comprobante = '".$_REQUEST['Comprobante']."'";

	$consulta="select to_char(to_timestamp(to_char(fecha, '99999999'), 'yyyyMMdd'),'yyyy/MM/dd') as fech, compañia, comprobante";
	$where = " from planillascontabilidad pla where cab=50 and tipo='P' $Fecha $Campañia $Comprobante order by fecha desc";
	$columnas=3;
	echo "<tr><th>FECHA</th><th>COMPAÑIA</th><th>COMPROBANTE</th></tr>";
}

if($_REQUEST['consulta'] == 'HojasVida'){	
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
	
	if($_REQUEST['Documento'] != null)
		$Documento="and numid='".$_REQUEST['Documento']."'";
		
	if($_REQUEST['Estado'] != null)
		$Estado="and estado='".$_REQUEST['Estado']."'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia='".$_REQUEST['Agencia']."'";


	$consulta="select tipid, numid, nombre, estado, agencia";
	$where = " from planillashojasdevida pla where cab=42 and tipo='P' and pla.numid!='".$row['numerodoc']."' $Nombre $Documento $Estado $Agencia order by nombre asc";

	$columnas=5;
	echo "<tr><th>TIPO</th><th>No. DOCUMENTO</th><th>NOMBRE</th><th>ESTADO</th><th>AGENCIA</th></tr>";
}

if($_REQUEST['consulta'] == 'JuridicoContratos'){	
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd') between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
	
	$consulta="select numid, nombre, codint, to_char(valor, '999,999,999,999,999'), objeto, fechafirma";
	$where = " from planillasintermediarios pla where cab=40 and tipo='P' $Nit $Nombre $Consecutivo $Valor $Objeto";
	$columnas=6;
	echo "<tr><th>NIT</th><th>NOMBRE</th><th>CONSECUTIVO</th><th>VALOR</th><th>OBJETO</th><th>FECHA FIRMA</th></tr>";
}

if($_REQUEST['consulta'] == 'LogHojasVida'){	
	
	if($_REQUEST['Usuario'] != null)
		$Nombre="and usuario = '".$_REQUEST['Usuario']."'";
		
	if($_REQUEST['Desde'] != null || $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd')='".$_REQUEST['Desde'].$_REQUEST['Hasta']."'";
				
	if($_REQUEST['Desde'] != null && $_REQUEST['Hasta'] != null)
		$Fecha="and  to_char(reg.feconsulta,'yyyyMMdd') between '".$_REQUEST['Desde'] ."' and '".$_REQUEST['Hasta'] ."'";
	
	if($_REQUEST['DocumentoHV'] != null)
		$DocumentoHV="and reg.numid like '%".$_REQUEST['DocumentoHV']."%'";
		
	if($_REQUEST['NombreHV'] != null)
		$NombreHV="and reg.numid='".$_REQUEST['NombreHV']."'";

	$consulta="SELECT nombres, are.area, to_char(reg.feconsulta,'yyyy-MM-dd HH:MI:SS AM'), reg.numid, (select nombre from planillashojasdevida where numid=reg.numid limit 1)";
	$where = " FROM dblink('dbname=administracion', 'select usuario_desc, COALESCE(usuario_nombres,\'\')  || \' \' || COALESCE(usuario_priape,\'\') || \' \' || COALESCE(usuario_segape,\'\'), area from admusuario') as usu(usuario_desc text, nombres text, areausu integer), registroconsultahv reg, tblareascorrespondencia are where reg.usuario=upper(usu.usuario_desc) and are.areasid=areausu $Nombre $Fecha $DocumentoHV $NombreHV order by feconsulta desc";	
	
	$columnas=5;
	echo "<tr><th>USUARIO</th><th>AREA</th><th>FECHA Y HORA</th><th>DOCUMENTO HV</th><th>NOMBRE HV</th></tr>";
}

if($_REQUEST['consulta'] == 'TesoreriaTransferencia'){	
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Consecutivo'] != null)
		$Consecutivo="and codint='".$_REQUEST['Consecutivo']."'";
		
	if($_REQUEST['Valor'] != null)
		$Valor="and valor='".$_REQUEST['Valor']."'";
		
	if($_REQUEST['Objeto'] != null)
		$Objeto="and objeto ilike '%".$_REQUEST['Objeto']."%'";
	
	$consulta="select numid, nombre, to_char(fecha,'yyyy-MM-dd HH:MI:SS AM')";
	$where = " from planillastesoreria pla where cab=45 and tipo='P' $Nit $Nombre $Consecutivo $Valor $Objeto";
	$columnas=3;
	echo "<tr><th>NIT / IDENTIFICACIÓN</th><th>NOMBRE / RAZÓN SOCIAL</th><th>FECHA</th></tr>";
}

if($_REQUEST['consulta'] == 'Intermediarios'){	
	
	if($_REQUEST['CodIntermediario'] != null)
		$CodIntermediario="and codint = '".$_REQUEST['CodIntermediario']."'";
	
	if($_REQUEST['Nit'] != null)
		$Nit="and numid = '".$_REQUEST['Nit']."'";

	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	if($_REQUEST['Agencia'] != null)
		$Agencia="and agencia = '".$_REQUEST['Agencia']."'";
	
	$consulta="select codint, numid, nombre, agencia, (select '<a href=\"#\" class=\"Planillalote\" onClick=\"MuestraVisor('|| pla.srtodo ||', \'pintermediarios\')\"> Ver ' || count(*) || '</a>' from planillasintermediarios where srtodo=pla.srtodo)";
	$where = " from planillasintermediarios pla where cab=21 and tipo='P' $CodIntermediario $Nit $Nombre $Agencia order by nombre";
	$columnas=4;
	echo "<tr><th>COD INTERMEDIARIO</th><th>NIT</th><th>NOMBRE</th><th>AGENCIA</th></tr>";
}

if($_REQUEST['consulta'] == 'Proveedores'){	
	
	if($_REQUEST['NoIdentificacion'] != null)
		$NoIdentificacion="and numid = '".$_REQUEST['NoIdentificacion']."'";
	
	if($_REQUEST['Nombre'] != null)
		$Nombre="and nombre ilike '%".$_REQUEST['Nombre']."%'";
		
	$consulta="select tipid, numid, nombre";
	$where = " from planillasintermediarios pla where cab=39 and tipo='P' $NoIdentificacion $Nombre order by nombre";
	$columnas=3;
	echo "<tr><th>TIPO</th><th>IDENTIFICACIÓN</th><th>NOMBRE / RAZÓN SOCIAL</th></tr>";
}


$result=$conect->querydb( $consulta . $where );

while ($row = pg_fetch_array($result)) {
	echo "<tr>";
	for( $i=0; $i<$columnas; $i++)
		echo "<td>".$row[$i]."</td>";
	echo "</tr>";	
}
echo "</table>";
$conect->cierracon();
?>