<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_detalle_CP" title="Detalles">
	<style>
		.TableRadicado td{
			text-align:left;
			padding: 2px 1px;
			color:#327E04;
			font-weight: bold;
			font-family: "Trebuchet MS", "Helvetica", "Arial", "Verdana", "sans-serif";
			font-size: 12px;
		}
		.TableRadicado span{	
			padding: 4px 15px;
			color: #424242;
		}
		#FacCP .ui-state-hover a{	color: white;}		
		#FacCP a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#FacCP a:hover{text-decoration: underline}
	</style>
	
	<div id="ContentPlantillaCP"></div>	
	<script id="Plantilla_Detalles_CP" type="text/x-handlebars-template">
		<fieldset>
			<legend><b style="font-size: 12px; ">Datos del tramite: </b></legend>		
			<table class="TableRadicado">				
				<tr><td>Numero comprobante de pago: <span style="color: #B40404;font-size: 13px;">{{num_comprobante}}</span></td><td>Valor CP: <span>{{#if valor_cp}}{{valor_cp}}{{else}}Sin definir{{/if}}</span></td></tr>
				<tr><td>Fecha de creación: <span>{{fecha_ins}}</span></td><td>Creado por: <span>{{usuario}}</span></td></tr>
				<tr><td>Medio de pago: <span>{{medio_pago}}</span></td><td>Cantidad de ordenes: <span>{{cant_ord}}</span></td></tr>	
				<tr><td colspan="2">Estado: <span>{{estado}}</span></td></tr>	
			</table>
		</fieldset>
		<br>
		<fieldset>
			<legend><b style="font-size: 12px;">Ordenes de giro: </b></legend>		
			<table id="FacCP"></table>
		</fieldset>
		<br>
		<fieldset id="observacionesCP">
			<legend><b style="font-size: 12px;">Observaciones: </b></legend>
		</fieldset>
	</script>
<script>
	$(document).ready(function() {		
		$( "#window_detalle_CP" ).dialog({
			autoOpen: false,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_detalle_CP" ).remove();
				if($("#ListFactura").lenght)
					$("#ListFactura").multiselect("open");
			},
			buttons: {
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		IniciaDetalles('<?=$_REQUEST['tramite']?>');
		$( "#window_detalle_CP" ).dialog("open");					
	});
	
	function IniciaDetalles(Tramite){
		var plantilla = Handlebars.compile($('#Plantilla_Detalles_CP').html());
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaCP", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			var html = plantilla(Detalles);
			
			$('#ContentPlantillaCP').html(html);		
			$('#observacionesCP').append(Detalles.observaciones);	
			tablesOrdenGiro(Tramite);
			setSize();			
			$( "#window_detalle_CP" ).dialog( "option", "position", { my: "center", at: "center"} );
		});
	}
	
	function tablesOrdenGiro(num_comprobante){
		$("#FacCP").jqGrid({
			url:'Facturacion/config/json.php',
			postData:{query:'OrdenGiroInCP', num_comprobante:num_comprobante},
			datatype: "json",
			height: 100,
			width: 450,
			colNames:[ 'Orden giro','Fecha creación', 'Usuario creo', 'Cant'],
			colModel:[
				{name:'num_ordengiro',index:'num_ordengiro', width:20, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
				{name:'fecha_insord',index:'fecha_insord', width:30},
				{name:'usuario',index:'usuario', width:45},
				{name:'cant_fac',index:'cant_fac', width:5},		
			],
			scroll:1,
			rowNum:50,
			pager: '#PagerFacCP',
			sortname: 'ord.fecha_ins',
			viewrecords: true,
			sortorder: "desc",
			altRows:true,
			altclass:'altClassrow',
			gridComplete: function (){
				$("#FacCP td[aria-describedby='FacCP_num_ordengiro'] a").click(function (){DetallesOrdenGiro($(this).text());})
			}
		});
	}
	
	function setSize(){
		var height =  $("#window_detalle_CP").height()+80;
		if(height > $(window).height ()){
			$("#ContentPlantillaCP").css('height',$(window).height()-130);
		}	
	}

	function DetallesOrdenGiro(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/ordenGiro/detalle.php",
			data: { tramite: Tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});	
	}
</script>
</div>