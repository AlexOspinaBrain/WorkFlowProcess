	<?php
	require_once ('config/ValidaUsuario.php');
	require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.core.js"></script>
	<script src="js/ui/js/jquery.ui.widget.js"></script>
	<script src="js/ui/js/jquery.ui.mouse.js"></script>
	<script src="js/ui/js/jquery.ui.draggable.js"></script>
	<script src="js/ui/js/jquery.ui.position.js"></script>
	<script src="js/ui/js/jquery.ui.resizable.js"></script>
	<script src="js/ui/js/jquery.ui.dialog.js"></script>
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script>
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/handlebars.js"></script>
		
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.multiselect.js"></script>
	<script type="text/javascript" src="js/jquery.multiselect.filter.js"></script>
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.filter.css" />
	
	<script type="text/javascript">
		$(document).ready(function() {				
			$("#ButtonFiltrosFactura").button({icons: {primary: "ui-icon-search"}}).click(function(){
				$( "#window_consulta_fac" ).dialog('open');
				setSize();
			});

			$("#ButtonExportaExcel").button({icons: {primary: "ui-icon-circle-arrow-s"}}).click(ExportExcel);

			$( "#window_consulta_fac" ).dialog({
				autoOpen: false,
				width: ($(window).width()-200),
				height : 'auto',
				closeOnEscape: true,
				resizable: false,    
	      		modal: true,
				buttons: {
					Filtrar: function() {
						FiltraFac();
					},
					"Borrar filtros": function() {
						$("#reset").click();						
					}
				},
			});		

			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php?query=ConsultaFactura',
				datatype: "json",
				height: ($(window).height()-290),
				width: ($(window).width()-280),
				colNames:['Tramite','Fecha radicado', 'Estado pendiente', 'Proveedor', 'No factura','Area destino','Tipo documento'],
				colModel:[
					{name:'serial_factura',index:'serial_factura', width:55, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'rad.fechahora_ins',index:'rad.fechahora_ins', width:60, align:"center"},
					{name:'status',index:'status', width:100},
					{name:'proveedor',index:'proveedor', width:130},
					{name:'no_factura',index:'no_factura', width:80},		
					{name:'area_desc',index:'area_desc', width:120},		
					{name:'desc_documento',index:'desc_documento', width:60}		
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListRadica',
				sortname: 'rad.fechahora_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Facturas y/o Cuentas de cobro por recibir",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListRadica td[aria-describedby='ListRadica_serial_factura'] a").click(function (){Detalles($(this).text());})
				}
			});
			jQuery("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});

			$("#window_consulta_fac input").keypress(function(e) {
				if(e.which == 13) 
					FiltraFac();		
			});			

			CalendarioFechas("FiltroDesdeFecRad", "FiltroHastaFecRad");
			CalendarioFechas("FiltroDesdeExp", "FiltroHastaExp");
			CalendarioFechas("FiltroDesdeVen", "FiltroHastaVen");
			$("#FiltroTipoDoc, #Aseguradora, #Agencia, #Area, #Actividad, #UsuarioPen").multiselect();

		});

		function FiltraFac(){
			var data = {};
		   	var arreglo = $("#formFiltrosFac").serializeArray();
		   	$.each(arreglo, function() {
		       	if (data[this.name]) {
		           	if (!data[this.name].push) {
		               	data[this.name] = [data[this.name]];
		           	}
		           	data[this.name].push(this.value || '');
		       	} else {
		           	data[this.name] = this.value || '';
		       	}
		   	});

			$("#ListRadica").setGridParam({ postData: data});
			$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid");
			$( "#window_consulta_fac" ).dialog( "close" );
		}

		function Detalles(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/radicar/detalle.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});		
		}

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

		function setSize(){
			var height =  $("#window_consulta_fac").height()+80;
			if(height > $(window).height ()){
				$("#window_consulta_fac").css('height',$(window).height()-130);
			}	
		}

		function ExportExcel(){
			if($("#FiltroDesdeFecRad").val()!="" && $("#FiltroHastaFecRad").val()!=""){
				var start = $('#FiltroDesdeFecRad').datepicker('getDate');
    			var end = $('#FiltroHastaFecRad').datepicker('getDate'); 
    			var days = (end - start) / 1000 / 60 / 60 / 24;
    			if(days<=31)
    				document.location.href ="Facturacion/ConsultaFacturaExcel.php?"+$("#formFiltrosFac").serialize();		
    			else
    				alert("Por favor seleccione un rango menor a un mes");
			}else{
				alert("Por favor seleccione un rango de fechas de radicación");
			}
			/*if($("#ListRadica").jqGrid('getGridParam', 'records') < 1000){				
				document.location.href ="Facturacion/ConsultaFacturaExcel.php?"+$("#formFiltrosFac").serialize();		
			}else{
				alert('Por favor realize un filtro con menos de 1000 resultados');
				return false;
			}*/		
		}
	</script>

	<style>	
	    .altClassrow td{
			background-color: #EAF2D3;
		}
		
		.ui-state-hover td{
			background-color: #327E04;
		}
				
		.ui-state-highlight td{
			background-color: #FFFFAB;
		}
		
		#ListRadica .ui-state-hover a{color: white;}		
		#ListRadica a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListRadica a:hover{text-decoration: underline}

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
	</style>

