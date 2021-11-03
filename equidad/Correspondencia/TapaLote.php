<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Lote.php'
paar correcta visualización
-->
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
	<script src="js/ui/js/datepicker/jquery.ui.datepicker-es.js"></script><!-- Calensario	configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->

	<script type="text/javascript">
	var segundos=0;
        $(document).ready(function() {	
			$( "#FechaLote" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yymmdd",
				onSelect: function() { CambiaFecha() }
			});
			$( "#planillas" ).dialog({
				autoOpen: false,
				width:	980,
				height: ($(window).height()-30),
				modal: true,
				close: function(event, ui) {  }
			});
			$( "#numguia" ).dialog({
				autoOpen: false,
				width:300,
				height: 180,
				modal: true,
				close: function(event, ui) {  }
			});
			
			$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
			tables();
		});
		
		function tables(){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'LotesPlanillas' },
							  { name:'fechalote', value:$('#FechaLote').val()},
							  { name: 'agenciausu', value: '<?=$_SESSION['agencia']?>' }							  
							],
				colModel : [ { display : 'Lote',	name : 'lot.lote', width : 100, sortable : true, align : 'center'}, 
							 { display : 'Fecha y hora inicio', name : 'fechacreacion', width : 120, sortable : true, align : 'center'},
							 { display : 'Fecha y hora cierre', name : 'fechacierre', width : 120, sortable : true, align : 'center'},
							 { display : 'Usuario cierre', name : 'usuariocerro', width : 180, sortable : true, align : 'left'},
							 { display : 'No radicados', width : 70, align : 'center'},
							 { display : 'Opcion', width : 60, align : 'center'},
							 { display : 'Num guia', width : 100, align : 'center'},
							 { display : 'Fecha y hora envio', width : 120, align : 'center'}
						   ],
				sortname : "lot.lote",
				sortorder : "desc",
				usepager : true,
				title : 'Lotes y planillas',
				useRp : true,
				rp : 50,
				width : ($(window).width()-40),
				height :  ($(window).height()-250)
			});
		}
		
		function CambiaFecha(){
			if($('#FechaLote').val().length == 8){
				$(".flexme3").flexOptions(
					{params:[{ name: 'consulta', value: 'LotesPlanillas' }, 
							 { name:'fechalote', value:$('#FechaLote').val()},
							 { name: 'agenciausu', value: '<?=$_SESSION['agencia']?>' }	
							]
					}); 
				$(".flexme3").flexReload();
			}
		}
		
		function CerrarLote(numlote){		
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "CierraLote", term: numlote, codusuario:'<?=$_SESSION['uscod']?>' }
				}).done(function( data ) {	
						$(".flexme3").flexReload();
				});
			return false;
		}
		
		function GeneraPlanilla(numlote){
			$("#planillas").html("");
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "GeneraPlanilla", term: numlote, codusuario:'<?=$_SESSION['uscod']?>' }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					$("#planillas").html(obj[0].value);
				});
			$("#planillas").dialog("open");
		}
		
		function agregaNumGuia(lote){
			$("#numguia" ).dialog("open");
			$("#NumGuia, #FechaEnvio" ).val("");
			$("#BotonGuia").val(lote);
		}
		
		function GuardaGuia(){
			if($('#formID').validationEngine('validate')){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "GuardaNumGuia", NumGuia: $("#NumGuia" ).val(), FechaEnvio:$("#FechaEnvio" ).val(), term:$("#BotonGuia" ).val(), codusuario:'<?=$_SESSION['uscod']?>', areausu:'<?=$_SESSION['area']?>' }
				}).done(function( data ) {
					var obj =$.parseJSON(data);	
							
					if(obj[0].value > 0){
						alert('Quedaron pendientes '+obj[0].value+' tramites por recibir en este lote');
						$(".flexme3").flexReload();
						$("#numguia" ).dialog("close");
					}else{
						$(".flexme3").flexReload();
						$("#numguia" ).dialog("close");
					}	
				});
			}
		}
		
		function Imprimir(elemento, link){	
			var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
			if(is_chrome){
				var Digital=new Date()
				var hours=Digital.getHours()
				var minutes=Digital.getMinutes()
				var seconds=Digital.getSeconds()	
			
				if((((minutes*60)+(hours*3600)+seconds)-segundos) > 40){
					$('#planilla'+elemento).jqprint();
					$(link).html("Volver a imprimir <img src='images/print.png' border='0' width='20px'/> ");
					segundos=(minutes*60)+(hours*3600)+seconds;
				}else{
					alert("por favor espere "+(40-(((minutes*60)+(hours*3600)+seconds)-segundos))+( " segundos para volver a imprimir"));
				}
			}else{
				$('#planilla'+elemento).jqprint();
			}
			return false;
		};	
    </script>
	
	<style>			
		.filtro{
			margin:10px;
			font-size: 12px;
			font-weight: bold;
			color:#08298A;
		}	
		.Cierralote{
			font-weight: bold;
			color:red;
			text-decoration: none;
		}		
		.Planillalote{
			font-weight: bold;
			color:blue;
			text-decoration: none;
		}
		label{
			display: inline-table;
			padding: 5px;
		}

	</style>
</head>
<body>
	<table align="center"><tr><td>
	<span class="filtro">Fecha lotes:</span>
	<input type="text" id="FechaLote" name="FechaLote" maxlength="8" size="6" value="<?=DATE('Ymd')?>"/>
	<button type="button" onClick="CambiaFecha()">Ir.. </button>
	<br><br>
	
	<table class="flexme3"></table>
	</td><tr></table>
	
	<div id="planillas"></div>
	
	<div id="numguia" title="Numero guia" style="display:none">
		<form class="formular" action="#" id="formID" method="post">
			<fieldset>
				<legend>Guia y envio</legend>
				<label>	<span>Numero Guia:</span> </label>
				<input type="text" id="NumGuia" name="NumGuia" class="validate[required]" style="width:150px"/>
					
				<div style="text-align: right">
					<button type="button" onClick="GuardaGuia()" id="BotonGuia">Guardar</button>
				</div>	
			</fieldset>
		</form>
	</div>
</body>
</html>