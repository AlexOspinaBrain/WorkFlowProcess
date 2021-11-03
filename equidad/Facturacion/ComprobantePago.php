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
	<script src="js/jquery.maskMoney.js" type="text/javascript"></script>
	<script src="js/handlebars.js" type="text/javascript"></script>
	<script src="js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			$("#GeneraCP").button({
				icons: {
					primary: "ui-icon-plus"
				},
				text:"Radica nuevo"
			}).click(function(){GenComprobantePango('')});

			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaOrdenGiro',
				    estado: ['Generar CP'],
				    usuario_cod: 'login',
				    columns:['','num_ordengiro', 'fecha_ins','proveedor', 'usuario', 'estado', 'cant_fac']
				},
				height: ($(window).height()-260),
				width:($(window).width()*0.6)-50,
				colNames:['','No orden de giro','Fecha creación','Proveedor', 'Usuario', 'Estado', 'Cantidad'],
				colModel:[
					{name:'devuelveOG', width:2, sortable:false,search:false},
					{name:'num_ordengiro',index:'num_ordengiro', width:8, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'ord.fecha_ins',index:'ord.fecha_ins', width:15, align:"center", search:false},
					{name:'proveedor',index:'proveedor', width:35, align:"left"},
					{name:'usuario',index:'usuario', width:20,search:false},
					{width:15, search:false},
					{name:'cant_fac',index:'cant_fac', width:5, search:false}
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListRadica',
				sortname: 'ord.fecha_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Ordenes de giro pendientes por generar comprobante de pago",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListRadica td[aria-describedby='ListRadica_num_ordengiro'] a").click(function (){DetallesOrdenGiro($(this).text());})
					$("#ListRadica td[aria-describedby='ListRadica_devuelveOG']").click(function (){Devolucion($(this).parent().attr("id"));})
					$("#ListRadica td[aria-describedby='ListRadica_devuelveOG']").html('<img src="images/prev.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListRadica td[aria-describedby='ListRadica_devuelveOG']").css('cursor', 'pointer');
				}
			});
			jQuery("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});
			jQuery("#ListRadica").jqGrid('filterToolbar',{searchOperators : false});

			$("#ListCP").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaCP',
				 	usuario_ord: true,
				    columns:['corregir', 'num_comprobante','fecha_ins', 'proveedor'],
				    estado: ['Corrección Comprobante de pago', 'Recibir Auditoria'],
				},
				height: ($(window).height()-240),
				width: ($(window).width()*0.4)-50,
				colNames:['','No comprobante','Fecha creación', 'Proveedor'],
				colModel:[
					{name:'LinkCorrige', width:5, sortable:false},
					{name:'num_comprobante',index:'num_comprobante', width:15, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'fecha_ins',index:'fecha_ins', width:20, align:"center"},
					{name:'usuario',index:'usuario', width:35},
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListCP',
				sortname: 'corregir',
				viewrecords: true,
				sortorder: "asc",
				caption:"Comprobantes de pago generados",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#search_ListOrdenGiro").hide();
					$("#ListCP td[aria-describedby='ListCP_num_comprobante'] a").click(function (){DetallesCP($(this).text());})
					$("#ListCP td[aria-describedby='ListCP_LinkCorrige']:contains('Corregir')").click(function (){GenComprobantePango($(this).parent().attr("id"));})
					$("#ListCP td[aria-describedby='ListCP_LinkCorrige']:contains('Corregir')").html('<img src="images/pencil_red.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListCP td[aria-describedby='ListCP_LinkCorrige']").css('cursor', 'pointer');
				}
			});
			$("#ListCP").jqGrid('navGrid','#PagerListCP',{del:false,add:false,edit:false});
			
			$("#Ir").click(function (){GenCausacion($("#NumTramite").val())});
			$("#NumTramite").keypress(function(e) {	if(e.which == 13) {	GenCausacion($("#NumTramite").val())}});
		});


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
				
		function GenComprobantePango(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/ComprobantePago/genCP.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});
		};

		function Devolucion(tramite){
			var tipo_tramite = "Orden de giro";
			$.ajax({
				type: "POST",
				url: "Facturacion/views/devolucion/devuelve.php",
				data: { Tramite: tramite, tipo_tramite:tipo_tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});	
		}

		function DetallesCP(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/ComprobantePago/detalle.php",
				data: { tramite: Tramite},
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
		
		#ListRadica .ui-state-hover a, #ListCP .ui-state-hover a{
			color: white;
		}
		.ui-state-highlight td{
			background-color: #FFFFAB;
		}
		
		#ListRadica a, #ListCP a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListRadica a:hover, #ListCP a:hover{text-decoration: underline}
	</style>

</head>
<body>
	<table align="center">
		<tr>
			<td>
				<button id="GeneraCP">Genera nuevo CP</button>
				<table id="ListRadica"></table>
				<div id="PagerListRadica"></div>
			</td>

			<td style="vertical-align: bottom;">
				<table id="ListCP"></table>
				<div id="PagerListCP"></div>
			</td>
		</tr>
	</table>
</body>
</html>