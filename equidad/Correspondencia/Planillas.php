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
	<script type="text/javascript" src="js/VisorImagine.js"></script><!--Visor-->
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
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	
	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->

	<script type="text/javascript">
		var segundos=0;
        $(document).ready(function() {				
			$( "#planillas" ).dialog({
				autoOpen: false,
				width:	980,
				height: ($(window).height()-30),
				modal: true,
				close: function(event, ui) {  }
			});
			tables();
		});
		
		function tables(){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'LotesPlanillas' },
							  { name: 'areausu', value: '<?=$_SESSION['area']?>' }			  
							],
				colModel : [ { display : '', width : 20, align : 'center'}, 
							 { display : 'Num tramite', name : 'tra.numtramite', width : 120, sortable : true, align : 'center'},
							 { display : 'Fecha y hora radicacion', name : 'cor.fecins', width : 120, sortable : true, align : 'center'},
							 { display : 'Remitente', name : 'cor.remitente', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Agencia destino',	name : 'ofi.descrip', width : 180, sortable : true,	align : 'left'}, 
							 { display : 'Area destino', name : 'are.area',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Destinatario', name : 'destinatario',	width : 200,	sortable : true, align : 'left'} ,
							 { display : 'Tipo doc', name : 'doc.tipo',	width : 120,	sortable : true, align : 'left'} ,
							 { display : 'Folios', name : 'cor.numfolios',	width : 30,	sortable : true, align : 'right'} 
						   ],
				buttons : [ { name : '<input type="checkbox" id="PadreSelect">', onpress : selecciona}, 
							{ separator : true} ,
							{ name : '<img src="images/print.png" border="0" width="15px"> Muestra planillas', onpress : muestra}, 
							{ separator : true} 
						  ],
				sortname : "tra.numtramite",
				sortorder : "desc",
				usepager : true,
				title : 'Lotes y planillas',
				useRp : false,
				rp : 100,
				width : ($(window).width()-40),
				height :  ($(window).height()-250)
			});
		}
		
		
		
		
		function GeneraPlanilla(tramites){
			$("#planillas").html("");
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "GeneraPlanilla", term: tramites, codusuario:'<?=$_SESSION['uscod']?>' }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					$("#planillas").html(obj[0].value);
				});
			$("#planillas").dialog("open");
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
		
		function muestra(){
			
			var tramites='';
			for(i=0; i<$(".Tramites:checked").length; i++)
				tramites+= "cor.numtramite='"+$(".Tramites:checked")[i].value+"' or ";
				
			if($(".Tramites:checked").length < 1)
				alert("No ha seleccionado ningun tramite");
			else
				GeneraPlanilla("and ("+tramites.substring(0, tramites.length-3)+") ");
			
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
	
	function selecciona(){
		$(".Tramites").attr('checked', ($("#PadreSelect").attr("checked") == 'checked'));
	}
</script>
	
	<style>			
		label{
			margin-top:10px;
		}
		
		input{
			display:inline;			
		}	
		#Area{
			font-size:9px;
		}	
		span.prioritario{
			color:red;
			font-weight:bold;
			margin-top:20px;
		}	
		.Planillalote{
			font-weight: bold;
			color:blue;
			text-decoration: none;
		}
		.Detalles{
			color:blue;
		}
		.TableResult{
			color:black;
			border-spacing: 0px;
			border-color: #F1F1F1;
		}
		.TableResult td{
			padding:5px;
		}
		.TableResult th{
			text-align: left
		}

	</style>
</head>
<body>
	<table class="flexme3"></table>
	<div id="planillas"></div>
	<div id="MuestraDetalles"></div>
</body>
</html>