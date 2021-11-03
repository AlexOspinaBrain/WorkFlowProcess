<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_OrdenGiro" title="Auditoria">	
	<form id="FormRadica" class="formular" style="padding:10px">
		<p>
			<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
			Visar comprobante de pago <a class="link2" href="#" onclick="DetallesCP('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> ?
		</p>	
		<table class="TblGreen">
			<tr class="alt"><td>Seleccione usuario (Tesorería): </td></tr>
			<tr><td>
				<select id="Usuario" name="Usuario" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>			
			</td></tr>	
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
			autoOpen: false,
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
				Si: function() {
					GuardaAuditoria();
				},
				No: function() {
					if(!$( "#Observaciones" ).validationEngine('validate'))
						Devolucion('<?=$_REQUEST['tramite']?>', 'Comprobante de pago', $( "#Observaciones" ).val());
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				},
			}
		});	

		CargaUsuarios();
		$( "#window_OrdenGiro" ).dialog("open");
	});


	function CargaUsuarios(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "UsuarioRecibeCierre"},
			success	:function (data){
				var json = $.parseJSON(data);

				$.each(json, function(index, value) {
				  $("#Usuario").append("<option value='"+value.usuario_cod+"'>"+value.usuario+"</option>");
				});
			}
		});	
	}	
	
	function GuardaAuditoria(){
		
		if($("#FormRadica").validationEngine('validate')){
			$(".ui-dialog-buttonpane button:contains('Visado')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Devolver')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Visado') span").text("Guardando ...");
		
			var datos =  $('#FormRadica').serializeArray();
			datos.push({name :"op", value:"GuardaAuditoria"});

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

	function Devolucion(tramite, tipo_tramite, observaciones){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/devolucion/devuelve.php",
			data: { Tramite: tramite, tipo_tramite: tipo_tramite, observaciones:observaciones},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});	
	}
</script>
</div>