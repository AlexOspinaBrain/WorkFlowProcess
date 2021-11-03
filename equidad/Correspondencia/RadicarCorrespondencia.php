<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Correspondencia/Trazabilidad.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/VisorImagine.js"></script><!--Visor-->
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->	
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->	
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	
	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->

	<script type="text/javascript">
        $(document).ready(function() {	
			$( "#dialog-modal" ).dialog({
				autoOpen: true,
				width:700,
				height: ($(window).height()-30),
				modal: true,
				closeOnEscape: true
			});
			
			$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
			
			$("#Agencia").focus();
			
			$("#Agencia").change(function() {
				$('#Area').html("<option>Espere ... </option>");
				
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscaarea", term: $(this).val(), where: 'are.areasid=tip.area and', addtablas: ', tbltiposdoccorresp tip'}
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
					$('#Area').html(opciones);
				});
			});
			

			$('#Destinatario').change(function() {

				$('#tDestinatario').val($("#Destinatario option:selected").text());
			
			});

			$('#Ciudad').change(function() {

				$('#tCiudad').val($("#Ciudad option:selected").text());
			
			})

			$('#Area').change(function() {//Funcion que se ejecuta al cambiar el select para cambiar combos #Destinatario y #TipoDoc
				$('#tarea').val($( "#Area option:selected" ).text());

				$('#Destinatario').html("<option>Espere ... </option>");
				$('#TipoDoc').html("<option>Espere ... </option>");
			
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscausuario", term: $(this).val() }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
					$('#Destinatario').html(opciones);
				});
				
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscatiposdocu", term: $(this).val() }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option id='"+obj[i].prioridad+"' value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
					$('#TipoDoc').html(opciones);
				});
			});	

			$('#TipoDoc').change(function() {
				$('#tTipoDoc').val($( "#TipoDoc option:selected" ).text());
			 
			 	var prioritario = $(this).find('option:selected').attr('id');
			 	var ty = $(this).find('option:selected').text();
				if(prioritario=='t'){
					divprioritario="<span class='prioritario'>El documento seleccionado es prioritario, <br>"+
									"ingrese los documentos scaneados .TIF , .JPG , .PDF<br>Tamaño maximo por archivo 4 Mb</span><br>"+
									"<label><span>Numero Adjuntos : </span>"+
									"<input type='text' id='NumAdj' name='NumAdj' class='validate[required]' style='text-transform:lowercase; width:30px; display:inline;' onblur='CreaInputfile()'/>"+
									"<button type='button'>Aceptar</button>"+
									"</label>";
				}else{
					divprioritario="";
				}
				$('#identifica').css('display', 'none');
				if(ty=='FACTURAS'){
					dividentificacion="";
					dividentificacion="<label><span>NIT/CC : "+
					"<input type='text' id='Identificacion' name='Identificacion' class='validate[required, maxSize[40]]' style='text-transform:lowercase; display:inline;' />"+
					"</label>";
				}else if(ty=='SINIESTROS'){

                                        dividentificacion="";
                                        $('#identifica').css('display', 'block');


				}else{
					dividentificacion="";
				}

				$('#prioridad').html(divprioritario);
				$('#identificaf').html(dividentificacion);
				$('#NumAdj').keypress(function(e){
					if(e.which == 13){
						CreaInputfile();
						return false;
					}
				});	
				$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	

			});

			$('#TipoDoc2').change(function() {
			 var ty = $(this).find('option:selected').text();
				if(ty=='FACTURAS'){
					dividentificacion="<label><span>NIT/CC : "+
					"<input type='text' id='Identificacion' name='Identificacion' class='validate[required, maxSize[40]]' style='text-transform:lowercase; display:inline;' />"+
					"</label>";
				}else{
					dividentificacion="";
				}

				$('#identificae').html(dividentificacion);
				$("#formID").validationEngine('attach', {promptPosition : "topLeft"});	
			});
			
			$( "#OrigenExterno" ).change(function (){			
				$( "#procedencia" ).css("display", "block");
				CargaCiudades('Ciudad');					
			});

			$( "#OrigenInterno" ).change(function (){
				$( "#procedencia" ).css("display", "none");
				$('#Ciudad').html("<option value='1'></option>");
			});
			
			$('#DestinoExterno, #DestinoInterno').click(function (){			
				if($(this).val() == 'Externo'){
					$('#DivDestinoInterno').css('display', 'none');
					$('#DivDestinoExterno').css('display', 'block');
					CargaCiudades('Ciudad2');
					
					$.ajax({
						type: "POST",
						url: "config/ajax_querys.php",
						data: { op: "buscatiposdocu", term: '68' }
					}).done(function( data ) {	
						var obj =$.parseJSON(data);	
						var opciones="<option value=''></option>";
						for(i=0; i<obj.length; i++)
							opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
						$('#TipoDoc2').html(opciones);
					});
				}else{
					$('#DivDestinoExterno').css('display', 'none');
					$('#DivDestinoInterno').css('display', 'block');
					$('#Ciudad2').html("<option value='1'></option>");
				}
			})
			
			$("#GuardarNuevo").click(function(){
				if($('#formID').validationEngine('validate')){
					$(this).val('Espere ...');
					setTimeout ('$("#Guardar").attr("disabled", "disabled")', 2)
					setTimeout ('$("#GuardarNuevo").attr("disabled", "disabled")', 2)

				}


			})
			
			tables();

			
			if('<?=$_REQUEST['env']?>'!= 'si'){
					
				$("#Agencia").val("<?=$_REQUEST['Agencia']?>"); 
			

				$("#Area").html("<option value='<?=$_REQUEST['Area']?>' selected><?=$_REQUEST['tarea']?></option>");
				$("#tarea").val('<?=$_REQUEST['tarea']?>');

				$("#Destinatario").html("<option value='<?=$_REQUEST['Destinatario']?>' selected><?=$_REQUEST['tDestinatario']?></option>");
				$("#tDestinatario").val('<?=$_REQUEST['tDestinatario']?>');

				$("#TipoDoc").html("<option value='<?=$_REQUEST['TipoDoc']?>' selected><?=$_REQUEST['tTipoDoc']?></option>");
				$("#tTipoDoc").val('<?=$_REQUEST['tTipoDoc']?>');

				if ('<?=$_REQUEST['tTipoDoc']?>' == 'FACTURAS'){
					dividentificacion="<label><span>NIT/CC : "+
					"<input type='text' id='Identificacion' name='Identificacion' class='validate[required, maxSize[40]]' style='text-transform:lowercase; display:inline;' />"+
					"</label>";
					$('#identificaf').html(dividentificacion);
								
					$("#Identificacion").val('<?=$_REQUEST['Identificacion']?>');
				}

				if ('<?=$_REQUEST['Ciudad']?>' != ''){

					$("#Remitente").val('<?=$_REQUEST['Remitente']?>');
					$("#Ciudad").html("<option value='<?=$_REQUEST['Ciudad']?>' selected><?=$_REQUEST['tCiudad']?></option>");
					$("#tCiudad").val('<?=$_REQUEST['tCiudad']?>');

					$("[name=Origen]").val(["Externo"]);	
					
					$("#NoGuia").val('<?=$_REQUEST['NoGuia']?>');	
					$("#Asunto").val('Factura No. ');	
					$( "#procedencia" ).css("display", "block");
										

				}
			}


		});



		function IsNumeric(valor) 
		{ 
			var log=valor.length; var sw="S"; 
			for (x=0; x<log; x++) 
			{ v1=valor.substr(x,1); 
			v2 = parseInt(v1); 
			//Compruebo si es un valor numérico 
			if (isNaN(v2)) { sw= "N";} 
			} 
			if (sw=="S") {return true;} else {return false; } 
		} 
		
		var primerslap=false; 
		var segundoslap=false; 
		
		function formateafecha(fecha) 
		{ 
			var long = fecha.length; 
			var dia; 
			var mes; 
			var ano; 
			if ((long>=2) && (primerslap==false)) { dia=fecha.substr(0,2); 
			if ((IsNumeric(dia)==true) && (dia<=31) && (dia!="00")) { fecha=fecha.substr(0,2)+"/"+fecha.substr(3,7); primerslap=true; } 
			else { fecha=""; primerslap=false;} 
			} 
			else 
			{ dia=fecha.substr(0,1); 
			if (IsNumeric(dia)==false) 
			{fecha="";} 
			if ((long<=2) && (primerslap=true)) {fecha=fecha.substr(0,1); primerslap=false; } 
			} 
			if ((long>=5) && (segundoslap==false)) 
			{ mes=fecha.substr(3,2); 
			if ((IsNumeric(mes)==true) &&(mes<=12) && (mes!="00")) { fecha=fecha.substr(0,5)+"/"+fecha.substr(6,4); segundoslap=true; } 
			else { fecha=fecha.substr(0,3);; segundoslap=false;} 
			} 
			else { if ((long<=5) && (segundoslap=true)) { fecha=fecha.substr(0,4); segundoslap=false; } } 
			if (long>=7) 
			{ ano=fecha.substr(6,4); 
			if (IsNumeric(ano)==false) { fecha=fecha.substr(0,6); } 
			else { if (long==10){ if ((ano==0) || (ano<1900) || (ano>2100)) { fecha=fecha.substr(0,6); } } } 
			} 
			if (long>=10) 
			{ 
			fecha=fecha.substr(0,10); 
			dia=fecha.substr(0,2); 
			mes=fecha.substr(3,2); 
			ano=fecha.substr(6,4); 
			// Año no viciesto y es febrero y el dia es mayor a 28 
			if ( (ano%4 > 1900) && (mes ==02) && (dia > 28) ) { fecha=fecha.substr(0,2)+"/"; } 
			} 
			return (fecha); 
		}		
		
		function radicanew(){
				$( "#dialog-modal" ).dialog("open"); 
		}
		
		function CreaInputfile(){
			var inputs="";
			$('#inputsfile').remove();
			if($('#NumAdj').val()>0){
				for(i=0; i<$('#NumAdj').val(); i++)
					inputs+="<input id='file"+i+"' name='file"+i+"' type='file' class='validate[required]' onChange='ValidaFile(this)'>";
			}else{
				$('#NumAdj').val("");
			}	
			$('#prioridad').append('<div id="inputsfile">'+inputs+'<div>');
		}
		
		function ValidaFile(nudo){
			var field=$(nudo).val();
			var ext=(field.substring(field.lastIndexOf(".")+1, field.length)).toLowerCase();
			if(!(ext=='tif' || ext=='tiff' || ext=='jpg' || ext=='jpeg' || ext=='pdf')){
				alert('El archivo debe se tif ó jpg ó pdf');
				$(nudo).replaceWith($(nudo).clone(true));
			}
		}
		
		function CargaCiudades(combo){
		$('#'+combo).html("<option>Espere ... </option>");
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscaciudades" }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
					$('#'+combo).html(opciones);
				});
		}
		
		function tables(){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'ActualRadica' },
							  { name: 'codusu', value: '<?=$_SESSION['uscod']?>' } ],
				colModel : [ { display : 'Num Tramite',	name : 'cor.numtramite', width : 100, sortable : true, align : 'center'}, 
							 { display : 'Codigo', width : 30, align : 'center'} ,
							 { display : 'Fecha y hora', name : 'cor.fecins', width : 120, sortable : true,	align : 'center'},
							 { display : 'Remitente', name : 'cor.remitente', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Agencia destino',	name : 'ofi.descrip', width : 180, sortable : true,	align : 'left'}, 
							 { display : 'Area destino', name : 'are.area',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Destinatario', name : 'destinatario',	width : 200,	sortable : true, align : 'left'} ,
							 { display : 'Tipo doc', name : 'doc.tipo',	width : 120,	sortable : true, align : 'left'} ,
							 { display : 'Adjuntos', width : 40, align : 'center'}	,
							 { display : 'Folios', name : 'cor.numfolios',	width : 30,	sortable : true, align : 'right'} ,
							 { display : 'Eliminar', width : 30, align : 'center'}							 
						   ],
				searchitems : [ {display : 'Num Tramite', name : 'numtramite', isdefault : true}, 
								{display : 'Fecha', name : 'fecins'} ],
				buttons : [ { name : 'Radicar nuevo', bclass : 'add', onpress : radicanew}, 
							{ separator : true} 
						  ],
				sortname : "cor.fecins",
				sortorder : "desc",
				usepager : true,
				title : 'Radicador correspondencia, a continuación se muestran los radicados de los ultimos 30 dias.',
				useRp : true,
				rp : 50,
				width : ($(window).width()-20),
				height :  ($(window).height()-280)
			});
		}
		function Eliminar(NumTramite){
			$( "#ConfirmaEliminacion" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Esta seguro que quiere eliminar el tramite '+NumTramite+'?</p>');
			$( "#ConfirmaEliminacion" ).dialog({
				autoOpen: true,
				modal: true,
				buttons: {
					Si: function() {
						$("#ConfirmaEliminacion" ).dialog( "destroy" );
						$.ajax({
							type: "POST",
							url: "config/ajax_querys.php",
							data: { op: "EliminaTramite", term: NumTramite, codusuario:'<?=$_SESSION['uscod']?>', areausuario:<?=$_SESSION['area']?> }
						}).done(function( data ) {
							var obj =$.parseJSON(data);	
							
							if(obj[0].value>0)
								$( "#ConfirmaEliminacion" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El tramite '+NumTramite+' ha sido eliminado con exito, por favor elimine el codigo de barras impreso</p>');
							else
								$( "#ConfirmaEliminacion" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El tramite '+NumTramite+' no ha podido ser eliminado</p>');
						
							$(".flexme3").flexReload();
						});
						
						$( "#ConfirmaEliminacion" ).dialog({
							autoOpen: true,
							modal: true,
							buttons: {
								Aceptar: function() {
									$("#ConfirmaEliminacion" ).dialog( "destroy" );
								}
							}
							
						});						
					},
					No: function() {
						$("#ConfirmaEliminacion" ).dialog( "destroy" );									
					}
				}
			});
		}
	function MuestraDetalles(Tramite){
		$.ajax({
			type: "POST",
			url: "config/ajax_querys.php",
			data: { op: "DetallesTramite", term: Tramite, usu:'<?=$_SESSION['uscod']?>', areausu:<?=$_SESSION['area']?>}
		}).done(function( data ) {	
			var obj =$.parseJSON(data);	
			$( "#MuestraDetalles" ).html(obj[0].value);
			
			if($(window).width() > ($('#MuestraDetalles>table').width()+80))
				$( '#MuestraDetalles' ).dialog( 'option', 'width', $('#MuestraDetalles>table').width()+80);
			else
				$( '#MuestraDetalles' ).dialog( 'option', 'width', $(window).width()-80);
						
			if($(window).height() > ($('#MuestraDetalles>table').height()+80)){
				if($.browser.msie)
					$( '#MuestraDetalles' ).dialog( 'option', 'height', ($('#MuestraDetalles>table').height()+250));
				else
					$( '#MuestraDetalles' ).dialog( 'option', 'height', ($('#MuestraDetalles>table').height()+100));
			}else
				$( '#MuestraDetalles' ).dialog( 'option', 'height', $(window).height()-80);
		
			$( '#MuestraDetalles' ).dialog( 'option', 'position', 'center' );
		});
		$( "#MuestraDetalles" ).dialog({
			autoOpen: true,
			modal: true,
			width:($(window).width()-100),
			height: ($(window).height()-100),			
			buttons: {
				Aceptar: function() {
					$("#MuestraDetalles" ).html(' ');
					$("#MuestraDetalles" ).dialog( "destroy" );									
				}
			}
		});
	}		
    </script>
	
	<style>			
		label{
			margin-top:10px;
		}
		
		input{
			display:inline;			
		}	
		#Area{
			font-size:9px;
		}	
		span.prioritario{
			color:red;
			font-weight:bold;
			margin-top:20px;
		}	
		.Planillalote{
			font-weight: bold;
			color:blue;
			text-decoration: none;
		}
		.Detalles{
			color:blue;
		}
		.TableResult{
			color:black;
			border-spacing: 0px;
			border-color: #F1F1F1;
		}
		.TableResult td{
			padding:5px;
		}
		.TableResult th{
			text-align: left
		}
	</style>
</head>
<body>
<table class="flexme3"></table>

<div id="dialog-modal" title="Radicar Correspondencia" style="display:none">
<table align="center" style="position: absolute;"><tr><td>
	<form class="formular" action="#" id="formID" method="post" enctype="multipart/form-data">
	<fieldset style="width:620px">
	<a href="#" onclick="jQuery('#formID').validationEngine('hide')" style="margin-left: 280px;">Cerrar validaciones</a>
		
		<label>	<span>Destino: </span> </label>
			<input type="radio" name="Destino" id="DestinoInterno" value="Interno" style="display:inline;" checked="checked"/> Interno (La Equidad) &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="radio" name="Destino" id="DestinoExterno" value="Externo" style="display:inline;"/> Externo (Otras entidades)
		
		<div id="DivDestinoInterno">
		<label>	<span>Agencia: </span> </label>
		<select id="Agencia" name="Agencia" class="validate[required]" style="text-transform:uppercase;">
			<option value=""></option>
		</select>
		
		<label>	<span>Area:</span> </label>
		<select id="Area" name="Area" class="validate[required]" style="text-transform:uppercase;"></select>
				
		<label>	<span>Destinatario:</span> </label>
		<select id="Destinatario" name="Destinatario" class="validate[required]" style="text-transform:uppercase;">
		</select>
				
		<label><span>Tipo documento:</span> </label>		
		<select id="TipoDoc" name="TipoDoc" class="validate[required]" style="text-transform:uppercase;">
		</select>		

		<div id="identificaf"></div></div>
        <div id="identifica" style='display: none'>
        	
			<table><tr><td colspan = 4><label><span>Siniestro : </span></label>
                                        <input type='text' id='numsin' name='numsin' class='validate[maxSize[20]]' style='text-transform:uppercase; display:inline;' /></td></tr>

										<tr><td><label><span>Nombre Reclamante : </span></label>
                                        <input type='text' id='nomrec' name='nomrec' class='validate[required, maxSize[100]]' style='text-transform:uppercase; display:inline;' /></td>

										<td><label><span>Dirección Reclamante : </span></label>
                                        <input type='text' id='dirrec' name='dirrec' class='validate[required, maxSize[80]]' style='text-transform:uppercase; display:inline;' /></td>		
                                        <td><label><span>Teléfono Reclamante : </span></label>
                                        <input type='text' id='telrec' name='telrec' class='validate[required, maxSize[20]]' style='text-transform:uppercase; display:inline;' /></td>
										
										<td><label><span>Email Reclamante : </span></label>
                                        <input type='text' id='mailrec' name='mailrec' class='validate[required, maxSize[100]]' style='text-transform:uppercase; display:inline;' /></td></tr>


										<tr><td><label><span>Placa : </span></label>
                                        <input type='text' id='placa' name='placa' class='validate[maxSize[6]]' style='text-transform:uppercase; display:inline;' /></td>
                                        <td><label><span>Poliza : </span></label>
                                        <input type='text' id='poliza' name='poliza' class='validate[maxSize[12]]' style='display:inline;' /></td>
                                        <td colspan = 2><label><span>Producto : </span></label>
                                        <select id='prodd' name='prodd' class='validate[required, maxSize[6]]' style='display:inline;' />
                                        </select></td>
                                        </tr>

                                        <tr>
										<td><label><span>Vigencia : (ddmmyyyy) </span></label>
                                        <input type='text' id='vig' name='vig' onKeyUp = 'this.value=formateafecha(this.value);' class='validate[maxSize[10]]' style='display:inline;' /></td>
                                        <td><label><span>Ocurrencia : (ddmmyyyy)</span></label>
                                        <input type='text' id='ocurr' name='ocurr' onKeyUp = 'this.value=formateafecha(this.value);' class='validate[required, maxSize[10]]' style='display:inline;' />
                                        </td>

                                        <td colspan = 2><label><span>Pretension : </span></label>
                                        <input type='text' id='prete' name='prete' class='validate[required, maxSize[12]]' style='display:inline;' /></td></tr>
					</table>

        </div>


		<label>	<span>Origen externo:</span></label>
			<input type="radio" name="Origen" id="OrigenInterno" value="Interno" style="display:inline;" checked="checked"/> Interno (La Equidad) &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="radio" name="Origen" id="OrigenExterno" value="Externo" style="display:inline;"/> Externo (Otras entidades)
		<!--<input type="checkbox" id="Externo" name="Externo"/>		-->
		
		<div id="procedencia" style="display:none">
			<label><span>Ciudad:</span></label>
			<select id="Ciudad" name="Ciudad" class="validate[required]" style="text-transform:uppercase;">
				<option value="1"></option>
			</select>
		
			<label><span>Remitente:</span></label>
			<input type="text" id="Remitente" name="Remitente" class="validate[required, maxSize[45]]" style="text-transform:uppercase; width:300px"/>
			
			<label>	<span>Numero de guia:</span></label>
			<input type="text" id="NoGuia" name="NoGuia" class="validate[required, maxSize[25]]" style="text-transform:uppercase;"/>
			
		</div>
		</div>
		
		<div id="DivDestinoExterno" style='display:none'>
		
			<label><span>Aseguradora:</span></label>
			<select id="Tipo" name="Tipo" class="validate[required]" style="text-transform:uppercase;">
				<option></option>
				<option value="SEGUROS DE VIDA">SEGUROS DE VIDA</option>
				<option value="SEGUROS GENERALES">SEGUROS GENERALES</option>
			</select>
		
			<label><span>Ciudad:</span></label>
			<select id="Ciudad2" name="Ciudad2" class="validate[required]" style="text-transform:uppercase;">
				<option value="1"></option>
			</select>
			
			<label><span>Dirección:</span></label>
			<input type="text" name="Direccion" class="validate[required]" style="text-transform:uppercase; width:400px"/>
			
			<label><span>Destinatario:</span></label>
			<input type="text" name="DestinatarioExt" class="validate[required, maxSize[30]]" style="text-transform:uppercase; width:400px"/>
			
			<label><span>Telefono:</span></label>
			<input type="text" name="Telefono" class="validate[required,custom[integer],minSize[7]]" style="text-transform:uppercase;"/>
			
			<label><span>Tipo documento: </span></label>
			<select id="TipoDoc2" name="TipoDoc2" class="validate[required]" style="text-transform:uppercase;">
				<option value="1"></option>
			</select>
			
			<div id="identificae"></div>
			
			<label>	<span>Prioridad: </span> </label>
			<input type="radio" name="Prioridad" value="Normal" style="display:inline;" checked="checked"/> Normal &nbsp; &nbsp; &nbsp; &nbsp;
			<input type="radio" name="Prioridad" value="Alta" style="display:inline;"/> Alta			
		</div>
		
		<label><span>Asunto:</span></label>
		<input type="text" id="Asunto" name="Asunto" class="validate[required, maxSize[200]]" style="text-transform:uppercase; width:400px"/>
		
		<label>	<span>Numero de folios:</span> </label>
		<input type="text" id="NumeroFolios" name="NumeroFolios" class="validate[required,custom[integer],min[1]]" style="text-transform:lowercase;width:30px;"/>


		<div id="prioridad"></div>
		
		<label>	<span>Observaciones:</span> </label>
		<textarea id="Observaciones" name="Observaciones" style="width:400px; height:50px; resize: none"></textarea>
		
		<div style="text-align: right">
			<!--<button type="reset" id="Reseter">Borrar</button>-->
		<input type="hidden" id="env" name="env" value="no">
		<input type="hidden" id="tarea" name="tarea">
		<input type="hidden" id="tTipoDoc" name="tTipoDoc">
		<input type="hidden" id="tDestinatario" name="tDestinatario">
		<input type="hidden" id="tCiudad" name="tCiudad">
		<button type="submit" name="GuardarNuevo" id="GuardarNuevo" value="Guardar">Guardar- Nuevo</button>
		<button type="button" name="Guardar" id="Guardar" onclick="$('#env').val('si');$('#GuardarNuevo').click()">Guardar</button>

		</div>	
		</fieldset>
	</form>
</td></tr></table></div>
<br>

<div id="ConfirmaEliminacion"></div>
<div id="MuestraDetalles"></div>

<?php $notrares=GuardaRadicacion(); ?>

<script>MuestraCodeBar('<?=$notrares?>', '<?=$_SESSION['uscod']?>')</script>

<?=OpcionesSelect('Agencia', 'tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp tip', ' DISTINCT (ofi.codigo)', 'ofi.descrip', "where ofi.codigo=are.agencia and are.areasid=tip.area and ofi.codigo!='999' and areasid!='40'")?>
<?=OpcionesSelect('prodd', 'radproducto', 'codigo','descripcion || ' . "'-'" . ' || codigo', '')?>
</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}

function GuardaRadicacion(){//Funcion que guarda nueva radicacion
	if(!$_REQUEST['GuardarNuevo'] == 'Guardar')
		return;//Si no envio una nueva radicacion termina 
	
	$salida="";
	
	$conect=new conexion();
	$consulta=$conect->queryequi("select agencia from adm_usuario usu join tblareascorrespondencia are on (usu.area=are.areasid) where usuario_cod=".$_SESSION['uscod']);
	$row = pg_fetch_array($consulta);//consulta la agencia de usuario logeado
	$AgenciaUsu=$row[0];

	if(strlen($AgenciaUsu) != 3)
		exit;

	$consulta=$conect->queryequi("select case when (substring(max(numtramite) from 12 for 4)='9999') 
								THEN 'false' when (substring(max(numtramite) from 12 for 4)!='9999') 
								THEN 'true' end as continua, to_number(max(numtramite),'000000000000000')+1 as 
								siguiente from  radcorrespondencia where numtramite like '".date ( "Ymd" ).$AgenciaUsu."%'");
	
	$row = pg_fetch_array($consulta);
	
	if($row['continua'] != 'false'){
		if(strlen($row['siguiente'])==15)
			$NumeroTramite=$row['siguiente'];
		else
			$NumeroTramite=date( "Ymd" ).$AgenciaUsu.'0001';
	}else{
		$salida.="<script>alert('No se pueden radicar mas documentos el dia de hoy')</script>";
		return $salida;
	}

	if($_REQUEST['numsin'] === '' ){
		
		$rstxc = $conect->queryequi("select tipodocid, tipo, prioridad from tbltiposdoccorresp 
			where tipodocid='" . $_REQUEST['TipoDoc'] . "' and tipo like 'SINIESTROS%'");
		if(pg_num_rows($rstxc)!=0)
			$numsinx = "STR" . $NumeroTramite;
	}else{
		$numsinx = $_REQUEST['numsin'];
		
	}
		$Remitente=((strlen($_REQUEST['Remitente']) > 0)?$_REQUEST['Remitente']:$Remitente=$_SESSION['uscod']);
		
	if($_REQUEST['Observaciones']!=null){
		$consulta = $conect->query("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  from admusuario where usuario_cod='".$_SESSION['uscod']."'");		
		$row = pg_fetch_array($consulta);
		$Observaciones="<b style=''color: #00009B; font-size: 9px''>".date("h:i:s A d-m-Y ")." - ".$row[0].":</b> <div style=''margin-left:30px; width:500px''>".str_replace(array("'", " "), '',$_REQUEST['Observaciones']).'</div>';// Guarda nombre comentario
	}
	
	$consulta=$conect->queryequi("insert into radcorrespondencia (sr, area, tipodoc, asunto, numguia, observaciones, fecins, 
		remitente, ciudad, numtramite, numfolios, destinatario, radicado, identificacion, siniestro) 
		values ((select max(sr)+1 from radcorrespondencia), ".(($_REQUEST['Destino'] == 'Externo') ? '68' : $_REQUEST['Area']).", 
		".(($_REQUEST['Destino'] == 'Externo') ? $_REQUEST['TipoDoc2'] : $_REQUEST['TipoDoc']).", upper('".str_replace(array("\t", "\n"), ' ',str_replace(array("'", " "), '', $_REQUEST['Asunto']))."'), upper('".str_replace(array("'", " "), '', $_REQUEST['NoGuia'])."'), '".$Observaciones."', now(), 
		upper('".str_replace(array("'", " "), '', $Remitente)."'), '".(($_REQUEST['Destino'] == 'Externo') ?  $_REQUEST['Ciudad2'] : $_REQUEST['Ciudad']  )."', '$NumeroTramite', '".$_REQUEST['NumeroFolios']."', '".(($_REQUEST['Destino'] == 'Externo') ? '0' : $_REQUEST['Destinatario'] )."', '".$_SESSION['uscod']."',
		'".(($_REQUEST['Destino'] == 'Externo') ? $_REQUEST['Identificacion'] : $_REQUEST['Identificacion'] )."',upper('".$numsinx."')  )");
	
	if($_REQUEST['Destino'] == 'Externo')
		$conect->queryequi("insert into radcorresext (numtramite, destinatario, telefono, direccion,  prioridad, tipo) values ('$NumeroTramite' , upper('".str_replace(array("'", "\""), '',$_REQUEST['DestinatarioExt'])."'), '".
					$_REQUEST['Telefono']."', upper('".str_replace(array("'", "\""), '', $_REQUEST['Direccion'])."'), upper('".$_REQUEST['Prioridad']."'), '".$_REQUEST['Tipo']."')");

	if ($numsinx != ''){
			$conect->queryequi("insert into radsiniestro (numtramite,nombre,direccion,telefono,email,placa,poliza,producto,vigencia,ocurrencia,pretencion) values ('$NumeroTramite',
				upper('".str_replace(array("'", "\""), '', $_REQUEST['nomrec'])."'),  upper('".str_replace(array("'", "\""), '', $_REQUEST['dirrec'])."'), 
				upper('".str_replace(array("'", "\""), '', $_REQUEST['telrec'])."'),  upper('".str_replace(array("'", "\""), '', $_REQUEST['mailrec'])."'), upper('".$_REQUEST['placa']."'),
					upper('".$_REQUEST['poliza']."'),upper('".$_REQUEST['prodd']."'),'".$_REQUEST['vig']."',
					'".$_REQUEST['ocurr']."','".$_REQUEST['prete']."' ) ");
	}
		
	$conect->cierracon();
	
	if($consulta)
		TrazabilidadCorrespondencia($NumeroTramite);
		
	if($_REQUEST['NumAdj'] > 0){
		EnviaCorreo($NumeroTramite);
		GuardaArchivos($NumeroTramite);
	}

	return $NumeroTramite;
	//return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&codebar=$NumeroTramite';</script>";
}

function GuardaArchivos($NumeroTramite){
	//if($_SERVER['SERVER_NAME']=='imagine.laequidadseguros.coop')
	//if($_SERVER['HTTP_HOST'] === '192.168.241.87')
		$PathAdjuntos="/vol2";
	//else
	//	$PathAdjuntos="vol2";

	if(!file_exists ( $PathAdjuntos.'/'.date("Ymd").'/Correspondencia' )){	
		if (!file_exists($PathAdjuntos.'/'.date("Ymd"))) {
			mkdir( $PathAdjuntos.'/'.date("Ymd").'/Correspondencia', 0775, true);
			chmod( $PathAdjuntos.'/'.date("Ymd"), 0775);
			chmod( $PathAdjuntos.'/'.date("Ymd").'/Correspondencia', 0775);
		}else{
			mkdir( $PathAdjuntos.'/'.date("Ymd").'/Correspondencia', 0775, true);
			chmod( $PathAdjuntos.'/'.date("Ymd").'/Correspondencia', 0775);
		}
	}
	$PathAdjuntos=$PathAdjuntos.'/'.date("Ymd").'/Correspondencia/';

	$values="";
	
	for($i=0; $i<$_REQUEST['NumAdj']; $i++){
		$NombreArchivo=$PathAdjuntos . $NumeroTramite. "-" . $i . strtolower(substr ($_FILES["file".$i]['name'], strrpos($_FILES["file".$i]['name'], ".")));
		if(move_uploaded_file($_FILES["file".$i]["tmp_name"], $NombreArchivo))
			$values .= "('$NumeroTramite$i', '$NumeroTramite', '".(($i==0)?"P":"A")."', '".realpath($NombreArchivo)."', '".$_FILES["file".$i]['name']."'), ";			
		else
			$errores.=$_FILES["file".$i]["name"];
	}
	
	if(strlen( $values )>0){
		$conect=new conexion();
		$values=substr( $values ,0,strlen( $values )-2);
		$consulta="insert into correspondencia (sr, srtodo, tipo, path, nombre)values".$values;
		$conect->queryequi($consulta);
		$conect->cierracon();
	}
	if($errores != null)
		echo "<script>alert('No se han podido subir los siguientes archivos: $errores')</script>";
}

function TrazabilidadCorrespondencia($NumeroTramite){
	$conect=new conexion();
	
	$inserts="('".$NumeroTramite."', now(), ".$_SESSION['uscod'].", 'RADICADO', ".$_SESSION['area']."), ";
	
	/*$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where  agencia=(select agencia from tblareascorrespondencia where areasid=".$_SESSION['area'].") and correspondencia='t'");
	$row = pg_fetch_array($consulta);
		
	if($row['areasid'] == $_SESSION['area']){
		if($_REQUEST['Destino'] == 'Interno'){
			$consulta=$conect->queryequi("select (case when agencia='".$_REQUEST['Agencia']."' then 'DISTRIBUCION' else 'ENVIADO' end) from tblareascorrespondencia where areasid=".$_SESSION['area']." and correspondencia='t'");
			$row = pg_fetch_array($consulta);
			$inserts.="('".$NumeroTramite."', ".(($row[0]=='DISTRIBUCION')?("now() + '00:00:10', ".$_SESSION['uscod']):"null, null").",'".$row[0]."', ".$_SESSION['area']."), ";
			$inserts.="('".$NumeroTramite."', null, null, 'RECIBIDO', ".$_REQUEST['Area']."), ";
		}else{
			$inserts.="('".$NumeroTramite."', null, null, 'DISTRIBUCION EXTERNA', ".$_SESSION['area']."), ";
		}		
	}else{
		$inserts.="('".$NumeroTramite."', null, null, 'RECIBIDO CORRESPONDENCIA', ".$row['areasid']."), ";
		if($_REQUEST['Destino'] == 'Interno'){						
			$inserts.="('".$NumeroTramite."', null, null, 'RECIBIDO', ".$_REQUEST['Area']."), ";
		}
	}*/
	
	$inserts=substr( $inserts ,0,strlen( $inserts )-2);
	$consulta=$conect->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values $inserts");
	$conect->cierracon();
	
	Trazabilidad($NumeroTramite, $_SESSION['uscod'], null);
}

function EnviaCorreo($NumTramite){
	require 'config/phpmailer/class.phpmailer.php';
	$conect=new conexion();
	$consulta=$conect->queryequi("select to_char(cor.fecins,'yyyy-MM-dd HH:MI:SS AM') as fecharad, ofi.descrip, doc.tipo, 
					cor.destinatario from radcorrespondencia cor, tblradofi ofi, tbltiposdoccorresp doc, 
					tblareascorrespondencia are where cor.area=are.areasid and are.agencia=ofi.codigo and 
					cor.tipodoc=doc.tipodocid and cor.numtramite='$NumTramite'");
	$row = pg_fetch_array($consulta);
	$consulta=$conect->query("select * from admusuario where usuario_cod='".$row['destinatario']."'");
	$row2 = pg_fetch_array($consulta);
	$conect->cierracon();
	
	$correos =array(strtolower($row2['usuario_correo']));
	//$correos =array("William.QuitianExt@laequidadseguros.coop");
	
	$body	= file_get_contents('config/phpmailer/contents.html');
	$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
	$body   = str_replace('<HoraRadicado>',$row['fecharad'], $body);
	$body   = str_replace('<Agencia>',$row['descrip'], $body);
	$body   = str_replace('<TipoDocumento>',$row['tipo'], $body);
	$body   = str_replace('<NumTramite>',$NumTramite, $body);

try {
	$mail = new PHPMailer(true); 
	$body = preg_replace('/\\\\/','', $body);
	
	$mail->IsSMTP();                           
	$mail->SMTPAuth   = false;             
	$mail->Port       = 25;                    
	$mail->Host       = "192.168.241.63";
	//$mail->Host       = "outlook.laequidad.com.co"; 
	$mail->From       = "correspondencia@laequidadseguros.coop";
	$mail->FromName   = "Correspondencia Equidad";
	$mail->Subject  = "Alerta de correspondencia";	

	if ($row['tipo'] =='REQUERIMIENTOS ENTES DE CONTROL'){
		$mail->AddCC("direccion.juridica@laequidadseguros.coop");
		$mail->AddCC("Johanna.Rodriguez@laequidadseguros.coop");
		$mail->AddBCC("William.QuitianExt@laequidadseguros.coop");
	}

	$mail->MsgHTML($body);
	$mail->IsHTML(true); 
	$intentos=0;
	
	foreach( $correos as $destino ) {
		$mail->addAddress( $destino );
	} 
	
	while ((!$mail->Send()) && ($intentos < 5)) {
		sleep(2);
		$intentos=$intentos+1;
	}
	//echo 'Message has been sent.'.$intentos;
} catch (phpmailerException $e) {
	echo "<script>alert('No se ha podido enviar el e-mail al destinatario debido a un error ');</script>";//echo $e->errorMessage();
}
}
?>
