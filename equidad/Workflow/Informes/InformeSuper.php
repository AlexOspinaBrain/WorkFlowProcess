<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Workflow/DetallesTramite.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
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
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script type="text/javascript" src="js/FunctionsWorkflow.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script language="Javascript" src="js/htmlbox.min.js" type="text/javascript"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->


	<script type="text/javascript">
        $(document).ready(function() {	
			$("#formID").validationEngine('attach', {promptPosition : "topLeft"});
			$( "#Desde" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#Hasta" ).datepicker( "option", "minDate", selectedDate );
				}
			});
			
			$( "#Hasta" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function( selectedDate ) {
					$( "#Desde" ).datepicker( "option", "maxDate", selectedDate );
				}
			});
			
			$( "button" ).button();	
			
			$( "#Ir" ).click(function(){
				if( $("#formID").validationEngine('validate') ===true)
					CargaInforme($( "#FiltroCompania" ).val());
			});	
		});	

		function ExportaExcel(){
			$("body").append('<form id="FormExporta" method="post" target="_blank" action="Workflow/Informes/InformeSuperExcel.php"><input type="hidden" id="datos_a_enviar" name="datos_a_enviar"></form>');
			$("#datos_a_enviar").val($("#ContenedorInforme").html());
			$("#FormExporta").submit();
		}
		
		function CargaInforme(IdCompania){
			$.ajax({
				type: "POST",
				url: "Workflow/ajax_querys.php",
				data: { op: "InformeSuper", id_compania: IdCompania, Desde: $( "#Desde" ).val(), Hasta: $( "#Hasta" ).val(), TipoTramite:'1'}
			}).done(function( data ) {	
				console.log(data);
				$("#ContenedorInforme").html('<div id="Informe">'+
											 '<button type="button" id="InformeExcel" onclick="ExportaExcel()"><img src="images/file_xls.png" width="18px"> Informe Excel</button>'+
											 '<button type="button" onclick="()"> <img src="images/file_txt.png" width="18px">Archivo plano (prn)</button></div>');
				$(":button").button();
				
				var obj =$.parseJSON(data);
				 $.each(obj[0].Informe, function(i,item){
					var unidad_captura=item.unidad_captura;	
					var salida = '<table border="1" align="center" class="customers">'+
									'<tr class="alt">'+
									'	<td colspan="10" style="text-align:left">'+item.desc_clasificacion+'</td>'+
									'</tr>'+
									'<tr>'+
									'	<th style="width:10px">Subcuenta </th>'+
									'	<th>Motivo de reclamacion </th>'+
									'	<th style="width:10px">Reclamaciones pendientes </th>'+
									'	<th style="width:10px">Reclamaciones recibidas </th>'+
									'	<th style="width:10px">Total reclamaciones por resolver </th>'+
									'	<th style="width:10px">Reclamaciones con respuesta final a favor del consumidor financiero </th>'+
									'	<th style="width:10px">Reclamaciones con respuesta final a favor de la entidad </th>'+
									'	<th style="width:10px">Trámites concluidos </th>'+
									'	<th style="width:10px">Total reclamaciones en trámite </th>'+
									'	<th style="width:10px">Unidad de captura </th>'+
									'</tr>';
					
					$.each(item.tipologias, function(i,item2){		
						salida+='<tr '+((i%2 == 1)? 'class="alt"' : "")+'>'+
								'	<td>'+((item2.codigo_entidad == null) ? "" : item2.codigo_entidad )+'</td>'+
								'	<td style="text-align:left;">'+item2.desc_tipologia+' </td>'+
								'	<td>'+item2.PendienteAnt+'</td>'+
								'	<td>'+item2.PendienteAhora+'</td>'+
								'	<td>'+item2.PendienteTot+'</td>'+
								'	<td>'+item2.FavorCliente+'</td>'+
								'	<td>'+item2.FavorCompania+'</td>'+
								'	<td>'+item2.Concluidos+'</td>'+
								'	<td>'+item2.EnTramite+'</td>'+
								((i==0)?'<td rowspan="'+item.tipologias.length+'">'+unidad_captura+'</td>' : '')+								
								'</tr>';					
					});		
					salida+='</table><br>';
					$("#Informe").append(salida);
				 });
				 
				 var salida = '<table border="1" align="center" class="customers">'+
									'<tr>'+
									'	<th>'+obj[0].Totales[0].codigo_entidad+'</th>'+
									'	<th>'+obj[0].Totales[0].desc_tipologia+'</th>'+
									'	<th>'+obj[0].Totales[0].PendienteAnt+'</th>'+
									'	<th>'+obj[0].Totales[0].PendienteAhora+'</th>'+
									'	<th>'+obj[0].Totales[0].PendienteTot+'</th>'+
									'	<th>'+obj[0].Totales[0].FavorCliente+'</th>'+
									'	<th>'+obj[0].Totales[0].FavorCompania+'</th>'+
									'	<th>'+obj[0].Totales[0].Concluidos+'</th>'+
									'	<th>'+obj[0].Totales[0].EnTramite+'</th>'+
									'	<th><b>63</b></th>'+
									'</tr></table>';
				$("#Informe").append(salida);
				 
				$("#Informe > table:first tbody tr:eq(1) th").each(function (index) {
					$("#Informe > table:last tbody tr:first th:eq("+index+")").width($(this).width()+1);
				});
				$("#Informe > table:last").css("font-size", "1.3em");
				 
				$("#InformeExcel").css("margin-left",  (($("body").width()-$("#Informe > table").width())/2+$("#Informe > table").width()-300));
			})
		}
		
    </script>
	
	<style>			
		.customers{
			font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
			border-collapse:collapse;
		}
		
		.customers td, .customers th {
			font-size:1.2em;
			border:1px solid #98bf21;
			padding:3px 7px 2px 7px;
			text-align:center;
		}
		
		.customers th {
			font-size:0.9em;
			text-align:left;
			padding-top:5px;
			padding-bottom:4px;
			background-color:#A7C942;
			color:#fff;
			text-align:center;
		}
		
		.customers tr.alt td {
			color:#000;
			background-color:#EAF2D3;
		}
		
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
<form id="formID">
<table align='center'><tr><td>
	<fieldset style="width:150px; height:50px;" class='filtros'>
		<legend><b>Aseguradora: </b></legend>		
			<select id="FiltroCompania"  class="validate[required] text-input">
				<option></option>
				<option value="1">Seguros generales</option>
				<option value="2">Seguros de vida</option>
			</select>
	</fieldset>	
	
	<fieldset style="width:150px;height:50px;" class='filtros'>
		<legend><b>Rango de fecha (YYYYMMDD): </b></legend>		
	<label style="display: inline-block;">		
		<input type="text" id="Desde"  class="validate[required] text-input" style='width:60px'/><br>
		Desde</label>
		<label style="display: inline-block;">	
		<input type="text" id="Hasta"  class="validate[required] text-input" style='width:60px'/><br>
		Hasta</label>
	</fieldset>
	
	<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
		<br><button type="button" id="Ir">Ir .. </button><br>	
	</fieldset>		
</td></tr></table>
</form>

<div id="ContenedorInforme"></div>

</body>
</html>