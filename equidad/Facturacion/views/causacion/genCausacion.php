<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_OrdenGiro" title="Causación">	
	<script id="Plantilla_Form" type="text/x-handlebars-template">
		<form id="FormRadica" class="formular" style="padding:10px">	
		<table class="TblGreen">
			<tr class="alt"><td>Seleccione usuario (Tesorería): </td></tr>
			<tr><td>
				<select id="usuario" name="usuario" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>			
			</td></tr>	
			<tr class="alt"><td>Observaciones: </td></tr>
			<tr><td>
				<textarea id="Observaciones" name="Observaciones" class="validate[required] text ui-widget-content ui-corner-all" style="resize: none;width:350px"></textarea>
			</td></tr>
		</table>
	</form>
	</script>
	
	<script id="Plantilla_NoExiste" type="text/x-handlebars-template">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
			<h3>Error</h3>
			<div style="margin-left:30px">La orden de giro <b>{{num_ordengiro}}</b> no 
				ha sido encontrada o no esta disponible para esta actividad, por favor verifique el número ingresado.<div>
		</p>
	</script>

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
				Cerrar: function() {
					$( this ).dialog( "close" );
				},
			}
		});	
		ValidaTramite('<?=$_REQUEST['tramite']?>');
	});

	function ValidaTramite(tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "ConfirmaGenCausacion", tramite:tramite},
			success	:function (data){
				var json = $.parseJSON(data);
			
				if(json){
					var plantilla = Handlebars.compile($('#Plantilla_Form').html());
					var html = plantilla();				
					$( "#window_OrdenGiro" ).dialog( "option", "buttons", [ 
						{ text: "Aceptar", click: function() { GuardaCausacion(tramite); } },
						{ text: "Devovler", click: function() { Devolucion('<?=$_REQUEST['tramite']?>', 'Orden de giro');	 } },
						{ text: "Cancelar", click: function() { $( this ).dialog( "close" ); } }
					 ] );
					$('#window_OrdenGiro').prepend(html);
					CargaUsuarios();
				}else{
					var plantilla = Handlebars.compile($('#Plantilla_NoExiste').html());
					var html = plantilla({num_ordengiro : tramite});
					$( "#window_OrdenGiro" ).dialog( "option", "title", "Error orden de giro" );
					$('#window_OrdenGiro' ).dialog( "option", "width", 300 );
					$('#window_OrdenGiro').prepend(html);
				}
				$( "#window_OrdenGiro" ).dialog("open");
			}
		});
	}

	function CargaUsuarios(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "UsuarioRecibeCausacion"},
			success	:function (data){
				var json = $.parseJSON(data);

				$.each(json, function(index, value) {
				  $("#usuario").append("<option value='"+value.usuario_cod+"'>"+value.usuario+"</option>");
				});
			}
		});	
	}	


	function Devolucion(tramite, tipo_tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/devolucion/devuelve.php",
			data: { Tramite: tramite, tipo_tramite: tipo_tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});	
	}
	
	function GuardaCausacion(tramite){
		if($("#FormRadica").validationEngine('validate')){
			$(".ui-dialog-buttonpane button:contains('Aceptar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Aceptar') span").text("Guardando ...");
		
			var datos = $("#FormRadica").serializeArray();
			datos.push({name :"op", value:"GenCausacion"});
			datos.push({name :"tramite", value:tramite});

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
					//alert(data);
					$('#window_OrdenGiro' ).dialog( "close");
				}
			});	
		}
	}
</script>
</div>