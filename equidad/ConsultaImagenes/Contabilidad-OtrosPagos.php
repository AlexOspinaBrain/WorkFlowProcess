<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Consulta.php'
paar correcta visualización
*/?>
<?php
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
			tables();
			$('#Ir').click(CambiaFiltros);
			$('#Borrar').click(function(){
				$('#Nombre, #Desde, #Hasta, #Transferencia, #Comprobante, #Cuenta, #Valor').val('');
				CambiaFiltros();
			});
			$('#Nombre, #Desde, #Hasta, #Transferencia, #Comprobante, #Cuenta, #Valor').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});
		});
		
		function tables(){
			$(".flexme3").flexigrid({
				url : 'ConsultaImagenes/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'OtrosPagos' }							  
							],
				colModel : [ { display : 'Nombre',	name : 'nombre', width : 300, sortable : true, align : 'left'}, 
							 { display : 'Fecha', name : 'fecha', width : 80, sortable : true, align : 'center'},
							 { display : 'Transferencia', name : 'transferencia', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Comprobante',	name : 'comprobante', width : 180, sortable : true,	align : 'left'}, 
							 { display : 'Banco', name : 'banco',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Cuenta', name : 'cuenta',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Valor', name : 'valor',	width : 100,	sortable : true, align : 'right'} ,
							 { display : 'Imagenes', width : 50,	align : 'center'} 
						   ],
				 buttons : [
					{name: '<img src="images/excel.png" width="15px" border="0"> Exportar Excel', onpress : ExportExcel},
					{separator: true}
				],
				sortname : "fecha",
				sortorder : "desc",
				usepager : true,
				useRp : true,
				title: "Otros pagos",
				rp : 50,
				width : ($(window).width()-20),
				height :  ($(window).height()-300)
			});
		}
	
		function CambiaFiltros(){
			$(".flexme3").flexOptions(
				{params:[{ name: 'consulta', value: 'OtrosPagos' }, 
					 { name:'Nombre', value:$('#Nombre').val()},
					 { name:'Desde', value:$('#Desde').val()},
					 { name:'Hasta', value:$('#Hasta').val()},
					 { name:'Transferencia', value:$('#Transferencia').val()},
					 { name:'Comprobante', value:$('#Comprobante').val()},
					 { name:'Cuenta', value:$('#Cuenta').val()},
					 { name:'Valor', value:$('#Valor').val()}					
					]
				} 
				); 
			$('.flexme3') .flexOptions({ newp: 1 }).flexReload();
		}	
		
		function ExportExcel(){
			parametros="consulta="+"OtrosPagos";
			parametros+="&Nombre="+$('#Nombre').val();
			parametros+="&Desde="+$('#Desde').val();
			parametros+="&Hasta="+$('#Hasta').val();
			parametros+="&Transferencia="+$('#Transferencia').val();
			parametros+="&Comprobante="+$('#Comprobante').val();
			parametros+="&Cuenta="+$('#Cuenta').val();
			parametros+="&Valor="+$('#Valor').val();
			
			document.location.href ="ConsultaImagenes/ExportaExcel.php?"+parametros;
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
<table align='center'><tr><td>
	<fieldset style="width:230px; height:50px;" class='filtros'>
		<legend><b>Nombre: </b></legend>			
		<input type="text" id="Nombre" style='width:220px'/>
	</fieldset>

	<fieldset style="width:150px;height:50px;" class='filtros'>
		<legend><b>Rango de fecha (YYYYMMDD): </b></legend>		
	<label style="display: inline-block;">		
		<input type="text" id="Desde" style='width:60px'/><br>
		Desde</label>
		<label style="display: inline-block;">	
		<input type="text" id="Hasta" style='width:60px'/><br>
		Hasta</label>
	</fieldset>
	
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Transferencia: </b></legend>			
		<input type="text" id="Transferencia" style='width:120px'/>
	</fieldset>

	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Comprobante: </b></legend>			
		<input type="text" id="Comprobante" style='width:120px'/>
	</fieldset>	
	
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Cuenta: </b></legend>			
		<input type="text" id="Cuenta" style='width:120px'/>
	</fieldset>	
	
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Valor: </b></legend>			
		<input type="text" id="Valor" style='width:120px'/>
	</fieldset>	
		
	<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
		<button type="button" id='Ir' style='vertical-align: bottom;'>Ir.. </button><br>
		<button type="button" id='Borrar' style='vertical-align: bottom;'>Borrar filtros </button>	
	</fieldset>	
	
</td></tr></table>

<table class="flexme3"></table>
<div id="dialog-modal" title="Recibir Correspondencia" style="display:none"></div>
<div id="MuestraDetalles"></div>
</body>
</html>