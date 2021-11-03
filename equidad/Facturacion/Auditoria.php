<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>

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
	<script src="js/handlebars.js"></script>
		
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	<script src="js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/redmond/jquery-ui-1.10.3.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css" />

	<script src="js/jqgrid/js/i18n/grid.locale-es.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {	
			$("#ListRadica").jqGrid({
				url:'Facturacion/config/json.php',
				datatype: "json",
				postData: {
				 	query: 'ConsultaCP',
				    estado: ['Auditoria'],
				    usuarioPend: 'login',
				    columns:['' ,'num_comprobante','fecha_ins', 'valor_cp', 'proveedor', 'usuario', 'estado', 'medio_pago', 'cant_ord'],
				    addInitial: true
				},
				height: ($(window).height()-220),
				width: ($(window).width()-200),
				colNames:['','No comprobante','Fecha creación',  'Valor CP', 'Proveedor', 'Usuario', 'Estado', 'Medio de pago', 'Cantidad'],
				colModel:[
					{name:'LinkRecibe', width:3, sortable:false, search:false},
					{name:'num_comprobante',index:'num_comprobante', width:5, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
					{name:'fecha_ins',index:'fecha_ins', width:10, align:"center", search:false},
					{name:'valor_cp',index:'valor_cp', width:5, search:false},
					{name:'proveedor',index:'proveedor', width:15},
					{name:'usuario',index:'usuario', width:30, search:false},
					{name:'estado',index:'status', width:15, search:false},
					{name:'medio_pago',index:'medio_pago', width:10, search:false},
					{name:'cant_ord',index:'cant_ord', width:5, search:false}
				],
				scroll:1,
				rowNum:50,
				pager: '#PagerListRadica',
				sortname: 'fecha_ins',
				viewrecords: true,
				sortorder: "desc",
				caption:"Comprobantes de pago pendientes por auditar",
				altRows:true,
				altclass:'altClassrow',
				gridComplete: function (){
					$("#ListRadica td[aria-describedby='ListRadica_num_comprobante'] a").click(function (){DetallesCP($(this).text());})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").click(function (){Auditar($(this).parent().attr("id"));})
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").html('<img src="images/good.png" width="15px" border="0" style="padding-left:5px"/>')
					$("#ListRadica td[aria-describedby='ListRadica_LinkRecibe']").css('cursor', 'pointer');
				}
			});
			$("#ListRadica").jqGrid('navGrid','#PagerListRadica',{edit:false,add:false,del:false});
			$("#ListRadica").jqGrid('filterToolbar',{searchOperators : false});
			
		});

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

		function Auditar(tramite){
			$.ajax({
				type: "POST",
				url: "Facturacion/views/auditoria/genAuditoria.php",
				data: { tramite: tramite},
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
		
		#ListRadica .ui-state-hover a{color: white;}		
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