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
				$('#Traslado, #Comprobante, #Banco, #Compania, #Desde, #Hasta, #Transferencia, #Valor, #Cuenta, #NroComprobante').val('');
				CambiaFiltros();
			});
			$('#Comprobante, #Desde, #Hasta, #Transferencia, #Valor, #Cuenta, #NroComprobante').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});
			
			$('#Traslado, #Banco, #Compania').change(function() {
				CambiaFiltros();				
			});
		});

		function tables(){
			$(".flexme3").flexigrid({
				url : 'ConsultaImagenes/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'Traslados' }							  
							],
				colModel : [ { display : 'Comprobante',	name : 'codint', width : 80, sortable : true, align : 'center'}, 
							 { display : 'Banco', name : 'banco', width : 130, sortable : true, align : 'left'},
							 { display : 'Compañia', name : 'compañia', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Fecha',	name : 'fecha', width : 80, sortable : true,	align : 'center'}, 
							 { display : 'Transferencia', name : 'transferencia',	width : 80, sortable : true, align : 'center'}, 
							 { display : 'Valor', name : 'valor',	width : 150, sortable : true, align : 'right'}, 
							 { display : 'Cuenta', name : 'cuenta',	width : 100,	sortable : true, align : 'center'} ,
							 { display : 'Nro Comprobante', name : 'comprobante',	width : 100,	sortable : true, align : 'center'} ,
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
				title: "Traslados",
				rp : 50,
				width : ($(window).width()-20),
				height :  ($(window).height()-370)
			});
		}
	
		function CambiaFiltros(){
			$(".flexme3").flexOptions(
				{params:[{ name: 'consulta', value: 'Traslados' }, 
					 { name:'Traslado', value:$('#Traslado').val()},
					 { name:'Comprobante', value:$('#Comprobante').val()},
					 { name:'Banco', value:$('#Banco').val()},
					 { name:'Compania', value:$('#Compania').val()},
					 { name:'Desde', value:$('#Desde').val()},
					 { name:'Hasta', value:$('#Hasta').val()},
					 { name:'Transferencia', value:$('#Transferencia').val()},
					 { name:'Valor', value:$('#Valor').val()},
					 { name:'Cuenta', value:$('#Cuenta').val()},
					 { name:'NroComprobante', value:$('#NroComprobante').val()}					
					]
				} 
				); 
			$('.flexme3') .flexOptions({ newp: 1 }).flexReload();
		}	
		
		function ExportExcel(){
			parametros="consulta="+"Traslados";
			parametros+="&Comprobante="+$('#Comprobante').val();
			parametros+="&Banco="+$('#Banco').val();
			parametros+="&Compania="+$('#Compania').val();
			parametros+="&Desde="+$('#Desde').val();
			parametros+="&Hasta="+$('#Hasta').val();
			parametros+="&Transferencia="+$('#Transferencia').val();
			parametros+="&Valor="+$('#Valor').val();
			parametros+="&Cuenta="+$('#Cuenta').val();
			parametros+="&NroComprobante="+$('#NroComprobante').val();
			
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
	<fieldset style="width:140px; height:50px;" class='filtros'>
		<legend><b>Traslado: </b></legend>			
		<select id="Traslado">
			<option value=""></option>
			<option value="55">Traslados de fondos</option>
			<option value="56">Traslados bancarios</option>
		</select>		
	</fieldset>

	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Comprobante: </b></legend>			
		<input type="text" id="Comprobante" style='width:120px'/>
	</fieldset>	
	
	<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Banco: </b></legend>			
		<select id="Banco">
			<option></option>
		</select>		
	</fieldset>
	
	<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Compañia: </b></legend>			
		<select id="Compania">
			<option></option>
		</select>		
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
	<br>
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Transferencia: </b></legend>			
		<input type="text" id="Transferencia" style='width:120px'/>
	</fieldset>

	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Valor: </b></legend>			
		<input type="text" id="Valor" style='width:120px'/>
	</fieldset>	
	
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Cuenta: </b></legend>			
		<input type="text" id="Cuenta" style='width:120px'/>
	</fieldset>	
	
	<fieldset style="width:130px;height:50px;" class='filtros'>
		<legend><b>Nro. Comprobante: </b></legend>			
		<input type="text" id="NroComprobante" style='width:120px'/>
	</fieldset>	
	
		
	<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
		<button type="button" id='Ir' style='vertical-align: bottom;'>Ir.. </button><br>
		<button type="button" id='Borrar' style='vertical-align: bottom;'>Borrar filtros </button>	
	</fieldset>	
	
</td></tr></table>

<table class="flexme3"></table>
<div id="dialog-modal" title="Recibir Correspondencia" style="display:none"></div>
<div id="MuestraDetalles"></div>
<?=OpcionesSelect('Banco', 'planillastesoreria pla', 'distinct(banco)', 'pla.banco', "where (cab='55' or cab='56')")?>
<?=OpcionesSelect('Compania', 'planillastesoreria pla', 'distinct(compañia)', 'pla.compañia',  "where (cab='55' or cab='56')")?>
</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->querydb("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}
?>