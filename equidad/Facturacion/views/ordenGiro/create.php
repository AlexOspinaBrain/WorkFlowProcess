<?php
	session_start();
	if(!isset($_SESSION['uscod']) && basename($_SERVER['PHP_SELF'])!='default.php'){
		$_SESSION['EstadoSesion']="La sesion a terminado";
		echo "<script>location.reload()</script>";
		exit();
	}
?>
<div id="WindowRadicador" title="Generar orden de giro">
<form id="FormRadica" class="formular" style="padding:10px">	
	<table class="TblGreen">
		<tr class="alt"><td>Seleccione una aseguradora: </td>
	<td>Nit proveedor: </td></tr>
		<tr><td>
			<select id="Aseguradora" name="Aseguradora" class="validate[required] text ui-widget-content ui-corner-all" style="padding:4px;">
				<option></option>
			</select>			
		</td>
		<td>
			<input type="text" id="Identificacion" name="Identificacion" class="validate[required, custom[number]] text ui-widget-content ui-corner-all" style="width:150px; display:inline">		
			<button id="buttonSearch" type="button">Search</button>
		</td>
		</tr>	
		<tr class="alt"><td colspan="2">Seleccione las facturas y/o cuentas de cobro: </td></tr>
		<tr><td align="center" colspan="2">
				<div id="DivListFactura"></div>
				<table id="ListFacPen"></table>
		</td></tr>
		<tr class="alt"><td colspan="2">Concepto: </td></tr>
		<tr><td colspan="2"><textarea id="Concepto" name="Concepto" class="validate[required] text ui-widget-content ui-corner-all" style="resize: none;width:95%"></textarea></tr>
		
		<tr class="alt"><td colspan="2">Observaciones: </td></tr>
		<tr><td colspan="2"><textarea id="Observaciones" name="Observaciones" class="validate[required] text ui-widget-content ui-corner-all" style="resize: none;width:95%;height:30px"></textarea></tr>
	</table>
	<input type="hidden" id="OrdenGiro" name="OrdenGiro">
</form>
<style>
	
	#ListFacPen td{font-size: 11px;border:0px;}
	#ListFacPen .ui-state-hover a{	color: white;}		
	#ListFacPen a {	color: #327E04;text-decoration: none;font-weight: bold;}
	#ListFacPen a:hover{text-decoration: underline}
