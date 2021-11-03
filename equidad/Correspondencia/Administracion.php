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
				autoOpen: false,
				width:480,
				height: ($(window).height()-30),
				modal: true
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
				if(ty=='FACTURAS'){
					dividentificacion="<label><span>NIT/CC : "+
					"<input type='text' id='Identificacion' name='Identificacion' class='validate[required]' style='text-transform:lowercase; display:inline;' />"+
					"</label>";
				}else{
						dividentificacion="";
				}

				$('#prioridad').html(divprioritario);
				$('#identifica').html(dividentificacion);
				$('#NumAdj').keypress(function(e){
					if(e.which == 13){
						CreaInputfile();
						return false;
					}
				});			
			});

			$('#TipoDoc2').change(function() {
			 var ty = $(this).find('option:selected').text();
				if(ty=='FACTURAS'){
					dividentificacion="<label><span>NIT/CC : "+
					"<input type='text' id='Identificacion' name='Identificacion' class='validate[required]' style='text-transform:lowercase; display:inline;' />"+
					"</label>";
				}else{
					dividentificacion="";
				}

				$('#identificae').html(dividentificacion);
			});
			
			$( "#Externo" ).click(function (){
				if($(this).is(':checked')) {  
					$( "#procedencia" ).css("display", "block");
					CargaCiudades('Ciudad');					
				}else{
					$( "#procedencia" ).css("display", "none");
					$('#Ciudad').html("<option value='1'></option>");
				}
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
					setTimeout ('$("#GuardarNuevo").attr("disabled", "disabled")', 2)
				}
			})
			
			tables();
			MuestraCodeBar('<?=$_REQUEST['codebar']?>', '<?=$_SESSION['uscod']?>');
		});
		
		
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
				title : 'Radicador correspondencia',
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
<form class="formular" action="#" id="formID" method="post">

</form>
<?php /*>
	<form class="formular" action="#" id="formID" method="post">
	<fieldset style="width:400px">
	
		
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

        <div id="identifica"></div>

		<label>	<span>Origen externo:</span></label>
		<input type="checkbox" id="Externo" name="Externo"/>		
		
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
			<input type="text" name="DestinatarioExt" class="validate[required]" style="text-transform:uppercase; width:400px"/>
			
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
			<button type="submit" name="GuardarNuevo" id="GuardarNuevo" value="Guardar">Guardar</button>
		</div>	
		</fieldset>
	</form>
</td></tr></table></div>
<br>
 */ ?>
</body>
</html>