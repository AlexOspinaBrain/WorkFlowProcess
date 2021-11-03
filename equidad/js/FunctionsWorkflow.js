var HtmlRespuesta;
 $(document).ready(function() {	
	$("form").validationEngine('attach', {promptPosition : "topLeft"});
	$( "button" ).button();	
	$('#BotonImpirmir').css("margin-left", $('#PostRadicado').width()-300);
	
	$('#BotonImpirmir').click(function(){
		$('#PostRadicado').jqprint();
		return false;
	});	

	$( "#codigobarras" ).dialog({
		open:function() {
			$("#GenerarNuevo").hide();
		}
	});		
});

function Semaforo(){
	$("table tbody tr").each(function (index) {
		$("[abbr='semaforo']").hide();
		if($(this).find("td[abbr='semaforo']").text() == '3')
			$(this).css('color','red');
		if($(this).find("td[abbr='semaforo']").text() == '2')
			$(this).css('color','orange');
		if($(this).find("td[abbr='semaforo']").text() == '1')
			$(this).css('color','green');
	});
}

function ModificarAdjuntos(){
	$(document).ready(function() {	
		$('.draggable').draggable({ 
			revert: "invalid"
		});
		$('#DocRequeridos, #DocAdicionales, #DocRespuesta').droppable({
			 drop:function(event, ui){
				$(this).append($(ui.draggable).css("left",0).css("top",0));
				$.ajax({
					type: "POST",
					url: "Workflow/ajax_querys.php",
					data: { op: "CambiaTipoAdj", term:$(ui.draggable).attr("id"), TipoAdjunto: $(this).attr('id')}
				});
			 }
		 });
		
		
		$(".draggable").editInPlace({
			bg_over:"ffffff",
			callback: function(original_element, html, original){
				if(html.length == 0)
					html=original;
					
				$.ajax({
					type: "POST",
					url: "Workflow/ajax_querys.php",
					data: { op: "CambiaNombreAdj", term:original_element, Nombre: html}
				});
				
				return('<img src=\'images/clip.png\' border=\'0px\' width=\'15px\'> '+html);
			},
		});
	 });
}

function AgregarAdjuntos(){
	$( "body" ).append("<div style='display:none' id='dialog-Adjunta'>"+
					   '<form action="#" method="post" id="formAdjunta" class="formular" enctype="multipart/form-data">'+
					   '<label><span>Adjuntos :<button style="margin-left:230px" type="button" id="AgregaAdjunto" title="Agregar otro adjunto" onClick="AgregaInputFile(this, \'FileAdicional\')"> + </button></span><input type="file" name="FileAdicional[]"></label>'+
					   '</form>'+
					   "</div>");
	$( "#dialog-Adjunta" ).dialog({
		autoOpen: true,
		modal: true,
		width: 300,
		height: 200,
		buttons: {
			Aceptar: function() {
				$('#formAdjunta').submit();									
			},				
			Cancelar: function() {
				$("#dialog-Adjunta").dialog( "destroy" );
				$("#dialog-Adjunta").remove();
			}
		},
		close:function(){
			$("#dialog-Adjunta").dialog( "destroy" );
			$("#dialog-Adjunta").remove();
		}
	});
}

