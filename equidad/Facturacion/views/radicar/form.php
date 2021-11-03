<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="WindowRadicador" title="Radicar nuevo">
<form id="FormRadica" class="formular" style="padding:10px;position:relative">	
	<table class="TblGreen" id="tblRadica">
		<tr><th colspan="2" style="text-align:left;font-size:1.2em;">Documento</th></tr>
		<tr>
			<td colspan="2">
				<label for="TipoDocumento">Tipo de documento: </label>
				<select id="TipoDocumento" name="TipoDocumento" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>
			</td>				
		</tr>

		<tr><th colspan="2" style="text-align:left;font-size:1.2em;">Datos <span class="pla_documento">Factura o Cuenta de cobro</span></th></tr>
		<tr>
			<td>
				<label for="Identificacion">Nit o CC proveedor</label>
				<input type="text" id="Identificacion" name="Identificacion" class="validate[required, custom[number]] text ui-widget-content ui-corner-all" style="width:150px">
				<div style="margin:-13px 18px; position: absolute;font-weight: bold;font-size: 10px;">( Sin digito de verificación )</div>
			</td>
			<td>
				<label for="NombreProveedor">Nombre proveedor</label>
				<input type="text" id="NombreProveedor" name="NombreProveedor" class="text ui-widget-content ui-corner-all" style="width:250px" readonly>
			</td>			
		</tr>
		<tr class="alt">
			<td>
				<label for="NoFactura">No. <span class="pla_documento">factura o Cuenta de cobro</span></label>
				<input type="text" id="NoFactura" name="NoFactura" class="validate[required] text ui-widget-content ui-corner-all" style="width:150px">
			</td>
			<td>
				<label for="ValorFactura">Valor <span class="pla_documento">factura o Cuenta de cobro</span></label>
				<input type="text" id="ValorFactura" name="ValorFactura" class="validate[required] text ui-widget-content ui-corner-all" maxlength="14" style="width:120px">
			</td>
		</tr>
		
		<tr>
			<td style="height:70px">
				<label for="fecha_expedicion">Fecha de expedición</label>
				<input type="text" id="fecha_expedicion" name="fecha_expedicion" class="validate[required, custom[date], custom[fechaExpedicion]] text ui-widget-content ui-corner-all" style="width:80px" />
				<div style="margin:-12px 7px; position: absolute; font-size: 10px;"><b>(DD/MM/AAAA)</b></div>
			</td>
			<td>
				<label for="fecha_vencimiento">Fecha de vencimiento</label>
				<input type="text" id="fecha_vencimiento" name="fecha_vencimiento" class="validate[required, custom[date], custom[fechaVencimiento]] text ui-widget-content ui-corner-all" style="width:80px">
				<div style="margin:-12px 7px; position: absolute; font-size: 10px;"><b>(DD/MM/AAAA)</b></div>
			</td>
		</tr>

		<tr><th colspan="2" style="text-align:left;font-size:1.2em;">Datos Destinatario</th></tr>
		<tr>
			<td>
				<label for="Aseguradora">Aseguradora</label>
				<select id="Aseguradora" name="Aseguradora" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>
			</td>
			<td>
				<label for="Agencia">Agencia</label>
				<select id="Agencia" name="Agencia" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>
			</td>			
		</tr>
		<tr class="alt">
			<td >
				<label for="Area">Area</label>
				<select id="Area" name="Area" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
				</select>
			</td>
	
			<td >
				<label for="Destinatario">Destinatario</label>
				<select id="Destinatario" name="Destinatario" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
				</select>
			</td>
		</tr>
	
		<tr style="display:none" id="docRequeridos"><th colspan="2" style="text-align:left;font-size:1.2em;">Documentos requeridos</th></tr>

	</table>
	<input type="hidden" id="id_proveedor" name="id_proveedor">
	<input type="hidden" id="Consecutivo" name="Consecutivo">
