<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_OrdenGiro" title="Cerrar comprobante de pago">	
	<form id="FormRadica" class="formular" style="padding:10px">
		<p>
			<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
			Cerrar comprobante de pago <a class="link2" href="#" onclick="DetallesCP('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> ?
		</p>	
		<table class="TblGreen">
			<tr class="alt"><td>Observaciones: </td></tr>
			<tr><td>
				<textarea id="Observaciones" name="Observaciones" class="validate[required] text ui-widget-content ui-corner-all" style="resize: none;width:350px"></textarea>
			</td></tr>
		</table>
		<input type="hidden" name="Comprobante" value="<?=$_REQUEST['tramite']?>"/>
	</form>


<style>
	p .link2 {	color: #327E04;text-decoration: none;font-weight: bold;}
	p .link2:hover{text-decoration: underline}
</style>	

<script>
	$(document).ready(function() {		
		$( "#window_OrdenGiro" ).dialog({
			autoOpen: true,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_OrdenGiro" ).remove();
				$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid")
			},
			buttons: {
				"Cerrar CP": function() {
					GuardaCierre();
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				},
			}
		});	
	});
	
	function GuardaCierre(){		
		if($("#FormRadica").validationEngine('validate')){
			$(".ui-dialog-buttonpane button:contains('Cerrar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Devolver')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Cerrar') span").text("Guardando ...");
		
			var datos =  $('#FormRadica').serializeArray();
			datos.push({name :"op", value:"GuardaCierre"});

			$.ajax({
				type: "POST",
				url: "Facturacion/config/ajax_querys.php",
				data: datos, 
				success	:function (data){
					try{
						var json = $.parseJSON(data);
						if (json.guardado)	
							$('#window_OrdenGiro' ).dialog( "close");
					}catch(err){
						alert(data);
					}  
					$('#window_OrdenGiro' ).dialog( "close");
				}
			});	
		}
	}
</script>
</div>