function CambiarMedioResp(Id){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "MediosRespuesta", term: Id}
	}).done(function( data ) {
		var obj =$.parseJSON(data);
		$("#MediosRespuesta").html("<option></option>");
		$.each(obj[0].MediosRespuesta, function(i,item){
			$("#MediosRespuesta").append("<option value='"+item.id_respuesta+"'>"+item.desc_respuesta+"</option>");
		});
		$("#MediosRespuesta").val(obj[0].DatoSeleccionado);
		$("#DatoMedio").val(obj[0].DatoMedio);
		
		if(obj[0].DatoSeleccionado == 1)
			$("#TipoDatoEnvio").html("Direccion : ");
		if(obj[0].DatoSeleccionado == 2)
			$("#TipoDatoEnvio").html("Correo eléctronico : ");
		if(obj[0].DatoSeleccionado == 3)
			$("#TipoDatoEnvio").html("Telefono : ");
	});	
	$("#MediosRespuesta").change(function(){
		$.ajax({
			type: "POST",
			url: "Workflow/ajax_querys.php",
			data: { op: "CambiaMedioRespuesta", term:Id, DatoSeleccionado: $("#MediosRespuesta").val()}
		}).done(function( data ) {
			var obj =$.parseJSON(data);
			
			if($("#MediosRespuesta").val() == 1)
				$("#TipoDatoEnvio").html("Direccion : ");
			if($("#MediosRespuesta").val() == 2)
				$("#TipoDatoEnvio").html("Correo eléctronico : ");
			if($("#MediosRespuesta").val() == 3)
				$("#TipoDatoEnvio").html("Telefono : ");
			
			$("#DatoMedio").val(obj[0].DatoMedio);
		});	
	});

	$( "#dialog-CambiaMedio" ).dialog({
		autoOpen: true,
		modal: true,
		width: 400,
		height: 200,
		buttons: {
			Aceptar: function() {
				if( $("#formCambiarMedio").validationEngine('validate') ===true){
					$('#formCambiarMedio').submit();					
				}
			},				
			Cancelar: function() {
				$("#dialog-CambiaMedio").dialog( "destroy" );
				$('#CambiaMedio').validationEngine('hide');
			}
		},
		close:function(){
			$("#dialog-CambiaMedio").dialog( "destroy" );
			$('#CambiaMedio').validationEngine('hide');
		}
	});
}

function AgregaInputFile(Elemento, Name){
	$(Elemento).parent().append('<input type="file" name="'+Name+'[]">');
}

function MuestraAdjunto(Id){
	location.href="Workflow/GenerarAdjunto.php?Id="+Id;
}

function Continuatramite(Id){
	

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "flujoreabrirentes", term:Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		
		if (obj[0].id!=='n'){
			
			SeleccionaUsuario(Id);
			
		}
	
	});

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "ipmprocesores", term:Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);

		if (obj[0].id!=='999'){
			$("#DIVProcesores").show();

			$.each(obj[0].procesose, function(i,item){
				$("#Procesores").append("<option value='"+item.id_proceso+"'>"+item.proceso_desc+"</option>");

			});

			$("#TipoRespuesta").show();
			$("#DIVasociada").show();
		}
	
	});

	$("#Procesores").change(function(){
		$.ajax({
			type: "POST",
			url: "Workflow/ajax_querys.php",
			data: { op: "TipoRsp", term: Id,proce:$("#Procesores").val()}
		}).done(function( data ) {	
			var obj =$.parseJSON(data);
			if (obj[0].id!=='999'){
				$("#TipRespuesta").html("<option></option>");
				$.each(obj, function(i,item){
					$("#TipRespuesta").append("<option value='"+item.id+"'>"+item.value+"</option>");
				});
				
			}
		})
	});


	$( "#dialog-Continua").dialog({
		autoOpen: true,
		modal: true,
		width: $("#dialog-Continua").width()+30,
		height: (((parseInt($( "#Dheight" ).val()) + 20) > $(window).height())? ($(window).height() - 50): 400),//$( "#Dheight" ).val(),
		buttons: {
			Aceptar: function() {
				if( $("#formContinua").validationEngine('validate') ===true){
					$('#formContinua').submit();					
				}
			},				
			Cancelar: function() {
				$("#dialog-Continua").dialog( "destroy" );
				$('#formContinua').validationEngine('hide');
			}
		},
		close:function(){
			$("#dialog-Continua").dialog( "destroy" );
			$('#formContinua').validationEngine('hide');
		}
	});
}

function Observaciones(){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+180);
	$( "#FieldObservaciones" ).append('<textarea class="validate[required] text-input" id="Observaciones" name="Observaciones" style="width:450px; height:50px"></textarea>');
	$( "#FieldObservaciones" ).show();
}

