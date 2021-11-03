<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_OrdenGiro" title="Envia orden de giro">		
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
		Desea enviar la orden de giro <a class="link2" href="#" onclick="DetallesOrdenGiro('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> ?
	</p>

	<form id="FormRadica" class="formular" style="padding:10px">	
		<table class="TblGreen">
			<tr class="alt"><td>Seleccione usuario: </td></tr>
			<tr><td>
				<select id="Usuario" name="Usuario" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>			
			</td></tr>	
		</table>
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
				Enviar: function() {
					if($("#FormRadica").validationEngine('validate')){
						RecibeTramite('<?=$_REQUEST['tramite']?>', $("#Usuario").val());
					}					
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				},
			}
		});	

		CargaUsuarios();
	});

	function CargaUsuarios(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "UsuarioRecibeOrdenGiro"},
			success	:function (data){
				var json = $.parseJSON(data);

				$.each(json, function(index, value) {
				  $("#Usuario").append("<option value='"+value.usuario_cod+"'>"+value.usuario+"</option>");
				});
			}
		});	
	}	
	
	function RecibeTramite(tramite, usuario){
		$(".ui-dialog-buttonpane button:contains('Enviar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Enviar') span").text("Enviado ...");
	
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "EnviaOrdenGiro", tramite:tramite, usuario:usuario},
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
</script>
</div>