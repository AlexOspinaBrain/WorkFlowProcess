<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_detalle_fac" title="Detalles">
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
	</style>
	
	<div id="ContentPlantillaFAC"></div>
	
	<script id="Plantilla_DetallesFac" type="text/x-handlebars-template">
		<fieldset>
			<legend><b style="font-size: 12px; ">Datos del tramite: </b></legend>		
			<table class="TableRadicado">
				<tr><td>Numero de tramite: <span style="color: #B40404;font-size: 13px;">{{serial_factura}}</span></td><td>Codigo de barras: <a href="#" onclick="barCode('{{serial_factura}}')"><img src="images/barcode.png" ></a></td></tr>
				<tr><td>Fecha radicación<span>{{fechahora_ins}}</span></td></tr>
				<tr><td>Tipo documento: <span>{{desc_documento}}</span><td>Numero de {{desc_documento}}: <span>{{no_factura}}</span></td><td>Valor {{desc_documento}}: <span>{{valor_factura}}</span></td></tr>
				<tr><td>Fecha expedición: <span>{{fecha_expedicion}}</span><td colspan="2">Fecha vencimiento: <span>{{fecha_vencimiento}}</span></td></tr>
				<tr><td>Proveedor: <span>{{tipo_doc}} {{documento}}</span><td colspan="2">Nombre proveedor: <span>{{nombre}}</span></td></tr>
				<tr><td>Teléfono proveedor: <span>{{telefono}}</span></td><td colspan="2">Dirección proveedor: <span>{{direccion}}</span></td></tr>
				<tr><td>Orden de giro: <span>{{num_ordengiro}}</span></td><td>Comprobante de pago: <span>{{num_comprobante}}</span></td>{{#if medio_pago}}<td>Medio de pago: <span>{{medio_pago}}</span></td>{{/if}}</tr>
				
			</table>
		</fieldset>
		<br>
		<fieldset>
			<legend><b style="font-size: 12px;">Datos del area destino: </b></legend>		
			<table style="font-size: 11px;" class="TableRadicado">				
				<tr><td>Aseguradora: <span>{{des_compania}}</span></td><td>Agencia: <span>{{descrip}}</span></td><td>Area: <span>{{area}}</span></td></tr>
			</table>
		</fieldset>
		<br>
		<fieldset>
			<legend><b style="font-size: 12px;">Historial tramite: </b></legend>		
			<table class="TblGreen" align="center" id="Historial">				
				<tr><th>Fecha Asignado</th><th>Fecha Terminado</th><th>Actividad</th><th>Estado</th><th>Usuario</th></tr>
				{{#each historial}}
					<tr><td>{{fecha_asig}}</td><td>{{fecha_term}}</td><td>{{actividad}}</td><td>{{estado}}</td><td>{{usuario}}</td></tr>
				{{/each}}
			</table>
		</fieldset>

		<fieldset>
			<legend><b style="font-size: 12px;">Observaciones: </b></legend>		
				<div id="ObservacionesFac">
					<p style="color: gray;font-family:Verdana;font-size: 11px;text-align : justify; width:300px">
							 <span style="color: #327E04;font-weight: bold">No hay observaciones</span><br>
					</p>
				</div>
			</table>
		</fieldset>
	</script>
	<script src="js/jquery.multiselect.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {		
		$( "#window_detalle_fac" ).dialog({
			autoOpen: false,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_detalle_fac" ).remove();
				$("#ListFactura").multiselect("open");
			},
			buttons: {
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		
		IniciaDetalles('<?=$_REQUEST['tramite']?>');
		$( "#window_detalle_fac" ).dialog("open");		
			
	});
	
	function IniciaDetalles(Tramite){
		var plantilla = Handlebars.compile($('#Plantilla_DetallesFac').html());
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaFactura", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			var html = plantilla(Detalles);
			$('#ContentPlantillaFAC').html(html);	

			if(Detalles.observaciones_fac != null)
				$('#ObservacionesFac').html(Detalles.observaciones_fac);	

			setSize();			
			$( "#window_detalle_fac" ).dialog( "option", "position", { my: "center", at: "center"} );

			for(var i=2; $('#Historial tr:eq('+i+')').length; i+=2){
				$('#Historial tr:eq('+i+')').addClass('alt');
			}
		});
	}
	
	function setSize(){


		var height =  $("#window_detalle_fac").height()+80;
		if(height > $(window).height ()){
			$("#window_detalle_fac").css('height',$(window).height()-130);
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
</script>
</div>