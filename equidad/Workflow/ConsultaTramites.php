<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Workflow/DetallesTramite.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.droppable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script type="text/javascript" src="js/FunctionsWorkflow.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script language="Javascript" src="js/htmlbox.min.js" type="text/javascript"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
		<script type="text/javascript" src="js/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/tinymce/tiny_mce.js"></script>
	<script src="js/jquery.editinplace.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script type="text/javascript" src="js/jquery.multiselect.js"></script>
	<script type="text/javascript" src="js/jquery.multiselect.filter.js"></script>
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	<script src="js/handlebars.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.filter.css" />


	<script type="text/javascript">
        $(document).ready(function() {				
			 $( "#dialog" ).dialog({
				autoOpen: false,
				modal: true,
				height: 600,
				width: ($(window).width()-80),
				position: [40,50],
				buttons: {
					Consultar: function() {
						CambiaFiltros();								
					}				
				},
				open: function(event, ui) { $('.ui-widget-overlay').bind('click', function(){ $("#dialog").dialog('close'); }); }
			});
					
			$('.filtros input:text').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});
			
			$("#FiltroEstado, #FiltroCiuReclamante, #FiltroAseguradoraProducto").multiselect({
				selectedList: 3 
			});
			
			$("#FiltroSemaforoTramite, #FiltroSemaforoActividad, #FiltroPendienteActividad, #FiltroAseguradoraTramite, #FiltroProcesoTramite, #FiltroTipoTramite, #FiltroServicioTramite, #FiltroAgenciaTramite, #FiltroAgenciaReclamante").multiselect();
			
			$("#FiltroNomReclamante, #FiltroNombreProducto, #FiltroUsuarioActividad, #FiltroTipologiaTramite").multiselect({minWidth:360}).multiselectfilter();
					
			CalendarioFechas("FiltroDesdeFecReal", "FiltroHastaFecReal");
			CalendarioFechas("FiltroDesdeSistema", "FiltroHastaSistema");
			CalendarioFechas("FiltroDesdeLimite", "FiltroHastaLimite");
			CalendarioFechas("FiltroDesdeCierre", "FiltroHastaCierre");
			
		});		
		
		function CalendarioFechas(Desde, Hasta){			
			$( "#"+Desde ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#"+Hasta ).datepicker( "option", "minDate", selectedDate );
				}
			});
			
			$( "#"+Hasta ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#"+Desde ).datepicker( "option", "maxDate", selectedDate );
				}
			});
		}
		
		function tables(Tramite){
			$("#GridTramites").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'ConsultaTramites' } ,
							  { name: 'Tramite', value: Tramite }				
							],
				colModel : [ 
							 { display: 'No Tramite', name : 'rad.id_radicacion', width : 50, sortable : true, align: 'center', }, 
							 { display: 'No. Pre - asignado', name : 'rad.id_radicacion', width : 100, sortable : true, align: 'center', }, 
							 { display : 'Fecha y hora limite',	name : 'his.fechahora_limite', width : 120, sortable : true, align : 'center'}, 
							 { display : 'Actividad pendiente', name : 'his.actividad', width : 100, sortable : true, align : 'left'}, 
							 { display : 'Tiempo restante',	name : 'tiemporestante', width : 120, sortable : false, align : 'left'}, 
							 { display : 'Usuario actividad',	name : 'nombres', width : 280, sortable : true, align : 'left'},
							 { display : 'Proceso',	name : 'pro.proceso_desc', width : 200, sortable : true, align : 'left'},
							 { display : 'Servicio',	name : 'ser.desc_servicio', width : 100, sortable : true, align : 'left'},
							 { display : 'Compañia',	name : 'com.des_compania', width : 100, sortable : true, align : 'left'},
							 { display : 'Tipo tramite',	name : 'tit.desc_tipotramite', width : 100, sortable : true, align : 'left'},
							 { display : '',	name : 'semaforo', width : 2, sortable : true, align : 'left'}						  
						   ],
				buttons : [ { name : '<img src="images/filter.png" border="0" width="15px"/> Filtros', onpress : MuestraFiltros }, 
							{ separator : true} ,
							{name: '<img src="images/excel.png" width="15px" border="0"> Exportar Excel', onpress : ExportExcel},
							{separator: true},
							{name: '<img src="images/excel.png" width="15px" border="0"> Informe Específico', onpress : ExportExcelSin},
							{separator: true},
							{name: '<img src="images/excel.png" width="15px" border="0"> Informe Seguimiento', onpress : ExportExcelSeg},
							{separator: true}
						  ],
				sortname : "semaforo",
				sortorder : "desc",				
				title : 'consulta general de tramites',
				width :($(window).width()-100),
				height :  ($(window).height()-230),
				resizable:false,
				usepager : true,
				showToggleBtn : false,
				rp:50,
				onSuccess:Semaforo
			});			
		}
		
		function ExportExcel(){
			var datos ="";
			
			datos += "consulta";
			datos += "=ConsultaTramites&";
			
			
			$(".customers :input").each(function(){
				if($(this).val() != null && $(this).val()!=''){
					datos += $(this).attr("id"); 
					if($(this).attr("multiple") == 'multiple')
						datos +="="+ JSON.stringify($(this).val())+"&";
					else
						datos +="="+$(this).val()+"&";
				}
			});
			
			document.location.href ="Workflow/ConsultaTramitesExportaExcel.php?"+datos;		
		}

		function ExportExcelSin(){
			var datos ="";
			
			datos += "consulta";
			datos += "=ConsultaTramites&";
			
			
			$(".customers :input").each(function(){
				if($(this).val() != null && $(this).val()!=''){
					datos += $(this).attr("id"); 
					if($(this).attr("multiple") == 'multiple')
						datos +="="+ JSON.stringify($(this).val())+"&";
					else
						datos +="="+$(this).val()+"&";
				}
			});
			
			document.location.href ="Workflow/ConsultaTramitesExportaExcelSin.php?"+datos;		
		}	

		function ExportExcelSeg(){
			var datos ="";
			
			datos += "consulta";
			datos += "=ConsultaTramitesSeg&";
			
			
			$(".customers :input").each(function(){
				if($(this).val() != null && $(this).val()!=''){
					datos += $(this).attr("id"); 
					if($(this).attr("multiple") == 'multiple')
						datos +="="+ JSON.stringify($(this).val())+"&";
					else
						datos +="="+$(this).val()+"&";
				}
			});
			
			document.location.href ="Workflow/ConsultaTramitesExportaExcelSin.php?"+datos;		
		}				
		
		
		function CambiaFiltros(){
			var datos = [];
			
			var myObject = new Object();
			myObject.name = "consulta";
			myObject.value = "ConsultaTramites";
			datos.push(myObject);
			
			$(".customers :input").each(function(){
				if($(this).val() != null && $(this).val()!=''){
					var myObject = new Object();
					myObject.name = $(this).attr("id"); 
					if($(this).attr("multiple") == 'multiple')
						myObject.value = JSON.stringify($(this).val());
					else
						myObject.value = $(this).val();
					datos.push(myObject);
				}
			});
			//console.log(datos);
			
			$("#GridTramites").flexOptions(
				{params:datos} 
			); 
			$('#GridTramites') .flexOptions({ newp: 1 }).flexReload();
			$( "#dialog" ).dialog( "close" );
		}	
		
		function MuestraTramite(IdTramite){
			var datosGet="";
			$(".customers :input").each(function(){
				if($(this).val() != null && $(this).val()!=''){
					datosGet+="&"+$(this).attr("id")+"="+$(this).val();
				}
			});
		
			 location.href='<?=$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]?>'+'&Tramite='+IdTramite+datosGet;					
		}
		
		function MuestraFiltros(){
			 $( "#dialog" ).dialog( "open" );
		}
    </script>

	<style>			
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
			font-size:11px;
		}
		.filtros input[type=text], .filtros select{
			margin:2px 2px;
			display:inline;		
			padding: .3em;	
			font-size: 12px;
		}
		.DatosCliente{
			font-size: 12px;
			font-family: Verdana;
			border-color: #BDBDBD;
		}
		.DatosCliente td, th {
			padding: 3px;
		}
		
		.DatosCliente th{
			background-color: #EFF2FB;
		}
		table a{
			color: #0101DF;
			font-weight: bold;
			text-decoration:none;
		}
		table a:hover{
			color: #819FF7;
		}
		.TableRadicado th{
			text-align:left;
			padding-right: 10px ;
			color:#08088A;
		}
		.TableRadicado b{			
			padding: 3px 15px;
			color:#424242;
		}
		.Activo{ color: #00891B;}
		
		.Vencido{color: #BA0000;}
		
		.customers{
			font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
			border-collapse:collapse;
		}
		
		.customers td, .customers th {
			font-size:1.2em;
			border:1px solid #98bf21;
			padding:3px 7px 2px 7px;
		}
		
		.customers th {
			font-size:0.9em;
			text-align:left;
			padding-top:5px;
			padding-bottom:4px;
			background-color:#A7C942;
			color:#fff;
		}
		
		.customers tr.alt td {
			color:#000;
			background-color:#EAF2D3;
		}
	</style>
</head>
<body>

<div id="dialog" title="Filtros consulta general" style="display:none">
	<table align='center' class="customers">
		<tr><th>
			Filtros del tramite
		</th></tr>
		<tr><td>
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>No Tramite: </b></legend>		
					<input type="text" id="FiltroTramite" style='width:70px' class="text ui-widget-content ui-corner-all" />
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Pre - asignado: </b></legend>		
					<input type="text" id="FiltroPreasignado" style='width:120px' class="text ui-widget-content ui-corner-all"/>
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Estado: </b></legend>	
					<select id="FiltroEstado" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Tramites en semaforo: </b></legend>	
					<select id="FiltroSemaforoTramite" multiple="multiple" class="text ui-widget-content ui-corner-all">
						<option value="1">Asignados (Verde)</option>
						<option value="2">Proximos a vencer (Amarillo)</option>
						<option value="3">Vencidos (Rojo)</option>
						<option value="0">Terminados (Negro)</option>
					</select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Agencia que tramita: </b></legend>	
					<select id="FiltroAgenciaTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Fecha real radicacion (YYYYMMDD): </b></legend>							
				<input type="text" id="FiltroDesdeFecReal"  style='width:70px' class="text ui-widget-content ui-corner-all"/>			
				<input type="text" id="FiltroHastaFecReal" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
				<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
			</fieldset>
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Fecha ingreso sistema (YYYYMMDD): </b></legend>							
				<input type="text" id="FiltroDesdeSistema"  style='width:70px' class="text ui-widget-content ui-corner-all"/>			
				<input type="text" id="FiltroHastaSistema" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
				<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
			</fieldset>
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Fecha limite tramite (YYYYMMDD): </b></legend>							
				<input type="text" id="FiltroDesdeLimite"  style='width:70px' class="text ui-widget-content ui-corner-all"/>			
				<input type="text" id="FiltroHastaLimite" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
				<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
			</fieldset>
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Fecha de cierre (YYYYMMDD): </b></legend>							
				<input type="text" id="FiltroDesdeCierre"  style='width:70px' class="text ui-widget-content ui-corner-all"/>			
				<input type="text" id="FiltroHastaCierre" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
				<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
			</fieldset>
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Aseguradora: </b></legend>
					<select id="FiltroAseguradoraTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Proceso: </b></legend>
					<select id="FiltroProcesoTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Tipo tramite: </b></legend>
					<select id="FiltroTipoTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Servicio: </b></legend>
					<select id="FiltroServicioTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Tipologia: </b></legend>
					<select id="FiltroTipologiaTramite" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
		</td></tr>
		
		<tr><th>
			Filtros del reclamante
		</th></tr>
		<tr><td>
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Documento Reclamante: </b></legend>		
					<input type="text" id="FiltroDocReclamante" style='width:150px' class="text ui-widget-content ui-corner-all"/>
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Nombre reclamante: </b></legend>
					<select id="FiltroNomReclamante" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Ciudad reclamante: </b></legend>
					<select id="FiltroCiuReclamante" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Agencia que recibe: </b></legend>
					<select id="FiltroAgenciaReclamante" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
		</td></tr>
		
		<tr><th>
			Filtros del producto
		</th></tr>
		<tr><td>
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Poliza: </b></legend>		
					<input type="text" id="FiltroPolizaProducto" style='width:150px' class="text ui-widget-content ui-corner-all"/>
			</fieldset>	
					
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Nombre producto: </b></legend>
					<select id="FiltroNombreProducto" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Aseguradora: </b></legend>
					<select id="FiltroAseguradoraProducto" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
			</fieldset>	
		</td></tr>
		
		<tr><th>
			Filtros del proceso
		</th></tr>
		<tr><td>
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Actividad pendiente: </b></legend>		
					<select id="FiltroPendienteActividad" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
			</fieldset>	
			
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Actividades en semaforo: </b></legend>		
					<select id="FiltroSemaforoActividad" multiple="multiple" class="text ui-widget-content ui-corner-all">
						<option value="1">Asignados (Verde)</option>
						<option value="2">Proximos a vencer (Amarillo)</option>
						<option value="3">Vencidos (Rojo)</option>
					</select>	
			</fieldset>	
					
			<fieldset style="height:50px;" class='filtros'>
				<legend><b>Usuario actividad pendiente: </b></legend>
				<select id="FiltroUsuarioActividad" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
			</fieldset>	
		</td></tr>
	</table>
</div>

<table align="center"><tr><td><table id="GridTramites"></table></td></tr></table>

<?=MuestraPostRadicacion($_REQUEST['Tramite'])?>

<?=Guardareasigna()?>
<?=GuardaDevolucion()?>
<?= OpcionesSelect('FiltroEstado', 'wf_radicacion', 'estado', 'estado', "group by estado")?>
<?= OpcionesSelect('FiltroNomReclamante', 'wf_radicacion', 'upper(nombre)', 'upper(nombre)', "group by nombre")?>
<?= OpcionesSelect('FiltroCiuReclamante', 'wf_radicacion rad, tblciudades ciu', 'rad.id_ciudad', 'ciu.ciudad', " where rad.id_ciudad=ciu.idciudad group by rad.id_ciudad, ciu.ciudad")?>
<?= OpcionesSelect('FiltroAgenciaReclamante', 'wf_radicacion rad, tblradofi ofi', 'rad.id_agencia', 'ofi.descrip', " where rad.id_agencia=ofi.codigo group by rad.id_agencia, ofi.descrip")?>
<?= OpcionesSelect('FiltroAgenciaTramite', 'wf_radicacion rad, tblradofi ofi, wf_tipologia tip', 'tip.id_agencia', 'ofi.descrip', " where tip.id_agencia=ofi.codigo and rad.id_tipologia=tip.id_tipologia group by tip.id_agencia, ofi.descrip")?>
<?= OpcionesSelect('FiltroNombreProducto', 'wf_radicacion rad, wf_producto pro', 'pro.descripcion', 'pro.descripcion', " where rad.id_producto=pro.id_producto group by pro.descripcion")?>
<?= OpcionesSelect('FiltroAseguradoraProducto', 'wf_radicacion rad, wf_producto pro', 'pro.compania', " pro.compania", " where rad.id_producto=pro.id_producto and pro.compania is not null group by compania")?>
<?= OpcionesSelect('FiltroAseguradoraTramite', 'wf_compania', 'id_compania', "des_compania", " where id_compania!=0")?>
<?= OpcionesSelect('FiltroPendienteActividad', 'wf_historial his', 'his.actividad', "his.actividad", " where his.fechahora is null group by his.actividad")?>
<?= OpcionesSelectOrder('FiltroUsuarioActividad', 'adm_usuario usu, wf_historial his', 'DISTINCT(his.usuario_cod)', "(COALESCE(usuario_nombres,'') || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'')) as nombres", " where his.usuario_cod=usu.usuario_cod and  fechahora is null", "nombres")?>
<?= OpcionesSelect('FiltroProcesoTramite', 'wf_proceso', 'id_proceso', "proceso_desc", "where id_proceso!=0")?>
<?= OpcionesSelect('FiltroTipoTramite', 'wf_tipotramite', 'desc_tipotramite', "desc_tipotramite", "group by desc_tipotramite")?>
<?= OpcionesSelect('FiltroServicioTramite', 'wf_servicio', 'id_servicio', "desc_servicio", "")?>
<?= OpcionesSelect('FiltroTipologiaTramite', 'wf_tipologia', 'desc_tipologia', "desc_tipologia", "group by desc_tipologia")?>


</body>
 </html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$result=queryQR("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = $result->FetchRow()){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	return $salida;
}

function OpcionesSelectOrder($IdSelect, $Tabla, $Id, $Value, $Extra, $Order){
	$salida="";
	$result=queryQR("select $Id, $Value from $Tabla $Extra order by $Order");
	while ($row = $result->FetchRow()){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	return $salida;
}

function MuestraPostRadicacion($Id){
	$salida ="";
	
	if( $Id == NULL){
		$salida.="<script>tables('".$_REQUEST['FiltroTramite']."'); CambiaFiltros();</script>";
		return $salida;
	}else{
		MuestraDetalles($Id, $_SESSION['uscod'], $_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"], 'ConsultaTramites');

	}
	return;
}
?>
