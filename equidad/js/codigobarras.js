 $(document).ready(function() {	
	$('body').append('<div id="codigobarras"></div>');
	$( "#codigobarras" ).dialog({
		autoOpen: false,
		width:420,
		height: 170,
		modal: true
	}); 
 });
 
 function MuestraCodeBar(Tramite, usuario, nuevo){
	$("#codigobarras").html("");
	$( "#codigobarras" ).dialog({
		width:420,
		height: 180,
		buttons:{},
		modal: true
	});
	
	if(Tramite.length < 1)
		return;				
		
	$.ajax({
		type: "POST",
		url: "config/ajax_querys.php",
		data: { op: "buscacodbar", term: Tramite, Usuario:usuario }
	}).done(function( data ) {	
		var obj =$.parseJSON(data);

		if(nuevo==null){
		var consenal="";
		var salida=	"<a href='#' id='Imprimir'>Imprimir</a>"+((obj[0].value=='true' && obj[3].value == 'CORRESPONDENCIA EXTERNA')?"<a id='GenerarNuevo' style='margin-left: 200px' href='#' onClick='NuevoSticker("+Tramite+","+obj[2].value+",\""+obj[1].value+"\")'>Generar nuevo</a>":'')+
					"<div style='padding:25px'><table border='0' class='printable' style='font-size:7px' width='340px'>"+
					"<tr>"+
					"<td style='padding:0px'>"+
					"<b>Fecha y hora: </b>"+obj[1].value+"<br>"+
					"<b>Asunto: </b>"+obj[6].value+"<br>"+ 
					"<b>No folios: </b>"+obj[2].value+"<br>";
							 
		if(obj[3].value != 'CORRESPONDENCIA EXTERNA'){
			salida +="<b>Agencia destino: </b>"+obj[5].value+"<br>"+
						 "<b>Area destino: </b>"+obj[3].value+"<br>";

			if(obj[8].value){
				salida +="<b>Siniestro: </b>"+obj[8].value+"<br>"+
					 "<b>Producto: </b>"+obj[9].value+"<br>";
				consenal="<tr style='font-size:15px'><td align='center'><b>SINIESTRO</b></td></tr>";
			}
		}
		else{
			salida +="<b>Ciudad: </b>"+obj[8].value+"<br>"+
					 "<b>Dirección: </b>"+obj[9].value+"<br>"+
					 "<b>Telefono: </b>"+obj[10].value+"<br>";
			if(obj[11].value){
				salida +="<b>Siniestro: </b>"+obj[11].value+"<br>"+
					 "<b>Producto: </b>"+obj[12].value+"<br>";
				consenal="<tr style='font-size:15px'><td align='center'><b>SINIESTRO</b></td></tr>";
			}
		}

							 
		salida+="<b>Remitente: </b>"+obj[7].value+"<br><b>Destinatario: </b>"+obj[4].value+"<br>"+
				"</td>"+
				"<td width='100px' align='center'><b>LA EQUIDAD SEGUROS O.C.</b><br><img src='config/barcode/image.php?filetype=PNG&dpi=72&thickness=30&scale=1&rotation=0&font_family=Arial.ttf&font_size=10&text="+Tramite+"&code=BCGcode128'/><br>"+((obj[3].value == 'CORRESPONDENCIA EXTERNA')?"<b>Destino externo</b>":"<b>Destino interno</b>")+"</td>"+
				"</tr>"+
				consenal +
				
				"</table><div>";
		}else{
			var salida=nuevo;
		}
		$("#codigobarras").html(salida);
		$("#codigobarras" ).dialog("open");
		$('#Imprimir').click(function(){		
		$('.printable').jqprint();
		//$('#Imprimir').text("Espere un munuto para volver a imprimir");
			return false;
		});		
	});			
}
		
function NuevoSticker(numtramite, folios, hora){
	$( "#codigobarras" ).dialog({
		height: 350,
		buttons: {
			Aceptar: function() {
				if($('#formID').validationEngine('validate')){
					var salida=	"<a href='#' id='Imprimir'>Imprimir</a><a id='GenerarNuevo' style='margin-left: 250px' href='#' onClick='NuevoSticker("+numtramite+", "+folios+", \""+hora+"\")'>Generar nuevo</a>"+
					"<div style='padding:25px'><table class='printable' style='font-size:8px' width='340px'>"+
					"<tr>"+
					"<td style='padding:0px'>"+
					"<b>Fecha y hora: </b>"+hora+"<br>"+
					"<b>No folios: </b>"+folios+"<br>"
							 
		
					salida+="<b>Ciudad: </b>"+$('#CiudadNC option:selected').text()+"<br>"+
							"<b>Dirección: </b>"+$("#DireccionNC").val()+"<br>"+
							"<b>Telefono: </b>"+$("#TelefonoNC").val()+"<br>"+							 
							"<b>Destinatario: </b>"+$("#DestinatarioNC").val()+"<br>"+
							"</td>"+
							"<td width='100px'><img src='config/barcode/image.php?filetype=PNG&dpi=72&thickness=30&scale=1&rotation=0&font_family=Arial.ttf&font_size=10&text="+numtramite+"&code=BCGcode128'/></td>"+
							"</tr>"+
							"</table><div>";
					MuestraCodeBar(numtramite, '0', salida);
				}
				
			},					
			Cancelar: function() {
				$("#codigobarras" ).dialog( "close" );
			}
		}
	}); 

	$( "#codigobarras" ).html('<form id="formID" class="formular"><fieldset>'+
												'<label><span>Ciudad:</span></label><select id="CiudadNC" class="validate[required]"></select>'+
												'<label><span>Direccion:</span></label><input type="text" class="validate[required] text-input" id="DireccionNC" style="text-transform:uppercase; width:350px">'+
												'<label><span>Destinatario:</span></label><input type="text" class="validate[required] text-input" id="DestinatarioNC" style="text-transform:uppercase; width:350px">'+
												'<label><span>Telefono:</span></label><input type="text" class="validate[required,custom[integer],minSize[7]] text-input" id="TelefonoNC" style="text-transform:uppercase;"></fieldset></form>');					
	
	$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
	CargaCiudades("CiudadNC");
	$("#formID").submit(function (){
		return false;
	});	

}

