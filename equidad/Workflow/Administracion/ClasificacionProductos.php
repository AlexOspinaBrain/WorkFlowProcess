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
	
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	
	<script type="text/javascript">
		var select;
		
        $(document).ready(function() {	
			$( "#DialogEspere" ).dialog({
				height: 140,
				modal: true,
				autoOpen: false,
				dialogClass: 'no-close'
			});
			tables();
			DatosClasificacion();
		});
		
		function tables(){
			$("#Gridproductos").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'ClasificacionProductos' } 
							 				
							],
				colModel : [ 
							 { display: 'Producto', name : 'producto', width : 300, sortable : true, align: 'left', }, 
							 { display: 'Clasificacion', name : 'clasificacion', width : 450, sortable : true, align: 'left' } 												 
						   ],
				sortname : "producto",
				sortorder : "asc",				
				title : 'Clasificación de productos',
				width : 800,
				height :  ($(window).height()-200),
				resizable:false,
				usepager : true,
				rp:50,
				onSuccess:editaTabla
			});			
		}
		
		function editaTabla(){
			
			 $("tr > td[abbr='clasificacion'] > div").click(function (){//Si hace click en la columna clasificacion
				
				if($("#SelectClasi").length > 0 && $(this).closest("tr").index()!=$("#SelectClasi").closest("tr").index()){
					var Producto = $("#SelectClasi").closest("tr");
					Producto = $(Producto).find("td[abbr='producto']").text();
					Clasificacion = $("#SelectClasi").val();
					GuardaClasificacion(Producto, Clasificacion);
					$("#SelectClasi").parent().text($("#SelectClasi option:selected").text());					
				}
			
			
				if($(this).find("select").length == 0){
					var anterior = $(this).text();
					
					$(this).html(select.val(""));
					$('#SelectClasi option:contains("'+anterior+'")').attr('selected', 'selected');
				}
			 }) 
			 
			$("#Gridproductos").mouseleave(function() {
				if($("#SelectClasi").length > 0){
					var Producto = $("#SelectClasi").closest("tr");
					Producto = $(Producto).find("td[abbr='producto']").text();
					Clasificacion = $("#SelectClasi").val();
					GuardaClasificacion(Producto, Clasificacion);
					$("#SelectClasi").parent().text($("#SelectClasi option:selected").text());
				}
			})
		}
		
		function GuardaClasificacion(Producto, Clasificacion){
			$( "#DialogEspere" ).dialog( "open" );
			
			$.ajax({
				type: "POST",
				url: "Workflow/ajax_querys.php",
				data: { op: "GuardaClasificacion", producto:Producto , clasificacion:Clasificacion}
			}).done(function( data ) {	
				$( "#DialogEspere" ).dialog( "close" );
			});
		}
		
		function DatosClasificacion(){
			$.ajax({
				type: "POST",
				url: "Workflow/ajax_querys.php",
				data: { op: "DatosClasificacion"}
			}).done(function( data ) {	
				var Clasificacion =$.parseJSON(data);
				select = $('<select id="SelectClasi" style="width:350px"><option value=" "></option></select>');
			
				$.each(Clasificacion, function(i,item){
					$("<option />", {value: item.id_clasificacion, text: item.clasificacion}).appendTo(select);
				});
			});
		}
    </script>
	
	<style>
		.no-close .ui-dialog-titlebar-close {display: none }
	</style>

</head>
<body>
	<table align="center"><tr><td>	<table id="Gridproductos"></table>	</td></tr></table>
	<div id="DialogEspere" style="display:none; padding:30px 0px 0px 50px">Espere .... <br> Actualizando datos.</div>
</body>
</html>