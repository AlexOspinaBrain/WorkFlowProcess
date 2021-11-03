<?php
	require_once ('config/ValidaUsuario.php');
	require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script src="js/ui/js/jquery.ui.core.js"></script>
	<script src="js/ui/js/jquery.ui.widget.js"></script>
	<script src="js/ui/js/jquery.ui.mouse.js"></script>
	<script src="js/ui/js/jquery.ui.draggable.js"></script>
	<script src="js/ui/js/jquery.ui.position.js"></script>
	<script src="js/ui/js/jquery.ui.resizable.js"></script>
	<script src="js/ui/js/jquery.ui.dialog.js"></script>
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script>
	<script src="js/ui/js/jquery.ui.button.js"></script>

	<script src="js/handlebars.js" type="text/javascript"></script>
	<script src="js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.multiselect.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.multiselect.filter.js"></script>
			
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.filter.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>

	<script src="js/codigobarras.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->

	<script type="text/javascript">
		$(document).ready(function() {	
			$("#GeneraOrden").button({
				icons: {
					primary: "ui-icon-plus"
				},
				text:"Radica nuevo"
			}).click(function(){GeneraOrden('')});
			
			tables();
		});
		
		function tables(){
			$("#ListPendientes").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaFactura',
				   	estado: ['Generar orden de giro'],
				    usuarioPend: 'login',
				    columns:['','serial_factura', 'no_factura', 'proveedor', 'desc_documento']
				},
				height: ($(window).height()-240),
				width: ($(window).width()*0.6)-50,
				colNames:[ '', 'Tramite','No. factura', 'Proveedor', 'Tipo documento'],
				colModel:[
					{name:'LinkRecibe', width:5, sortable:false, search:false},
					{name:'serial_factura',index:'serial_factura', width:10, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'no_factura',index:'no_factura', width:15},
					{name:'proveedor',index:'proveedor', width:55},
					{name:'desc_documento',index:'desc_documento', width:15, search:false},		
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListPendientes',
				sortname: 'rad.fechahora_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Facturas y/o Cuentas de cobro por generar orden de giro",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListPendientes td[aria-describedby='ListPendientes_serial_factura'] a").click(function (){Detalles($(this).text());});
					$("#ListPendientes td[aria-describedby='ListPendientes_LinkRecibe']").click(function (){Devolucion($(this).parent().attr("id"));})
					$("#ListPendientes td[aria-describedby='ListPendientes_LinkRecibe']").html('<img src="images/prev.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListPendientes td[aria-describedby='ListPendientes_LinkRecibe']").css('cursor', 'pointer');
					$("#search_ListPendientes").hide();
				}
			});
			$("#ListPendientes").jqGrid('navGrid','#PagerListPendientes',{del:false,add:false,edit:false});
			$("#ListPendientes").jqGrid('filterToolbar',{searchOperators : false});

			$("#ListOrdenGiro").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaOrdenGiro',
				 	usuario_ord: true,
				    columns:['corregir', 'num_ordengiro','fecha_ins', 'usuario', 'estado', 'cant_fac']
				},
				height: ($(window).height()-220),
				width: ($(window).width()*0.4)-50,
				colNames:['','No orden de giro','Fecha creación', 'Usuario', 'Estado', 'Cantidad'],
				colModel:[
					{name:'LinkCorrige', width:5, sortable:false},
					{name:'num_ordengiro',index:'num_ordengiro', width:15, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'ord.fecha_ins',index:'ord.fecha_ins', width:20, align:"center"},
					{name:'usuario',index:'usuario', width:35},
					{name:'estado',index:'estado', width:25},
					{name:'cant_fac',index:'cant_fac', width:5}
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListOrdenGiro',
				sortname: 'ord.fecha_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Ordenes de giro generadas",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#search_ListOrdenGiro").hide();
					$("#ListOrdenGiro td[aria-describedby='ListOrdenGiro_num_ordengiro'] a").click(function (){DetallesOrdenGiro($(this).text());})
					$("#ListOrdenGiro td[aria-describedby='ListOrdenGiro_LinkCorrige']:contains('Corregir')").click(function (){GeneraOrden($(this).parent().attr("id"));})
					$("#ListOrdenGiro td[aria-describedby='ListOrdenGiro_LinkCorrige']:contains('Corregir')").html('<img src="images/pencil_red.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListOrdenGiro td[aria-describedby='ListOrdenGiro_LinkCorrige']").css('cursor', 'pointer');
				}
			});
			$("#ListOrdenGiro").jqGrid('navGrid','#PagerListOrdenGiro',{del:false,add:false,edit:false});
		}
		
		function Detalles(Tramite){
			$.ajax({
				type: "GET",
				url: "Facturacion/views/radicar/detalle.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});		
		}

		function DetallesOrdenGiro(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/ordenGiro/detalle.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});		
		}
		
		function GeneraOrden(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/ordenGiro/create.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});		
		}

		function Devolucion(tramite){
			var tipo_tramite = $("#"+tramite+" td[aria-describedby='ListPendientes_desc_documento']").text();
			$.ajax({
				type: "POST",
				url: "Facturacion/views/devolucion/devuelve.php",
				data: { Tramite: tramite, tipo_tramite:tipo_tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});	
		}

		
	</script>

	<style>	
	    .formular label, .formular input { display:block; }
	    .formular input.text { margin-bottom:12px; width:95%; padding: .4em; }
	    .formular fieldset { padding:0; border:0; margin-top:25px; }
		.ui-autocomplete-loading { 
			background: white url('js/ui/css/base/images/ui-anim_basic_16x16.gif') right center no-repeat; 
		}
		.ui-autocomplete {
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
			height: 100px;
		}
	    .altClassrow td{
			background-color: #EAF2D3;
		}
		
		.ui-state-hover td{
			background-color: #327E04;
		}
				
		.ui-state-highlight td{
			background-color: #FFFFAB;
		}
		
		#ListPendientes .ui-state-hover a, #ListOrdenGiro .ui-state-hover a{	color: white;}		
		#ListPendientes a, #ListOrdenGiro a  {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListPendientes a:hover, #ListOrdenGiro a:hover{text-decoration: underline}
	</style>

</head>
<body>
	<table align="center">
		<tr>
			<td>
				<button id="GeneraOrden">Genera nueva orden de giro</button>
				<table id="ListPendientes"></table>
				<div id="PagerListPendientes"></div>
			</td>

			<td style="vertical-align: bottom;">
				<table id="ListOrdenGiro"></table>
				<div id="PagerListOrdenGiro"></div>
			</td>
		</tr>
	</table>
</body>
</html>