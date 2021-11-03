<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	
	<script type="text/javascript">
		
		
        $(document).ready(function() {	
			CalendarioFechas("Desde", "Hasta");
			tables();
			
			$('.filtros input:text').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});

			$('.filtros select').change(function() {		
				CambiaFiltros();				
			});
		});
		
		function tables(){
			$("#Gridproductos").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'InformeOportunidad' } ,
							  { name:'TipoInforme', value:$('#TipoInforme').val()}
							],
				colModel : [ 
							 { display: 'Proceso', name : 'informe', width : 300, sortable : true, align: 'left', }, 
							 { display: 'Total<br>Tramies', name : 'total', width : 40, sortable : true, align: 'center' }, 												 
							 { display: 'Tramites<br>solucionados<br>oportunamente', name : 'in_estandar', width : 70, sortable : true, align: 'center' }, 												 
							 { display: 'Tramites<br>solucionados<br>no oportunamente', name : 'out_estandar', width : 90, sortable : true, align: 'center' }, 												 
							 { display: 'Tramites <br> pendientes', name : 'sin_solucion', width : 60, sortable : true, align: 'center' }, 												 
							 { display: 'Indicador<br>solucion', name : 'total', width : 50, sortable : true, align: 'center' },
							 { display: 'Indicador<br>oportunidad', name : 'total', width : 60, sortable : true, align: 'center' }											 
						   ],
				sortname : "informe",
				sortorder : "asc",				
				title : 'Informe de oportunidad',
				width : 800,
				height :  ($(window).height()-250),
				resizable:false,
				usepager : false,
				rp:999999
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
		
		function CambiaFiltros(){
			$("#Gridproductos").flexOptions(
			{params:[{ name: 'consulta', value: 'InformeOportunidad' }, 
					 { name:'Desde', value:$('#Desde').val()},
					 { name:'Hasta', value:$('#Hasta').val()},
					 { name:'TipoInforme', value:$('#TipoInforme').val()}
				]
			}); 

			$("#Gridproductos").flexReload();

			$("th[abbr='informe'] div").text($("#TipoInforme").val());
		}
    </script>
	
	<style>
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
		}
		input[type=text]{
		margin:0px 2px;
			display:inline;			
		}
	</style>
</head>
<body>
	<table align='center'><tr><td>
		<fieldset style=" height:40px;" class='filtros'>
			<legend><b>Informe por: </b></legend>		
				<select id="TipoInforme">
					<option value = "Proceso">Proceso</option>
					<option value = "Respuestas">Respuestas</option>
					<option value = "Agencia">Agencia</option>
					<option value = "Tipologia">Tipología</option>
					<option value = "Compania">Compañia</option>
				</select>
		</fieldset>
		
		<fieldset style="height:40px;" class='filtros'>
			<legend><b>Rango fechas (YYYYMMDD): </b></legend>							
			<input type="text" id="Desde"  style='width:70px' />			
			<input type="text" id="Hasta" style='width:70px' />	
			<div style="margin:-2px 20px; position: absolute;">Desde</div><div style="position: absolute; margin: -2px 110px;">Hasta</div>
		</fieldset>
				
		</fieldset>
		<input type="hidden" id="Tramite" name="Tramite"/>
		<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
			<br><button onClick="CambiaFiltros()">Buscar.. </button><br>	
		</fieldset>		
	</td></tr></table>

	<table align="center"><tr><td>	<table id="Gridproductos"></table>	</td></tr></table>
</body>
</html>