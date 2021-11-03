<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_RecibeCP" title="Recibir comprobante de pago">		
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
		Recibiendo CP <a class="link2" href="#" onclick="DetallesCP('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> ?
	</p>
<style>
	p .link2 {	color: #327E04;text-decoration: none;font-weight: bold;}
	p .link2:hover{text-decoration: underline}
</style>	

<script>
	$(document).ready(function() {		
		$( "#window_RecibeCP" ).dialog({
			autoOpen: true,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_RecibeCP" ).remove();
				$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid")
			},
			buttons: {
				Recibir: function() {					
					RecibeTramite('<?=$_REQUEST['tramite']?>', $("#Usuario").val());								
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				},
			}
		});	

		RecibeTramite('<?=$_REQUEST['tramite']?>');
	});

	function RecibeTramite(tramite){
		$(".ui-dialog-buttonpane button:contains('Recibir')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Recibir') span").text("Recibiendo ...");
	
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "RecibirCP", tramite:tramite},
			success	:function (data){
				try{
					var json = $.parseJSON(data);
					if (json.guardado)	
						$('#window_RecibeCP' ).dialog( "close");
				}catch(err){
					alert(data);
				}  
				$('#window_RecibeCP' ).dialog( "close");
			}
		});	
	}
</script>
</div>