</head>
<body>
	<table align="center">
		<tr>
			<td>
				<button id="ButtonFiltrosFactura">Filtros</button>
				<button id="ButtonExportaExcel">Exporta excel</button>
				<table id="ListRadica"></table>
				<div id="PagerListRadica"></div>
			</td>
		</tr>
	</table>

	<div id="window_consulta_fac" title="Filtros Facturas y/o Cuentas de cobro" style="display:none">	
		<form id="formFiltrosFac">	
		<table class="TblGreen " style="padding:10px" align="center">
			<tr>
				<td class="alt">Datos de la factura o cuenta de cobro</td>
			</tr>
			<tr>
				<td>
					<fieldset style="height:50px;" class='filtros'>
						<legend><b>No Tramite: </b></legend>		
						<input type="text" name="serial_factura" id="serial_factura" style='width:100px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	


					<fieldset style="height:50px;" class='filtros'>
						<legend><b>No factura: </b></legend>		
						<input type="text" name="no_factura" id="no_factura" style='width:100px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Valor factura: </b></legend>		
						<input type="text" name="ValorFacturaDesde" id="ValorFacturaDesde" style='width:60px' class="text ui-widget-content ui-corner-all" />
						<input type="text" name="ValorFacturaHasta" id="ValorFacturaHasta" style='width:60px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Orden de giro: </b></legend>		
						<input type="text" name="num_ordengiro" id="num_ordengiro" style='width:100px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Comprobante de pago: </b></legend>		
						<input type="text" name="num_comprobante" id="num_comprobante" style='width:100px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Fecha radicación (YYYYMMDD): </b></legend>							
						<input type="text" name="FiltroDesdeFecRad" id="FiltroDesdeFecRad" style='width:70px' class="text ui-widget-content ui-corner-all"/>			
						<input type="text" name="FiltroHastaFecRad" id="FiltroHastaFecRad" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
						<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
					</fieldset>

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Tipo documento: </b></legend>
							<select id="FiltroTipoDoc" name="FiltroTipoDoc" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>	
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Fecha expedición (YYYYMMDD): </b></legend>							
						<input type="text" name="FiltroDesdeExp" id="FiltroDesdeExp" style='width:70px' class="text ui-widget-content ui-corner-all"/>			
						<input type="text" name="FiltroHastaExp" id="FiltroHastaExp" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
						<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
					</fieldset>

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Fecha vencimiento (YYYYMMDD): </b></legend>							
						<input type="text" name="FiltroDesdeVen" id="FiltroDesdeVen" style='width:70px' class="text ui-widget-content ui-corner-all"/>			
						<input type="text" name="FiltroHastaVen" id="FiltroHastaVen" style='width:70px' class="text ui-widget-content ui-corner-all"/>	
						<div style="margin:-5px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -5px 110px;">Hasta</div>
					</fieldset>
				</td>	
			</tr>
			<tr>
				<td class="alt">Datos del proveedor</td>
			</tr>
			<tr>
				<td>
					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Identificación: </b></legend>		
						<input type="text" name="identificacion" id="identificacion" style='width:100px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Nombre proveedor: </b></legend>		
						<input type="text" name="NombrePro" id="NombrePro" style='width:180px' class="text ui-widget-content ui-corner-all" />
					</fieldset>	
				</td>		
			</tr>
			<tr>
				<td class="alt">Datos de destino de la factura o cuenta de cobro</td>
			</tr>
			<tr>
				<td>
					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Aseguradora: </b></legend>		
						<select id="Aseguradora" name="Aseguradora" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Agencia: </b></legend>		
						<select id="Agencia" name="Agencia" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Área: </b></legend>		
						<select id="Area" name="Area" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
					</fieldset>	
				</td>		
			</tr>
			<tr>
				<td class="alt">Datos de estados</td>
			</tr>
			<tr>
				<td>
					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Estado: </b></legend>		
						<select id="Actividad" name="Actividad" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
					</fieldset>	

					<fieldset style="height:50px;" class='filtros'>
						<legend><b>Usuario actividad pendiente: </b></legend>		
						<select id="UsuarioPen" name="UsuarioPen" multiple="multiple" class="text ui-widget-content ui-corner-all"></select>
					</fieldset>	
				</td>		
			</tr>
		</table>
			<input type="reset" id="reset" style="display:none">	
		</form>
	</div>
	<?= OpcionesSelect('FiltroTipoDoc', 'fac_documento', 'id_documento', 'desc_documento', "")?>
	<?= OpcionesSelect('Actividad', 'fac_radica', 'estado', 'estado', "group by estado")?>
	<?= OpcionesSelect('UsuarioPen', 'fac_historial join adm_usuario using(usuario_cod)', 'usuario_cod', "COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') ", "where fecha_terminado is null group by usuario_cod, usuario_nombres, usuario_priape, usuario_segape")?>
	<?= OpcionesSelect('Aseguradora', 'wf_compania', 'id_compania', 'des_compania', "where id_compania!=0")?>
	<?= OpcionesSelect('Area', 'fac_radica rad join tblareascorrespondencia are on are.areasid=rad.id_area', 'areasid', 'area', "group by are.areasid, are.area")?>
	<?= OpcionesSelect('Agencia', 'fac_radica rad join tblareascorrespondencia are on are.areasid=rad.id_area join tblradofi age on are.agencia=age.codigo', 'codigo', 'descrip', "group by age.codigo, age.descrip")?>
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
?>