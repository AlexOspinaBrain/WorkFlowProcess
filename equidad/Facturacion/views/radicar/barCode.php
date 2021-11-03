<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_codebar">
	<div style="padding:25px">
		<table border="0" class="printable" style="font-size:8px" width="340px">
			<tr>
				<td style="padding:0px">
					<b>Fecha y hora: </b><span id="fechahora_insCB"></span><br>
					<b>Proveedor: </b><span id="nombreCB"></span><br>
					<b>Aseguradora: </b><span id="des_companiaCB"></span><br>
					<b>Area destino: </b><span id="areaCB"></span><br>					
					<b>Destinatario: </b><span id="destinatarioCB"></span><br>
				</td>
				<td width="100px" align="center">
					<b>LA EQUIDAD SEGUROS O.C.</b><br>
					<img src='config/barcode/image.php?filetype=PNG&dpi=72&thickness=30&scale=1&rotation=0&font_family=Arial.ttf&font_size=10&text=<?= $_REQUEST['codebar']?>&code=BCGcode128'/>
					<b>Pago de facturas</b>
				</td>
			</tr>
		</table>
	</div>
<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
<script>
	$(document).ready(function() {
		$( "#window_codebar" ).dialog({
			width:'auto',
			closeOnEscape: true,
			resizable: false,      			
      		modal: true,
			buttons: {
				"Imprimir": function() {
					$('.printable').jqprint();
				},
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function (){
				$( "#window_codebar" ).remove();
			}
		});
		
		IniciaForm('<?=$_REQUEST['codebar']?>');
	});
	
	function IniciaForm(codebar){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaFactura", tramite:codebar},
			success	:function (data){
				var datos=$.parseJSON(data);
				$("#fechahora_insCB").html(datos.fechahora_ins);
				$("#nombreCB").html(datos.nombre);
				$("#des_companiaCB").html(datos.des_compania);
				$("#areaCB").html(datos.area);
				$.each(datos.historial, function(index, value) {
  					if(value.actividad == 'Recibir en el área')  						
  						$("#destinatarioCB").html(value.usuario);
				});
			}
		});
	}
</script>
</div>