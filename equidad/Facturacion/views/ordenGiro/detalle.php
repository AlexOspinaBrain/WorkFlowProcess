<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="window_detalle_orden" title="Detalles">
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
		#FacOrden .ui-state-hover a{	color: white;}		
		#FacOrden a {	color: #327E04;text-decoration: none;font-weight: bold;}
		#FacOrden a:hover{text-decoration: underline}
		.TableRadicado .link2 {	color: #327E04;text-decoration: none;font-weight: bold;}
		.TableRadicado .link2:hover{text-decoration: underline}
	</style>
	
	<div id="ContentPlantillaOrden"></div>	
	<script id="Detalles_Orden" type="text/x-handlebars-template">
		<fieldset>
			<legend><b style="font-size: 12px; ">Datos del tramite: </b></legend>		
			<table class="TableRadicado">				
				<tr><td>Numero de Orden de giro: <span style="color: #B40404;font-size: 13px;">{{num_ordengiro}}</span></td></tr>
				<tr><td>Fecha de creación: <span>{{fecha_ins}}</span></td><td>Creado por: <span>{{usuario}}</span></td></tr>
				<tr><td>Aseguradora: <span>{{des_compania}}</span></td><td>Cantidad tramites: <span>{{cant_fac}}</span></td></tr>	
				<tr><td>Estado: <span>{{estado}}</span></td><td><a id="link_PintOG" class="link2" href="#">Orden de giro versión impresa</a></td></tr>	
			</table>
		</fieldset>
		<br>
		<fieldset>
			<legend><b style="font-size: 12px;">Facturas y/o cuentas de cobro: </b></legend>		
			<table id="FacOrden"></table>
		</fieldset>
		<br>
		<fieldset id="observaciones">
			<legend><b style="font-size: 12px;">Observaciones: </b></legend>
		</fieldset>
	</script>
<script>
	$(document).ready(function() {		
		$( "#window_detalle_orden" ).dialog({
			autoOpen: false,
			width: 'auto',
			height : 'auto',
			closeOnEscape: true,
			resizable: false,    
      		modal: true,
			close: function (){
				$( "#window_detalle_orden" ).remove();
				if($("#ListFactura").lenght)
					$("#ListFactura").multiselect("open");
			},
			buttons: {
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		IniciaDetalles('<?=$_REQUEST['tramite']?>');
		$( "#window_detalle_orden" ).dialog("open");					
	});
	
	function IniciaDetalles(Tramite){
		var plantilla = Handlebars.compile($('#Detalles_Orden').html());
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaOrdenGiro", tramite:Tramite}
		}).done(function( data ) {	
			var Detalles=$.parseJSON(data);
			var html = plantilla(Detalles);
			$('#ContentPlantillaOrden').html(html);		
			$('#link_PintOG').click(function(){formatoOG(Detalles.num_ordengiro)})
			$('#observaciones').append(Detalles.observaciones);	
			tables(Detalles.id_ordengiro);
			setSize();			
			$( "#window_detalle_orden" ).dialog( "option", "position", { my: "center", at: "center"} );
		});
	}
	
	function tables(id_ordengiro){
		$("#FacOrden").jqGrid({
			url:'Facturacion/config/json.php',
			postData:{query:'FacturaInOrdenGiro', id_ordengiro:id_ordengiro},
			datatype: "json",
			height: 100,
			width: 600,
			colNames:[ 'Tramite','No. factura', 'Proveedor', 'Tipo documento'],
			colModel:[
				{name:'serial_factura',index:'serial_factura', width:20, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
				{name:'no_factura',index:'no_factura', width:15},
				{name:'proveedor',index:'proveedor', width:50},
				{name:'desc_documento',index:'desc_documento', width:15},		
			],
			scroll:1,
			rowNum:50,
			pager: '#PagerFacOrden',
			sortname: 'rad.fechahora_ins',
			viewrecords: true,
			sortorder: "desc",
			altRows:true,
			altclass:'altClassrow',
			gridComplete: function (){
				$("#FacOrden td[aria-describedby='FacOrden_serial_factura'] a").click(function (){DetallesFactura($(this).text());})
			}
		});
	}
	
	function setSize(){
		var height =  $("#window_detalle_orden").height()+80;
		if(height > $(window).height ()){
			$("#ContentPlantillaOrden").css('height',$(window).height()-130);
		}	
	}
	
	function DetallesFactura(Tramite){
		$.ajax({
			type: "GET",
			url: "Facturacion/views/radicar/detalle.php",
			data: { tramite: Tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});	
	}

	function formatoOG(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/views/ordenGiro/formatoOG.php",
			data: { tramite: Tramite},
			success	:function (data){
				$( "body" ).append(data);	
			}
		});		
	}
</script>
</div>