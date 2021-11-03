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
		
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>

	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			$('button').button();
			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php',
				postData: {
				 	query: 'ConsultaFactura',
				   	estado: ['Recibir en el área', 'Recibir corrección radicación'],
				    usuarioPend: 'login',
				    columns:['','serial_factura','fecha_insfac', 'status', 'proveedor', 'no_factura', 'valor_fac', 'area_desc', 'desc_documento']
				},
				datatype: "json",
				height: ($(window).height()-260),
				width: ($(window).width()-280),
				colNames:['', 'Tramite','Fecha radicado', 'Estado pendiente', 'Proveedor', 'No factura', 'Valor','Area destino','Tipo documento'],
				colModel:[
					{name:'LinkRecibe', width:2, sortable:false, search:false},
					{name:'serial_factura',index:'serial_factura', width:10, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'rad.fechahora_ins',index:'rad.fechahora_ins', width:10, align:"center", search:false},
					{name:'status',index:'status', width:10, search:false},
					{name:'proveedor',index:'proveedor', width:20},
					{name:'no_factura',index:'no_factura', width:10},	
					{name:'valor_fac',index:'valor_fac', width:10, search:false},	
					{name:'area_desc',index:'area_desc', width:18, search:false},		
					{name:'desc_documento',index:'desc_documento', width:10, search:false}					
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
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").click(function (){RecibeTramtie($(this).parent().attr("id"));})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").html('<img src="images/recibir.png" width="15px" border="0" style="padding-left:5px"/>');
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").css('cursor', 'pointer');
				}
			});
			$("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});
			jQuery("#ListRadica").jqGrid('filterToolbar',{searchOperators : false});
			
			$("#Ir").click(function (){RecibeTramtie($("#NumTramite").val())});
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
				
		function RecibeTramtie(tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/escritorio/recibe.php",
				data: { tramite: tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});	
		};
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
		
		#ListRadica .ui-state-hover a{
			color: white;
		}
		.ui-state-highlight td{
			background-color: #FFFFAB;
		}

		.ui-dialog {background: #FFFfff;}
		
		#ListRadica a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#ListRadica a:hover{text-decoration: underline}
	</style>

</head>
<body>
	<table align="center">
			<td>
				<table id="ListRadica"></table>
				<div id="PagerListRadica"></div>
			</td>
		</tr>
	</table>
</body>
</html>