function RedactaRespuesta(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+30);
	$( "#GeneraRespuesta" ).show();
	$( "#GeneraRespuesta" ).append('<textarea name="CartaRespuestaHide" id="CartaRespuestaHide" style="display:none"></textarea>');
	$( '#GeneraRespuesta' ).append('<div id="dialog-RedactaRespuesta" title="Redactar respuesta" style="display:none;"><textarea id="CartaRespuesta"></textarea></div>');
	$("#CartaRespuesta").css("height",($(window).height()-220)).css("width","980");
	
	$( "#dialog-RedactaRespuesta" ).dialog({
		autoOpen: false,
		modal: true,
		width: 1000,
		height: ($(window).height()-50),
		buttons: {
			Aceptar: function() {
				$("#dialog-RedactaRespuesta").dialog( "close" );
			},				
		},
		close:function(){			
			$('#CartaRespuestaHide').val(tinymce.get('CartaRespuesta').getContent());
			$('#ContenedorRespuesta').html(tinymce.get('CartaRespuesta').getContent());
		}
	});
	
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "CartaRespuesta", term: Id}
	}).done(function( data ) {
		var obj =$.parseJSON(data);
		$('#CartaRespuesta').val(obj[0].termino);
		$('#CartaRespuestaHide').val(obj[0].termino);
	});	
	
	tinyMCE.init({
		mode : "exact",
		elements :"CartaRespuesta",
		theme : "advanced",
		imagemanager_rootpath: "ImagenesWF",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,imagemanager",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,|,insertimage",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true

	});
}

function VerRespuesta(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+30);
	$( "#VerRespuesta" ).show();
	$( '#VerRespuesta' ).append('<div id="dialog-VerRespuesta" title="Ver respuesta" style="display:none;"><a href="#" id="Imprimir">Imprimir</a><a href="#" id="DescargarDoc">Descargar</a><div class="printable" id="ContenedorRespuesta" style="margin:80px 50px 50px 50px"></div></div>');
	$("#CartaRespuesta").css("height",($(window).height()-220)).css("width","980");
	$( 'body' ).append('<form id="DescargaWord" action="Workflow/GenerarWord.php" method="post"><textarea id="carta" name="carta" style="display:none;"></textarea></form>');
	
	$('#DescargarDoc').click(function(){
		$( '#DescargaWord' ).submit();
	});	
	
	$( "#dialog-VerRespuesta" ).dialog({
		autoOpen: false,
		modal: true,
		width: 1000,
		height: ($(window).height()-50),
		buttons: {
			Cerrar: function() {
				$("#dialog-VerRespuesta").dialog( "close" );
			},				
		}
	});
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "CartaRespuesta", term: Id}
	}).done(function( data ) {
		var obj =$.parseJSON(data);
		$('#ContenedorRespuesta').html($('<div/>').html(obj[0].termino));
	});	
	
	$('#Imprimir').click(function(){		
		$('.printable').jqprint();
			return false;
	});	
}

function BotonRedactaRespuesta(){
	$( "#dialog-RedactaRespuesta" ).dialog( "open" );	
}

function BotonVerRespuesta(){
	$( "#dialog-VerRespuesta" ).dialog( "open" );
}

