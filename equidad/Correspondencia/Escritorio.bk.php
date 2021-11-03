<?/*
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/Escritorio.php'
paar correcta visualización
*/?>
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Correspondencia/Trazabilidad.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>	
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
	<script src="js/ui/js/jquery.ui.datepicker.js"></script><!-- Calensario	configuration -->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/ui/js/datepicker/jquery.ui.datepicker-es.js"></script><!-- Calensario	configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script src="js/jquery.maskedinput.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->
	
	<script type="text/javascript" src="js/VisorImagine.js"></script><!--Visor-->
	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" /><!-- Visor configuration -->
    <script src="js/jquery.Jcrop.js" type="text/javascript"></script><!-- Visor configuration -->
	<script src="js/jquery.hotkeys.js" type="text/javascript"></script><!-- Visor configuration -->

	<script type="text/javascript">
        $(document).ready(function() {		
			tables();
			setTimeout("$('#NumTramite').focus()",1);	
			$('#Correspondencia').change(function(){
				$(".flexme3").flexOptions(
					{params:[{ name: 'consulta', value: 'Escritorio' },
							  { name: 'areausu', value: '<?=$_SESSION['area']?>' },
							  { name: 'uscod', value: '<?=$_SESSION['uscod']?>' },
							  { name: 'FiltroEstado', value:$('#Correspondencia').val() }
							]
					}); 
				$(".flexme3").flexReload();
				$("#Corresp").val($('#Correspondencia').val());
			});
			$( "#codigobarras" ).dialog({
				autoOpen: false,
				width:520,
				height: 200,
				modal: true,
				close: function(event, ui) {  }
			});
			
			$( "#numguia" ).dialog({
				autoOpen: false,
				width:350,
				height: 220,
				modal: true,
				close: function(event, ui) { 
					$("#NumTramite" ).val('');
				}
			});
			
			$( "#Observaciones" ).dialog({
				autoOpen: false,
				width:350,
				height: 320,
				modal: true,
				close: function(event, ui) { 
					$("#NumTramite" ).val('');
				},
				buttons: {
					Aceptar: function() {
						if($('#FormOb').validationEngine('validate'))
							$.ajax({
								type: "POST",
								url: "config/ajax_querys.php",
								data: { op: "DevolucionTramite", term: $('#TramiteDev').val(), usu:'<?=$_SESSION['uscod']?>' , areausu:'<?=$_SESSION['area']?>', AreaRedi:$('#AreaRedi').val(), Causa:$('#Causa').val(), Observacion:$('#Obs').val() }
							}).done(function( data ) {	
								var obj =$.parseJSON(data);	
								if(obj[0].value !='t')
									alert("Error: "+data);
								else
									alert("Tramite devuelto.");
									$(".flexme3").flexReload();
									$("#Observaciones" ).dialog( "close" );													
							});
					},
					Cancelar: function() {
						$("#Observaciones" ).dialog( "close" );				
					}
				}
			});
					
			$('#Ir').click(function(){BuscaTramite(); BuscaCierraTramite();EnviaTramite();});
			$('#NumTramite').keypress(function(e) {
				if(e.which == 13) {
					BuscaTramite();		
					BuscaCierraTramite();
					EnviaTramite();
				}
			});
			
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
			
			$('#Area').change(function() {//Funcion que se ejecuta al cambiar el select para cambiar combos #Destinatario y #TipoDoc
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
			});	
						
			$( "#Guia").autocomplete({
				source: "config/ajax_querys.php?op=BuscaGuia",
				minLength: 1,
				delay: 200,
				height : 200
			});
		
			$("#formID, #FormEnvio, #FormOb, #FormObCe, #formIDexterno").validationEngine('attach', {promptPosition : "topLeft"});	
			MuestraCodeBar('<?=$_REQUEST['codebar']?>');
		});
		
		function tables(){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'Escritorio' },
							  { name: 'areausu', value: '<?=$_SESSION['area']?>' },
							  { name: 'uscod', value: '<?=$_SESSION['uscod']?>' },
							  { name: 'FiltroEstado', value:$('#Correspondencia').val() }							  
							],
				colModel : [ { display : '', width : 20, align : 'center'},
							 { display : 'Num Tramite',	name : 'cor.numtramite', width : 100, sortable : true, align : 'center'}, 
							 { display : 'Fecha y hora radicacion', name : 'cor.fecins', width : 120, sortable : true, align : 'center'},
							 { display : 'Remitente', name : 'cor.remitente', width : 200, sortable : true,	align : 'left'}, 
							 { display : 'Agencia destino',	name : 'ofi.descrip', width : 180, sortable : true,	align : 'left'}, 
							 { display : 'Area destino', name : 'are.area',	width : 220, sortable : true, align : 'left'}, 
							 { display : 'Destinatario', name : 'destinatario',	width : 200,	sortable : true, align : 'left'} ,
							 { display : 'Tipo doc', name : 'doc.tipo',	width : 120,	sortable : true, align : 'left'} ,
							 { display : 'Folios', name : 'cor.numfolios',	width : 30,	sortable : true, align : 'right'} ,
							 { display : 'Nit/CC', name : 'cor.identificacion',	width : 55,	sortable : true, align : 'right'} 
						   ],
				sortname : "cor.fecins",
				sortorder : "desc",
				usepager : true,
				title : 'Escritorio',
				useRp : true,
				rp : 50,
				width : ($(window).width()-20),
				height :  ($(window).height()-300)
			});
		}
		
		function BuscaTramite(){
			if($('#Correspondencia').val() == 0 && $('#NumTramite').val().length == 15){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "SiRecibirCor", term: $('#NumTramite').val(), areausu:'<?=$_SESSION['area']?>', usucod:'<?=$_SESSION['uscod']?>' }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					if(obj[0].value == 'f'){	
						$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El tramite numero '+$('#NumTramite').val()+' no se puede recibir</p><br>');					
						$( "#dialog-modal" ).dialog({
							autoOpen: true,
							modal: true,
							buttons: {
								Aceptar: function() {
									$( '#NumTramite' ).val('');
									$( '#NumTramite' ).focus(); 
									$("#dialog-modal" ).dialog( "destroy" );
								}
							}
						});
					}else{
						if(obj[2].value == 'DEVUELTO'){	
							var msj="Este tramite ha sido devulelto por otro usuario. <br><br>";
							NoRecibirTramite($('#NumTramite').val(), msj, obj[1].value, obj[3].value);
							$( '#NumTramite' ).val('');
						}else{
							$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Desea recibir la correspondencia con el numero de tramite '+$('#NumTramite').val()+'?</p>'+obj[0].value);
							$( "#dialog-modal" ).dialog({
								autoOpen: true,
								modal: true,
								buttons: {
									Si: function() {
										RecibirTramite($('#NumTramite').val());
										$( '#NumTramite' ).val('');
										$( '#NumTramite' ).focus(); 
										$("#dialog-modal" ).dialog( "destroy" );
									},
									No: function() {
										$("#dialog-modal" ).dialog( "destroy" );									
										NoRecibirTramite($('#NumTramite').val(), ' ', obj[1].value);
										$( '#NumTramite' ).val('');
									}
								}
							});
						}
					}
					
				});			
				$( "#dialog-modal" ).dialog("open"); 
			}
		}
		
		function BuscaCierraTramite(){
			if($('#Correspondencia').val() == 1 && $('#NumTramite').val().length == 15){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "SiCerrarCor", term: $('#NumTramite').val(), uscod:'<?=$_SESSION['uscod']?>' , areausu:'<?=$_SESSION['area']?>'}
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					if(obj[0].value == 'f'){	
						$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El tramite numero '+$('#NumTramite').val()+' no se puede cerrar</p><br>');					
						$( "#dialog-modal" ).dialog({
							autoOpen: true,
							modal: true,
							buttons: {
								Aceptar: function() {
									$( '#NumTramite' ).val('');
									$( '#NumTramite' ).focus(); 
									$("#dialog-modal" ).dialog( "destroy" );
								}
							}
						});
					}else{
						var mensaje="";
						if(obj[0].value == 'interno')
							mensaje="Desea cerrar el caso con el numero de tramite " +$('#NumTramite').val()+" ?" +obj[1].value;
							
						if(obj[0].value == 'externo')
							mensaje="La correspondencia con el tramite "+$('#NumTramite').val()+" ha sido entregada correctamente ?"+obj[1].value;
						
						$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+mensaje+'</p>');
						$( "#dialog-modal" ).dialog({
							autoOpen: true,
							modal: true,
							buttons: {
								Si: function() {
									CerrarTramite($('#NumTramite').val(), obj[0].value);
									$( '#NumTramite' ).val('');
									$( '#NumTramite' ).focus(); 
									$("#dialog-modal" ).dialog( "destroy" );
								},
								No: function() {
									$("#dialog-modal" ).dialog( "destroy" );
									NoRecibirTramite($('#NumTramite').val(), '', obj[0].value);
								}
							}	
						});
					}
				});			
				$( "#dialog-modal" ).dialog("open"); 
			}
		}
		
		function EnviaTramite(){
			if($('#Correspondencia').val() == 4 && $('#NumTramite').val().length == 15){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "SiEnviarCor", term: $('#NumTramite').val(), areausu:'<?=$_SESSION['area']?>'}
				}).done(function( data ) {
					var obj =$.parseJSON(data);	
					if(obj[0].value == 't'){
						$("#Guia, #EmpresaMsj" ).val("");
						$("#numguia" ).dialog("open");						
					}else{
						$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El tramite numero '+$('#NumTramite').val()+' no puede ser enviado</p><br>');					
						$( "#dialog-modal" ).dialog({
							autoOpen: true,
							modal: true,
							buttons: {
								Aceptar: function() {
									$( '#NumTramite' ).val('');
									$( '#NumTramite' ).focus(); 
									$("#dialog-modal" ).dialog( "destroy" );
								}
							}
						});
					}
				});	
			}
		}
		
		function RecibirTramite(NumTramite){
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "RecibirTramite", term: NumTramite, usu:'<?=$_SESSION['uscod']?>' , areausu:'<?=$_SESSION['area']?>' }
				}).done(function( data ) {	
					$(".flexme3").flexReload();
				});
		}
		
		function CerrarTramite(NumTramite, tipo){
			if(tipo == 'externo'){
				$( "#dialog-espera" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Espere un momento, por favor.<br>Enviando e-mail de confirmación ....</p>');
				$( "#dialog-espera" ).dialog({
					autoOpen: true,
					modal: true,
				});
			}
			$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "CerrarTramite", term: NumTramite, uscod:'<?=$_SESSION['uscod']?>', TipoCor: tipo }
				}).done(function( data ) {	
					$(".flexme3").flexReload();
					$("#dialog-espera" ).dialog( "destroy" );
					if(tipo == 'externo')
						alert("Un e-mail ha sido enviado al usuario que radico la correspondencia.");
				});
		}
		
	function NoRecibirTramite(NumTramite, msj, tipo, area){
		var botones = [{text: "Redireccionar", 
						click: function() {
							$("#dialog-modal" ).dialog( "destroy" );
							RedireccionarTramite(NumTramite, tipo);						
						}
					}];
					
		if(area == <?=$_SESSION['area']?>){
			botones.push({
				text: "Cerrar tramite",
				click: function() {
					$("#dialog-modal" ).dialog( "destroy" );
					$('#ObsDev').val("");
					$('#FormObCe').validationEngine('hide');
					$( "#ObCerrar" ).dialog({
						autoOpen: true,
						width:350,
						height: 250,
						modal: true,
						buttons: [
							{text:"Cerrar tramite", click: function() {
								if($('#FormObCe').validationEngine('validate'))
									$.ajax({
										type: "POST",
										url: "config/ajax_querys.php",
										data: { op: "CerrarTramiteDev", term: NumTramite, observacion: $("#ObsDev").val(), areausu:'<?=$_SESSION['area']?>', idusu:'<?=$_SESSION['uscod']?>'}
									}).done(function( data ) {	
										var obj =$.parseJSON(data);	
										alert("Tramite cerrado");
										$(".flexme3").flexReload();
										$("#ObCerrar" ).dialog( "destroy" );
									});
																		
							}},
							{text:"Cancelar", click: function() {
								$("#ObCerrar" ).dialog( "destroy" );
							}}
						]
					});
				}
			});
		}else{
			botones.push({
				text: "Devolver",
				click: function() {
					$("#dialog-modal" ).dialog( "destroy" );
					$.ajax({
						type: "POST",
						url: "config/ajax_querys.php",
						data: { op: "AreaDevolucion", term: NumTramite, areausu:'<?=$_SESSION['area']?>'}
					}).done(function( data ) {	
						var obj =$.parseJSON(data);	
						if(obj[0].value !='f'){
							$('#ObDevolucion').html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Este tramite se devolvera al area '+obj[1].value+' </p>');
							$('#TramiteDev').val(NumTramite);
							$('#AreaRedi').val(obj[0].value);
							$('#FormOb').validationEngine('hide');
							$('#Causa, #Obs').val('');
							$( "#Observaciones" ).dialog( "open" );
						}else{
							$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Este tramite no puede ser devuelto. </p><br>');					
							$( "#dialog-modal" ).dialog({
								autoOpen: true,
								modal: true,
								buttons: {
									Redireccionar: function() {
										$("#dialog-modal" ).dialog( "destroy" );
										RedireccionarTramite(NumTramite, tipo);											
									},
									Cancelar: function() {
										$("#dialog-modal" ).dialog( "destroy" );
									}
								}
							});
						}
					});
				}
			});
		}
				
			//console.log(botones);
			$( "#dialog-modal" ).html('<br><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+msj+'Que desea hacer con el tramite numero '+$('#NumTramite').val()+' ?</p><br>');					
			$( "#dialog-modal" ).dialog({
				autoOpen: true,
				modal: true,
				buttons: botones
			});
		}
		
		function RecibirIcono(Tramite){
			$('#NumTramite').val(Tramite);
			$('#Ir').click();			
		}
		
		function CerrarIcono(Tramite){
			$('#NumTramite').val(Tramite);
			$('#Ir').click();			
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
		
		function RedireccionarTramite(Tramite, tipo){
		
			if(tipo == 'externo'){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "DatosExterno",  term: Tramite, areausu:'<?=$_SESSION['area']?>' }
				}).done(function( data ) {
					var obj =$.parseJSON(data);	
					$('#Ciudad').val(obj[0].value);
					$('#Direccion').val(obj[1].value);
					$('#DestinatarioExt').val(obj[2].value);
					$('#Telefono').val(obj[3].value);
				});
				formulario="FormularioRedireccionarexterno";
				$('#formIDexterno>#NumTramtie').val(Tramite);
			}else
				formulario="FormularioRedireccionarinterno";
				
			$('#NumTramtie').val(Tramite);
			$( "#"+formulario).dialog({
				autoOpen: true,
				modal: true,
				width:500,
				height: 350
			});
		}
		
		function GuardaGuia(){
			if($('#FormEnvio').validationEngine('validate')){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "GuardaNumGuia", NumGuia: $("#Guia" ).val(), EmpresaMsj: $("#EmpresaMsj" ).val(), term:$("#NumTramite" ).val(), codusuario:'<?=$_SESSION['uscod']?>', areausu:'<?=$_SESSION['area']?>' }
				}).done(function( data ) {
					$(".flexme3").flexReload();
					$("#numguia" ).dialog("close");
				});
			}
		}
	</script>
	
	<style>
		.filtros{
			color:#08298A;	
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
		.ui-autocomplete {
			max-height: 100px;
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
		}

	</style>
</head>
<body>	
<table align='center' style='padding:10px'><tr><td>
	<fieldset style="width:150px;" class='filtros'>
		<legend><b>Estado: </b></legend>			
		<select id='Correspondencia'>
			<option value="0">Por recibir</option>
			<?=MasOpciones();?>
			<option value="1">Por cerrar</option>
			<option value="5">Proximos a recibir</option>
			<option value="2">Radicada</option>
			<option value="3">Cerrada</option>			
		</select>
	</fieldset>
</td><td>
	<fieldset style="width:130px;" class='filtros'>
		<legend><b>Numero de tramite: </b></legend>			
		<input type="text" id="NumTramite" style='width:120px'/>
	</fieldset>
</td><td>
	<button type="button" id='Ir'>Ir.. </button>
</td></tr></table>

<table class="flexme3"></table>
<div id="dialog-modal" title="Recibir Correspondencia" style="display:none"></div>
<div id="dialog-espera" title="Recibir Correspondencia" style="display:none"></div>
<div id="MuestraDetalles"></div>

<div id="FormularioRedireccionarinterno" title="Redireccionar" style="display:none">
<table align="center"><tr><td>
	<form class="formular" action="#" id="formID" method="post">
	<input type="hidden" id="NumTramtie" name="NumTramtie"/>
	<fieldset style="width:320px">
		
		<label>	<span>Agencia: </span> </label>
		<select id="Agencia" name="Agencia" class="validate[required]" style="text-transform:uppercase;">
			<option value=""></option>
		</select>
		
		<label>	<span>Area:</span> </label>
		<select id="Area" name="Area" class="validate[required]" style="text-transform:uppercase;"></select>
				
		<label>	<span>Destinatario:</span> </label>
		<select id="Destinatario" name="Destinatario" class="validate[required]" style="text-transform:uppercase;">
		</select>
		
		<label>	<span>Observación:</span> </label>
			<textarea class="validate[required] text-input" rows="2" cols="20" id="Obs" name="observacion"></textarea>
		
		<div style="text-align: right">
			<button type="submit" name="Guardar" id="Guardar" value="Redireccionar">Redireccionar</button>
		</div>	
		</fieldset>
	</form>
</td></tr></table></div>

<div id="FormularioRedireccionarexterno" title="Redireccionar" style="display:none">
<table align="center"><tr><td>
	<form class="formular" action="#" id="formIDexterno" method="post">
	<input type="hidden" id="NumTramtie" name="NumTramtie"/>
	<fieldset style="width:420px">
		
		<label>	<span>Ciudad: </span> </label>
		<select id="Ciudad" name="Ciudad" class="validate[required]" style="text-transform:uppercase;">
			<option value=""></option>
		</select>
		
		<label><span>Dirección:</span></label>
		<input type="text" name="Direccion"  id="Direccion" class="validate[required]" style="text-transform:uppercase; width:400px"/>
			
		<label><span>Destinatario:</span></label>
		<input type="text" name="DestinatarioExt" id="DestinatarioExt" class="validate[required, custom[onlyLetterSp]" style="text-transform:uppercase; width:400px"/>
			
		<label><span>Telefono:</span></label>
		<input type="text" name="Telefono" id="Telefono" class="validate[required,custom[integer],minSize[7]]" style="text-transform:uppercase;"/>
		
		<label>	<span>Observación:</span> </label>
			<textarea class="validate[required] text-input" rows="2" cols="20" id="Obs" name="observacion"></textarea>
		
		<div style="text-align: right">
			<button type="submit" name="Guardar" id="Guardar" value="Redireccionar">Redireccionar</button>
		</div>	
		</fieldset>
	</form>
</td></tr></table></div>
<div id="codigobarras"></div>

<div id="numguia" title="Numero guia" style="display:none">
	<form class="formular" action="#" id="FormEnvio" method="post">
		<fieldset>
			<legend>Numero guia y envio</legend>
			<label>	<span>Numero Guia:</span> </label>
			<input type="text" id="Guia" name="Guia" class="validate[required]" style="width:150px"/>
			
			<label>	<span>Empresa mensajeria:</span> </label>
			<select id="EmpresaMsj" name="EmpresaMsj" class="validate[required]" style="text-transform:uppercase;">
				<option value=""></option>
			</select>
					
			<div style="text-align: right">
				<button type="button" onClick="GuardaGuia()" id="BotonGuia">Guardar</button>
			</div>	
		</fieldset>
	</form>
</div>

<div id="Observaciones" title="Devolución" style="display:none">
	<form class="formular" action="#" id="FormOb" method="post">
		<fieldset>
			<div id="ObDevolucion"></div>
			<input type='hidden' id='TramiteDev'>
			<input type='hidden' id='AreaRedi'>
			<label>	<span>Causa de devolución:</span> </label>
			<select id="Causa" class="validate[required]">
				<option value=""></option>
				<option value="Destinatario equivocado">Destinatario equivocado</option>
				<option value="Dirección errada">Direccion errada</option>
			</select>
			
			<label>	<span>Observación:</span> </label>
			<textarea class="validate[required] text-input" rows="2" cols="20" id="Obs"></textarea>
	
		</fieldset>
	</form>
</div>

<div id="ObCerrar" title="Cerrar tramite" style="display:none">
	<form class="formular" action="#" id="FormObCe" method="post">
		<fieldset>
			<label>	<span>Escriba una observacion por la cual cierra el caso:<br><br></span> </label>

			<textarea class="validate[required] text-input" rows="2" cols="20" id="ObsDev"></textarea>
		</fieldset>
	</form>
</div>
<?=OpcionesSelect('Ciudad', 'tblciudades ciu', ' ciu.idciudad', 'ciu.ciudad', "")?>
<?=OpcionesSelect('EmpresaMsj', 'tblempresamsj msj', ' msj.id_empresa', 'msj.empresa', "")?>
<?=OpcionesSelect('Agencia', 'tblradofi ofi, tblareascorrespondencia are, tbltiposdoccorresp tip', ' DISTINCT (ofi.codigo)', 'ofi.descrip', "where ofi.codigo=are.agencia and are.areasid=tip.area and areasid!='40' and ofi.codigo!='999'")?>
<?=GuardaRedirrecionar()?>
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
?>

<?php
function GuardaRedirrecionar(){
	if($_REQUEST['Guardar']==null)
		return;
		
	$conect=new conexion();
	
	$consulta = $conect->query("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  from admusuario where usuario_cod='".$_SESSION['uscod']."'");		
	$row = pg_fetch_array($consulta);
	$Observaciones="<b style=\'color: #00009B; font-size: 9px\'>".date("h:i:s A d-m-Y ").' - '.$row[0].":</b> <div style=\'margin-left:30px; width:500px\'><b>OBSERVACIÓN REDIRECCIONAR:</b> <br>".str_replace(array("'", "\""), '', $_REQUEST['observacion']).'</div>';// Guarda nombre comentario
	
	if($_REQUEST['Area'] != null){
		$conect->queryequi("update radcorrespondencia set area='".$_REQUEST['Area']."', destinatario='".$_REQUEST['Destinatario']."', observaciones = COALESCE(observaciones, '') ||'$Observaciones' where numtramite='".$_REQUEST['NumTramtie']."'");
	}else{
		$conect->queryequi("update radcorrespondencia set ciudad='".$_REQUEST['Ciudad']."', observaciones = COALESCE(observaciones, '') ||'$Observaciones' where numtramite='".$_REQUEST['NumTramtie']."'");
		$conect->queryequi("update radcorresext set direccion='".str_replace(array("'", "\""), '', $_REQUEST['Direccion'])."', destinatario='".str_replace(array("'", "\""), '', $_REQUEST['DestinatarioExt'])."', telefono='".str_replace(array("'", "\""), '', $_REQUEST['Telefono'])."' where numtramite='".$_REQUEST['NumTramtie']."'");
	}
	$consulta=$conect->queryequi("delete from trasacorrespondencia where fechahora is null and numtramite='".$_REQUEST['NumTramtie']."';
				insert into trasacorrespondencia (numtramite, estado, fechahora, usuario, area) values ('".$_REQUEST['NumTramtie']."', 
					'REDIRECCIONADO', NOW(), '".$_SESSION['uscod']."', ".$_SESSION['area'].");");
	$rows=pg_affected_rows($consulta);
	/*if($rows > 0){
		if($_REQUEST['Area'] == null){
			$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where agencia=(select agencia from tblareascorrespondencia where areasid=".$_SESSION['area'].") and correspondencia='t'");
			$row = pg_fetch_array($consulta);
			$inserts.="('".$_REQUEST['NumTramtie']."', null, null, '".(($_SESSION['area'] == $row['areasid'])?'DISTRIBUCION EXTERNA':'RECIBIDO CORRESPONDENCIA')."', ".$row['areasid']."), ";
		}else{
			$consulta=$conect->queryequi("select (case when agencia='".$_REQUEST['Agencia']."' then 'DISTRIBUCION' else 'ENVIADO' end) from tblareascorrespondencia where areasid=".$_SESSION['area']." and correspondencia='t'");
			if($row = pg_fetch_array($consulta)){
				$inserts.="('".$_REQUEST['NumTramtie']."', ".(($row[0] == 'DISTRIBUCION')? "now(), '".$_SESSION['uscod']."', '".$row[0]."'" : "null, null, '".$row[0]."'").", ".$_SESSION['area']."), ";
			}else{
				if($_REQUEST['Agencia'] == $_SESSION['agencia']){
					$inserts.="('".$_REQUEST['NumTramtie']."', now()+'00:01:00', '".$_SESSION['uscod']."', 'DISTRIBUCION', '".$_SESSION['area']."'), ";					
				}else{
					$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where agencia=(select agencia from tblareascorrespondencia where areasid='".$_SESSION['area']."') and correspondencia='t'");
					$row = pg_fetch_array($consulta);
					$inserts.="('".$_REQUEST['NumTramtie']."', null, null, 'RECIBIDO CORRESPONDENCIA', ".$row[0]."), ";
				}
				
				
			}	
			
			if($_REQUEST['Area'] == $_SESSION['area']){
				$inserts.="('".$_REQUEST['NumTramtie']."', null, '".$_REQUEST['Destinatario']."', 'CERRADO', '".$_REQUEST['Area']."'), ";
			}else{
				$inserts.="('".$_REQUEST['NumTramtie']."', null, null, 'RECIBIDO', ".$_REQUEST['Area']."), ";
			}
			
		}
		
		$inserts=substr( $inserts ,0,strlen( $inserts )-2);
		$consulta=$conect->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values $inserts");
	}*/
	$conect->cierracon();
	Trazabilidad($_REQUEST['NumTramtie'], $_SESSION['uscod'], null);
	
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&codebar=".$_REQUEST['NumTramtie']."';</script>";
}

function MasOpciones(){
	$conect=new conexion();
	$consulta=$conect->queryequi("select correspondencia from tblareascorrespondencia where areasid='".$_SESSION['area']."'");
	$row = pg_fetch_array($consulta);
	$conect->cierracon();
	
	if($row['correspondencia']=='t')
		return '<option value="4">Por enviar</option>';

}
?>
