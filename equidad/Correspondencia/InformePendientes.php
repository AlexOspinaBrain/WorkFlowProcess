<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Consulta.php'
paar correcta visualización
*/?>
<?php
	
generaInforme();

require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/ui/js/datepicker/jquery.ui.datepicker-es.js"></script><!-- Calensario	configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/jquery.maskedinput.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	
	<script type="text/javascript" src="js/VisorImagine.js"></script><!--Visor-->
	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->

	<script type="text/javascript">
        $(document).ready(function() {	
			$( "#Desde" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#Hasta" ).datepicker( "option", "minDate", selectedDate );
					CambiaFiltros();
				}
			});
			
			$( "#Hasta" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#Desde" ).datepicker( "option", "maxDate", selectedDate );
					CambiaFiltros();
				}
			});
		
			$('#NumTramite, #Lote, #Desde, #Hasta').keypress(function(e) {
				if(e.which == 13) 
					return;
					//CambiaFiltros();				
			});
			
			
			
			
		});

		function generaExcel(){
			window.open("Correspondencia/InformePendientes.php?excel&"+$("form").serialize());			
		}
		

	
	

	</script>
	
	<style>
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
		}
		.TableResult{
			color:black;
			border-spacing: 0px;
			border-color: #F1F1F1;
		}
		.Planillalote{
			font-weight: bold;
			color:blue;
			text-decoration: none;
		}		
		.TableResult td{
			padding:5px;
		}
		.TableResult th{
			text-align: left
		}
		input[type=text]{
		margin:0px 2px;
			display:inline;			
		}
		.Detalles{
			color:blue;
		}
	</style>
</head>
<body>	
<form action="#" method="post" style="margin-left: auto;margin-right: auto;width: 80%;">

	<fieldset style="width:150px;height:50px;" class='filtros'>
		<legend><b>Rango de fecha (aaaammdd): </b></legend>		
<label style="display: inline-block;">		
		<input type="text" name="Desde" id="Desde" style='width:60px'/><br>
		Desde</label>
		<label style="display: inline-block;">	
		<input type="text" name="Hasta" id="Hasta" style='width:60px'/><br>
		Hasta</label>
	</fieldset>
	
	<fieldset style="width:350px; height:50px;" class='filtros'>
		<legend><b>Area origen: </b></legend>			
		<select id="AreaOrigen" name="AreaOrigen">
			<option></option>
		</select>		
	</fieldset>
	
	<fieldset style="width:350px; height:50px;" class='filtros'>
		<legend><b>Area destino: </b></legend>			
		<select id="AreaDestino" name="AreaDestino">
			<option></option>
		</select>		
	</fieldset>
<br>
	<fieldset style="width:250px; height:50px;" class='filtros'>
		<legend><b>Tipo documento: </b></legend>			
		<select id="tipo_documento" name="tipo_documento">
			<option></option>
		</select>		
	</fieldset>
	<fieldset style="width:250px; height:50px;" class='filtros'>
		<legend><b>Estado pendiente: </b></legend>			
		<select id="estado_pendiente" name="estado_pendiente">
			<option value=""></option>
			<option value="CERRADO">CERRADO</option>
			<option value="DEVUELTO">DEVUELTO</option>
			<option value="DISTRIBUCION">DISTRIBUCION</option>
			<option value="DISTRIBUCION EXTERNA">DISTRIBUCION EXTERNA</option>
			<option value="ELIMINADO">ELIMINADO</option>
			<option value="ENVIADO">ENVIADO</option>
			<option value="ENVIADO">ENVIADO</option>
			<option value="RADICADO">RADICADO</option>
			<option value="RECIBIDO">RECIBIDO</option>
			<option value="RECIBIDO CORRESPONDENCIA">RECIBIDO CORRESPONDENCIA</option>
			<option value="RECIBIDO DESTINATARIO">RECIBIDO DESTINATARIO</option>
			<option value="REDIRECCIONADO">REDIRECCIONADO</option>
		</select>		
	</fieldset>

	<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
<br>
		<input type="button" value="Generar informe" style='vertical-align: bottom;' onClick="generaExcel()"/>
	</fieldset>	


</form>