function CambiaTipologia(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+140);
	
	$( "#FieldTipologia" ).append('<table><tr><td><label><span>Aseguradora:</span></label><select class="validate[required] text-input" id="CambiaCompania" name="CambiaCompania"><option></option></select></td>'+
									'<td><label><span>Agencia:</span></label><select class="validate[required] text-input" id="CambiaAgencia" name="CambiaAgencia"><option></option></select></td>'+
									'<td><label><span>Proceso:</span></label><select class="validate[required] text-input" id="CambiaProceso" name="CambiaProceso"><option></option></select></td>'+
									'<td><label><span>Servicio:</span></label><select class="validate[required] text-input" id="CambiaServicio" name="CambiaServicio" disabled><option></option></select></td></tr>'+
									'<tr><td colspan="4"><label><span>Tipologia:</span></label><select class="validate[required] text-input" id="CambiaTipologia" name="CambiaTipologia" style="width:500px"><option></option></select></td></tr></table>');

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "buscaCambioTipologias", term: Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$.each(obj[0].companias, function(i,item){
			$("#CambiaCompania").append("<option value='"+item.id_compania+"'>"+item.des_compania+"</option>");
		});
		$.each(obj[0].agencias, function(i,item){
			$("#CambiaAgencia").append("<option value='"+item.codigo+"'>"+item.descrip+"</option>");
		});
		$.each(obj[0].procesos, function(i,item){
			$("#CambiaProceso").append("<option value='"+item.id_proceso+"'>"+item.desc_proceso+"</option>");
		});
		$.each(obj[0].servicios, function(i,item){
			$("#CambiaServicio").append("<option value='"+item.id_servicio+"'>"+item.desc_servicio+"</option>");
		});
		$.each(obj[0].tipologias, function(i,item){
			$("#CambiaTipologia").append("<option value='"+item.id_tipologia+"'>"+item.desc_tipologia+"</option>");
		});
		$("#CambiaCompania").val(obj[0].compania);
		$("#CambiaAgencia").val(obj[0].agencia);
		$("#CambiaProceso").val(obj[0].proceso);
		$("#CambiaServicio").val(obj[0].servicio);
		$("#CambiaTipologia").val(obj[0].tipologia);
		$tptra = obj[0].tptramite;

	})	
	
	$("#CambiaCompania, #CambiaAgencia, #CambiaProceso, #CambiaServicio").change(function(){//si cambia compania o agencia, oproceso, servicio, carga las tipologias
		
		if($("#CambiaCompania").val()!='' && $("#CambiaAgencia").val()!='' && $("#CambiaProceso").val()!='' )
		$.ajax({
			type: "POST",
			url: "Workflow/ajax_querys.php",
			data: { op: "BuscaTipologia", term: Id, tptra: $tptra, id_compania: $("#CambiaCompania").val(),codigo:$("#CambiaAgencia").val(),id_proceso:$("#CambiaProceso").val(),id_servicio:$("#CambiaServicio").val()}
		}).done(function( data ) {	
			var obj =$.parseJSON(data);
			$("#CambiaTipologia").html("<option></option>")
			$.each(obj[0].tipologias, function(i,item){
				$("#CambiaTipologia").append("<option value='"+item.id_tipologia+"'>"+item.desc_tipologia+"</option>");
			});
		})
	});

	$("#CambiaTipologia").change(function(){
		$.ajax({
			type: "POST",
			url: "Workflow/ajax_querys.php",
			data: { op: "CambiaProximaActividad", term: Id,Tipologia:$("#CambiaTipologia").val()}
		}).done(function( data ) {	
			var obj =$.parseJSON(data);
			$("#PasoProximo").html("");

			$.each(obj, function(i,item){
				$("#PasoProximo").append("<option value='"+item.id_workflow+"'>"+item.desc_actividad+"</option>");
				$("#UsuarioProximo").html("<option></option>");
			});
			CambiaUsuarios(obj);
			$("#PasoProximo").change(function(){CambiaUsuarios(obj)});
		})
	});
		
	$( "#FieldTipologia" ).show();
}

function CambiaUsuarios(obj){
	$.each(obj, function(i,item){					
		if(item.id_workflow==$("#PasoProximo").val()){
			$("#UsuarioProximo").html("<option></option>");
			$.each(item.usuarios, function(i,item2){
				$("#UsuarioProximo").append("<option value='"+item2.usuario_cod+"'>"+item2.nombres+"</option>");
			});
		}
	});
}

function SeleccionaUsuario(Id){


	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "muestrasu", term: Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		if(obj[0].simuestra == 'S'){
			$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+70);
			$( "#FieldActividadProxima" ).show();
		}

	});	

}

