<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Consulta.php'
paar correcta visualización
*/?>
<?php
	
generaInforme();

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
			
			/*  */
			$("#informe").validationEngine('attach', {promptPosition : "topLeft"});	
			
		});

		function generaExcel(){
		if($("#informe").validationEngine('validate'))
			window.open("Workflow/Informes/InformeEncuestas.php?excel&"+$("form").serialize());			
		}
	</script>

</head>
<body>
<center>
<h1>Informe de Encuestas</h1>
<form action="#" method="post" style="margin-left: auto;margin-right: auto;width: 80%;" id="informe">

	<fieldset style="width:150px;height:80px;" >
		<legend><b>Fecha de Encuesta (aaaammdd): </b></legend>		
<label style="display: inline-block;">		
		<input type="text" name="Desde" id="Desde" class="validate[required]" style='width:60px'/><br>
		Desde</label>
		<label style="display: inline-block;">	
		<input type="text" name="Hasta" id="Hasta" class="validate[required]" style='width:60px'/><br>
		Hasta<br /></label>
	</fieldset><br>
	
	
	<fieldset style="width:130px;height:50px;border:0px"
<br>
		<input type="button" value="Generar informe" onClick="generaExcel()"/>
	</fieldset>	


</form>
</body>
</html>
<?php
function generaInforme(){	
	if(!isset($_GET['excel']))
		return;
	
	require_once ('../../config/conexion.php');
	header("Content-type: application/vnd.ms-excel; name='excel'");
   	header("Content-Disposition: filename=ListaExcel.xls");
   	header("Pragma: no-cache");
   	header("Expires: 0");
	
	$conect=new conexion();
	$consulta=$conect->queryequi("select id_radicacion,fechahora,fechahora_limite, respuesta from wf_historial join wf_encuesta USING(id_radicacion)
where actividad ='Encuesta' and fechahora between '".date('Ymd', strtotime($_GET['Desde']))."' and '".date('Ymd', strtotime($_GET['Hasta'].'+ 1 day'))."' order by id_radicacion, fechahora, pregunta limit 100");
 	
	echo "<table border='1' cellpadding='0' cellspacing='0'>";
	echo "<tr><th>TRAMITE</th>
</th><th>FECHA DE ENCUESTA
</th><th>FECHA LIMITE
</th><th>CALIFICACIÓN ATENCIÓN
</th><th>CORREO 
</th><th>CALIFICACIÓN CALIDAD
</th><th>TIEMPO DE RESPUESTA
</th><th>NOMBRE
</th><th>COMENTARIOS
</th><th>TIPO DE SEGURO
</th></tr>";
	$i=0;
	while ($row = pg_fetch_array($consulta)){
	if($i==0){
		echo "<tr><td style='mso-number-format:\"\@\"'> {$row['id_radicacion']}</td>
				<td style='mso-number-format:\"\@\"'> ".date('Y/m/d h:m a', strtotime($row['fechahora']))."</td>
				<td style='mso-number-format:\"\@\"'> ".date('Y/m/d h:m a', strtotime($row['fechahora_limite']))."</td>";				
	}
		$i++;
		echo "<td style='mso-number-format:\"\@\"'> {$row['respuesta']}</td>";
	if($i==7){
		$i=0;
		echo "</tr>";
	}
		
	}
	echo "</tr></table>";
	exit;
}

?>