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

	<script src="js/handlebars.js" type="text/javascript"></script>
	<script src="js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.mask.min.js" type="text/javascript"></script>
	<script src="js/jquery.maskMoney.js" type="text/javascript"></script>
	<script src="js/date.js" type="text/javascript"></script>
		
	<link rel="stylesheet" href="css/template.css" type="text/css">
	<link rel="stylesheet" href="css/flexigrid.pack.css" ><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" >
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" >

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			$("#Radicanuevo").button({
				icons: {
					primary: "ui-icon-plus"
				},
				text:"Radica nuevo"
				}).click(function(){FormRadica()});

			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php',
				postData: {
				 	query: 'ConsultaFactura',
				    estado: ['Recibir en el área', 'Correción radicación'],
				    columns:['update','serial_factura','fecha_insfac', 'status', 'proveedor', 'no_factura', 'area_desc', 'desc_documento', 'elimina']
				},
				datatype: "json",
				height: ($(window).height()-220),
				width: ($(window).width()-250),
				colNames:['', 'Tramite','Fecha radicado', 'Estado pendiente', 'Proveedor', 'No factura','Area destino','Tipo documento',''],
				colModel:[
					{name:'LinkRecibe', width:15, sortable:false},
					{name:'serial_factura',index:'serial_factura', width:55, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'rad.fechahora_ins',index:'rad.fechahora_ins', width:60, align:"center"},
					{name:'status',index:'status', width:100},
					{name:'proveedor',index:'proveedor', width:130},
					{name:'no_factura',index:'no_factura', width:80},		
					{name:'area_desc',index:'area_desc', width:120},		
					{name:'desc_documento',index:'desc_documento', width:60},
					{name:'elimina',index:'elimina', width:15}		
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListRadica',
				sortname: 'update, rad.fechahora_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Facturas y/o Cuentas de cobro por recibir",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListRadica td[aria-describedby='ListRadica_serial_factura'] a").click(function (){Detalles($(this).text());})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']:contains('Corregir')").click(function (){FormRadica($(this).parent().attr("id"));})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']:contains('Corregir')").html('<img src="images/pencil_red.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").css('cursor', 'pointer');
					$("#ListRadica td[aria-describedby='ListRadica_elimina']:contains('Eliminar')").click(function (){FormElimina($(this).parent().attr("id"));})
					$("#ListRadica td[aria-describedby='ListRadica_elimina']:contains('Eliminar')").html('<img src="images/delete.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListRadica td[aria-describedby='ListRadica_elimina']").css('cursor', 'pointer');	
				}
			});
			jQuery("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});
			
		});
		

		
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
		
		function FormRadica(Tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/radicar/form.php",
				data: { tramite: Tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});		
		}

		function FormElimina(Tramite){

			var r = confirm("Esta seguro de eliminar este tramite?");
			if (r == true){
			    $.ajax({
					type: "POST",
					url: "Facturacion/config/elimina.php",
					data: { tramite: Tramite},
					success	:function (data){
						alert(data);
						window.location.href = "http://imagine.laequidadseguros.coop/equidad/principal?p=3-1";
					}
				});	
			}
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
		#ListRadica .ui-state-hover a{color: white;}
		#ListRadica a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListRadica a:hover{text-decoration: underline}
	</style>

</head>
<body>
	<table align="center">
		<tr>
			<td>
				<button id="Radicanuevo"> Radica nuevo</button>
				<table id="ListRadica"></table>
				<div id="PagerListRadica"></div>
			</td>
		</tr>
	</table>
</body>
</html>