function AdjuntosRespuesta(){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+70);
	$( "#AdjuntosRespuesta" ).show();
}

function seleccionaagenciausuario(AreaU){

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "ageusu", Areaa:AreaU}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		
		$.each(obj, function(i,item){

			$("#AgenciaReclamante").append("<option value='"+item.id+"' selected>"+item.value+"</option>");
		});

	});

	

}

function ProximaActividad(Id){
	//alert('ee');
	$( "#FieldActividadProxima" ).html('');
	$( "#FieldActividadProxima" ).append('<table><tr><td><label><span>Actividad proxima:</span></label><select class="validate[required] text-input" id="PasoProximo" name="PasoProximo"><option></option></select></td>'+
				 '<td><label><span>Usuario:</span></label><select class="validate[required] text-input" id="UsuarioProximo" name="UsuarioProximo"><option></option></select></td></tr></table>');

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "ProximaActividad", term: Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#PasoProximo").html("");

		$.each(obj, function(i,item){

			$("#PasoProximo").append("<option value='"+item.id_workflow+"'>"+item.desc_actividad+"</option>");
			
		});
			
		var changeUsers= function (){
			$("#UsuarioProximo").html("<option></option>");
				$.each(obj, function(i,item){
					if(item.id_workflow == $("#PasoProximo").val())
						$.each(item.usuarios, function(i,item2){					
							$("#UsuarioProximo").append("<option value='"+item2.usuario_cod+"'>"+item2.nombres+"</option>");
						});
				});				
			}

		$("#PasoProximo").change(changeUsers);
		changeUsers();

	});	
}

function Reasigna(Id, Usuario){
	var CantidadUsuarios=0;
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "ReasignaTramite", Tramite:Id, UsuarioActual:Usuario}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#UsuarioReasignar").html("<option></option>");
		$.each(obj, function(i,item){
			$("#UsuarioReasignar").append("<option value='"+item.usuario_cod+"'>"+item.nombres+"</option>");
		});
		
		if( obj.length < 1){
			alert("No hay mas usuarios para reasignar este tramite");
			$("#dialog-Reasigna").dialog( "destroy" );
		}
	});

		$( "#dialog-Reasigna" ).dialog({
			autoOpen: true,
			modal: true,
			width: 500,
			height: 250,
			buttons: {
				Aceptar: function() {
					if( $("#formReasigna").validationEngine('validate') ===true){
						GuardaReasigna();
						//$('#formReasigna').submit();							
					}
				},				
				Cancelar: function() {
					$("#dialog-Reasigna").dialog( "destroy" );
					$('#formReasigna').validationEngine('hide');
				}
			},
			close:function(){
				$("#dialog-Reasigna").dialog( "destroy" );
				$('#formReasigna').validationEngine('hide');
			}
		});

	
}

function GuardaReasigna(){
	var datos = $("#formReasigna").serializeArray();
	datos.push({name :"op", value:"GuardaReasignaTramite"});

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: datos, 
		success	:function (data){
			location.reload();
		}
	});	
}

function EncuestaSatisfaccion(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+450);
		
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "getCompanias"}
	}).done(function( data ) {	
		var datos=$.parseJSON(data)[0];
		$.ajax({
			url: 'Workflow/Views/Encuesta.html',
			success: function (data) {
				var plantilla = Handlebars.compile(data);
				var html = plantilla(datos); 
				$( "#EncuestaSatisfaccion" ).html(html);
			}
		})
	});
}
		
