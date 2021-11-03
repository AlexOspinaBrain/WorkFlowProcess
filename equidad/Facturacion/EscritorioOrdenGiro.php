
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
			var estado;
			if('<?= $_REQUEST['estado']?>' == 'Por recibir'){
				estado = ['Recibir contabilidad', 'Recibir tesorería', 'Recibir corrección orden de giro', 'Recibir corrección causación'];
				titulo = "Ordenes de giro pendientes por recibir";
			}
//'', 'num_ordengiro','fecha_ins', 'usuario', 'estado', 'cant_fac'
			if('<?= $_REQUEST['estado']?>' == 'Por enviar'){
				estado = ['Enviar área contabilidad'];
				titulo = "Ordenes de giro pendientes por enviar";
			}

			$('button').button();
			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaOrdenGiro',
				    estado: estado,
				    addInitial: true,
				    usuarioPend:'login',
				    columns:['', 'num_ordengiro', 'fecha_ins','proveedor', 'usuario', 'estado', 'cant_fac']
				},
				height: ($(window).height()-210),
				width:($(window).width()-200),
				colNames:['', 'No orden de giro', 'Fecha creación', 'Proveedor', 'Usuario', 'Estado', 'Cantidad'],
				colModel:[
					{name:'LinkRecibe', width:3, sortable:false,search:false},
					{name:'num_ordengiro',index:'num_ordengiro', width:10, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'ord.fecha_ins',index:'ord.fecha_ins', width:10, align:"center",search:false},
					{name:'proveedor',index:'proveedor', width:20},
					{name:'usuario',index:'usuario', width:30,search:false},
					{name:'estado',index:'estado2', width:25, search:false},
					{name:'cant_fac',index:'cant_fac', width:5, search:false}
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListRadica',
				sortname: 'ord.fecha_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:titulo,
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListRadica td[aria-describedby='ListRadica_num_ordengiro'] a").click(function (){DetallesOrdenGiro($(this).text());})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").css('cursor', 'pointer');
					if('<?= $_REQUEST['estado']?>' == 'Por recibir'){
						$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").click(function (){RecibeOrden($(this).parent().attr("id"));})
						$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").html('<img src="images/recibir.png" width="15px" border="0" style="padding-left:5px"/>');
					
					}else{
						$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").click(function (){EnviaOrden($(this).parent().attr("id"));})
						$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").html('<img src="images/envia.png" width="15px" border="0" style="padding-left:5px"/>');
					}

				}
			});
			jQuery("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});
			$("#ListRadica").jqGrid('filterToolbar',{searchOperators : false});

			$("#Estado").change(function(){FiltraInfo()});
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
				
		function RecibeOrden(tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/escritorio/recibeOrden.php",
				data: { tramite: tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});
		};

		function EnviaOrden(tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/escritorio/enviaOrden.php",
				data: { tramite: tramite},
				success	:function (data){
					$( "body" ).append(data);	
				}
			});
		};

		function FiltraInfo(){
			$("#ListRadica").setGridParam({postData: {estado: $("#Estado").val()}, page:1}).trigger("reloadGrid")
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
		<tr>
			<td>
				<table id="ListRadica"></table>
				<div id="PagerListRadica"></div>
			</td>
		</tr>
	</table>
</body>
</html>