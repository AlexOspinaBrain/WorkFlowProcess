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
			$('#NumTramite, #Lote, #Desde, #Hasta, #Asunto').keypress(function(e) {
				if(e.which == 13) 
					CambiaFiltros();				
			});
			
			$('#Estado, #AreaOrigen, #AreaDestino').change(function() {
				CambiaFiltros();				
			});
			
			$('#NumTramite, #Lote, #Desde, #Hasta, #Estado, #AreaOrigen, #AreaDestino, #Asunto').focus(function() {
				$('#Hasta').val('');
			});
		});
		
	function tables(){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'ConsultaTramite' }, { name:'Hasta', value:$('#Hasta').val()}							  
							],
				colModel : [ { display : 'Num Tramite',	name : 'cor.numtramite', width : 100, sortable : true, align : 'center'}, 
							 { display : 'Fecha y hora radicacion', name : 'cor.fecins', width : 120, sortable : true, align : 'center'},
							 { display : 'Remitente', name : 'cor.remitente', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Agencia destino',	name : 'ofi.descrip', width : 180, sortable : true,	align : 'left'}, 
							 { display : 'Area destino', name : 'are.area',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Destinatario', name : 'destinatario',	width : 200,	sortable : true, align : 'left'} ,
							 { display : 'Tipo doc', name : 'doc.tipo',	width : 120,	sortable : true, align : 'left'} ,
							 { display : 'Folios', name : 'cor.numfolios',	width : 30,	sortable : true, align : 'right'} 
						   ],
				sortname : "cor.fecins",
				sortorder : "desc",
				usepager : true,
				title : 'Consulta tramites',
				useRp : false,
				rp : 100,
				width : ($(window).width()-20),
				height :  ($(window).height()-300)
			});
		}
	function MuestraDetalles(Tramite){
		$.ajax({
			type: "POST",
			url: "config/ajax_querys.php",
			data: { op: "DetallesTramite", term: Tramite, usu:'<?=$_SESSION['uscod']?>', areausu:<?=$_SESSION['area']?>}
		}).done(function( data ) {	
			var obj =$.parseJSON(data);	
			$( "#MuestraDetalles" ).html(obj[0].value);
			
			if($(window).width() > ($('#MuestraDetalles>table').width()+80))
				$( '#MuestraDetalles' ).dialog( 'option', 'width', $('#MuestraDetalles>table').width()+80);
			else
				$( '#MuestraDetalles' ).dialog( 'option', 'width', $(window).width()-80);
						
			if($(window).height() > ($('#MuestraDetalles>table').height()+80)){
				if($.browser.msie)
					$( '#MuestraDetalles' ).dialog( 'option', 'height', ($('#MuestraDetalles>table').height()+250));
				else
					$( '#MuestraDetalles' ).dialog( 'option', 'height', ($('#MuestraDetalles>table').height()+100));
			}else
				$( '#MuestraDetalles' ).dialog( 'option', 'height', $(window).height()-80);
		
			$( '#MuestraDetalles' ).dialog( 'option', 'position', 'center' );
		});
		$( "#MuestraDetalles" ).dialog({
			autoOpen: true,
			modal: true,
			width:($(window).width()-100),
			height: ($(window).height()-100),			
			buttons: {
				Aceptar: function() {
					$("#MuestraDetalles" ).html(' ');
					$("#MuestraDetalles" ).dialog( "destroy" );									
				}
			}
		});
	}	
	function CambiaFiltros(){
		$(".flexme3").flexOptions(
			{params:[{ name: 'consulta', value: 'ConsultaTramite' }, 
					 { name:'NumTramite', value:$('#NumTramite').val()},
					 { name:'AreaOrigen', value:$('#AreaOrigen').val()},
					 { name:'AreaDestino', value:$('#AreaDestino').val()},
					 { name:'Desde', value:$('#Desde').val()},
					 { name:'Hasta', value:$('#Hasta').val()},
					 { name:'Estado', value:$('#Estado').val()},
					 { name:'Asunto', value:$('#Asunto').val()}
					
				]
			}); 
		$(".flexme3").flexReload();
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
	<fieldset style="width:130px; height:50px;" class='filtros'>
		<legend><b>Numero de tramite: </b></legend>			
		<input type="text" id="NumTramite" style='width:120px'/>
	</fieldset>
	
	<fieldset style="width:160px; height:50px;" class='filtros'>
		<legend><b>Asunto: </b></legend>			
		<input type="text" id="Asunto" style='width:150px'/>
	</fieldset>
	
	<fieldset style="width:350px; height:50px;" class='filtros'>
		<legend><b>Area origen: </b></legend>			
		<select id="AreaOrigen">
			<option></option>
		</select>		
	</fieldset>
	
	<fieldset style="width:350px; height:50px;" class='filtros'>
		<legend><b>Area destino: </b></legend>			
		<select id="AreaDestino">
			<option></option>
		</select>		
	</fieldset>

	<fieldset style="width:150px;height:50px;" class='filtros'>
		<legend><b>Rango de fecha: </b></legend>		
<label style="display: inline-block;">		
		<input type="text" id="Desde" style='width:60px'/><br>
		Desde</label>
		<label style="display: inline-block;">	
		<input type="text" id="Hasta" style='width:60px' value="<?=date('Ymd')?>"/><br>
		Hasta</label>
	</fieldset>

	<fieldset style="width:130px; height:50px;" class='filtros'>
		<legend><b>Estado: </b></legend>			
		<select id='Estado'>
			<option value="0">Todos</option>
			<option value="1">Por recibir</option>
			<option value="2">Por enviar</option>
			<option value="3">Por cerrar</option>
			<option value="4">Devueltos</option>
			<option value="5">Redireccionados</option>
			<option value="6">Cerrados</option>
		</select>
	</fieldset>
	
	<button type="button" id='Ir' style='vertical-align: bottom;'>Ir.. </button>
</td></tr></table>

<table class="flexme3"></table>
<div id="dialog-modal" title="Recibir Correspondencia" style="display:none"></div>
<div id="MuestraDetalles"></div>
<?=OpcionesSelect('AreaOrigen', 'tblareascorrespondencia are', ' are.areasid', 'are.area', '')?>
<?=OpcionesSelect('AreaDestino', 'tblareascorrespondencia are', ' are.areasid', 'are.area', '')?>
</body>
</html>

<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}
?>