function Devolucion(Id){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "DevolucionTramite", Tramite:Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#ActividadDevolucion").html("<option></option>");
		$("#CausalDevolucion").html("<option></option>");

		$.each(obj[0].actividades, function(i,item){
			$("#ActividadDevolucion option:contains("+item.actividad+")").remove()
			$("#ActividadDevolucion").append("<option value='"+item.id_workflow+"'>"+item.actividad+"</option>");

		});
		if(obj[0].actividades.length < 1){
			alert("No hay actividades disponibles para devolver este tramite");
			$("#dialog-Devuelve").dialog( "destroy" );
		}
		
		$.each(obj[0].causales, function(i,item){
			$("#CausalDevolucion").append("<option>"+item.desc_causal_devolucion+"</option>");
		});
	});
	
	$( "#dialog-Devuelve" ).dialog({
		autoOpen: true,
		modal: true,
		width: 500,
		height: 300,
		buttons: {
			Aceptar: function() {
				if( $("#formDevuelve").validationEngine('validate') ===true){
					$('#formDevuelve').submit();							
				}
			},				
			Cancelar: function() {
				$("#dialog-Devuelve").dialog( "destroy" );
				$('#formDevuelve').validationEngine('hide');
			}
		},
		close:function(){
			$("#dialog-Devuelve").dialog( "destroy" );
			$('#formDevuelve').validationEngine('hide');
		}
	});
}

function RespuestaFavor(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+70);
	$( "#FavorRespuesta" ).show();

	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "TipoServicio", term:Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		if(obj[0].IdTipoServicio == 2)
			$('input[name="FavorRespuesta"]').removeClass("validate[required]");
	});

}

function Seguimiento(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+70);
	$( "#seguimienton" ).show();

}


function EnviarRespuesta(Id){
	$( "#Dheight" ).val(parseInt($( "#Dheight" ).val())+40);
	
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "MedioRespuesta", term:Id}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$( "#MedioRespuesta" ).html	(obj[0].MedioRespuesta);
		$('#formContinua').append('<input type="hidden" name="MedioRespuesta" value="'+obj[0].MedioRespuesta+'" />');
	});
	
	$( "#EnviaResuesta" ).show();

}

function CerrarTramiteAuto(Id){

	$( "#formContinua" ).append('<input type="hidden" name="CerrarTramiteAuto" value="'+Id+'"/>');
}

function MuestraObservacion(Observacion){
	$( "body" ).append("<div title='Observación' id='dialog-Observacion'>"+Observacion+"</div>")
	
	$( "#dialog-Observacion" ).dialog({
		autoOpen: true,
		modal: true,
		width: 350,
		buttons: {
			Aceptar: function() {
				$("#dialog-Observacion").dialog( "destroy" );
				$("#dialog-Observacion").remove();
				
			},				
		},
		close:function(){
			$("#dialog-Observacion").dialog( "destroy" );
			$("#dialog-Observacion").remove();
		}
	});
}

function CerrarTramite(){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "CausalesCierre"}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#CausalCierre").html("<option></option>");
		$.each(obj, function(i,item){
			$("#CausalCierre").append("<option value='"+item.value+"'>"+item.value+"</option>");
		});
	});
	
	$( "#dialog-CerrarTramite" ).dialog({
		autoOpen: true,
		modal: true,
		width: 450,
		buttons: {
			"Cerrar tramite": function() {
				if( $("#formCerrarTramite").validationEngine('validate') ===true){
					$('#formCerrarTramite').submit();					
				}		
			},				
		},
		close:function(){
			$("#dialog-Observacion").dialog( "destroy" );
			$('#formCerrarTramite').validationEngine('hide');
		}
	});
}

function AnularTramite(){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "CausalesAnular"}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#CausalAnulacion").html("<option></option>");
		$.each(obj, function(i,item){
			$("#CausalAnulacion").append("<option value='"+item.value+"'>"+item.value+"</option>");
		});
	});
	
	$( "#dialog-AnularTramite" ).dialog({
		autoOpen: true,
		modal: true,
		width: 450,
		buttons: {
			"Anular tramite": function() {
				if( $("#formAnularTramite").validationEngine('validate') ===true){
					$('#formAnularTramite').submit();					
				}		
			},				
		},
		close:function(){
			$("#dialog-AnularTramite").dialog( "destroy" );
			$('#formAnularTramite').validationEngine('hide');
		}
	});
}

