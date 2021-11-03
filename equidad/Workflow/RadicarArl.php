<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Workflow/DetallesTramite.php');

$companias = OpcionesSelect('wf_compania com', 'com.id_compania', 'com.des_compania', "where com.id_compania!=0");
$agencias = OpcionesSelect('tblradofi', 'codigo', 'descrip', " where codigo!='0' and codigo!='094' and codigo!='999'");
$ciudad = OpcionesSelect('tblciudades', 'idciudad', 'ciudad', "");
$servicio = OpcionesSelect('wf_servicio', 'id_servicio', 'desc_servicio', "");
$recepcion = OpcionesSelect('wf_recepcion', 'id_recepcion', 'desc_recepcion', "");
$reclamo = OpcionesSelect('wf_respuesta', 'id_respuesta', 'desc_respuesta', "");
$producto = OpcionesSelect('wf_producto', 'id_producto', 'descripcion', " where id_producto between 1 and 210");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
   <script type="text/javascript" src="js/jquery.min.js"></script>	
	<script type="text/javascript" src="js/jquery.jqprint-0.3.js"></script>	<!-- Imprime areas configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.mouse.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.draggable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.droppable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.resizable.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.dialog.js"></script><!-- Dialog configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script type="text/javascript" src="js/flexigrid.pack.js"></script><!--Tablas-->
	<script type="text/javascript" src="js/FunctionsWorkflow.js"></script><!--Tablas-->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	<script src="js/codigobarras.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script type="text/javascript" src="js/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/tinymce/tiny_mce.js"></script>
	<script src="js/jquery.editinplace.js" type="text/javascript"></script><!--Config mascaras inputs-->
	<script src="js/handlebars.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" type="text/css" href="css/flexigrid.pack.css" /><!--Tablas-->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- Dialog configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- Dialog configuration -->

	<script type="text/javascript">
        $(document).ready(function(){	
			$("#FiltroCompañia").val("<?=$_REQUEST['FiltroCompañia']?>");
			$("#FiltroTipoCliente").val("<?=$_REQUEST['FiltroTipoCliente']?>");
			$("#FiltroNegocio").val("<?=$_REQUEST['FiltroNegocio']?>");
			$("#FiltroDocumento").val("<?=$_REQUEST['FiltroDocumento']?>");
			CargaTipologias();
			$( "button" ).button();	
			$( "#codigobarras" ).dialog({
				open:function() {
					$("#GenerarNuevo").hide();
				}
			});
				
			$('#Descripcion').focus(function(){
				$( "#dialog-AlertaDescripcion" ).dialog({
					autoOpen: true,
					modal: true,
					width: 300,
					height: 150,
					buttons: {
						Aceptar: function() {
							$("#dialog-AlertaDescripcion").dialog( "close" );
							$( "#dialog-AlertaDescripcion" ).remove();
							$('#Descripcion').focus();
						},				
					},
					close:function() {
						$( "#dialog-AlertaDescripcion" ).remove();
						$('#Descripcion').focus();
					},		
				});
			})
			
			if("<?=$_REQUEST['Tramite']?>" != ""){
				$("#formRadica").remove();
				$("#formID").hide();
				$("#Linlvolver").text("Volver a pantalla de radicación");	
				$("#Linlvolver").attr("href", "#");	
				$("#FiltroDocumento").attr("name","FiltroDocumento");
				$("#FiltroDocumento").val("<?=$_REQUEST['FiltroDocumentoHide']?>");
				$("#tramite").val("");
				$("#Linlvolver, #ButtonVolver").click(function(){$("#formID").submit();});	
			}
			$("#TipoSiniestro").change(function(){
				if($("#TipoSiniestro").val() == "SS"){
					$("#siniestro").hide();
					$("#labsin").hide();
				}else{
					$("#siniestro").show();
					$("#labsin").show();
				}


        	});

        	$("#RecepcionReclamo").change(function(){
				if($("#RecepcionReclamo").val() == "5" || $("#RecepcionReclamo").val() == "4"){
					$("#RadCorrespondencia").show();
					$("#labrad").show();
					
					if($("#RecepcionReclamo").val() == "5")
						$("#RadCorrespondencia").removeClass("validate[required] text-input");  
					else
						$("#RadCorrespondencia").addClass("validate[required] text-input");  
					
				}else{
					$("#RadCorrespondencia").hide();
					$("#labrad").hide();
				}

        	});

		});
		function BuscaCliente(){
			if(($("#tipoIde").val() !== null) && ($("#FiltroDocumento").val() !== null)){
				$.ajax({	          	
		 			type: 'POST',
		 			dataType: "json",
		 			url: 'Workflow/buscaIdentificacion.php', 
		 			data: { 
		 				tipo_doc: $("#tipoIde").val(), 
						documento: $("#FiltroDocumento").val()	 				
		 			},
		 			success: function(data){
		 				//alert(data.nombre);
		 				if(data.rta == 'base'){
		 					$("#TipoDoc").val(data.tipoDoc);
		 					$("#Identificacion").val(data.numDoc);
		 					$("#NombreReclamante").val(data.nombre);
		 					$("#DireccionReclamante").val(data.direccion);
		 					$("#CiudadReclamante").val(data.ciudad);
		 					
		 				}
		 				if(data.rta == 'cifin'){
		 					$("#TipoDoc").val(data.tipoDoc);
		 					$("#Identificacion").val(data.numDoc);
		 					$("#NombreReclamante").val(data.nombre);

		 				}
		 				if(data.rta == 'ninguna'){
		 					$("#TipoDoc").prop("disabled", false);
		 					$("#Identificacion").removeAttr("readonly");
		 					$("#NombreReclamante").removeAttr("readonly");
		 				}else{
		 					$("#TipoDoc").prop("disabled", "disabled");
		 					$("#Identificacion").attr("readonly","readonly");
		 					$("#NombreReclamante").attr("readonly","readonly");
		 				}

		 				$("#dialog-Radica").dialog({
							autoOpen: true,
							modal: true,
							width: 1100,
							height: ($(window).height()-10),
							buttons: {
								Aceptar: function() {
									if( $("#formRadica").validationEngine('validate') ===true){
										var answer = confirm("Los datos son correctos?")
										if (answer){
											//$('#formRadica').submit();
											guardaRadicacion();
										}
									}
								},				
								Cancelar: function() {
									$(this).dialog( "close" );					
								}
							},
							close:function(){
								$("#reset").click();
								$("#dialog-Radica").dialog( "destroy" );
								$('#formRadica').validationEngine('hide');
								$('input[type=file]').remove();	
								$( "#formRadica>input" ).val('');						
							}
						});	
				    }
		 		});
			}						
		}

		function guardaRadicacion(){

			var m_data = new FormData();  

		

            m_data.append( 'tipo_doc', $('select[name=TipoDoc]').val());
            m_data.append( 'id', $('input[name=Identificacion]').val());
            m_data.append( 'tipo_cli', $('select[name=FiltroTipoCliente]').val());
            m_data.append( 'nombre', $('input[name=NombreReclamante]').val());
            m_data.append( 'correo', $('input[name=EmailReclamante]').val());
            m_data.append( 'telefono', $('input[name=TelefonoReclamante]').val());
            m_data.append( 'direccion', $('input[name=DireccionReclamante]').val());
            m_data.append( 'ciudad', $('select[name=CiudadReclamante]').val());
            m_data.append( 'agencia', $('select[name=AgenciaReclamante]').val());
            m_data.append( 'empresa', $('input[name=Empresa]').val());
            m_data.append( 'nit', $('input[name=IdEmpresa]').val());
            m_data.append( 'radicado', $('input[name=RadCorrespondencia]').val());
            m_data.append( 'tipo_sin', $('select[name=TipoSiniestro]').val());
            m_data.append( 'siniestro', $('input[name=siniestro]').val());
            m_data.append( 'fecha', $('input[name=FechaRealReclamo]').val());
            m_data.append( 'recepcion', $('select[name=RecepcionReclamo]').val());
            m_data.append( 'desc', $('textarea[name=Descripcion]').val());
            m_data.append( 'files', $('input[name=upload]')[0].files[0]);
            
                         
            //instead of $.post() we are using $.ajax()
            //that's because $.ajax() has more options and flexibly.
            $.ajax({
				url: 'Workflow/Modelo/guardaPeticion.php',
				data: m_data,
				processData: false,
				contentType: false,
				type: 'POST',
				dataType:'json',
				success: function(response){
					location.href='<?=$_SERVER["SCRIPT_NAME"]."?p=4-1-3"?>'+'&Tramite='+response;
				}
            });    
		}

		function CargaTipologias(Campo, Id){
			$("#ServicioReclamo").change(function(){//carga los tipos de reclamo segun el servicio
				if($("#ServicioReclamo").val().length >0){
					$('#TipoTramiteReclamo').html("<option value=''>Espere ...</option>");
					$.ajax({
						type: "POST",
						url: "Workflow/ajax_querys.php",
						data: { op: "buscaTipoTramite", term: $("#ServicioReclamo").val()}
					}).done(function( data ) {	
						var obj =$.parseJSON(data);	
						var opciones="<option value=''></option>";
						for(i=0; i<obj.length; i++)
							opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
						$('#TipoTramiteReclamo').html(opciones);
					});				
				}else
					$('#TipoTramiteReclamo').html("<option value=''></option>");
			});
			
			$("#TipoTramiteReclamo").change(function(){//carga los tipos de reclamo segun el servicio
				if($("#TipoTramiteReclamo").val() == '3' || $("#TipoTramiteReclamo").val() == '2')
					AgregaCampos("Ente _de_Control");
				else
					$("#InputAdicional").html('');
			});
		}
		
		function AgregaFile(elemento){
			$(elemento).parent().parent().append('<input type="file" id="FileAdicional" name="FileAdicional[]"/>');
		}

    </script>
	
	<style>			
		.filtros{
			color:#08298A;	
			display:inline-block;
			vertical-align:top;
		}
		input[type=text]{
		margin:0px 2px;
			display:inline;			
		}
		.DatosCliente{
			font-size: 12px;
			font-family: Verdana;
			border-color: #BDBDBD;
		}
		.DatosCliente td, th {
			padding: 3px;
		}
		
		.DatosCliente th{
			background-color: #F2F2F2;
		}
		table a{
			color: #0101DF;
			font-weight: bold;
			text-decoration:none;
		}
		table a:hover{
			color: #819FF7;
		}
		.TableRadicado th{
			text-align:left;
			padding-right: 10px ;
			color:#08088A;
		}
		.TableRadicado b{			
			padding: 3px 15px;
			color:#424242;
		}
		.Activo{ color: #00891B;}
		
		.Vencido{color: #BA0000;}
	</style>
</head>
<body>
<form action="#" id="formID" method="post" >
	<center>
	<h3>RADICACI&Oacute;N DERECHO DE PETICI&Oacute;N ARL</h3>
	<br>
	<table align='center'>
		<tr>
			<td>
				<fieldset style="width:180px; height:50px;" class='filtros'>
					<legend><b>Documento: </b></legend>		
					<label style="display: inline-block;">		
					<select id="tipoIde" class="validate[required]">
						<option></option>
						<option value="NIT">NIT</option>
						<option value="C.C.">CC</option>
					</select><br>
					Tipo
					</label>
					<label style="display: inline-block;">		
						<input type="text" id="FiltroDocumento"  name="FiltroDocumento"  class="validate[required]" style='width:120px' value="<?=$_REQUEST['FiltroDocumento']?>"/><br>
						Numero
					</label>
				</fieldset>

				<fieldset style="width:100px; height:50px;" class='filtros'>
					<legend><b>Tipo cliente: </b></legend>
					<select id="FiltroTipoCliente" name="FiltroTipoCliente" class="validate[required]">
						<option></option>
						<option value="Afiliado">Afiliado</option>
						<option value="Aportante">Aportante</option>
						<option value="Otro">Otros</option>
					</select><br>
						
				</fieldset>

				<input type="hidden" id="Tramite" name="Tramite"/>
			</td>
		</tr>
		<tr>
			<td align="center">
				<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
					<br><button type="button" onclick="BuscaCliente()">Buscar.. </button><br>	
				</fieldset>	
			</td>
		</tr>
	</table>
	</center>
</form>
<br>

<div id="dialog-Radica" style="display:none">
	<div id="InformacionProducto"></div>
	
	<form id="formRadica" class="formular" style="padding: 10px; position: absolute" action="#" method="post" enctype="multipart/form-data">
		
		<fieldset style="width:1000px; " class='filtros'>
			<legend><b>Datos del reclamante: </b></legend>
			<table>
				<tr>
					<td style="width:400px">
						<label><span>Identificaci&oacute;n:</span></label>
						<select id="TipoDoc" name="TipoDoc" class="validate[required] text-input" style="width:60px;display:inline">
							<option></option>
							<option value="NIT">NIT</option>
							<option value="C.C.">CC</option>
						</select>
						<input type="text" class="validate[required] text-input" id="Identificacion" name="Identificacion" style="width:200px;"/>
					</td>
					<td style="width:400px">
						<label><span>Nombre / Raz&oacute;n Social:</span></label>
						<input type="text" class="validate[required] text-input" id="NombreReclamante" name="NombreReclamante" style="width:300px;"/>
					</td>
					<td>
						<label><span>E-mail:</span></label>
						<input type="text" class="validate[custom[email]] text-input" id="EmailReclamante" name="EmailReclamante" style="width:300px;"/>
					</td>
				</tr>
				<tr>
					<td>
						<label><span>Tel&eacute;fono:</span></label>
						<input type="text" class="validate[required] text-input" id="TelefonoReclamante" name="TelefonoReclamante" style="width:200px;"/>
					</td>
					<td>
						<label><span>Direcci&oacute;n:</span></label>
						<input type="text" class="validate[required] text-input" id="DireccionReclamante" name="DireccionReclamante" style="width:300px;"/>
					</td>
					<td>
						<label><span>Ciudad:</span></label>
						<select id="CiudadReclamante" name="CiudadReclamante" class="validate[required] text-input"><option></option><?=$ciudad?></select>
					</td>
				</tr>
				<tr>
					<td>
						<label><span>Agencia que recibe:</span></label>
						<select id="AgenciaReclamante" name="AgenciaReclamante" class="validate[required] text-input"><option></option><?=$agencias?></select>
					</td>
				</tr>
			</table>
		</fieldset>
		<br>
		<fieldset style="width:1000px; " class='filtros'>
			<legend><b>Informaci&oacute;n del reclamo: </b></legend>
			<table>
				<tr>
					<td>
						<label><span>Empresa:</span></label>
						<input type="text" class="validate[required] text-input" id="Empresa" name="Empresa" style="width:300px;"/>
					</td>
					<td>
						<label><span>No. Identificaci&oacute;n:</span></label>
						<input type="text" class="validate[required] text-input" id="IdEmpresa" name="IdEmpresa" style="width:200px;"/>
					</td>
					<td>
						<label><span>Fecha real de radicaci&oacute;n:</span></label>
						<input type="text" class="validate[required, custom[date],past[now]] text-input" id="FechaRealReclamo" name="FechaRealReclamo" style="width:100px;" value="<?= date("Y-m-d")?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<label><span>Tipo siniestro:</span></label>
						<select id="TipoSiniestro" name="TipoSiniestro" class="validate[required] text-input">
							<option value=''></option>
							<option value='AT'>Accidente de Trabajo</option>
							<option value='EL'>Enfermedad Laboral</option>
							<option value='SS'>Sin Siniestro</option>
						</select>
					</td>
					<td>
						<label><span>Canal de recepci&oacute;n:</span></label>
						<select id="RecepcionReclamo" name="RecepcionReclamo" class="validate[required] text-input"><option></option><?=$recepcion?></select>
					</td>
					<td>
						<label id="labrad"><span>Radicado Correspondencia:</span></label>
						<input type="text" class="validate[required] text-input" id="RadCorrespondencia" name="RadCorrespondencia" style="width:200px;"/>
					</td>
				</tr>	
				<tr>			
					<td>
						<label id="labsin"><span>Numero siniestro:</span></label>
						<input type="text" class="validate[required] text-input" id="siniestro" name="siniestro" style="width:200px;"/>
					</td>
					<td colspan="2">
						<label><span>Descripci&oacute;n:</span></label>
						<textarea class="validate[required] text-input" id="Descripcion" name="Descripcion" style="width:600px; height:50px"></textarea>
					</td>	
				</tr>
				<tr>
					<td rowspan="2">
						<label><span>Adjuntos: </span></label>
						<input name="upload" type="file"/>
					</td>
				</tr>
		
				<tr id="InputAdicional"></tr>
			</table>
		</fieldset>
		
		<input type="reset" id="reset" style="display:none"/>
	</form>
</div>


</body>
</html>
<?php
function OpcionesSelect($Tabla, $Id, $Value, $Extra){
	$salida="";
	$result=queryQR("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = $result->FetchRow()){
		$salida.="<option value='".$row[0]."'>".$row[1]."</option>";
	}
	return $salida;
}

?>
