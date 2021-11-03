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
			$.ajax({
				type: "POST",
				url: "config/ajax_querys.php",
				data: { op: "menuwq" }
			}).done(function( data ) {	
				var obj =$.parseJSON(data);	
				var columnas=[ { display : 'Usuario',	name : 'usuario_desc', width : 100, sortable : true, align : 'left'}, 
							   { display : 'Nombre', name : 'usuario_nombres', width : 250, sortable : true, align : 'left'}
							]
				for(i=0; i<obj.length; i++)
					columnas.push({ display :obj[i].value , width : 40, align : 'center'} );	
				
				tables(columnas);
			});
			
			$( "#AutoUsuario").autocomplete({
				source: "config/ajax_querys.php?op=buscaNombreYUsuario",
				minLength: 1,
				delay: 200,
				select: function( event, ui ) {
					$("#usuario_cod").val(ui.item.id);
					$("#AutoUsuario").val(ui.item.value);
					CambiaFiltros();
				}
			});
			
			$( "#confirm" ).dialog({
				//width: 160,
				height: 50,
				autoOpen: false,
				show: "blind",
				hide: "blind",
				position: "left top",
				resizable: false
				
			});
			
			$('#Ir').click(CambiaFiltros);
			$('#Borrar').click(function(){
				$('#AutoUsuario, #usuario_cod').val('');
				CambiaFiltros();
			});
			$('#Permiso').change(function() {
				CambiaFiltros();				
			});
		});
		
		function tables(columnas){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'TodosLosPermisos' }],
				colModel : columnas,
				sortname : "usuario_desc",
				sortorder : "asc",
				usepager : true,
				useRp : true,
				title: "Usuarios",
				rp : 50,
				width :  ($(window).width()-20),
				height :  ($(window).height()-270)
			});
		}
	
		function CambiaFiltros(){
			$(".flexme3").flexOptions(
				{params:[{ name: 'consulta', value: 'TodosLosPermisos' }, 
						 { name:'usuario_cod', value:$('#usuario_cod').val()},
						 { name:'Permiso', value:$('#Permiso :selected').val()}
						]
				} 
			); 
			$('.flexme3') .flexOptions({ newp: 1 }).flexReload();
		}	
		
		function GuardaPermiso(Usuario, Opcion, elemento){
			var Accion;
			if($(elemento).attr('checked') == 'checked')
				Accion='Habilita';
			else
				Accion='Deshabilita';
				
			$.ajax({
				type: "POST",
				url: "config/ajax_querys.php",
				data: { op: "GuardaPermisos", usuario:Usuario, opcion:Opcion, accion:Accion }
			}).done(function( data ) {
				var obj =$.parseJSON(data);	
				if(obj[0].value==1){
					if(Accion=='Habilita')
						$( "#confirm" ).html("Permiso habilitado con exito")
					
					if(Accion=='Deshabilita')
						$( "#confirm" ).html("Permiso deshabilitado con exito")
					
					$( "#confirm" ).dialog( "open" );
					setTimeout ('$( "#confirm" ).dialog( "close" );', 2000); 
					
				}
			});
			
		}
	</script>
	
	<style>
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
		}		
		.Planillalote{
			font-weight: bold;
			color:blue;
			text-decoration: none;
		}		
		input[type=text]{
			margin:0px 2px;
			display:inline;			
		}		
		
		.ui-autocomplete-loading { 
			background: white url('js/ui/css/base/images/ui-anim_basic_16x16.gif') right center no-repeat; 
		}
		.ui-autocomplete {
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
			height: 100px;
		}
	</style>
</head>
<body>	
<table align='center'><tr><td>
	<input type="hidden" id="usuario_cod">
	<fieldset style="height:50px;" class='filtros'>
		<legend><b>Nombre: </b></legend>			
		<input type="text" id="AutoUsuario" style='width:260px'/>
	</fieldset>
	
	<fieldset style="height:50px;" class='filtros'>
		<legend><b>Usuarios con permisos: </b></legend>			
		<select id='Permiso'>
			<option></option>
		</select>
	</fieldset>
			
	<fieldset style="height:50px;border:0px" class='filtros'>
		<button type="button" id='Ir' style='vertical-align: bottom;'>Ir.. </button><br>
		<button type="button" id='Borrar' style='vertical-align: bottom;'>Borrar filtros </button>	
	</fieldset>	
	
</td></tr></table>

<table class="flexme3"></table>
<div id="confirm"></div>

<?=OpcionesSelect('Permiso', 'adm_menu men', 'men.jerarquia_opcion', '( men.jerarquia_opcion || \' --> \' || men.opcion)', "order by men.jerarquia_opcion")?>
</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$result = queryQR("select $Id, $Value from $Tabla $Extra");
	while ($row = $result->FetchRow()){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	return $salida;
}
?>