</form>
<script>
	$(document).ready(function() {
		$( "#WindowRadicador" ).dialog({
			autoOpen: false,
			width:'auto',
			closeOnEscape: true,
			resizable: false,      			
      		modal: true,
			close: function (){	$( "#WindowRadicador" ).remove();},
			buttons: {
				Guardar: function() {
					if($("#FormRadica").validationEngine('validate')){
						datos = $('#FormRadica').serialize();
						guardaRadicacion(datos);
					}
				},
				Cerrar: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	
		$("#FormRadica").validationEngine();		
		$('#fecha_vencimiento, #fecha_expedicion').mask("99/99/9999");
		$("#ValorFactura").maskMoney({symbol:' $ ', thousands:'.', decimal:',' , symbolStay: true, precision:0});
		
		$( "#Identificacion").change(function (){
			BuscaProveedor($( "#Identificacion").val());
			
		});
		
		$( "#Agencia").change(function (){
			BuscaAreas($( "#Agencia").val());
		});
		
		$( "#Area").change(function (){
			BuscaDestinatarios($( "#Area").val());
		});
		
		$( "#Identificacion").keypress(function(e) {
			if(e.which == 13) 
				BuscaProveedor($( "#Identificacion").val());		
		});			

		
		CargaAseguradora();
		CargaAgencias();
		BuscaTipoDoc();
		IniciaForm('<?= @$_REQUEST['tramite'] ?>');
		
		$( "#WindowRadicador" ).dialog("open");
		setSize();
	});
	
	function BuscaProveedor(Identificacion){
		if (!isNaN(Identificacion))
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaProveedor", identificacion:Identificacion},
			success	:function (data){
				var datos=$.parseJSON(data);
				$( ".DocRec" ).remove();
				$( "#docRequeridos" ).hide();

				if(!datos){			
					$( "#Identificacion").val('');
					$('#Identificacion').validationEngine('showPrompt', 'Proveedor no existe', 'error')
					$( "#NombreProveedor" ).val('');	
				}else{				
					$( "#NombreProveedor" ).val(datos.nombre);	
					$( "#id_proveedor" ).val(datos.id_proveedor);

					if(datos.requeridos.length>0)
						$( "#docRequeridos" ).show();

					$.each(datos.requeridos,function(indice,valor) {
						var clase="";
						if(indice%2==1)
							clase="alt";
						$( "#tblRadica" ).append('<tr class="DocRec '+clase+'"><td colspan="2"><input type="checkbox" name="Doc'+valor.id_documento+'" class="validate[required] checkbox"/> '+valor.desc_documento+'</td></tr>');
					});
					setSize();
				}
			}
		});		
	}
	
	function guardaRadicacion(datos){
		$(".ui-dialog-buttonpane button:contains('Terminar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Atras')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Terminar') span").text("Guardando ...");
		
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php?"+datos,
			data: { op: "GuardaRadicacion"},
			success	:function (data){
				try{
					var json = $.parseJSON(data);
					$.ajax({
						type: "POST",
						url: "Facturacion/views/radicar/barCode.php",
						data: { codebar: json},
						success	:function (data){
							$( "body" ).append(data);	
							$( "#WindowRadicador" ).dialog("close");
							$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid")
						}
					});				
				}catch(err){
					alert(data);
					$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Terminar");
					$(".ui-dialog-buttonpane button:contains('Terminar')").button("enable");
					$(".ui-dialog-buttonpane button:contains('Atras')").button("enable");
				}  
			}
		});
	}
	
	function BuscaTipoDoc(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaTipoDoc"},
			success	:function (data){
				var datos=$.parseJSON(data);
				$.each(datos,function(indice,valor) {
					$( "#TipoDocumento" ).append('<option value="'+valor.id_documento+'">'+valor.desc_documento+'</option>');
				});
			}
		});
		
		/*$( "#TipoDocumento" ).change(function (){			
			$( ".pla_documento").html($( "#TipoDocumento option:selected" ).text());
			GetCheckList($( "#TipoDocumento" ).val());
		});*/
	}
	
	function CargaAseguradora(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "CargaAseguradora"},
			success	:function (data){
				var datos=$.parseJSON(data)
				$.each(datos,function(indice,valor) {
					$( "#Aseguradora" ).append('<option value="'+valor.id_compania+'">'+valor.des_compania+'</option>');
				});
			}
		});
	}
	
	function CargaAgencias(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "CargaAgenciaRadicaFac"},
			success	:function (data){
				var datos=$.parseJSON(data)
				if(datos.length == 1)
					$( "#Agencia" ).html('');

				$.each(datos,function(indice,valor) {
					$( "#Agencia" ).append('<option value="'+valor.codigo+'">'+valor.descrip+'</option>');
				});

				if(datos.length == 1)
					BuscaAreas($( "#Agencia" ).val())
			}
		});
	}	
	
	function BuscaAreas(IdAgencia){
		if (IdAgencia == null ) 
			return;

		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "CargaAreasRadicaFac", id_agencia:IdAgencia},
			success	:function (data){
				var datos=$.parseJSON(data)
				$( "#Area" ).html('<option></option>');
				$.each(datos,function(indice,valor) {
					$( "#Area" ).append('<option value="'+valor.areasid+'">'+valor.area+'</option>');
				});
			}
		});
	}
	
	function BuscaDestinatarios(IdArea){
		if (IdArea == null ) 
			return;

		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "CargaDestinatariosRadicaFac", id_area:IdArea},
			success	:function (data){
				var datos=$.parseJSON(data)
				$( "#Destinatario" ).html('<option></option>');
				$.each(datos,function(indice,valor) {
					$( "#Destinatario" ).append('<option value="'+valor.usuario_cod+'">'+valor.usuario_nombres+' '+valor.usuario_priape+' '+valor.usuario_segape+'</option>');
				});
			}
		});
	}
	
	function IniciaForm(IdFactura){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaFactura", tramite:IdFactura},
			success	:function (data){
				var datos=$.parseJSON(data);
				$("#fecha_expedicion").val(datos.fecha_expedicion);
				$("#fecha_vencimiento").val(datos.fecha_vencimiento);
				$("#TipoDocumento").val(datos.id_documento);
				$("#Identificacion").val(datos.documento);
				$("#NombreProveedor").val(datos.nombre);
				$("#NoFactura").val(datos.no_factura);
				$("#ValorFactura").val(datos.valor_fac);				
				$("#Agencia").val(datos.codigo);
				$("#id_proveedor").val(datos.id_proveedor);
				setTimeout(function(){$("#TipoDocumento").val(datos.id_documento);},200);
				setTimeout(function(){$("#Aseguradora").val(datos.id_compania_rad);},400);
				setTimeout(function(){$("#Agencia").val(datos.codigo);BuscaAreas(datos.codigo)},600);
				setTimeout(function(){$("#Area").val(datos.id_area);BuscaDestinatarios(datos.id_area)},800);
				//setTimeout(function(){$("#Destinatario").val(datos.usuario_cod)},1000);
				setTimeout(function(){BuscaProveedor(datos.documento)},1200);
				setTimeout(function(){$(".DocRec > td > input").attr('checked','checked');},1400);
				
				$("#Consecutivo").val(IdFactura);
			}
		});
	}
	
	/*
	function GetCheckList(IdDocumento){
		CeldaAlt=false;
		$("#TblCheckList > tbody > tr").remove();
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "GetCheckList", id_documento:IdDocumento},
			success	:function (data){
				var datos=$.parseJSON(data);
				$.each(datos,function(indice,valor) {					
					$("#TblCheckList > tbody").append('<tr '+((CeldaAlt)?'class="alt"':'')+'><td><input class="validate[required] checkbox" type="checkbox" id="checklist['+valor.id_list+']" name="checklist['+valor.id_list+']"> '+valor.desc_list+'</td></tr>');
					CeldaAlt= !CeldaAlt;
				});
				
				$("#TblCheckList > tbody").append(
					'<tr '+((CeldaAlt)?'class="alt"':'')+'><td><label for="Observaciones">Observaciones</label>'+
					'<textarea id="Observaciones" name="Observaciones" class="validate[required] text ui-widget-content ui-corner-all" style=" resize: none;width: 500px"></textarea>'+
					'</td></tr>');
			}
		});
	}*/

	function setSize(){
		var height =  $("#WindowRadicador").height()+80;
		if(height > $(window).height ()){
			$("#WindowRadicador").css('height',$(window).height()-130);
		}
		$( "#WindowRadicador" ).dialog( "option", "position", { my: "center", at: "center"} );
	}

</script>
</div>	