</style>
<script>
	var Predefini;
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
						guardaRadicacion();
					}
				},
				Cancelar: function() {
					$( "#WindowRadicador" ).dialog("close");
				}
			},
		});

		 $( "#buttonSearch" ).button({
      		icons: {
        		primary: "ui-icon-search"
      		},
      		text: false
	    })
			
		CargaAseguradora();
		tables();
		$("#ListFactura").multiselect({minWidth:360}).multiselectfilter(); 
		$( "#Aseguradora, #Identificacion" ).change(function (){
			CargaTramites($( "#Aseguradora" ).val(), $( "#Identificacion" ).val());
		});

		$( "#Identificacion").keypress(function(e) {
			if(e.which == 13) 
				CargaTramites($( "#Aseguradora" ).val(), $( "#Identificacion" ).val());		
		});	
		
		$("#FormRadica").validationEngine();	
		$( "#WindowRadicador" ).dialog("open");
		iniciaForm('<?=$_REQUEST['tramite']?>');

	});

	function iniciaForm(Tramite){
		$.ajax({
			type: "POST",
			url: "Facturacion/config/ajax_querys.php",
			data: { op: "BuscaOrdenGiro2", tramite:Tramite}
		}).done(function( data ) {
			if(!data)
				return;

			var datos=$.parseJSON(data);
			setTimeout(function(){$("#Aseguradora").val(datos.id_compania)},200);
			setTimeout(function(){$("#Identificacion").val(datos.ListOrdenes[0].documento)},200);
			setTimeout(function(){CargaTramites(datos.id_compania, datos.ListOrdenes[0].documento, datos.id_ordengiro);},400);
			$("#Aseguradora").attr('disabled','disabled');
			$("#OrdenGiro").val(Tramite);
			Predefini =  datos.ListOrdenes;
		});
	}

	function tables(){
		$("#ListFacPen").jqGrid({
			url:'Facturacion/config/json.php',
			datatype: "json",
			postData: {
			 	query: 'ConsultaFactura',
			 	estado: ['Generar orden de giro'],
			    usuario: 'login',
			    aseguradora:[0],
			    columns:['serial_factura', 'proveedor', 'valor_fac'],
			    IdentifiProveedor:[0]
			},
			height: 100,
			width:600,
			colNames:['Tramite','Proveedor', 'Valor'],
			colModel:[
				{name:'num_ordengiro',index:'num_ordengiro', width:20, align:"center", formatter:'showlink', formatoptions:{baseLinkUrl:'#'}},
				{name:'ord.fecha_ins',index:'ord.fecha_ins', width:60, align:"center"},
				{name:'valor_fac',index:'valor_fac', width:20, align:"center"}
			],
			scroll:1,
			rowNum:50,
			sortname: 'fechahora_ins',
			sortorder: "desc",
			altRows:true,
			altclass:'altClassrow',
			multiselect: true,
			gridComplete: function (){
				$("#ListFacPen td[aria-describedby='ListFacPen_num_ordengiro'] a").click(function (){Detalles($(this).text());})
				$("#ListFacPen td[aria-describedby='ListFacPen_cb']").css("padding-left", " 6px")
				$("#ListFacPen").css("border", "1px solid #dfd9c3");
				setPredefinido();
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
	
	function CargaTramites(Aseguradora, Identificacion, IncluyeTramite){
		if( !$( "#Identificacion" ).validationEngine('validate') && !$( "#Aseguradora" ).validationEngine('validate'))
			$("#ListFacPen").setGridParam({ postData: {aseguradora: [Aseguradora],IdentifiProveedor:[Identificacion], IncluyeTramite:IncluyeTramite}});
		$("#ListFacPen").setGridParam({page:1}).trigger("reloadGrid");
	}

	function setPredefinido(){
		if(Predefini != null)
			$.each(Predefini, function(index, value) {
				
				$("#ListFacPen tr#"+value.serial_factura).click()
			});
	}		
	
	function guardaRadicacion(){
		var listado = $("#ListFacPen").jqGrid('getGridParam','selarrrow');
		if(listado.length == 0){
			$('#DivListFactura').validationEngine('showPrompt', 'Seleccione un item', 'error')
			return;
		}

		var datos =  $('#FormRadica').serializeArray();
		$.each(listado, function(index, value) {
			datos.push({name :"ListFactura[]", value:value});
		});
		datos.push({name :"op", value:"GuardaOrdenGiro"});
		
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
					ConfimaOG(json.orden_giro);
					$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Guardar");
					$(".ui-dialog-buttonpane button:contains('Guardar')").button("enable");		
					$("#ListPendientes").setGridParam({page:1}).trigger("reloadGrid");
					$("#ListOrdenGiro").setGridParam({page:1}).trigger("reloadGrid");
					$( "#WindowRadicador" ).dialog("close");
				}catch(err){
					alert(data);
					$(".ui-dialog-buttonpane button:contains('Guardando ...') span").text("Guardar");
					$(".ui-dialog-buttonpane button:contains('Guardar')").button("enable");						
				}  
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

<div id="WindowConfirmOG" title="Orden de giro generada" style="display:none">
	<p>
		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 20px 0;"></span>
		Se ha generado la orden de giro <a id="link_OG" class="link2" href="#">  </a> 
	</p>	

	<style>
		p .link2 {	color: #327E04;text-decoration: none;font-weight: bold;}
		p .link2:hover{text-decoration: underline}
	</style>	

	<script type="text/javascript">
	function ConfimaOG(orden_giro){
		$( "#link_OG" ).text(orden_giro);
		$( "#link_OG" ).click(function(){
			DetallesOrdenGiro(orden_giro);
		});
		$( "#WindowConfirmOG" ).dialog({
			autoOpen: true,
			width:'auto',
			closeOnEscape: true,
			resizable: false,      			
      		modal: true,
			close: function (){	$( "#WindowConfirmOG" ).remove();},
			buttons: {		
				'Imprimir OG': function() {
					formatoOG(orden_giro);
				},
				Aceptar: function() {
					$( "#WindowConfirmOG" ).dialog("close");
				},
			},
		});
	}
	</script>
</div>