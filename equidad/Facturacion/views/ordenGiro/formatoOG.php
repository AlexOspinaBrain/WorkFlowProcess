<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_formato_orden" title="Formato Orden de Giro">
	
	
	<div id="FormatoOrden"></div>	
	<script id="Plantilla_Formato_Orden" type="text/x-handlebars-template">
	<br><br>
		<table class="printable"  width="100%">
			<tr>
				<td align="center">
					<h3>LA EQUIDAD SEGUROS<br>ORDEN DE GIRO <span style="font-size: 18px;">{{num_ordengiro}}</span></h3>
					<img style="width: 150px;position: absolute;top: 40px;right: 50px;" src="images/Logo_horizontal.png"/>
				</td>			
			</tr>

			<tr>
				<td align="center" colspan="2">
					<span style="padding: 20px;">LA EQUIDAD SEGUROS GENERALES <input type="checkbox" onclick="this.checked=!this.checked;" id="CheckGenerales"/></span>
					<span style="padding: 20px;">LA EQUIDAD SEGUROS DE VIDA <input type="checkbox" onclick="this.checked=!this.checked;" id="CheckVida"/></span>
				</td>			
			</tr>

			<tr>
				<td align="center" colspan="2">PAGO EN:		
					<span style="padding: 30px;">CHEQUE <input type="checkbox"/></span>
					<span>TRANSFERENCIA <input type="checkbox"/></span>
					<br><br><br><br>
				</td>				
			</tr>

			<tr>
				<td align="center" colspan="2">
					<table border="1" cellspacing="0" width="100%" style="text-align:center">
						<tr>
							<th colspan="1" style="padding: 10px;">FECHA SOLICITUD</th>
							<th >DEPENDENCIA/AGENCIA</th>
							<th>DEPARTAMENTO / AREA QUE AUTORIZA</th>
							<th >AUTORIZACION VICEPRESIDENCIA </th>
						</tr>
						
						<tr>
							<td style="padding: 10px;">
								<table border="0" cellspacing="0" width="100%" style="text-align:center">
								<tr><th>DD</th><th>MM</th><th>AA</th></tr>
								<tr><td><?php echo date("d")?></td>	<td><?php echo date("m")?></td>	<td><?php echo date("Y")?></td></tr>
								</table>
							</td>
							<td>{{area}}</td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th colspan="4" style="text-align:left;padding: 10px;">CONCEPTO</th>
						</tr>
						<tr>							
							<td colspan="4" style="text-align:left;height:80px;padding: 10px;" id="conceptoFormato"><br></td>							
						</tr>
						<tr>
							<th style="text-align:left;padding: 10px;">PAGUESE A NOMBRE DE</th>
												
							<td style="text-align:left;padding: 10px;">{{nombre}}<br></td>							
						
							<th colspan="1" style="padding: 10px;">DOCUMENTO DE IDENTIFICACION / NIT</th>
							<td colspan="1" style="padding: 10px;">( {{tipo_doc}} ) {{documento}}</td>
							</tr>
							<tr><th colspan="3" style="text-align:left;padding: 10px;">LA SUMA DE (EN LETRAS)</th>
							<th>VALOR EN PESOS</th>							
						</tr>

						<tr>
							
							<td colspan="3" style="text-align:left;padding: 10px;">{{valor_letras}}</td>
							<td>{{valor_total}}</td>							
						</tr>

						<tr>
							<th colspan="4" style="text-align:left;padding: 10px;">DOCUMENTOS SOPORTE ENTREGADOS</th>				
						</tr>

						<tr>
							<td colspan="4" style="text-align:left; padding: 10px;">
								{{#each facturas}}
  									* FACTURA {{no_factura}} (TRAMITE : {{serial_factura}}) - {{soportes}}<br>
								{{/each}}
							</td>					
						</tr>

						<tr>
							<th colspan="4" style="text-align:left; padding: 10px;">OBSERVACIONES</th>				
						</tr>

						<tr>
							<td colspan="4" style="text-align:left;height:50px">
								
							</td>					
						</tr>

						<tr>
							<th colspan="1" style="padding: 10px;">FECHA RECIBIDO</th>
							<th colspan="2" style=>FECHA CAUSACIÓN</th>
							<th>RECIBIDO TESORERIA</th>							
						</tr>

						<tr>
							<td colspan="1">
								<table border="0" cellspacing="0" width="100%" style="text-align:center">
									<tr><th>DD</th><th>MM</th><th>AA</th></tr>
									<tr style="height:20px"><td></td>	<td></td>	<td></td></tr>
								</table>
							</td>
							<td colspan="2">
								<table border="0" cellspacing="0" width="100%" style="text-align:center">
									<tr><th>DD</th><th>MM</th><th>AA</th></tr>
									<tr style="height:20px"><td></td>	<td></td>	<td></td></tr>
								</table>
							</td>
							<td>
								<table border="0" cellspacing="0" width="100%" style="text-align:center">
									<tr><th>DD</th><th>MM</th><th>AA</th></tr>
									<tr style="height:20px"><td></td>	<td></td>	<td></td></tr>
								</table>
							</td>							
						</tr>

						
					</table>
				</td>				
			</tr>
			<tr><td>TES-002 (11/2013)</td></tr>
		</table>

	</script>
<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
<script>
	$(document).ready(function() {		
		$( "#window_formato_orden" ).dialog({
			autoOpen: false,
			width: 840,
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_formato_orden" ).remove();
				if($("#ListFactura").lenght)
					$("#ListFactura").multiselect("open");
			},
			buttons: {
				Imprimir: function() {
					$('.printable').jqprint();
				},
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		IniciaDetalles('<?=$_REQUEST['tramite']?>');
		$( "#window_formato_orden" ).dialog("open");					
	});
	
	function IniciaDetalles(Tramite){
		var plantilla = Handlebars.compile($('#Plantilla_Formato_Orden').html());
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "FormatoOrdenGiro", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			var html = plantilla(Detalles);
			$('#FormatoOrden').html(html);	
			$('#conceptoFormato').html(Detalles.concepto);	
			
			if(Detalles.id_compania == "1")
				$('#CheckGenerales').attr('checked', true);
			else
				$('#CheckVida').attr('checked', true);
			
			setSize();			
			$( "#window_formato_orden" ).dialog( "option", "position", { my: "center", at: "center"} );


		});
	}
	
	
	
	function setSize(){
		var height =  $("#window_formato_orden").height()+80;
		if(height > $(window).height ()){
			$("#FormatoOrden").css('height',$(window).height()-130);
		}	
	}
	
	
</script>
</div>