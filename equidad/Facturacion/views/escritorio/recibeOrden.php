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
		Espere, recibiendo Orden de giro <a class="link2" href="#" onclick="DetallesOrdenGiro('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> .
	</p>
	
	<!--<p>
		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
		Desea recibir la orden de giro <a class="link2" href="#" onclick="DetallesOrdenGiro('<?=$_REQUEST['tramite']?>')"> <?=$_REQUEST['tramite']?> </a> ?
	</p>-->
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
				/*Recibir: function() {					
					RecibeTramite('<?=$_REQUEST['tramite']?>');								
				},
				Devolver: function() {					
					Devolucion('<?=$_REQUEST['tramite']?>', 'OrdenGiro');								
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				},*/
			}
		});	
		RecibeTramite('<?=$_REQUEST['tramite']?>');
	});

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
	
	function RecibeTramite(tramite){
		$(".ui-dialog-buttonpane button:contains('Recibir')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Recibir') span").text("Recibiendo ...");
	
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "EnviaOrdenGiro", tramite:tramite},
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