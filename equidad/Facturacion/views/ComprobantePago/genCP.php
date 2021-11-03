<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="WindowGenCp" title="Generar comprobante de pago">
<form id="FormRadica" class="formular" style="padding:10px">	
	<table class="TblGreen">
		<tr class="alt"><td colspan="4">Datos comprobante de pago: </td></tr>
		<tr>
			<td>
				<label for="Comprobante">Comprobante de pago</label>
				<input type="text" id="Comprobante" name="Comprobante" class="validate[required] text ui-widget-content ui-corner-all" style="width:100px">	
			</td>

			<td>
				<label for="ValorCP">Valor <span class="pla_documento">Valor CP</span></label>
				<input type="text" id="ValorCP" name="ValorCP" class="validate[required] text ui-widget-content ui-corner-all" maxlength="14" style="width:120px">
			</td>

			<td>
				<label for="MedioPago">Medio de pago</label>
				<select id="MedioPago" name="MedioPago" class="validate[required] text ui-widget-content ui-corner-all" style="padding:5px;">
					<option></option>
					<option>Cheque</option>
					<option>Transferencia</option>
				</select>			
			</td>
		</tr>
		<tr>
			<td>
				<label for="Aseguradora">Aseguradora</label>
				<select id="Aseguradora" name="Aseguradora" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>
			</td>

			<td colspan="2">
				<label for="Identificacion" >Nit proveedor</label>
				<input type="text" id="Identificacion" name="Identificacion" class="validate[required, custom[number]] text ui-widget-content ui-corner-all" style="width:150px">		
			</td>
		</tr>	
		<tr class="alt"><td colspan="4">Seleccione las ordenes de giro: </td></tr>
		<tr>
			<td align="center" colspan="4">
				<div id="ContenidoError"></div>
				<table id="ListOrdenesPen"></table>
			</td>
		</tr>
		<tr class="alt"><td colspan="4">Seleccione usuario (Auditoria): </td></tr>
		<tr>
			<td colspan="4">
				<select id="Usuario" name="Usuario" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
					<option></option>
				</select>	
			</td>
		</tr>
		<tr class="alt"><td colspan="4">Observaciones: </td></tr>
		<tr>
			<td colspan="4">
				<textarea id="Observaciones" name="Observaciones" class="validate[required] ui-widget-content ui-corner-all" style="resize: none;width:350px"></textarea>
			</td>
		</tr>
	</table>
	<input type="hidden" name="id_comprobante" id="id_comprobante"/>
</form>
<style>
	
	#ListOrdenesPen td{font-size: 11px;border:0px;}
	#ListOrdenesPen .ui-state-hover a{	color: white;}		
	#ListOrdenesPen a {	color: #327E04;text-decoration: none;font-weight: bold;}
	#ListOrdenesPen a:hover{text-decoration: underline}
</style>

	<script id="PlantillaErrorMesagge" type="text/x-handlebars-template">
		<div id="ErrorMesagge" title="Generar comprobante de pago"
			<p>
				<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
				<h3>Error</h3>
				<div style="margin-left:30px">
					{{messagee}}
				<div>
			</p>
		</div>
	</script>