function ViewEncuesta(Tramite){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "getEncuesta", term:Tramite}
	}).done(function( data ) {	
	//alert(data);
		var datos=$.parseJSON(data)[0];
		$.ajax({
			url: 'Workflow/Views/ViewEncuesta.html',
			success: function (data) {
			//console.log(datos);
				var plantilla = Handlebars.compile(data);
				var html = plantilla(datos); 
				$( "#ViewEncuesta" ).html(html);
			}
		})
	});
}

function ViewRespuesta(Tramite){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "existRespuesta", term:Tramite}
	}).done(function( data ) {	
		var datos=$.parseJSON(data)[0];
		if(datos.exists)
			$("#ViewRespuesta").html('<fieldset style="font-size: 11px;">'+
			'<legend><b style="font-size: 16px;">Carta respuesta: </b></legend>'+	
			'<button onClick="VerRespuesta('+Tramite+');BotonVerRespuesta();">Ver respuesta</button>'+
			'</fieldset>');
	});
}

function SuspenderTramite(){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "CausalesSuspender"}
	}).done(function( data ) {	
		var obj =$.parseJSON(data);
		$("#CausalSuspension").html("<option></option>");
		$.each(obj, function(i,item){
			$("#CausalSuspension").append("<option value='"+item.value+"'>"+item.value+"</option>");
		});
	});
	
	$( "#dialog-SuspenderTramite" ).dialog({
		autoOpen: true,
		modal: true,
		width: 450,
		buttons: {
			"Suspender tramite": function() {
				if( $("#formSuspenderTramite").validationEngine('validate') ===true){
					$('#formSuspenderTramite').submit();					
				}		
			},				
		},
		close:function(){
			$("#dialog-SuspenderTramite").dialog( "destroy" );
			$('#formSuspenderTramite').validationEngine('hide');
		}
	});
}

function ReabrirTramite(Id){
	$.ajax({
		type: "POST",
		url: "Workflow/ajax_querys.php",
		data: { op: "ReAbrirTramite", Tramite:Id}
	}).done(function( data ) {	
		var obj = $.parseJSON(data);
		$("#EstadoReabrir").val(obj[0].estado);
		if(obj[0].estado == 'Suspendido'){
			$("#ActividadReabrir").html("");
			$.each(obj[0].actividades[0].usuarios, function(i,item2){
				$("#UsuarioReabrir").append("<option value='"+item2.usuario_cod+"'>"+item2.usuario+"</option>");
			});
		}else{
			$("#ActividadReabrir").html("<option></option>");
			$("#DivActividadReabrir").show();
		}

		$.each(obj[0].marcaed, function(i,item3){
			$("#marcaed").append("<option value='"+item3.idd+"'>"+item3.desc+"</option>");
		});
		
		$.each(obj[0].actividades, function(i,item){
			$("#ActividadReabrir").append("<option value='"+item.id_workflow+"'>"+item.actividad+"</option>");
		});

		$("#ActividadReabrir").change(function(){
			$.each(obj[0].actividades, function(i,item){
				if(item.id_workflow == $("#ActividadReabrir").val()){
					$("#UsuarioReabrir").html("<option></option>");
					$.each(item.usuarios, function(i,item2){
						$("#UsuarioReabrir").append("<option value='"+item2.usuario_cod+"'>"+item2.usuario+"</option>");
					});
				}			
			});
		});

	});

	$( "#dialog-ReabrirTramite" ).dialog({
		autoOpen: true,
		modal: true,
		width: 450,
		buttons: {
			"Re-abrir tramite": function() {
				if( $("#formReabrirTramite").validationEngine('validate') ===true){
					$('#formReabrirTramite').submit();					
				}		
			},				
		},
		close:function(){
			$("#dialog-ReabrirTramite").dialog( "destroy" );
			$('#formReabrirTramite').validationEngine('hide');
		}
	});
}
