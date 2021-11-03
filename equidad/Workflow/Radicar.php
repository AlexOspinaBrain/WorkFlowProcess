<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=Correspondencia/RadicarCorrespondencia.php'
paar correcta visualización
-->
<?php
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
require_once ('Workflow/DetallesTramite.php');
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
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
        $(document).ready(function() {	
			$("#FiltroCompania").val("<?=$_REQUEST['FiltroCompania']?>");
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
			
			$('#asocia').change(function(){
				if ($('#asocia').val()== 'SI'){
					$('#nasociado').prop('disabled',false);

					$('#nasociado').addClass("validate[required]");
				}
				else{
					$('#nasociado').prop('disabled',true);
					$('#nasociado').val('');
					$('#nasociado').removeClass("validate[required]");
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
			seleccionaagenciausuario('<?=$_SESSION['area']?>'); //selecciona la agencia que recibe igual a la agencia del usuario
		});
				
		function tables(codigo, Producto, Compania, TipoCliente){	
			$("#GridProductos").flexigrid({
				url : 'Workflow/BusquedaTablesXMLOsiris.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'BuscaProductos' } ,
							  { name: 'codigo', value: codigo } ,	  
							  { name: 'Producto', value: Producto },  
							  { name: 'Compania', value: Compania },
							  { name: 'TipoCliente', value: TipoCliente }
							],
				colModel : [ 
							 { display: 'Poliza', name : 'poliza', width : 80, sortable : true, align: 'center', }, 
							 { display : 'Certificado',	name : 'certificado', width : 80, sortable : true, align : 'center'}, 							 
							 { display : 'Aseguradora', name : 'compania', width : 100, sortable : true, align : 'left'}, 
							 { display : 'Estado',	name : 'estado', width : 80, sortable : true, align : 'left'}, 
							 { display : 'Orden',	name : 'orden', width : 20, sortable : true, align : 'left'}, 
							 { display : 'Tipo cer',	name : 'tipocer', width : 20, sortable : true, align : 'left'}, 
							 { display : 'Producto',	name : 'producto', width : 200, sortable : true, align : 'left'},
							 { display : 'Agencia',	name : 'agencia', width : 150, sortable : true, align : 'left'},
							 { display : 'Inicio tecnico',	name : 'InicioTecnico', width : 80, sortable : true, align : 'left'},
							 { display : 'Inicio certificado',	name : 'InicioCertificado', width : 80, sortable : true, align : 'left'},
							 { display : 'Fin tecnico',	name : 'FinTecnico', width : 80, sortable : true, align : 'left'},
							 { display : 'Fin certificado',	name : 'FinCertificado', width : 80, sortable : true, align : 'left'},
							 { display : 'Tomador',	name : 'Tomador', width : 200, sortable : true, align : 'left'},
							 { display : 'Asegurado',	name : 'Asegurado', width : 200, sortable : true, align : 'left'},
							 { display : 'Beneficiario',	name : 'Beneficiario', width : 200, sortable : true, align : 'left'},
							 { display : 'Intermediario',	name : 'Intermediario', width : 200, sortable : true, align : 'left'}							  
						   ],
				buttons : [ { name : 'Radicar sin producto', bclass : 'add', onpress : RadicaProducto }, 
							{ separator : true} 
						  ],
				sortname : "fintecnico",
				sortorder : "desc",				
				title : 'Productos del cliente',
				width :($(window).width()-50)*0.6,
				height :  ($(window).height()-370),
				resizable:false,
				usepager : true,
				showToggleBtn : false,
				rp:50,
				onSuccess:linksProductos
			});
			
			$("#GridServicios").flexigrid({
				url : 'Workflow/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'BuscaServiciosAnteriores' } ,
							  { name: 'codigo', value: $("#IdCliente").text() },
							  { name: 'Producto', value: Producto } 	  							  
							],
				colModel : [ 
							 { display: 'No. tramite', name : 'rad.id_radicacion', width : 80, sortable : true, align: 'center', }, 
							 { display : 'Estado',	name : 'estado', width : 80, sortable : true, align : 'center'}, 
							 { display : 'Servicio', name : 'ser.desc_servicio', width : 100, sortable : true, align : 'left'}, 
							 { display : 'Aseguradora',	name : 'com.des_compania', width : 120, sortable : true, align : 'left'}, 
							 { display : 'Tipologia',	name : 'tip.desc_tipologia', width : 350, sortable : true, align : 'left'}, 
							 { display : 'Fecha real',	name : 'rad.fechareal', width : 100, sortable : true, align : 'center'},
							 { display : 'Fecha sistema',	name : 'rad.fechahora', width : 130, sortable : true, align : 'center'},
							 { display : 'Fecha restante',	name : 'tiemporestante', width : 130, sortable : true, align : 'center'},
							 { display : 'Semaforo',	name : 'semaforo', width : 10, sortable : true, align : 'center'}
							  
						   ],
				sortname : "rad.fechahora",
				sortorder : "desc",				
				title : 'Servicios anteriores',
				width :($(window).width()-50)*0.4,
				height :  ($(window).height()-345),
				resizable:false,
				usepager : true,
				showToggleBtn : false,
				rp:50,
				onSuccess:Semaforo
			});
		}
		
		function MuestraTramite(IdTramite){
			$("#Tramite").val(IdTramite);
			$("#FiltroDocumento").attr("name","FiltroDocumentoHide");
			$("#formID").attr("target","_blank");
			$("#formID").submit();
			$("#Tramite").val('');
			$("#FiltroDocumento").attr("name","FiltroDocumento");
			$("#formID").attr("target","");
		}

		function RadicaProducto(idproducto, compania){
			//alert($(idproducto).parent().parent().parent().html());
			if(idproducto=="Radicar sin producto"){
				$("#DatosPoliza").hide();
				$("#ProductoCliente").val(0);
				$("#Aseguradora").val($("#FiltroCompania").val());
			}else{


				DatosProducto($(idproducto).parent().parent().parent());
				//$("#Producto").parent().parent().hide();
				$("#Producto").attr('disabled','disabled');
				
				
				$("#DatosPoliza").show();
				//$("#ProductoCliente").val(idproducto);
			}
			DatosCliente();
			
			$( "#dialog-Radica" ).attr("title", "Radicacion "+compania);
			$( "#ProductoCompania" ).val(compania);
			
			
			$( "#dialog-Radica" ).dialog({
				autoOpen: true,
				modal: true,
				width: 1100,
				height: ($(window).height()-10),
				buttons: {
					Aceptar: function() {
						if( $("#formRadica").validationEngine('validate') ===true){
							var answer = confirm("Los datos son correctos?")
							if (answer){
								$('#formRadica').submit();
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
			$("#RespuestaReclamo").change(function (){
				if($("#RespuestaReclamo").val()=='2'){
					$("#EmailReclamante").removeClass("validate[custom[email]]");
					$("#EmailReclamante").addClass("validate[required,custom[email]]");
				}else{
					$("#EmailReclamante").removeClass("validate[required,custom[email]]");
					$("#EmailReclamante").removeClass("validate[custom[email]]");
					$("#EmailReclamante").addClass("validate[custom[email]]");					
				}
					
			})
			
			$('body').append('<div title="Alerta" id="dialog-AlertaDescripcion"><p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Es importante que en este campo se diligencie modo, tiempo y lugar de lo ocurrido</p></div>');
			
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

				if ($("#ServicioReclamo").val() != '2') {
					$('#lineaT1').hide();

					$("#AgenciaT").prop("disabled", true);
					$("#ProcesoServicio").prop("disabled", true);
					$("#IdTipologiaReclamo").prop("disabled", true);
					
					
				}else{
					$('#lineaT1').show();
					
					$("#AgenciaT").prop("disabled", false);
					$("#ProcesoServicio").prop("disabled", false);
					$("#IdTipologiaReclamo").prop("disabled", false);
					
				}

			});
		
			
	
			$("#TipoTramiteReclamo").change(function(){//carga los tipos de reclamo segun el servicio
				if($("#TipoTramiteReclamo").val() == '3'){ 
					AgregaCampos("Ente _de_Control");
					
						seleccionaagenciausuario('<?=$_SESSION['area']?>'); //selecciona la agencia que recibe igual a la agencia del usuario
						$('#lineaT1').show();
						$('#lineaT2').show();

							$("#AgenciaT").prop("disabled", false);
							$("#ProcesoServicio").prop("disabled", false);
							$("#IdTipologiaReclamo").prop("disabled", false);
							$("#usuasignado").prop("disabled", false);
					
				}else{
					if($("#TipoTramiteReclamo").val() == '2'){
						AgregaCampos("Ente _de_Control");
					}
					else{
						$("#InputAdicional").html('');
					}

					
					if ($("#ServicioReclamo").val() != '2'){

						$('#lineaT1').hide();
						$('#lineaT2').hide();

						$("#AgenciaT").prop("disabled", true);
						$("#ProcesoServicio").prop("disabled", true);
						$("#IdTipologiaReclamo").prop("disabled", true);
						$("#usuasignado").prop("disabled", true);
					}
				}
			});

			$("#AgenciaT").change(function(){//carga procesos para tipologia
				if($("#AgenciaT").val().length >0){
					$('#ProcesoServicio').html("<option value=''>Espere ...</option>");
					$.ajax({
						type: "POST",
						url: "Workflow/ajax_querys.php",
						data: { op: "buscaProcesoEC", term: $("#AgenciaT").val(), 
							compan: $("#FiltroCompania").val(), servee: $("#ServicioReclamo").val() }
					}).done(function( data ) {	
						var obj =$.parseJSON(data);	
						var opciones="<option value=''></option>";
						for(i=0; i<obj.length; i++)
							opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
						$('#ProcesoServicio').html(opciones);
					});				
				}
			});

			$("#ProcesoServicio").change(function(){//carga tipologias
				if($("#ServicioReclamo").val().length >0 && $("#ProcesoServicio").val().length >0 && $("#AgenciaT").val().length >0 && $("#FiltroCompania").val().length > 0 ){
                                        $('#IdTipologiaReclamo').html("<option value=''>Espere ...</option>");
                                        $.ajax({
                                                type: "POST",
                                                url: "Workflow/ajax_querys.php",
						//data: { op: "BuscaTipologia", term: $("#ServicioReclamo").val()}
                                                data: { op: "BuscaTipologiaR", id_servicio: $("#ServicioReclamo").val(), id_proceso: $("#ProcesoServicio").val(),codigo: $("#AgenciaT").val(),id_compania: $("#FiltroCompania").val(),
                                                	tpreclamo: $("#TipoTramiteReclamo").val()}
                                        }).done(function( data ) {
                                                var obj =$.parseJSON(data);
                                                var opciones="<option value=''></option>";
                                                for(i=0; i<obj.length; i++)
                                                        opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";

                                                $('#IdTipologiaReclamo').html(opciones);
                                        });

				}
				if($("#ProcesoServicio").val()==3  ){ //si es diferente a indemnizacion muestra el usuario
						$('#lineaT2').hide();
						$("#usuasignado").prop("disabled", true);
				}else{
						$('#lineaT2').show();
						$("#usuasignado").prop("disabled", false);
				}
			});

			$("#IdTipologiaReclamo").change(function(){//carga usuarios para generar respuesta / solo QYR entes

                $('#usuasignado').html("<option value=''>Espere ...</option>");
                $.ajax({
                        type: "POST",
                        url: "Workflow/ajax_querys.php",

                        data: { op: "BuscaUsuResp", id_tipologia: $("#IdTipologiaReclamo").val()}
                }).done(function( data ) {
                        var obj =$.parseJSON(data);
                        var opciones="<option value=''></option>";
                        for(i=0; i<obj.length; i++)
                                opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";

                        $('#usuasignado').html(opciones);
                });

			});

		}
		
		function AgregaCampos(op){
			if(op == 'Ente _de_Control'){
				$("#InputAdicional").html('<td><label><span>Numero presignado:</span></label><input type="text" id="PreasignadoReclamo" name="PreasignadoReclamo" class="validate[required] text-input"/></td>'+
						'<td><label><span>Fecha o dias de respuesta:</span></label><label style="display: inline-block;"><input type="text" class="validate[required, custom[date],future[now]] text-input" id="FechaRespuesta" name="FechaRespuesta" style="display: inline-block; width:130px;"/><br>Fecha (aaaa/mm/dd)</label><label style="display: inline-block;"><input type="text" id="DiasRespuesta" name="DiasRespuesta" class="validate[required,custom[integer],min[1] text-input" style="display: inline-block;width:20px;"/><br>Días</label></td>');
						
				$("#FechaRespuesta").keypress(function(){
					$("#DiasRespuesta").val('');
					$("#DiasRespuesta").removeClass("validate[required,custom[integer],min[1]");
					$("#FechaRespuesta").removeClass("validate[required, custom[date],future[now]]");
					$("#FechaRespuesta").addClass("validate[required, custom[date],future[now]]");
				});		
				
				$("#DiasRespuesta").keypress(function(){
					$("#FechaRespuesta").val('');
					$("#FechaRespuesta").removeClass("validate[required, custom[date],future[now]]");
					$("#DiasRespuesta").removeClass("validate[required,custom[integer],min[1]");
					$("#DiasRespuesta").addClass("validate[required,custom[integer],min[1]");
				});	
			}
		}
		
		function DatosCliente(){
			if($("#IdCliente").html() !='Sin datos')
				$("#Identificacion").val($("#IdCliente").html());
				
			if($("#NombreCliente").html()!='Sin datos')
				$("#NombreReclamante").val($("#NombreCliente").html());
			
			if($("#EmailCliente").html()!='Sin datos')
				$("#EmailReclamante").val($("#EmailCliente").html());
			
			if($("#TelefonoCliente").html()!='Sin datos')
				$("#TelefonoReclamante").val($("#TelefonoCliente").html());
			
			if($("#DireccionCliente").html()!='Sin datos')
				$("#DireccionReclamante").val($("#DireccionCliente").html());			
		}
		
		function DatosProducto(producto){
			$("#PPoliza").html(producto.find("td[abbr='poliza']").text());
			$("#PCertificado").html(producto.find("td[abbr='certificado']").text());
			$("#PTipoCertificado").html(producto.find("td[abbr='tipocer']").text());
			$("#PEstado").html(producto.find("td[abbr='estado']").text());
			$("#POrden").html(producto.find("td[abbr='orden']").text());
			$("#PAgencia").html(producto.find("td[abbr='agencia']").text());
			$("#PAseguradora").html(producto.find("td[abbr='compania']").text());
			$("#PProducto").html(producto.find("td[abbr='producto']").text());
			$("#PFechaInicioTec").html(producto.find("td[abbr='InicioTecnico']").text());
			$("#PFEchaFinTec").html(producto.find("td[abbr='FinTecnico']").text());
			$("#PFechaInicioCer").html(producto.find("td[abbr='InicioCertificado']").text());
			$("#PFechaFinCer").html(producto.find("td[abbr='FinCertificado']").text());
			$("#PTomador").html(producto.find("td[abbr='Tomador']").text());
			$("#PAsegurado").html(producto.find("td[abbr='Asegurado']").text());
			$("#PBeneficiario").html(producto.find("td[abbr='Beneficiario']").text());
			$("#PIntermediario").html(producto.find("td[abbr='Intermediario']").text());	
			$("#InPPoliza").val(producto.find("td[abbr='poliza']").text());
			$("#InPCertificado").val(producto.find("td[abbr='certificado']").text());
			$("#InPTipoCertificado").val(producto.find("td[abbr='tipocer']").text());
			$("#InPEstado").val(producto.find("td[abbr='estado']").text());
			$("#InPOrden").val(producto.find("td[abbr='orden']").text());
			$("#InPAgencia").val(producto.find("td[abbr='agencia']").text());
			$("#InPAseguradora").val(producto.find("td[abbr='compania']").text());
			$("#InPProducto").val(producto.find("td[abbr='producto']").text());
			$("#InPFechaInicioTec").val(producto.find("td[abbr='InicioTecnico']").text());
			$("#InPFEchaFinTec").val(producto.find("td[abbr='FinTecnico']").text());
			$("#InPFechaInicioCer").val(producto.find("td[abbr='InicioCertificado']").text());
			$("#InPFechaFinCer").val(producto.find("td[abbr='FinCertificado']").text());
			$("#InPTomador").val(producto.find("td[abbr='Tomador']").text());
			$("#InPAsegurado").val(producto.find("td[abbr='Asegurado']").text());
			$("#InPBeneficiario").val(producto.find("td[abbr='Beneficiario']").text());
			$("#InPIntermediario").val(producto.find("td[abbr='Intermediario']").text());						
		}
		
		function AgregaFile(elemento){
			$(elemento).parent().parent().append('<input type="file" id="FileAdicional" name="FileAdicional[]"/>');
		}

		function linksProductos(){			
			$("#GridProductos tbody tr").each(function (index) {
				if($(this).find("td[abbr='compania']").text() == $("#FiltroCompania option:selected").text())
					$(this).find("td[abbr='poliza'] > div").html("<a href='#' onClick='RadicaProducto(this)'>"+$(this).find("td[abbr='poliza']").text()+"</>");				
			});
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
<table align='center' width="880px"><tr><td>
	<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Documento: </b></legend>		
		<label style="display: inline-block;">		
			<select><option>Nit</option></select><br>
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
			<option>Tomador</option>
			<option>Asegurado</option>
			<option>Beneficiario</option>
		</select>

<br>
	</fieldset>

	<!--<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Nombre / Razón social: </b></legend>		
			<input type="text" id="FiltroNombre"  name="FiltroNombre" style='width:170px'/>
	</fieldset>-->
	
	<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Negocio / Poliza: </b></legend>		
			<input type="text" id="FiltroNegocio"  name="FiltroNegocio"  style='width:170px' value="<?=$_REQUEST['FiltroNegocio']?>" />
	</fieldset>
			

	
	<fieldset style="width:180px; height:50px;" class='filtros'>
		<legend><b>Aseguradora: </b></legend>
		<select id="FiltroCompania" name="FiltroCompania" class="validate[required]">
			<option></option>
		</select><br>
			
	</fieldset>
	<input type="hidden" id="Tramite" name="Tramite"/>
	<fieldset style="width:130px;height:50px;border:0px" class='filtros'>
		<br><button type="submit">Buscar.. </button><br>	
	</fieldset>		
</td></tr></table>
</form>
<br>

<table border ="1" align="center" class="DatosCliente" id="DatosCliente" cellspacing="0" style="display:none">
<tr><th colspan="20">Datos del cliente</th></tr>
<tr>
	<th>Identificación: </th><td id="IdCliente">Sin datos</td>
	<th>Nombre / Razón social: </th><td id="NombreCliente">Sin datos</td>
	<th>Télefono: </th><td id="TelefonoCliente">Sin datos</td>
	<th>Dirección: </th><td id="DireccionCliente">Sin datos</td>
	<th>E-mail: </th><td id="EmailCliente">Sin datos</td>
</tr>
</table>
<br>
<table border ="1" align="center" class="DatosCliente" id="SinDatos" cellspacing="0" style="display:none">
<tr><th colspan="20">Datos del cliente </th></tr>
<tr>
	<th>No hay datos para este cliente </th>
	<td>¿Desea ingresar un nuevo cliente? <b><a href="#" style="padding: 30 0;" onClick="RadicaProducto('Radicar sin producto')">SI</a><a href="#" style="padding-left: 30px;">NO</a></b></td>
</tr>
</table>



<table align="center"><tr><td><table id="GridProductos"></table></td><td><table id="GridServicios"></table></td></tr></table>


<div id="dialog-Radica" style="display:none">
	<div id="InformacionProducto"></div>
	<form id="formRadica" class="formular" style="padding: 10px; position: absolute" action="#" method="post" enctype="multipart/form-data">
		<fieldset style="width:1000px; " class='filtros' id="DatosPoliza">
			<legend><b>Información de la poliza: </b></legend>
			<table style="color:black; text-align:left; width: 100%">
				<tr><th style="width:18%">Poliza: </th><td style="width:25%" id="PPoliza"></td><th style="width:20%">Fecha Inicio tecnico:</th><td id="PFechaInicioTec"></td></tr>
				<tr><th>Certificado: </th><td id="PCertificado"></td><th>Fecha Fin tecnico:</th><td id="PFEchaFinTec"></td></tr>
				<tr><th>Tip. Certificado: </th><td id="PTipoCertificado"></td><th>Fecha Inicio certificado:</th><td id="PFechaInicioCer"></td></tr>
				<tr><th>Estado Poliza:</th><td id="PEstado"></td><th>Fecha fin certificado:</th><td id="PFechaFinCer"></td></tr>
				<tr><th>Orden: </th><td id="POrden"></td><th>Nombre Tomador:</th><td id="PTomador"></td></tr>
				<tr><th>Agencia: </th><td id="PAgencia"></td><th>Nombre Asegurado:</th><td id="PAsegurado"></td></tr>
				<tr><th>Aseguradora:</th><td id="PAseguradora"></td><th>Nombre Beneficiario:</th><td id="PBeneficiario"></td></tr>
				<tr><th>Producto:</th><td id="PProducto"></td><th>Nombre Intermediario:</th><td id="PIntermediario"></td></tr>
			</table>
		</fieldset>

		<input type="hidden" id="InPPoliza" name="InPPoliza"/>
		<input type="hidden" id="InPCertificado" name="InPCertificado"/>
		<input type="hidden" id="InPTipoCertificado" name="InPTipoCertificado"/>
		<input type="hidden" id="InPEstado" name="InPEstado"/>
		<input type="hidden" id="InPOrden" name="InPOrden"/>
		<input type="hidden" id="InPAgencia" name="InPAgencia"/>
		<input type="hidden" id="InPAseguradora" name="InPAseguradora"/>
		<input type="hidden" id="InPProducto" name="InPProducto"/>
		<input type="hidden" id="InPFechaInicioTec" name="InPFechaInicioTec"/>
		<input type="hidden" id="InPFEchaFinTec" name="InPFEchaFinTec"/>
		<input type="hidden" id="InPFechaInicioCer" name="InPFechaInicioCer"/>
		<input type="hidden" id="InPFechaFinCer" name="InPFechaFinCer"/>
		<input type="hidden" id="InPTomador" name="InPTomador"/>
		<input type="hidden" id="InPAsegurado" name="InPAsegurado"/>
		<input type="hidden" id="InPBeneficiario" name="InPBeneficiario"/>
		<input type="hidden" id="InPIntermediario" name="InPIntermediario"/>
		
		<fieldset style="width:1000px; " class='filtros'>
			<legend><b>Datos del reclamante: </b></legend>
			<table>
			<tr><td style="width:400px">
			<label><span>Identificación:</span></label>
			<select id="TipoDoc" name="TipoDoc" class="validate[required] text-input" style="width:60px;display:inline"><option>Nit</option><option>CC</option></select>
			<input type="text" class="validate[required] text-input" id="Identificacion" name="Identificacion" style="width:200px;"/>
			</td>
			
			<td style="width:400px">
			<label><span>Nombre / Razón Social:</span></label>
			<input type="text" class="validate[required] text-input" id="NombreReclamante" name="NombreReclamante" style="width:300px;"/>
			</td>
			
			<td>
			<label><span>E-mail:</span></label>
			<input type="text" class="validate[custom[email]] text-input" id="EmailReclamante" name="EmailReclamante" style="width:300px;"/>
			</td></tr>
			
			<tr><td>
			<label><span>Teléfono:</span></label>
			<input type="text" class="validate[required] text-input" id="TelefonoReclamante" name="TelefonoReclamante" style="width:200px;"/>
			</td>
			
			<td>
			<label><span>Dirección:</span></label>
			<input type="text" class="validate[required] text-input" id="DireccionReclamante" name="DireccionReclamante" style="width:300px;"/>
			</td>
			</TR>
			<TR>
			<td>
			<label><span style="color:#FF0000";>Ciudad Residencia del Quejoso:</span></label>
			<select id="CiudadReclamante" name="CiudadReclamante" class="validate[required] text-input" ><option></option></select>
			</td>

			<td>
			<label ><span style="color:#FF0000";>Ciudad Del Evento que Genera la Queja:</span></label>
			<select id="CiudadEvento" name="CiudadEvento" class="validate[required] text-input"><option></option></select>
			</td>
			</tr>
			
			<tr><td>
			<label><span>Agencia que recibe:</span></label>
			<select id="AgenciaReclamante" name="AgenciaReclamante" class="validate[required] text-input">
				<option value=''></option></select>
			</td>


                    <td>
					<label><span>Canal de recepción:</span></label>
					<select id="RecepcionReclamo" name="RecepcionReclamo" class="validate[required] text-input"><option></option></select>
					</td>	
				
					<td>
					<label><span>Medio de respuesta:</span></label>
					<select id="RespuestaReclamo" name="RespuestaReclamo" class="validate[required] text-input"><option></option></select>
					</td>
			</tr>
			<tr>
                        <td>
                        <label><span>Fecha real de radicación:</span></label>
                        <input type="text" class="validate[required, custom[date],past[now]] text-input" id="FechaRealReclamo" name="FechaRealReclamo" style="width:100px;" value="<?= date("Y-m-d")?>"/>
                        </td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset style="width:1000px; " class='filtros'>
			<legend><b>Información del reclamo: </b></legend>
			<table>

			<tr><td>
			<label><span>Queja Asociada:</span></label>
			<select id="asocia" name="asocia" class="text-input">
			<option value ="NO" selected>NO</option><option value ="SI">SI</option></select>
			</td>
			<td>
			<label><span># Caso Asociado:</span></label>
			<input type="text" id="nasociado" name="nasociado" class="text-input" style="width:170px"  maxlength="20" disabled>
			</td>
			</tr>
			
			<tr>
			<td>
			<label><span>Servicio:</span></label>		
			<select id="ServicioReclamo" name="ServicioReclamo" class="validate[required] text-input"><option value=''></option></select>
			</td>
			<td>
			<label><span>Tipo tramite:</span></label>
			<select id="TipoTramiteReclamo" name="TipoTramiteReclamo" class="validate[required] text-input"><option value=''></option></select>
			</td>
			</tr>

			<tr id="lineaT1" style='display: none'>
                    <td>
                    <label><span>Agencia tipología:</span></label>
                    <select id="AgenciaT" name="AgenciaT" class="validate[required] text-input"><option value=''></option></select>
                    </td>

                    <td>
                    <label><span>Proceso:</span></label>
                    <select id="ProcesoServicio" name="ProcesoServicio" class="validate[required] text-input"><option value=''></option></select>
                    </td>

		
					<td>
					<label><span>Sub-Tipología:</span></label>
					<select id="IdTipologiaReclamo" name="IdTipologiaReclamo" class="validate[required] text-input" style="width:500px;"><option></option></select>
					</td>
			</tr>
			<tr id="lineaT2" style='display: none'>
                        <td>
                        <label><span>Usuario Asignado:</span></label>
						<select id="usuasignado" name="usuasignado" class="validate[required] text-input"><option value=''></option></select>
                        </td>
			</tr>			
            
            <tr>
				<td colspan ="3">
				<label><span>Adjuntos :</span><a href="#" onClick="AgregaFile(this)" style="margin-left:100px;color:#0101DF;">Agregar otro archivo</a></label>
				<input type="file" name="FileAdicional[]"/>
				</td>
			</tr>

			<tr>
			<td colspan ="2">
			<label><span>Producto:</span></label>
			<select id="Producto" name="Producto" class="validate[required] text-input" style="width:500px;"><option></option></select>
			</td>

                        <td>
                        <label><span>Tipo Cliente:</span></label>
                        <select id="Tipocliente" name="Tipocliente" class="validate[required] text-input" style="width:500px;"><option></option></select>
                        </td>


			</tr>
			<tr><td colspan="3">
				<label><span>Descripción:</span></label>
				<textarea class="validate[required] text-input" id="Descripcion" name="Descripcion" style="width:900px; height:50px"></textarea>
			</td></tr>

			<tr id="InputAdicional"></tr>
			</table>
		</fieldset>
		<input type="hidden" id="ProductoCompania" name="ProductoCompania"/>
		<input type="hidden" id="ProductoCliente" name="ProductoCliente"/>
		<input type="hidden" id="Aseguradora" name="Aseguradora"/>
		<select id="PasoProximo" name="PasoProximo" style="display:none"></select>
		<input type="reset" id="reset" style="display:none"/>
	</form>
</div>



<?= BuscaCliente() ?>
<?= GuardaRadicacion(GuardaProducto()) ?>

<?= MuestraDetalles($_REQUEST['Tramite'], $_SESSION['uscod'], $_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"], 'Radicar'); ?>
<?= OpcionesSelect('FiltroCompania', 'wf_compania com', 'com.id_compania', 'com.des_compania', " where com.id_compania!=0 ")?>
<?= OpcionesSelect('AgenciaReclamante', 'tblradofi', 'codigo', 'descrip', " where codigo!='0' and codigo!='094' and codigo!='999'")?>

<?= OpcionesSelect('AgenciaT', 'tblradofi of inner join wf_tipologia tp on of.codigo = tp.id_agencia', 'distinct of.codigo', 'of.descrip', " where of.codigo!='0' and of.codigo!='094' and of.codigo!='999' and tp.eliminado_tipologia = false")?>

<?= OpcionesSelect('CiudadReclamante', 'tblciudades', 'idciudad', 'ciudad', "")?>
<?= OpcionesSelect('CiudadEvento', 'tblciudades', 'idciudad', 'ciudad', "")?>
<?= OpcionesSelect('ServicioReclamo', 'wf_servicio', 'id_servicio', 'desc_servicio', "")?>
<?= OpcionesSelect('RecepcionReclamo', 'wf_recepcion', 'id_recepcion', 'desc_recepcion', "")?>
<?= OpcionesSelect('RespuestaReclamo', 'wf_respuesta', 'id_respuesta', 'desc_respuesta', "")?>
<?= OpcionesSelect('Producto', 'wf_producto', 'id_producto', 'descripcion', " where id_producto between 1 and 9")?>
<?= OpcionesSelect('Tipocliente', 'wf_tipocliente', 'id_tipocliente', 'descripcion', "")?>

<?= OpcionesSelect('ProcesoServicio', 'wf_proceso pr inner join wf_tipologia tp on pr.id_proceso = tp.id_proceso inner join wf_compania cmp on tp.id_compania = cmp.id_compania', 'distinct pr.id_proceso', 'pr.proceso_desc', "where proceso_eliminado = false and tp.eliminado_tipologia = false  ")?>


</body>
</html>
<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Extra){
	$salida="";
	$result=queryQR("select $Id, $Value from $Tabla $Extra order by $Value");
	while ($row = $result->FetchRow()){
		$salida.='<script>$("#'.$IdSelect.'").append("<option value=\"'.$row[0].'\">'.$row[1].'</option>");</script>';
	}
	return $salida;
}


function GuardaProducto(){
	if(!empty($_POST['Producto']))
		return $_POST['Producto'];

	if(!isset($_POST['InPPoliza']))
		return 0;

	preg_match('#\((.*?)\)#', $_POST['InPTomador'], $match);
	$nittomador= trim($match[1]);	
	preg_match('#\)(.*?)\z#', $_POST['InPTomador'], $match);
	$nombretomador= trim($match[1]);	
	preg_match('#\((.*?)\)#', $_POST['InPAsegurado'], $match);
	$nitasegurado= trim($match[1]);	
	preg_match('#\)(.*?)\z#', $_POST['InPAsegurado'], $match);
	$nombreasegurado= trim($match[1]);	
	preg_match('#\((.*?)\)#', $_POST['InPBeneficiario'], $match);
	$nitbeneficiario= trim($match[1]);	
	preg_match('#\)(.*?)\z#', $_POST['InPBeneficiario'], $match);
	$nombrebeneficiario= trim($match[1]);	
	preg_match('#\((.*?)\)#', $_POST['InPIntermediario'], $match);
	$nitintermediario= trim($match[1]);	
	preg_match('#\)(.*?)\z#', $_POST['InPIntermediario'], $match);
	$nombreintermediario= trim($match[1]);	

	queryQR("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
			 nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, iniciocertificado, fincertificado) 
			 values ('".$_POST['InPPoliza']."', '".$_POST['InPFechaInicioTec']."', '".$_POST['InPFEchaFinTec']."', '".$_POST['InPProducto']."', '".$_POST['InPAgencia']."', '".$nittomador."',
			 '".$nombretomador."', '".$nitasegurado."', '".$nombreasegurado."', '".$nitbeneficiario."', '".$nombrebeneficiario."', 
			 '".$nitintermediario."', '".$nombreintermediario."', '".$_POST['InPAseguradora']."', '".$_POST['InPTipoCertificado']."', '".$_POST['InPEstado']."', 
			  '".$_POST['InPFechaInicioCer']."', '".$_POST['InPFechaFinCer']."')");

	$consulta = queryQR("select max(id_producto) as id_producto from wf_producto where poliza = '".$_POST['InPPoliza']."'");
	$row = $consulta->FetchRow();
	return $row['id_producto'];
}

function BuscaCliente(){
	if( $_POST['FiltroDocumento'] == NULL && $_POST['FiltroNombre'] == NULL && $_POST['FiltroNegocio'] == NULL)
		return;
	
	if($_POST['FiltroDocumento'] != NULL )
		$result = queryOci("select * from OSIRIS.S03500 where CODIGO like '%".$_POST['FiltroDocumento']."%' order by FECMOD DESC");
	
	if($row = $result->FetchRow()){
		$Identificacion=((strlen(trim($row['NIT'])) > 0)?$row['NIT']:$row['CODIGO']);
		$Identificacion=intval($Identificacion);
		$salida.="<script>$('#NombreCliente').html('".$row['NOMBRE']."')</script>";
		$salida.="<script>$('#IdCliente').html('".$Identificacion."')</script>";

		$salida.="<script>tables('".$row['CODIGO']."', '".($_REQUEST['FiltroNegocio'])."', '".$_REQUEST['FiltroCompania']."', '".$_REQUEST['FiltroTipoCliente']."');</script>";

		$result = queryOci("select * from OSIRIS.S03502 b where B.CODIGO = '".$row['CODIGO']."'");
		while ($row = $result->FetchRow()){
			if($row['CODDET'] == '620' && strlen(trim($row['VALSTRING'])) > 0)
				$salida.="<script>$('#DireccionCliente').html('".$row['VALSTRING']."')</script>";
					
			if($row['CODDET'] == '700' && strlen(trim($row['VALSTRING'])) > 0)
				$salida.="<script>$('#TelefonoCliente').html('".$row['VALSTRING']."')</script>";
					
			if($row['CODDET'] == '00000014' && strlen(trim($row['VALSTRING'])) > 0)
				$salida.="<script>$('#EmailCliente').html('".$row['VALSTRING']."')</script>";
		}
		$salida.="<script>$('#DatosCliente').show()</script>";
		
	}else{
		$salida.="<script>$('#SinDatos').show()</script>";
	}
	//-----
	/*$result = queryQR("select * from wf_persona where identificacion='".$_POST['FiltroDocumento']."'");
	if($row = $result->FetchRow()){
		$salida.="<script>$('#NombreCliente').html('".$row['nombres']."')</script>";
		$salida.="<script>$('#IdCliente').html('".$row['identificacion']."')</script>";
		if(strlen(trim($row['direccion'])) > 0)
			$salida.="<script>$('#DireccionCliente').html('".$row['direccion']."')</script>";
		if(strlen(trim($row['telefono'])) > 0)
			$salida.="<script>$('#TelefonoCliente').html('".$row['telefono']."')</script>";
		if(strlen(trim($row['correo'])) > 0)
			$salida.="<script>$('#EmailCliente').html('".$row['correo']."')</script>";
		$salida.="<script>$('#DatosCliente').show()</script>";
		$salida.="<script>tables('".$row['identificacion']."', '".($_REQUEST['FiltroNegocio'])."', '".$_REQUEST['FiltroCompañia']."');</script>";
	}else{
		$salida.="<script>$('#SinDatos').show()</script>";
	}*/

	//var_dump($result->FetchRow());
	//exit;
	return $salida;	 
}


function GuardaRadicacion($idProducto){
	if( $_POST['TipoTramiteReclamo'] == NULL || !isset($idProducto))
		return;

	$Observaciones=str_replace(array("'", "\""), '`',$_POST['Descripcion']);
	$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);

	$compania = (!empty($_POST['InPAseguradora'])) ? ($_POST['InPAseguradora'] == 'Seguros Generales')?'1':'2' : $_POST['Aseguradora'];

        if ($_POST['ServicioReclamo']=='3'){
			$compania=4;
        }

	if ($_POST['ServicioReclamo']!='2' && $_POST['TipoTramiteReclamo'] != 3){
		
		
		//obtiene tipologia 'sin definir' para el proceso de clasificacion posterior
		$result = queryQR("select * from wf_tipologia where id_servicio=".$_POST['ServicioReclamo']." and id_compania=".$compania." and id_proceso=0 and id_agencia='0'");
		$row = $result->FetchRow();
		$Id_Tipologia = $row['id_tipologia'];

	}
	else{
		$Id_Tipologia = $_POST['IdTipologiaReclamo'];
	}


	$TiempoDias = TiempoDias($_POST['FechaRespuesta'], $_POST['DiasRespuesta'], $_POST['TipoTramiteReclamo']);	
	$TiempoLimite = TiempoLimiteTramite($_POST['FechaRespuesta'], $_POST['DiasRespuesta'], 
		$_POST['FechaRealReclamo'], $_POST['TipoTramiteReclamo']);//Obtiene tiempo limite del tramite

		$statusw = "En tramite";
		if ($_POST['ServicioReclamo']=='4')
			$statusw = "Cerrado";


		if($_REQUEST['nasociado'] != NULL )
			$casociad = $_POST['nasociado'];
		else
			$casociad = '';
	

        $result = queryQR("insert into wf_radicacion (id_ciudad, id_ciu_queja, id_agencia, id_tipologia, id_tipotramite, id_producto, id_recepcion, id_respuesta, descripcion, tipo_doc, numero_doc, fechareal,
                                 preasignado, nombre, email, telefono, direccion, estado, fechahora, fechahora_limite, tiempo_tramite, id_tipocliente, casociado) 
        						values ('".$_POST['CiudadReclamante']."','".$_POST['CiudadEvento']."', '".$_POST['AgenciaReclamante']."', 
                                '".$Id_Tipologia."',
                                '".$_POST['TipoTramiteReclamo']."',     '".$idProducto."', '".$_POST['RecepcionReclamo']."', '".$_POST['RespuestaReclamo']."', '".str_replace(array("'", "\""), '',$Observaciones)."',
                                '".$_POST['TipoDoc']."', '".$_POST['Identificacion']."', '".$_POST['FechaRealReclamo']."', '".$_POST['PreasignadoReclamo']."', '".$_POST['NombreReclamante']."',
                                '".$_POST['EmailReclamante']."', '".$_POST['TelefonoReclamante']."', '".$_POST['DireccionReclamante']."', '$statusw', now(), '$TiempoLimite', '$TiempoDias', '".$_POST['Tipocliente']."','$casociad') ");


	if($result == true){	
		//selecciona el ultimo id ingresado
		$result = queryQR("select id_radicacion, id_tipotramite from wf_radicacion where numero_doc='".$_POST['Identificacion']."' and id_producto='".$idProducto."' order by id_radicacion desc limit 1");
		$row = $result->FetchRow();
		
		//selecciona la primer actividad de la tipologia
		$result2 = queryQR("select * from wf_tipologia tip, wf_workflow wor, wf_actividad act where act.id_actividad=wor.id_actividad and
				tip.id_tipologia=wor.id_tipologia and wor.inicio_workflow is true 
				and tip.id_tipologia=".$Id_Tipologia);
		$row2 = $result2->FetchRow();		
		
		//inserta la primer actividad de la tipologia
		$result = queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora, fechahora_limite, observacion, id_workflow, tiempo_actividad) values (".$row['id_radicacion'].", '".$row2['desc_actividad']."',
			 '".$_SESSION['uscod']."', now(), now(), '".str_replace(array("'", "\""), '',$Observaciones)."' , ".$row2['id_workflow'].", '0')");

		
		//si es felicitacion cierra el tramite
		if ($_POST['ServicioReclamo']=='4'){

				$result = queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora,
					fechahora_limite, id_workflow, tiempo_actividad) values (".$row['id_radicacion']."
					, 'Cierre tramite', '".$_SESSION['uscod']."', now(), now(), 
					".$row2['id_workflow'].", '0')");
		}else{
			$PasoProximo=ProximaActividad($row['id_radicacion']);// obtiene la segunda actividad para asignar

			//selecciona la actividad siguiente 
			$result2 = queryQR("select * from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
					act.id_actividad=wor.id_actividad and wor.id_workflow='".$PasoProximo."' and tie.id_tipotramite='".$row['id_tipotramite']."'");
			$row2 = $result2->FetchRow();

			//si se selecciono usuario asignacion al radicar
			if (!$_POST['usuasignado']){
				//selecciona el usuario para la siguienete actividad, haciendo balanceo de cargas
				$UsuarioRegla=ReglasUsuarioActividad($PasoProximo, $row['id_radicacion']);
				
				if( $UsuarioRegla!= null){
					$Usuario=$UsuarioRegla;
				}else{
					$result3 = queryQR("select *, (select count(*) from wf_historial where usuario_cod=usu.usuario_cod and fechahora is null) as tramites  from wf_workflow wor, wf_workflowusuarios usu,
							adm_usuario adm where adm.usuario_cod=usu.usuario_cod and wor.id_workflow=usu.id_workflow and adm.usuario_bloqueado=false and 
							wor.id_workflow='".$PasoProximo."' order by tramites asc limit 1");
					$row3 = $result3->FetchRow();
					$Usuario =$row3['usuario_cod'];
				}
			}else{
				$Usuario=$_POST['usuasignado'];
			}
			//inserta la segunda actividad de la tipologia
			
			$TiempoHoras = TiempoHoras($row['id_radicacion'], $row2['id_actividad']);
			$Limite = CalculaTiempoLimite($row['id_radicacion'], $TiempoHoras);
			
			$result = queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) values (".$row['id_radicacion'].", '".$row2['desc_actividad']."',
				 '".$Usuario."', '$Limite', ".$row2['id_workflow'].", '$TiempoHoras')");



			/*//inserta la radicacion en correspondencia.
			if($_POST['ServicioReclamo']=='1'){
				
				//echo "<script>alert('VA?? ". $_POST['TipoTramiteReclamo']."');</script>";

				$resultcr = queryQR("select usuario_cod from adm_usuario usu inner join adm_usumenu prm 
					using (usuario_cod)
					where usu.usuario_bloqueado = false and prm.jerarquia_opcion = '4.1.7'");
				
				$rowcr = $resultcr->FetchRow();

				//if($_POST['TipoTramiteReclamo']==1)
					$tiposervicioo="Queja o Reclamo Normal";
				//else
					//$tiposervicioo="Derecho de Petición";

				$tt=GuardaRadicacionCorrespondencia($row['id_radicacion'], 
					"Tramite radicado automáticamente del workflow Quejas y Reclamos", $tiposervicioo, 
					$_POST['CiudadReclamante'], $_POST['NombreReclamante'], $_POST['TelefonoReclamante'], 
					$_POST['DireccionReclamante'], $_REQUEST['FiltroCompania'],$rowcr['usuario_cod']);

				if($tt){
					queryQR("update wf_radicacion set correspondencia = '$tt'
						where id_radicacion = " . $row['id_radicacion']);
				}
			}*/
		}
		GuardaAdjuntos($row['id_radicacion'], 'FileAdicional', "Adicional");

		$fp = fopen("/tmp/LogRadicacion.txt","a");
		fwrite($fp, "[ ".date("d-m-Y H:i:s")." ] | Tramite :".$row['id_radicacion']." | Aseguradora: ".(($compania == 1)?"Seguros Generales":"Seguros de Vida")." \n");
		fclose($fp);

		return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$row['id_radicacion']."';</script>";
		//MuestraDetalles($row['id_radicacion'], $_SESSION['uscod'], $_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"], 'Radicar');
		
		
	}else{
		return "<br><br>Error en la radicación.";
	}
}

function ProximaActividad($IdRadicacion){
	$result=queryQR("select id_flujo from wf_historial his, wf_workflow wor, wf_flujo flu where flu.id_workflow=wor.id_workflow and
		wor.id_workflow=his.id_workflow and his.id_radicacion='".$IdRadicacion."' order by id_flujo limit 1");
	
	if ($row = $result->FetchRow()){
		$result2=queryQR("select * from wf_workflow wor, wf_actividad act where wor.id_actividad=act.id_actividad and wor.id_workflow='".$row['id_flujo']."'");
		$row2 = $result2->FetchRow();
		return $row2['id_workflow'];
	}
}

function TiempoDias($Fecha, $Dias, $TipoReclamo){
	if($Fecha !=NULL || $Dias !=NULL){//Obtiene la fechalimite
		if($Fecha !=NULL){
			$dias=0;
			$result = queryQR("select EXTRACT(day FROM (to_date('".$Fecha."', 'YYYY/MM/DD') - now())) as fechahora");
			$row = $result->FetchRow();
			return $row['fechahora'];
		}else{
			return $Dias;
		}
	}else{
		$result = queryQR("select * from wf_tipotramite where id_tipotramite=".$TipoReclamo);
		$row = $result->FetchRow();

		return $row['tiempo_tipotramite'];
	}
}


?>