<script>
	var Predefini;
	$(document).ready(function() {
		$( "#WindowGenCp" ).dialog({
			autoOpen: false,
			width:'auto',
			height:'auto',
			closeOnEscape: true,
			resizable: false,      			
      		modal: true,
			close: function (){	$( "#WindowGenCp" ).remove();},
			buttons: {
				Guardar: function() {
					if($("#FormRadica").validationEngine('validate')){
						guardaRadicacion();
					}
				},
				Cancelar: function() {
					$( "#WindowGenCp" ).dialog("close");
				}
			},
		});

		tables();
		CargaUsuarios();
		CargaAseguradora();

		$( "#Aseguradora, #Identificacion" ).change(function (){
			CargaTramites($( "#Aseguradora" ).val(), $( "#Identificacion" ).val());
		});

		$( "#Identificacion").keypress(function(e) {
			if(e.which == 13) 
				CargaTramites($( "#Aseguradora" ).val(), $( "#Identificacion" ).val());		
		});	
				
		$("#FormRadica").validationEngine();	
		$( "#WindowGenCp" ).dialog( "open");
		iniciaForm('<?=$_REQUEST['tramite']?>');
		$("#ValorCP").maskMoney({symbol:' $ ', thousands:'.', decimal:',' , symbolStay: true, precision:0});
		setSize();

	});

	function iniciaForm(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaCP2", tramite:Tramite}
		}).done(function( data ) {
			if(!data)
				return;
			
			var datos=$.parseJSON(data);
			$("#id_comprobante").val(datos.id_comprobante);
			$("#ValorCP").val(datos.valor_cp);
			setTimeout(function(){$("#Aseguradora").val(datos.id_compania)},200);
			setTimeout(function(){$("#Comprobante").val(datos.num_comprobante)},200);
			setTimeout(function(){$("#MedioPago").val(datos.medio_pago)},200);
			setTimeout(function(){$("#Identificacion").val(datos.documento)},200);
			setTimeout(function(){CargaTramites(datos.id_compania, datos.documento, datos.num_comprobante);},400);
			$("#Aseguradora").attr('disabled','disabled');
			$("#Comprobante").attr('readonly', true);
			$("#Identificacion").attr('disabled','disabled');
			Predefini =  datos.ListOrdenes;
		});
	}

	function setPredefinido(){
		if(Predefini != null)
			$.each(Predefini, function(index, value) {				
				$("#ListOrdenesPen tr#"+value.num_ordengiro).click()
			});
	}	


	function CargaTramites(Aseguradora, Identificacion, IncluyeTramite){
		if( !$( "#Identificacion" ).validationEngine('validate') && !$( "#Aseguradora" ).validationEngine('validate'));
			$("#ListOrdenesPen").setGridParam({ postData: {aseguradora: [Aseguradora],IdentifiProveedor:[Identificacion], IncluyeTramite:IncluyeTramite}});
		$("#ListOrdenesPen").setGridParam({page:1}).trigger("reloadGrid");
	}

	function CargaUsuarios(){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "UsuarioRecibeAuditoria"},
			success	:function (data){
				var json = $.parseJSON(data);

				$.each(json, function(index, value) {
				  $("#Usuario").append("<option value='"+value.usuario_cod+"'>"+value.usuario+"</option>");
				});
			}
		});	
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

	function tables(){
		$("#ListOrdenesPen").jqGrid({
			url:'Facturacion/config/json.php',
			datatype: "json",
			postData: {
			 	query: 'ConsultaOrdenGiro',
			    estado: ['Generar CP'],
			    usuario_cod: 'login',
			    aseguradora:[0],
			    columns:['num_ordengiro', 'proveedor', 'des_compania'],
			    IdentifiProveedor:[0]
			},
			height: 100,
			width:500,
			colNames:['Orden de giro','Proveedor', 'Aseguradora'],
			colModel:[
				{name:'num_ordengiro',index:'num_ordengiro', width:20, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
				{name:'proveedor',index:'proveedor', width:50, align:"center"},
				{name:'aseguradora',index:'Aseguradora', width:30, align:"center"}
			],
			scroll:1,
			rowNum:50,
			sortname: 'ord.fecha_ins',
			sortorder: "desc",
			altRows:true,
			altclass:'altClassrow',
			multiselect: true,
			gridComplete: function (){
				$("#ListOrdenesPen td[aria-describedby='ListOrdenesPen_num_ordengiro'] a").click(function (){DetallesOrdenGiro($(this).text());})
				$("#ListOrdenesPen td[aria-describedby='ListOrdenesPen_cb']").css("padding-left", " 6px")
				$("#ListOrdenesPen").css("border", "1px solid #dfd9c3");
				setPredefinido();
			}
		});		
	}
	
		
	
	function guardaRadicacion(){
		$('#FormRadica').validationEngine('hide');
		//if($("#ListOrdenesPen").jqGrid('getGridParam','selarrrow').length == 0 && $("#id_comprobante").val()==null){

		if($("#ListOrdenesPen").jqGrid('getGridParam','selarrrow').length == 0 ){
			$('#ContenidoError').validationEngine('showPrompt', 'Seleccione un item', 'error')
			return;
		}

		var lisado = $("#ListOrdenesPen").jqGrid('getGridParam','selarrrow');

		var datos =  $('#FormRadica').serializeArray();
		$.each(lisado, function(index, value) {
			datos.push({name :"ListOrdenes[]", value:value});
		});
		datos.push({name :"op", value:"GuardaCP"});
		
		$(".ui-dialog-buttonpane button:contains('Guardar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Cancelar')").button("disable");
		$(".ui-dialog-buttonpane button:contains('Guardar') span").text("Guardando ...");
		
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: datos,
			success	:function (data){
				try{
					var json = $.parseJSON(data);
					if(json.error){
						MuestraError(json.error);
						$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Guardar");
						$(".ui-dialog-buttonpane button:contains('Guardar')").button("enable");	
					}else{
						$("#ListRadica").setGridParam({page:1}).trigger("reloadGrid");
						$("#ListCP").setGridParam({page:1}).trigger("reloadGrid");
						$( "#WindowGenCp" ).dialog("close");
					}
				}catch(err){
					alert(data);
					$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Guardar");
					$(".ui-dialog-buttonpane button:contains('Guardar')").button("enable");						
				}  
			}
		});
	}

	function MuestraError(mensagge){
		var plantilla = Handlebars.compile($('#PlantillaErrorMesagge').html());
		var html = plantilla({messagee:mensagge});
		$('body').append(html);
		$( "#ErrorMesagge" ).dialog({
			autoOpen: true,
			width:'auto',
			height:'auto',
			closeOnEscape: true,
			resizable: false,      			
      		modal: true,
			close: function (){	$( "#ErrorMesagge" ).remove();},
			buttons: {
				Aceptar: function() {
					$( "#ErrorMesagge" ).dialog("close");
				}
			},
		});
	}

	function setSize(){
		var height =  $("#WindowGenCp").height()+80;
		if(height > $(window).height ()){
			$("#WindowGenCp").css('height',$(window).height()-130);
		}	
	}
</script>
</div>	