<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_devuelve" title="Devolver tramite">
	<script id="Plantilla_Devuelve" type="text/x-handlebars-template">
		<form id="FormDevolucion" class="formular" style="padding:10px">
			<p>
				<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
				<h3>Devolucion de tramite</h3>
				<div style="margin-left:30px">
					La <?=$_REQUEST['tipo_tramite']?> <b><?=$_REQUEST['Tramite']?></b> quedara devuelta al estado <b id="ActividadProx"></b>. <br> <br>
					Desea devolverla?
				<div>
			</p>	
			<table class="TblGreen">
				<!--<tr class="alt"><td>Seleccione usuario: </td></tr>
				<tr><td>
					<select id="UsuarioDevolcucion" name="UsuarioDevolcucion" class="validate[required] ui-widget-content ui-corner-all" style="padding:4px;">
						<option></option>
					</select>			
				</td></tr>	-->
				<tr class="alt"><td>Observaciones: </td></tr>
				<tr><td>
					<textarea id="ObservacionesDev" name="ObservacionesDev" class="validate[required] ui-widget-content ui-corner-all" style="resize: none;width:350px"><?=$_REQUEST['observaciones']?></textarea>
				</td></tr>
			</table>
			<input type="hidden" id="UsuarioDevolcucion" name="UsuarioDevolcucion"/>
		</form>
	</script>
	
	<script id="Plantilla_NoAceptaDev" type="text/x-handlebars-template">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
			<h3>Error</h3>
			<div style="margin-left:30px">
				El tramite <b><?=$_REQUEST['Tramite']?></b> no puede ser devuelto en esta actividad.
			<div>
		</p>
	</script>

	<script id="Plantilla_NoRecibe" type="text/x-handlebars-template">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
			<h3>Devolver tramite</h3>
			<div style="margin-left:30px">
				Que desea hacer con el tramite <b>{{serial_factura}}</b>.
			<div>
		</p>
	</script>
<?php 
	$_REQUEST['observaciones']=str_replace(array("'", "\""), '´',utf8_decode($_REQUEST['observaciones']));
	$_REQUEST['observaciones']=str_replace(array("\r\n", "\n"), '<br>',$_REQUEST['observaciones']);
	$_REQUEST['observaciones']=str_replace("\r", '',$_REQUEST['observaciones']);
?>

<script>
	$(document).ready(function() {		
		var Tramite = '<?=$_REQUEST['Tramite']?>';
		var tipo_tramite = '<?=$_REQUEST['tipo_tramite']?>';
		var observaciones='<?=$_REQUEST['observaciones']?>';
		$( "#window_devuelve" ).dialog({
			autoOpen: false,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_devuelve" ).remove();
				$("#ListPendientes").setGridParam({page:1}).trigger("reloadGrid");
				$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid");
			},
			buttons: {
				Aceptar: function() {
					ContinuaDevuelve(Tramite, tipo_tramite);
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		$("#FormDevolucion").validationEngine();	
		//statusWorkflow(Tramite, tipo_tramite);
		validaDevolucion(Tramite, tipo_tramite);
		//if(observaciones.length > 0)
		//	ContinuaDevuelve(Tramite, tipo_tramite);
	});

	function validaDevolucion(Tramite, tipo_tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "ConfirmaRecibeFactura", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			if(Detalles.actividad == 'Generar orden de giro' || Detalles.actividad == 'Recibir en el área')
				NoRecibe(Tramite, Detalles.desc_documento);
			else
				statusWorkflow(Tramite, tipo_tramite);
		});
	}

	function NoRecibe(tramite, tipo_doc){
		var plantilla = Handlebars.compile($('#Plantilla_NoRecibe').html());
		var html = plantilla({serial_factura:tramite});	
		$('body').append("<div id='window_NoRecibe'>"+html+"</div>");
			$( "#window_NoRecibe" ).dialog({
			autoOpen: true,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,  
      		modal: true,
			close: function (){
				$( "#window_NoRecibe" ).remove();
			},
			buttons: {
				"Devolver": function() {statusWorkflow(tramite, tipo_doc);},
				"Devolver al proveedor": function() {DevolverProveedor(tramite);},
				"Cancelar": function() {$( this ).dialog( "close" );}
			}
		});	
	}
	
	function DevolverProveedor(tramite){
		$(".ui-dialog-buttonpane button:contains('Devolver')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Devolver al proveedor')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Devolver al proveedor') span").text("Espere ...");

		$.ajax({
			type: "POST",
			url: "Facturacion/views/devolucion/devuelveProveedor.php",
			data: { Tramite: tramite},
			success	:function (data){
				$(".ui-dialog-content").dialog("close");
				MuestraCodeBar(data, '<?=$_SESSION['uscod']?>');
			}
		});	
	}

	function statusWorkflow(Tramite, tipo_doc){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys2.php",
			data: { op: "statusWorkflow", tramite:Tramite, workflow:'Devolución', tipo_doc:tipo_doc},
			success	:function (data){
				var Detalles=$.parseJSON(data);
				
				if(Detalles.proxima == null){
					var plantilla = Handlebars.compile($('#Plantilla_NoAceptaDev').html());
					$('#window_devuelve').append(plantilla);
					$( "#window_devuelve" ).dialog( "option", "buttons", [ 
						{ text: "Aceptar", click: function() { $( this ).dialog( "close" ); } },
					 ] );
				}else{
					var plantilla = Handlebars.compile($('#Plantilla_Devuelve').html());
					$('#window_devuelve').append(plantilla);
					$("#ActividadProx").html(Detalles.proxima);
					$( "#tipo_doc" ).html(tipo_doc);
					$("#UsuarioDevolcucion").val(Detalles.usuario);
					//CargaUsuariosDevolucion(Detalles.usuario);
				}

				$( "#window_devuelve" ).dialog("open");				
			}
		});		
	}

	function ContinuaDevuelve(Tramite, tipo_doc){
		if($("#FormDevolucion").validationEngine('validate')){
			$(".ui-dialog-buttonpane button:contains('Aceptar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
			$(".ui-dialog-buttonpane button:contains('Aceptar') span").text("Guardando ...");
			var wf = "Devolución";
			$.ajax({
				type: "POST",
				url: "Facturacion/config/ajax_querys.php",
				data: { op: "ContinuaDevuelve", usuario:$("#UsuarioDevolcucion").val(), Observaciones: $("#ObservacionesDev").val(), tramite:Tramite, tipo_doc:tipo_doc, workflow:wf},
				success	:function (data){
					try{
						var json = $.parseJSON(data);
						if (json.guardado)	
							$(".ui-dialog-content").dialog("close");	
					}catch(err){
						alert(data);
						$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Terminar");
						$(".ui-dialog-buttonpane button:contains('Terminar')").button("enable");
						$(".ui-dialog-buttonpane button:contains('Atras')").button("enable");
					}		
				}
			});		
		}
	}

	function CargaUsuariosDevolucion(usuPreselect){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "UsuarioRecibeRadicacion"},
			success	:function (data){
				var json = $.parseJSON(data);
				$.each(json, function(index, value) {
				  	$("#UsuarioDevolcucion").append("<option value='"+value.usuario_cod+"'>"+value.usuario+"</option>");
				});
				$("#UsuarioDevolcucion").val(usuPreselect);
			}
		});	
	}	
</script>
</div>