<?=OpcionesSelect('AreaOrigen', 'tblareascorrespondencia are', ' are.areasid', 'are.area', '')?>
<?=OpcionesSelect('AreaDestino', 'tblareascorrespondencia are', ' are.areasid', 'are.area', '')?>
<?=OpcionesSelect('tipo_documento', 'tbltiposdoccorresp doc', ' DISTINCT on (tipo) tipo', 'tipo', '')?>
</body>
</html>
<?php
function generaInforme(){	
	if(!isset($_GET['excel']))
		return;
	
	require_once ('../config/conexion.php');
	header("Content-type: application/vnd.ms-excel; name='excel'");
   	header("Content-Disposition: filename=ListaExcel.xls");
   	header("Pragma: no-cache");
   	header("Expires: 0");

	$conect=new conexion();
	
	$conect->queryequi("DROP TABLE IF EXISTS pendientes;
		CREATE TEMP TABLE pendientes AS
		select numtramite 
		from 
		radcorrespondencia rad 
		left join trasacorrespondencia tra using (numtramite)
        left join tblareascorrespondencia are_des on rad.area=are_des.areasid
        left join tblradofi age_des on are_des.agencia=age_des.codigo
        left join (
            select *
            from adm_usuario usu 
            left join tblareascorrespondencia are on are.areasid=usu.area
            left join tblradofi age on age.codigo=are.agencia
            ) 
        org on rad.radicado=cast(org.usuario_cod as text )       
		left join tbltiposdoccorresp doc on rad.tipodoc=doc.tipodocid
        WHERE 
        tra.fechahora is  null "
		.(!empty($_GET['Desde']) && !empty($_GET['Hasta']) ? " and rad.fecins BETWEEN '".$_GET['Desde']."' and '".$_GET['Hasta']."'" : '')
		.(!empty($_GET['AreaOrigen']) ? " and org.areasid = ".$_GET['AreaOrigen'] : '')
		.(!empty($_GET['AreaDestino']) ? " and rad.area = ".$_GET['AreaDestino'] : '')
		.(!empty($_GET['estado_pendiente']) ? " and tra.estado = '".$_GET['AreaOrigen']."'" : '')
		.(!empty($_GET['tipo_documento']) ? " and doc.tipo ilike '%".$_GET['tipo_documento']."%'" : '')
		);
		
		/*
		are_des.areasid = 17 AND
        age_des.codigo = '008' and
        org.areasid = 17 and
        org.codigo = '008' and 
        rad.tipodoc = 1226 and
        tra.estado = 'CERRADO'
        order by fecins;"*/
		
	$consulta=$conect->queryequi("
		select * from 
		radcorrespondencia rad 
		left join tbltiposdoccorresp doc on rad.tipodoc=doc.tipodocid
		left join (
			select usu.usuario_cod, are.areasid as id_area_remitenete, are.area as area_remitente, 
		    age.codigo as id_agencia_remitente,
			age.descrip as agencia_remitente, 
			(COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres_radica
			from adm_usuario usu 
			left join tblareascorrespondencia are on are.areasid=usu.area
			left join tblradofi age on age.codigo=are.agencia
			) 
		remit on rad.radicado=cast(remit.usuario_cod as text )
		left join (
			select usu.usuario_cod, are.areasid as id_area_destino, are.area as area_destino, 
		    age.codigo as id_agencia_destino,
			age.descrip as agencia_destino,
			(COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres_destinatario
			from adm_usuario usu 
			left join tblareascorrespondencia are on are.areasid=usu.area
			left join tblradofi age on age.codigo=are.agencia
			) 
		destino on rad.destinatario=cast(destino.usuario_cod as text )
        left join(
			select  DISTINCT on (numtramite) numtramite, are.area as area_pendiente, estado as estado_pendiente,
				COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as nombre_pendiente 
			from trasacorrespondencia tra
				left join adm_usuario usu on tra.usuario=cast(usu.usuario_cod as text)
				left join tblareascorrespondencia are on tra.area=are.areasid 
			where 
				numtramite in (select * from pendientes) and
				fechahora is null
			order by numtramite, sr desc) estado_pend USING(numtramite)
		left join(
			select  DISTINCT on (numtramite) numtramite, are.area as area_ult, estado as estado_ult, fechahora as fecha_ult,
				COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as nombre_ult 
			from trasacorrespondencia tra
				left join adm_usuario usu on tra.usuario=cast(usu.usuario_cod as text)
				left join tblareascorrespondencia are on tra.area=are.areasid 
			where 
				numtramite in (select * from pendientes) and
				fechahora is not null
				order by numtramite, sr desc
		) estado_ult USING(numtramite)
		left join (select numtramite, destinatario as destinatario_ext from radcorresext) ext USING(numtramite)
		
			WHERE numtramite in (select * from pendientes)
		order by fecins");
 	
	echo "<table border='1'>";
	echo "<tr><th>TRAMITE</th><th>FECHA RADICADO</th><th>PERSONA RADICA
</th><th>AREA REMITENTE
</th><th>AGENCIA REMITENTE
</th><th>DESTINATARIO
</th><th>AREA DESTINO
</th><th>AGENCIA DETINO
</th><th>TIPO DOCUMENTO
</th><th>ESTADO
</th><th>FECHA ACTIVIDAD
</th><th>USUARIO
</th><th>AREA
</th><th>ESTADO2
</th><th>USUARIO2
</th><th>AREA2
</th></tr>";
	while ($row = pg_fetch_array($consulta)){
		echo "<tr>
				<td style='mso-number-format:\"\@\"'> {$row['numtramite']}</td>
				<td style='mso-number-format:\"\@\"'> ".date('Y/m/d h:m a', strtotime($row['fecins']))."</td>
				<td style='mso-number-format:\"\@\"'> {$row['nombres_radica']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['area_remitente']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['agencia_remitente']}</td>
				<td style='mso-number-format:\"\@\"'> ".($row['destinatario'] == 0 ? $row['destinatario_ext']: $row['nombres_destinatario'])."</td>				
				<td style='mso-number-format:\"\@\"'> ".($row['destinatario'] == 0 ? "CORRESPONDENCIA EXTERNA": $row['area_destino'])."</td>				
				<td style='mso-number-format:\"\@\"'> ".($row['destinatario'] == 0 ? "DESTINO EXTERNO": $row['agencia_destino'])."</td>								
				<td style='mso-number-format:\"\@\"'> {$row['tipo']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['estado_ult']}</td>				
				<td style='mso-number-format:\"\@\"'> ".date('Y/m/d h:m a', strtotime($row['fecha_ult']))."</td>
				<td style='mso-number-format:\"\@\"'> {$row['nombre_ult']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['area_ult']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['estado_pendiente']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['nombre_pendiente']}</td>
				<td style='mso-number-format:\"\@\"'> {$row['area_pendiente']}</td>
			</tr>";
			
	}
	echo "</table>";
	exit;
}

function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}
?>