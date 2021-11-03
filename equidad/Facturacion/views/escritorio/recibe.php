<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_detalle" title="Recibe tramite">
	<style>
		.TableRadicado td{
			text-align:left;
			padding: 2px 1px;
			color:#08088A;
			font-weight: bold;
			font-family: "Trebuchet MS", "Helvetica", "Arial", "Verdana", "sans-serif";
			font-size: 12px;
		}
		.TableRadicado span{	
			padding: 4px 15px;
			color: #424242;
		}
	</style>
	
	<div id="ContentPlantilla"></div>
	
	<script id="Plantilla_Recibe" type="text/x-handlebars-template">
		<p>
			<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
			Desea recibir el tramite <b>{{serial_factura}}</b> ?
		</p>
		<fieldset style="width:300px">
			<legend><b style="font-size: 12px; ">Datos del tramite: </b></legend>		
			<table class="TableRadicado">				
				<tr><td>Fecha radicación<br><span>{{fechahora_ins}}</span></td></tr>
				<tr><td>Tipo documento:  <br><span>{{desc_documento}}</span></td></tr>
				<tr><td>Numero de {{desc_documento}}: <br><span>{{no_factura}}</span></td></tr>			
				<tr><td>Proveedor: <br><span>{{tipo_doc}} {{documento}}</span><br><span>{{nombre}}</span></td></tr>
			</table>
		</fieldset>
	</script>
	
	<script id="Plantilla_NoExiste" type="text/x-handlebars-template">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
			<h3>Error</h3>
			<div style="margin-left:30px">
				El tramite <b>{{serial_factura}}</b> no ha sido encontrado o no esta disponible 
				para esta actividad, por favor verifique el tramite.
			<div>
		</p>
	</script>

<script>
	$(document).ready(function() {		
		$( "#window_detalle" ).dialog({
			autoOpen: false,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_detalle" ).remove();
				$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid")
			},
			buttons: {
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		
		//IniciaDetalles('<?=$_REQUEST['tramite']?>');
		RecibeTramite('<?=$_REQUEST['tramite']?>');
	});
	
	function IniciaDetalles(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "ConfirmaRecibeFactura", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			if(Detalles.serial_factura == null || Detalles.recibe=='f'){
				var plantilla = Handlebars.compile($('#Plantilla_NoExiste').html());
				var html = plantilla({serial_factura:Tramite});				
				$( "#window_detalle" ).dialog( "option", "title", "Tramite no encontrado" );
				$('#window_detalle' ).dialog( "option", "width", 300 );
				$('#ContentPlantilla').html(html);
			}
			
			if(Detalles.serial_factura != null && Detalles.recibe=='t'){
				var plantilla = Handlebars.compile($('#Plantilla_Recibe').html());
				var html = plantilla(Detalles);
				$( "#window_detalle" ).dialog( "option", "buttons", [ 
					{ text: "Si", click: function() { RecibeTramite(Tramite); } },
					{ text: "No", click: function() { Devolucion(Tramite); } }
				 ] );

				$('#ContentPlantilla').html(html);
			}
			$( "#window_detalle" ).dialog("open");
		});
	}
	
	function setSize(){
		var height = $(".ui-dialog").height ();
		if(height >$(window).height ()){
			$("#ContentPlantilla").css('height',$(window).height()-130);
		}	
	}
	
	function barCode(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/radicar/barCode.php",
			data: { codebar: Tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});		
	}

	function Devolucion(tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/devolucion/devuelve.php",
			data: { Tramite: tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});	
	}

	function RecibeTramite(tramite){
		$(".ui-dialog-buttonpane button:contains('Si')").button("disable");
		$(".ui-dialog-buttonpane button:contains('No')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Si') span").text("Guardando ...");
	
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "RecibeFactura", tramite:tramite},
			success	:function (data){
				try{
					var json = $.parseJSON(data);
					if (json.guardado)	
						$('#window_detalle' ).dialog( "close");
				}catch(err){
					alert(data);
				}  
				$('#window_detalle' ).dialog( "close");
			}
		});		
	}
</script>
</div>