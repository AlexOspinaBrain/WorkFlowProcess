<script src="js/codigobarras.js" type="text/javascript"></script>
<?php
require_once ('Correspondencia/Trazabilidad.php');

?>
<script>
	$(document).ready(function(){	
		$('#Tip').change(function(){
			MuestraUsuarios($(this).val());
		});
		
		//al dar click en el campo seguimiento se habilita la fecha de alerta
		$('#seguimiento').change(function(){
			if ($('#seguimiento').is(":checked"))
				$("#fechaseguimiento").addClass("validate[required]");
			else
				$("#fechaseguimiento").removeClass("validate[required]");
		});

	});

	function validanum(event){
		if (event.charCode>=48 && event.charCode<=57){
			return true;
		}
		return false;
	}

	function MuestraUsuarios(id_tipologia){
		$('#Usu').html("<option>Espere... </option>");
		$.ajax({
			type: "POST",
			url: "Workflow/Modelo/cargaUsuarios.php",
			data: {
				id: id_tipologia
			}
		}).done(function(data){
			$('#Usu').html(data);
		});
	}

</script>

<?

function MuestraDetalles($Id, $Usuario, $Url, $Procedente){

	if($Id == null)
		return;
	
	if($Procedente == 'Radicar'){
		$boton='<button id="ButtonVolver" onClick="location.href=\''.$Url.'\';">Volver</button>';
		$link='<a id="Linlvolver" href="#" onClick="location.href=\''.$Url.'\';" style="font-size:12px">Volver</a>';
	}	
	
	if($Procedente == 'Seguimiento'){
		$boton='<button onClick="location.href=\''.$Url.'\';">Cancelar</button>';
		$link='<a href="#" onClick="location.href=\''.$Url.'\';" style="font-size:12px">Volver a seguimiento de tramites</a>';
	}
	
	if($Procedente == 'ConsultaTramites'){
		$boton='<button onClick="location.href=\''.$Url.'\';">Volver</button>';
		$link='<a href="#" onClick="location.href=\''.$Url.'\';" style="font-size:12px">Volver a la consulta de tramites</a>';
	}

	$salida='

	<table align="center" id="PostRadicado" style="display:none">
	<tr><td>
		'.$link.'<button id="BotonImpirmir"><img src="images/print2.png" width="15px"/> Imprimir</button>
		<fieldset>
			<legend><b style="font-size: 16px;">Datos del tramite: </b></legend>		
			<table style="font-size: 12px;" class="TableRadicado">
				<tr><th>Numero de radicado:<b id="PostNoRadicado" style="color: #B40404;font-size: 15px;">Sin datos</b></th><th><b id="OpcionesTramite"></b></th></tr>
				
				<tr><th>Estado tramite:<b id="PostEstado">Sin datos</b></th><th style="display:none">Fecha estado:<b id="PostFechaEstado">Sin datos</b></th><th style="display:none">Respuesta a favor de:<b id="PostRespuestaFavor">Sin datos</b></th></tr>
				
				<tr><th colspan="3" style="display:none">Causal estado:<b id="PostCausalEstado">Sin datos</b></th></tr>
				
				<tr><th>Fecha sistema:<b id="PostFechaSistema">Sin datos</b></th><th>Fecha real de radicación:<b id="PostFechaReal">Sin datos</b></th><th>Fecha limite:<b id="PostFechaLimite">Sin datos</b></th></tr>
				<tr><th colspan="3" style="display:none">Tipo tramite re-apertura:<b id="PostMarcaed">Sin datos</b></th></tr>
				<tr><th> Aseguradora:<b id="PostCompania">Sin datos</b></th><th> Proceso:<b id="PostProceso">Sin datos</b></th><th> Tipo tramite:<b id="PostTipoTramite">Sin datos</b></th></tr>
				<tr><th> Servicio:<b id="PostServicio">Sin datos</b></th><th>Canal de recepcion:<b id="PostRecepcion">Sin datos</b></th><th>Medio de respuesta:<b id="PostRespuesta">Sin datos</b></th></tr>
				<tr id="tr1"><th> No. de radicado (recibido):<b id="PostCartaRec">Sin datos</b></th><th>No. Siniestro:<b id="PostSiniestro">Sin datos</b></th><th>Tipo de siniestro:<b id="PostTiposin">Sin datos</b></th></tr>
				<tr id="tr2"><th colspan="2">No. de radicado (carta de envío):<b id="PostCartaEnv">Sin datos</b></th><th>No. de la guia:<b id="PostGuia">Sin datos</b></th></tr>				
				<tr id="tr3"><th colspan="3">Empresa:<b id="PostEmpresa">Sin datos</b></th></tr>
				<tr><th>Agencia que tramita:<b id="PostAgenciaTramita">Sin datos</b></th><th style="display:none">Numero de preasignado:<b id="PostPreasignado">Sin datos</b></th></tr>

				<tr><th colspan="3">Tipología:<b id="PostTTipologia">Sin datos</b></th></tr>
				<tr><th colspan="3">Sub-Tipología:<b id="PostTipologia">Sin datos</b></th></tr>
				<tr><th colspan="3" style="display:none">Tipología de cierre:<b id="Posttprespuesta">Sin datos</b>
					</th></tr>
				<tr align="center"><th colspan="3"  style="color: #B40404;display:none">Esta queja ya fue respondida o está siendo tramitada en el radicado numero :<b id="Postquejaasociada" style="color: #B40404">Sin datos</b>
					</th></tr>
				<tr><th colspan="3">Producto:<b id="PostProducto">Sin datos</b></th></tr>
				<tr><th colspan="3" style="width:300px">Descripción:<b id="PostDescripcion">Sin datos</b></th></tr>
				
			</table>
		</fieldset>
		<br>
		<fieldset>
			<legend><b style="font-size: 16px;">Datos reclamante: </legend>		
			<table style="font-size: 12px;" class="TableRadicado">
				<tr><th>Identificación:<b id="PostidentificacionCliente">Sin datos</b></th><th> Nombre:<b id="PostNombreCliente">Sin datos</b></th><th> E-mail:<b id="PostEmailCliente">Sin datos</b></th></tr>
				<tr><th>Teléfono:<b id="PostTelefonoCliente">Sin datos</b></th><th> Dirección:<b id="PostDireccionCliente">Sin datos</b></th><th> Ciudad:<b id="PostCiudadCliente">Sin datos</b></th></tr>
				<tr><th>Departamento:<b id="PostDepartamentoCliente">Sin datos</b></th><th>Agencia que recibió:<b id="PostAgenciaCliente">Sin datos</b></th></tr>
			</table>
		</fieldset>
		<br>
		<fieldset id="DatosProducto">
			<legend><b style="font-size: 16px;">Datos Producto: </legend>		
			<table style="font-size: 12px;" class="TableRadicado">
				<tr><th>Poliza:<b id="PostPoliza">Sin datos</b></th><th> Nombre producto:<b id="tPostProducto">Sin datos</b></th><th> Agencia:<b id="PostAgencia">Sin datos</b></th></tr>
				<tr><th>Estado:<b id="PostEstadoPoliza">Sin datos</b></th><th> Inicio Poliza:<b id="PostFechaInicio">Sin datos</b></th><th> Vencimiento poliza:<b id="PostFechaFin">Sin datos</b></th></tr>
				<tr><th>Aseguradora:<b id="PostCompaniaP">Sin datos</b></th><th> Ciudad:<b id="PostCiudad">Sin datos</b></th><th> Departamento:<b id="PostDepartamento">Sin datos</b></th></tr>
				<tr><th colspan="3">Nombre del tomador:<b id="PostTomador">Sin datos</b></th></tr>
				<tr><th colspan="3">Nombre del asegurado:<b id="PostAsegurado">Sin datos</b></th></tr>
				<tr><th colspan="3">Nombre del beneficiario:<b id="PostBeneficiario">Sin datos</b></th></tr>
				<tr><th colspan="3">Nombre del intermediario:<b id="PostIntermediario">Sin datos</b></th></tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend><b style="font-size: 16px;">Linea de proceso: </legend>		
			<table style="font-size: 12px;" class="TableRadicado" border="1" align="center" id="Historial" cellspacing="0">
				<tr><th>Actividad</th><th>Usuario</th><th>Fecha real de terminación</th><th>Fecha limite</th><th>Observacion</th><th class=\'OpcionesActividad\'>C - R - D</th></tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend><b style="font-size: 16px;">Documentos adjuntos: </legend>		
			<table style="font-size: 12px;" class="TableRadicado" border="1" align="center" cellspacing="0">
				<tr><th>Documentos soporte</th><th>Documentos adicionales</th><th>Documentos de respuesta</th></tr>
				<tr style="display:none"><td id="DocRequeridos" style="vertical-align: top;"></td><td id="DocAdicionales" style="vertical-align: top;"></td><td id="DocRespuesta" style="vertical-align: top;"></td></tr>
				<tr><td colspan="3" style="text-align:center"><a href="#" onClick="AgregarAdjuntos()">Agregar nuevo adjunto</a></td></tr>
			</table>
		</fieldset>
		
		<div id="ViewEncuesta"></div>
		<div id="ViewRespuesta"></div>		
		
		<fieldset>
			<legend><b style="font-size: 16px;">Comentarios: </legend>		
			<div id="TodosComentarios" style="font-size: 12px;"></div>
			<form action="#" method="post">
				<textarea class="text-input" id="Comentarios" name="Comentarios" style="width:85%; height:30px; resize:none;font-family: verdana"></textarea>
				<button type="submit" style="font-size:12px; vertical-align:top">Enviar</button>
			</form>
		</fieldset>
		
		<fieldset style="border: 0;text-align: right;font-size: 11px;">
			'.$boton.'
		</fieldset>
	</td></tr>
	</table>';

	
	$salida.='<div id="dialog-Continua" title="Continuar tramite" style="display:none;">
		<form id="formContinua" class="formular" style="padding: 5px; position:relative;" action="#" method="post" enctype="multipart/form-data">

		<div id="FieldTipologia" style="display:none"></div>
				
		<div id="FieldActividadProxima" style="display:none">
			<label><span>Actividad proxima:</span></label>
		</div>
		
		<div id="EncuestaSatisfaccion"></div>

		<div id="EnviaResuesta" style="display:none">
			<label><span>Enviar respuesta:</span></label><br>
			<input type="checkbox" name="EnviaRespuesta" style="display: inline;"/> Enviar respuesta por medio de <div id="MedioRespuesta" style="display: inline;"></div><a class="link" href="#" style="margin-left: 50px;" onClick="CambiarMedioResp('.$Id.')">Cambiar medio</a><br><br>
		</div>

		<div id="GeneraRespuesta" style="display:none">
			<button onClick="BotonRedactaRespuesta()">Redactar respuesta</button>
		</div>
		
		<div id="VerRespuesta" style="display:none">
			<button onClick="BotonVerRespuesta()">Ver respuesta</button>
		</div>
		
		<div id="FavorRespuesta" style="display:none">

			<label><span>Resuesta a favor del consumidor de forma:</span></label><br>

				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="total">Total
				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="parcial" style="margin-left: 30px;">Parcial
				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="neutra" style="margin-left: 30px;">Neutra
				<br><br>

			
		</div>
		<div id="seguimienton" style="display:none">

			<label><span>Desea marcar para seguimiento:</span></label><br>
			<input type="checkbox" name="seguimiento" id="seguimiento" style="display: inline;"/> Seguimiento - Fecha de Alerta
			<input type="date" class="validate[future[now]] text-input" id="fechaseguimiento" name="fechaseguimiento" style="width:200px;">
		<br><br>
		</div>		

		<div id="AdjuntosRespuesta" style="display:none">
			<label><span>Adjuntos Respuesta:<button style="margin-left:230px" type="button" id="AgregaAdjunto" title="Agregar otro adjunto" onClick="AgregaInputFile(this, \'FileRespuesta\')"> + </button></span>
			<input type="file" name="FileRespuesta[]"></label>
		</div>

		<div id="DIVProcesores" style="display:none">
			<label><span>Proceso:</span></label>
			<select id="Procesores" name="Procesores" class="validate[required] text-input">
			<option value =""></option></select>
		</div>
		

		<div id="TipoRespuesta" style="display:none">
			<label><span>Tipología Respuesta:</span></label>
			<select id="TipRespuesta" name="TipRespuesta" class="validate[required] text-input">
			<option></option></select>
		</div>
		

		<div id="FieldObservaciones" style="display:none">
			<label><span>Observaciones:</span></label>	
		</div>
		
		<input type="hidden" id="Dheight" value="0"/>
		<input type="hidden" name="accion" value="continua"/>
		<input type="reset" id="reset" style="display:none"/>
		</form>
		</div>';

	$salida.='
	<div id="dialog-Reasigna" title="Reasigna tramite" style="display:none;">
		<form id="formReasigna" class="formular" style="padding: 5px; position:relative;" action="#" method="post">
			
			<label><span>Usuario a asignar:</span></label>
			<select id="UsuarioReasignar" name="UsuarioReasignar" class="validate[required] text-input"><option></option></select>
		
			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="ObservacionesReasigna" name="ObservacionesReasigna" style="width:450px; height:50px"></textarea>
			<input type="hidden" id="Tramite" name="Tramite" value="'.$_REQUEST['Tramite'].'"/>
		</form>
	</div>

	<div id="dialog-Devuelve" title="Devolución tramite" style="display:none;">
		<form id="formDevuelve" class="formular" style="padding: 5px; position:relative;" action="#" method="post">
			
			<label><span>Actividad a devolver:</span></label>
			<select id="ActividadDevolucion" name="ActividadDevolucion" class="validate[required] text-input"><option></option></select>
			
			<label><span>Causal de devolción:</span></label>
			<select id="CausalDevolucion" name="CausalDevolucion" class="validate[required] text-input"><option></option></select>
		
			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="Observaciones" name="Observaciones" style="width:450px; height:50px"></textarea>
		</form>
	</div>

	<div id="dialog-CerrarTramite" title="Cerrar tramite" style="display:none;">
		<form id="formCerrarTramite" class="formular" style="padding: 5px; position:relative;" action="#" method="post">		
			<br>
			<label><span>Resuesta a favor del consumidor de forma:</span></label><br>

				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="total">Total
				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="parcial" style="margin-left: 30px;">Parcial
				<input class="validate[required] radio" type="radio" name="FavorRespuesta" value="neutra" style="margin-left: 30px;">Neutra
				<br><br>
			
			<label><span>Causal de cierre:</span></label>
			<select id="CausalCierre" name="CausalCierre" class="validate[required] text-input"><option></option></select>
			
			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="ObservacionesCierre" name="ObservacionesCierre" style="width:400px; height:50px"></textarea>
			
		</form>
	</div>

	<div id="dialog-SuspenderTramite" title="Suspender tramite" style="display:none;">
		<form id="formSuspenderTramite" class="formular" style="padding: 5px; position:relative;" action="#" method="post">		
			
			<label><span>Causal de cierre:</span></label>
			<select id="CausalSuspension" name="CausalSuspension" class="validate[required] text-input"><option></option></select>
			
			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="ObservacionesSuspension" name="ObservacionesSuspension" style="width:400px; height:50px"></textarea>
			
		</form>
	</div>

	<div id="dialog-AnularTramite" title="Anular tramite" style="display:none;">
		<form id="formAnularTramite" class="formular" style="padding: 5px; position:relative;" action="#" method="post">		
			
			<label><span>Causal de anulacion:</span></label>
			<select id="CausalAnulacion" name="CausalAnulacion" class="validate[required] text-input"><option></option></select>
			
			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="ObservacionesAnulacion" name="ObservacionesAnulacion" style="width:400px; height:50px"></textarea>
			
		</form>
	</div>

	<div id="dialog-ReabrirTramite" title="Re-abrir tramite" style="display:none;">
		<form id="formReabrirTramite" class="formular" style="padding: 5px; position:relative;" action="#" method="post">		
			
			<div id="DivActividadReabrir" style="display:none">
				<label><span>Actividad a continuar tramite:</span></label>
				<select id="ActividadReabrir" name="ActividadReabrir" class="validate[required] text-input"><option></option></select>
			</div>
			
			<label><span>Usuario:</span></label>
			<select id="UsuarioReabrir" name="UsuarioReabrir" class="validate[required] text-input"><option></option></select>

			<label><span>Tipo tramite:</span></label>
			<select id="marcaed" name="marcaed" class="validate[required] text-input"><option></option></select

			<label><span>Observaciones:</span></label>
			<textarea class="validate[required] text-input" id="ObservacionesReabrir" name="ObservacionesReabrir" style="width:400px; height:50px"></textarea>
			<input type="hidden" id="EstadoReabrir" name="EstadoReabrir"/>
		</form>
	</div>

	<div id="dialog-CambiaMedio" title="Cambiar medio de respuesta" style="display:none;">
		<form id="formCambiarMedio" class="formular" style="padding: 5px; position:relative;" action="#" method="post">		
			
			<label><span>Medio de respuesta:</span></label>
			<select id="MediosRespuesta" name="MediosRespuesta" class="validate[required] text-input"><option></option></select>
			
			<label><span id="TipoDatoEnvio"></span></label>
			<input type="text" id="DatoMedio" name="DatoMedio" class="validate[required] text-input" style="width:350px">
		</form>
	</div>';
	
	//echo $_POST['TipRespuesta'] . " - " . $_REQUEST['Tramite'] . " - " .$_REQUEST['accion'] . " - " .$_REQUEST['accion'] ;


	//if ($_REQUEST['Tramite']==25880){
	//	var_dump($_REQUEST);
		//die;
	//}

	echo $salida;
	echo MuestraDatosDetalles($Id, $Usuario);	
	echo Guardareasigna();
	echo GuardaContinua();
	
	echo GuardaDevolucion();
	echo GuardaCerrar();
	echo GuardaAnular();
	echo GuardaSuspender();
	echo GuardaReAbrir();
	echo GuardaComentario();
	echo GuardaCambiaMedioRespuesta();
	echo GuardaAdjuntos($_REQUEST['Tramite'], 'FileAdicional', 'Adicional');
	

	return;
}




function GuardaCambiaMedioRespuesta(){
	if($_REQUEST['MediosRespuesta'] != null && $_REQUEST['DatoMedio'] != null){
		if($_REQUEST['MediosRespuesta'] == 1)
			$Campo="direccion";
			
		if($_REQUEST['MediosRespuesta'] == 2)
			$Campo="email";
		
		if($_REQUEST['MediosRespuesta'] == 3)
			$Campo="telefono";
	
		$result = queryQR("update wf_radicacion set id_respuesta='".$_REQUEST['MediosRespuesta']."', ".$Campo."='".$_REQUEST['DatoMedio']."'  where id_radicacion='".$_REQUEST['Tramite']."'");
		return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
	}
	
}

function GuardaComentario(){
	if($_REQUEST['Comentarios'] != null){
		$Comentarios=str_replace(array("'", "\""), '`',$_REQUEST['Comentarios']);
		$Comentarios=str_replace(array("\r\n", "\r", "\n"), '<br>',$Comentarios);
		
		$result = queryQR("insert into wf_comentario (desc_comentario, fechahora_comentario, usuario_cod, id_radicacion) values('$Comentarios', now(), '".$_SESSION['uscod']."', '".$_REQUEST['Tramite']."')");
		return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
	}
}

function GuardaAdjuntos($Id_Radicacion, $Adjuntos, $TipoAdj){
	if($_FILES[$Adjuntos]['name'] != null){
		if($_SERVER['SERVER_NAME']=='imagine.laequidadseguros.coop')
			$PathAdjuntos="/vol2";
		else
			$PathAdjuntos="/vol2";

		if(!file_exists ( $PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos' )){	
			if (!file_exists($PathAdjuntos.'/'.date("Ymd"))) {
				mkdir( $PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos', 0775, true);
				chmod( $PathAdjuntos.'/'.date("Ymd"), 0775);
				chmod( $PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos', 0775);
			}else{
				mkdir( $PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos', 0775, true);
				chmod( $PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos', 0775);
			}
		}
		
		$PathAdjuntos=$PathAdjuntos.'/'.date("Ymd").'/Quejas&Reclamos/';
		$CantDocs = queryQR("select * from wf_adjuntos where id_radicacion=".$Id_Radicacion);
		$CantDocs = $CantDocs->RecordCount();
		for ($i=0; $i<count($_FILES[$Adjuntos]['name']); $i++){
			$NombreArchivo=$PathAdjuntos . $Id_Radicacion. "-" . ($i+$CantDocs) . strtolower(substr ($_FILES[$Adjuntos]['name'][$i], strrpos($_FILES[$Adjuntos]['name'][$i], ".")));
			if(move_uploaded_file($_FILES[$Adjuntos]['tmp_name'][$i], $NombreArchivo))
				$values.= "('".$_FILES[$Adjuntos]['name'][$i]."', '".realpath($NombreArchivo)."', '$TipoAdj', '$Id_Radicacion'), ";						
			else
				$errores.=$_FILES[$Adjuntos]['name'][$i];
		}
	
		if(strlen( $values )>0){
			$values=substr( $values ,0,strlen( $values )-2);
			$result = queryQR("insert into wf_adjuntos (desc_adjunto, ruta_adjunto, tipo_adjunto, id_radicacion) values ".$values);
		}
		if($errores != null)
			echo "<script>alert('No se han podido subir los siguientes archivos: $errores')</script>";
			
		return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
	}
}

function MuestraDatosDetalles($Id, $Usuario){
	if( $Id == NULL)
		return;

	$salida ="";
	
	$opcionesTramite=PermisosTramite($Id, $Usuario);

	$result = queryQR("select *,rad.correspondencia, rad.descripcion as descripciontramite , rad.estado as estadotramite, ciu.ciudad as ciudadcliente,  depto.desc_departamento as deptocliente, (case when now() BETWEEN iniciotecnico AND fintecnico then '<z class=\"Activo\">Vigente</z>' else '<z class=\"Vencido\">Cancelada</z>' end) 
				as estadopoliza, to_char(fechahora,'yyyy-MM-dd HH:MI:SS AM') as fechasistema, to_char(fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as fechacierre, 
				to_char(fechahora_estado,'yyyy-MM-dd HH:MI:SS AM') as fechaestado, (select descrip from tblradofi where codigo=tip.id_agencia) as agenciatramita

				from wf_radicacion rad, wf_tipotramite tra, wf_servicio ser, wf_tipologia tip, wf_proceso pro, wf_compania com, wf_recepcion rec, wf_respuesta res, 
				tblciudades ciu, tbldepartamentos depto, tblradofi ofi, wf_producto produ 

				where rad.id_radicacion='".$Id."' and tra.id_tipotramite=rad.id_tipotramite and ser.id_servicio=tra.id_servicio and 
				tip.id_tipologia=rad.id_tipologia and pro.id_proceso=tip.id_proceso and com.id_compania=tip.id_compania and rec.id_recepcion=rad.id_recepcion and res.id_respuesta=rad.id_respuesta 
				and ciu.idciudad=rad.id_ciudad and ofi.codigo=rad.id_agencia and produ.id_producto=rad.id_producto and ciu.id_departamento=depto.id_departamento");


	$compa = queryQR("SELECT t.id_compania, h.id_historial, r.id_tipologia, ht.actividad, r.casociado, 
			t.id_tipotramite
		FROM wf_radicacion AS r 
		INNER JOIN wf_tipologia AS t ON t.id_tipologia = r.id_tipologia 
		INNER JOIN (SELECT MAX(id_historial) AS id_historial, id_radicacion FROM wf_historial 
		GROUP BY id_radicacion) AS h ON h.id_radicacion = r.id_radicacion
		INNER JOIN wf_historial AS ht ON ht.id_historial = h.id_historial
		WHERE r.id_radicacion = '".$Id."'");

	$inicio = $compa->FetchRow();
	
	if($inicio['id_compania'] == "4"){
		$qryInfo = queryQR("SELECT * FROM wf_radarl AS r WHERE r.id_radicacion = '".$Id."'");
		$infoExt = $qryInfo->FetchRow();
	}
				
	if($row = $result->FetchRow()){
		$salida.="<script>$('#OpcionesTramite').html('".$opcionesTramite."')</script>";
		$salida.="<script>$('#PostNoRadicado').html('".$Id."')</script>";
		$salida.="<script>$('#PostServicio').html('".$row['desc_servicio']."')</script>";
		$salida.="<script>$('#PostTipoTramite').html('".$row['desc_tipotramite']."')</script>";
		$salida.="<script>$('#PostFechaSistema').html('".$row['fechasistema']."')</script>";
		$salida.="<script>$('#PostTTipologia').html('".$row['desc_tipologiaalterna']."')</script>";
		$salida.="<script>$('#PostTipologia').html('".$row['desc_tipologia']."')</script>";
		$salida.="<script>$('#PostProceso').html('".$row['proceso_desc']."')</script>";
		$salida.="<script>$('#PostCompania').html('".$row['des_compania']."')</script>";
		$salida.="<script>$('#PostRecepcion').html('".$row['desc_recepcion']."')</script>";
		$salida.="<script>$('#PostRespuesta').html('".$row['desc_respuesta']."')</script>";
		$salida.="<script>$('#PostDescripcion').html('".$row['descripciontramite']."')</script>";

		$qrytpres = queryQR("SELECT respuesta, proceso_desc FROM wf_historial as h,wf_tiporespuesta AS r, wf_proceso as pr
			WHERE h.id_radicacion = '$Id' and h.actividad = 'Generar respuesta' and h.cod_respuesta=r.cod_respuesta
			AND r.id_proceso = pr.id_proceso ORDER BY h.id_historial desc limit 1");
		$infotpres = $qrytpres->FetchRow();

		if($infotpres['respuesta']!=null){
			$salida.="<script>$('#Posttprespuesta').html('".$infotpres['proceso_desc'] . " - " . $infotpres['respuesta']."')</script>";
			$salida.="<script>$('#Posttprespuesta').parent().show()</script>";
		}
		if($row['casociado']!=null){
			$salida.="<script>$('#Postquejaasociada').html('".$row['casociado']."')</script>";			
			$salida.="<script>$('#Postquejaasociada').parent().show()</script>";

		}

		/*if($row['correspondencia']!=null){
			$codebar="<img title=\'Código Barras\' src=\'images/barcode.png\' onClick=\'MuestraCodeBar(".$row['correspondencia'].")\' style=\'padding-left: 20px;\' width=\'20px\'/>";

			$salida.="<script>$('#PostCorresp').html('".$codebar." - ".$row['correspondencia']."')</script>";
		//HTML
		<tr><th colspan="3" style="width:300px">Correspondencia:<b id="PostCorresp">Sin datos</b></th></tr>

		}*/
	
		$salida.="<script>$('#PostEstado').html('".$row['estadotramite']."')</script>";
		$salida.="<script>$('#PostFechaReal').html('".$row['fechareal']."')</script>";
		$salida.="<script>$('#PostFechaLimite').html('".$row['fechacierre']."')</script>";
		$salida.="<script>$('#PostAgenciaTramita').html('".$row['agenciatramita']."')</script>";

		if($row['preasignado'] != null){
			$salida.="<script>$('#PostPreasignado').html('".$row['preasignado']."')</script>";
			$salida.="<script>$('#PostPreasignado').parent().show()</script>";
		}	
		
		if($row['fechahora_estado'] != null){
			$salida.="<script>$('#PostFechaEstado').html('".$row['fechaestado']."')</script>";
			$salida.="<script>$('#PostFechaEstado').parent().show()</script>";
		}
		
		if($row['respuesta_favor'] != null){
			$salida.="<script>$('#PostRespuestaFavor').html('".$row['respuesta_favor']."')</script>";
			$salida.="<script>$('#PostRespuestaFavor').parent().show()</script>";
		}

		if($row['marced'] != null){
				$rsmarced = queryQR("select * from wf_tipotramite where id_tipotramite = ".$row['marced']);
				$marcedP = $rsmarced->FetchRow();
			$salida.="<script>$('#PostMarcaed').html('".$marcedP['desc_tipotramite']."')</script>";
			$salida.="<script>$('#PostMarcaed').parent().show()</script>";
		}
		
		if($row['causalestado'] != null){
			$salida.="<script>$('#PostCausalEstado').html('".$row['causalestado']."')</script>";
			$salida.="<script>$('#PostCausalEstado').parent().show()</script>";
		}

		//-----------------Nuevo flujo D.P. ARL--------------
/*
		if($inicio['id_compania'] == "4"){
			//$salida.="<script>$('#CambiaProceso').hide()</script>";
			$salida.="<script>$('#CambiaProceso').removeClass('validate[required] text-input')</script>";
			$salida.="<script>$('#CambiaProceso').addClass('text-input')</script>";
			$salida.="<script>$('#CambiaProceso').attr('disabled', 'disabled')</script>";
			$salida.="<script>$('#CambiaCompania').attr('disabled', 'disabled')</script>";

			$salida.="<script>$('#PostEmpresa').html('"."( Nit ".$infoExt['nit']." ) ".$infoExt['empresa']."')</script>";
			$salida.="<script>$('#PostSiniestro').html('".$infoExt['numero_siniestro']."')</script>";

			if ($infoExt['tipo_siniestro'] == "AT")
				$tiposin = "ACCIDENTE DE TRABAJO";
			else
				$tiposin = "ENFERMEDAD PROFESIONAL";

			$salida.="<script>$('#PostTiposin').html('".$tiposin."')</script>";
			$salida.="<script>$('#PostCartaRec').html('".$infoExt['radicado_recibido']."')</script>";
			$salida.="<script>$('#PostCartaEnv').html('".$infoExt['radicado_envio']."')</script>";
			$salida.="<script>$('#PostGuia').html('".$infoExt['num_guia']."')</script>";

			$salida.="<script>$('#tr1').show()</script>";
			$salida.="<script>$('#tr2').show()</script>";
			$salida.="<script>$('#tr3').show()</script>";
			$salida.="<script>$('#FavorRespuesta').hide()</script>";

		}else{
			$salida.="<script>$('#tr1').hide()</script>";
			$salida.="<script>$('#tr2').hide()</script>";
			$salida.="<script>$('#tr3').hide()</script>";
		}
*/		
		//-------------------------------------------------------

                        $salida.="<script>$('#tr1').hide()</script>";
                        $salida.="<script>$('#tr2').hide()</script>";
                        $salida.="<script>$('#tr3').hide()</script>";


		$salida.="<script>$('#PostidentificacionCliente').html('".$row['tipo_doc']." ".$row['numero_doc']."')</script>";
		$salida.="<script>$('#PostNombreCliente').html('".$row['nombre']."')</script>";
		$salida.="<script>$('#PostCiudadCliente').html('".$row['ciudadcliente']."')</script>";
		$salida.="<script>$('#PostDepartamentoCliente').html('".$row['deptocliente']."')</script>";
		$salida.="<script>$('#PostAgenciaCliente').html('".$row['descrip']."')</script>";
		
		if($row['email'] != null)
			$salida.="<script>$('#PostEmailCliente').html('".$row['email']."')</script>";

		if($row['telefono'] != null)
			$salida.="<script>$('#PostTelefonoCliente').html('".$row['telefono']."')</script>";
			
		if($row['direccion'] != null)
			$salida.="<script>$('#PostDireccionCliente').html('".$row['direccion']."')</script>";

			
		$salida.="<script>$('#PostProducto').html('".$row['descripcion']."')</script>";
		$salida.="<script>$('#tPostProducto').html('".$row['descripcion']."')</script>";
		if($row['id_producto'] >= 0 && $row['id_producto'] < 210){
			$salida.="<script>$('#DatosProducto').hide()</script>";
		}else{	
			$salida.="<script>$('#PostPoliza').html('".$row['poliza']."')</script>";
			$salida.="<script>$('#PostAgencia').html('".$row['radicada']."')</script>";
			$salida.="<script>$('#PostFechaInicio').html('".$row['iniciotecnico']."')</script>";
			$salida.="<script>$('#PostFechaFin').html('".$row['fintecnico']."')</script>";
			$salida.="<script>$('#PostEstadoPoliza').html('".$row['estadopoliza']."')</script>";
			$salida.="<script>$('#PostTomador').html('"."( Nit ".$row['nittomador']." ) ".$row['nombretomador']."')</script>";
			$salida.="<script>$('#PostAsegurado').html('( Nit ".$row['nitasegurado']." ) ".$row['nombreasegurado']."')</script>";
			$salida.="<script>$('#PostBeneficiario').html('( Nit ".$row['nitbeneficiario']." ) ".$row['nombrebeneficiario']."')</script>";
			$salida.="<script>$('#PostIntermediario').html('( Nit ".$row['nitintermediario']." ) ".$row['nombreintermediario']."')</script>";
			$salida.="<script>$('#PostCompaniaP').html('".$row['compania']."')</script>";
			$salida.="<script>$('#PostCiudad').html('".$row['ciudad']."')</script>";
			$salida.="<script>$('#PostDepartamento').html('".$row['departamento']."')</script>";
		}
		$result = queryQR("select *, COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as 
						   nombres, to_char(fechahora,'yyyy-MM-dd HH:MI:SS AM') as fechahor, to_char(fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as 
						   fechahora_limit, usu.usuario_cod as usuxmdfadj
						   from wf_historial his
						   	inner join adm_usuario usu using (usuario_cod)
						   	left join wf_tiporespuesta tpresp using (cod_respuesta)
						    where his.id_radicacion='".$Id."'
						    order by fechahora asc");
						   
		$intervinoactual = false;
		while($row = $result->FetchRow()){
			if($row['fechahor']==null){
				$style="style=\'color: red;\'";
				if($row['usuario_cod']== $_SESSION['uscod']){
					$opciones="<img id=\'Continuar\' onClick=\'Continuatramite($Id)\' title=\'Continuar\' src=\'images/good.png\' style=\'cursor: pointer; margin:0 3px\'/> ";
					
					$result2 = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.1'");
					if($result2->RecordCount() > 0)
						$opciones.="<img id=\'Reasignar\' onClick=\'Reasigna($Id, $Usuario)\' title=\'Reasignar\' src=\'images/reasignar.gif\' style=\'cursor: pointer; margin:0 3px\'/> ";
					
					if($inicio['actividad'] != "Clasificar"){
						$result2 = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.2'");
						if($result2->RecordCount() > 0)
							$opciones.="<img id=\'Devolver\' onClick=\'Devolucion($Id)\' title=\'Devolver\' src=\'images/prev.png\' style=\'cursor: pointer; margin:0 3px\'/>";
					}

				}else{
					$result2 = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.4'");
					if($result2->RecordCount() > 0)
						$opciones="<img id=\'Continuar\' onClick=\'Continuatramite($Id)\' title=\'Continuar\' src=\'images/good.png\' style=\'cursor: pointer; margin:0 3px\'/> ";

					$result2 = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.3'");
					if($result2->RecordCount() > 0)
						$opciones.="<img id=\'Reasignar\' onClick=\'Reasigna($Id, $Usuario)\' title=\'Reasignar\' src=\'images/reasignar.gif\' style=\'cursor: pointer; margin:0 3px\'/> ";

					//////////////////		CAMBIO REQUERIMIENTO No. 45  //////////////////////////////////////////
					if($inicio['actividad'] != "Clasificar"){
						$result2 = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.2'");
						if($result2->RecordCount() > 0)
							$opciones.="<img id=\'Devolver\' onClick=\'Devolucion($Id)\' title=\'Devolver\' src=\'images/prev.png\' style=\'cursor: pointer; margin:0 3px\'/>";
					//////////////////		CAMBIO REQUERIMIENTO No. 45  //////////////////////////////////////////

					}

				}
			}
						
			if(strlen ($row['observacion']) > 0){
				$muestiprees="";
				if($row['cod_respuesta'])
					$muestiprees="Tipo de Respuesta : " .$row['respuesta'] ;

				$Observacion="<a href=\'#\' onclick=\'MuestraObservacion(\""."<b>".$row['nombres']." (".$row['fechahor'].") :</b><br>".$row['observacion']."<br>".$muestiprees."\")\'>".str_replace("<br>", "", substr($row['observacion'], 0, 8))." ...</a>";
			}else
				$Observacion="";
			



			$salida.="<script>$('#Historial').append('<tr><th><b $style>".$row['actividad']."</b></th>".
														 "<th><b $style>".$row['nombres']."</b></th>".
														 "<th><b $style>".$row['fechahor']."</b></th>".
														 "<th><b $style>".$row['fechahora_limit']."</b></th>".
														 "<th>$Observacion</th>".
														 "<th class=\'OpcionesActividad\'>$opciones</th></tr>')
														 	</script>";
			if($row['estado_tramite'] == 'Cerrado')
				$salida.="<script>$('#Historial tr:last').children().children('b').css('color', 'green')</script>";
				
			if($row['estado_tramite'] == 'Anulado')
				$salida.="<script>$('#Historial tr:last').children().children('b').css('color', 'red')</script>";
				
			if($row['estado_tramite'] == 'Suspendido')
				$salida.="<script>$('#Historial tr:last').children().children('b').css('color', '#DF7401')</script>";
				
			if($row['estado_tramite'] == 'Re-abierto')
				$salida.="<script>$('#Historial tr:last').children().children('b').css('color', '#2E9AFE')</script>";
				
			$id_workflow=$row['id_workflow'];
			$id_usuario=$row['usuario_cod'];
			
			if($id_usuario== $_SESSION['uscod'])
				$intervinoactual = true;	
		}

		$permiso = queryQR("select * from adm_usumenu where usuario_cod=".$_SESSION['uscod']." and jerarquia_opcion='4.1.2.5.4'");
		
		if($id_usuario== $_SESSION['uscod'] || $permiso->RecordCount() > 0 ){
				$result2 = queryQR("select * from wf_inputs_actividades ia, wf_workflow wor, wf_inputs inp 
					where inp.id_input=ia.id_input and wor.id_actividad=ia.id_actividad 
					and inp.id_input <> 12 and wor.id_workflow=".$id_workflow." order by inp.id_input");				
			while($row2 = $result2->FetchRow()){//imprime las funciones de las actividades
				echo str_replace("Id", $Id, $row2['function_input']);
			}
		}

		$result = queryQR("select * from wf_adjuntos where id_radicacion=$Id");//Muestra todos los comentarios
		while($row = $result->FetchRow()){//TodosComentarios
			if($row['tipo_adjunto']=='Adicional')
				$salida.="<script>$('#DocAdicionales').append('<a href=\'#\' id=\'Adj".$row['id_adjunto']."\' class=\'draggable\' ondblclick=\'MuestraAdjunto(".$row['id_adjunto'].")\' style=\'display: block;\'><img src=\'images/clip.png\' border=\'0px\' width=\'15px\'> ".$row['desc_adjunto']."</a>')</script>";
				
			if($row['tipo_adjunto']=='Requerido')
				$salida.="<script>$('#DocRequeridos').append('<a href=\'#\' id=\'Adj".$row['id_adjunto']."\' class=\'draggable\' ondblclick=\'MuestraAdjunto(".$row['id_adjunto'].")\' style=\'display: block;\'><img src=\'images/clip.png\' border=\'0px\' width=\'15px\'> ".$row['desc_adjunto']."</a>')</script>";
				
			if($row['tipo_adjunto']=='Respuesta')
				$salida.="<script>$('#DocRespuesta').append('<a href=\'#\' id=\'Adj".$row['id_adjunto']."\' class=\'draggable\' ondblclick=\'MuestraAdjunto(".$row['id_adjunto'].")\' style=\'display: block;\'><img src=\'images/clip.png\' border=\'0px\' width=\'15px\'> ".$row['desc_adjunto']."</a>')</script>";
			
				
			$salida.="<script>$('#DocAdicionales').parent().show()</script>";
		}
	
		$salida.="<script>$('#DocAdicionales').parent().parent().parent().width($('#PostRadicado').width()-100)</script>";
		
		//solicitud de paula dejar que cualquiera pueda modificar adjuntos
		if ($intervinoactual && $row['estadotramite']!='Cerrado')
			echo "<script>ModificarAdjuntos(".$Id.")</script>";
		
		//Muestra todos los comentarios
		$result = queryQR("select *, COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as nombres, 
						   to_char(fechahora_comentario,'yyyy-MM-dd HH:MI:SS AM') as fechahora from wf_comentario com, adm_usuario usu where 
						   com.usuario_cod=usu.usuario_cod and com.id_radicacion=$Id order by fechahora_comentario asc");
				
		while($row = $result->FetchRow()){//TodosComentarios
			$salida.="<script>$('#TodosComentarios').width($('#PostRadicado').width()-100)</script>";
			$salida.="<script>$('#TodosComentarios').append('<table class=\'TableRadicado\'><tr><th>".$row['nombres']." - ".$row['fechahora']."<br><div style=\'padding-left: 50px;\'><b>".$row['desc_comentario']."</b></div></th></tr></table>')</script>";
		}
		
		
		$salida.="<script>ProximaActividad('".$Id."');</script>";
		
		
		$salida.="<script>
					if($('#Continuar').length ==0 && $('#Reasignar').length==0 && $('#Reasignar').length==0)
						 $('.OpcionesActividad').remove();
				</script>";

		
		$salida.="<script>$('#PostRadicado').show()</script>";
		$salida.="<script>ViewEncuesta('".$Id."')</script>";
		$salida.="<script>ViewRespuesta('".$Id."')</script>";
	}
	return $salida;
}

function PermisosTramite($Id, $Usuario){
	$salida="";

	$result = queryQR("select * from adm_usumenu where usuario_cod=$Usuario and jerarquia_opcion like '4.1.2.%'");

	$result2 = queryQR("select estado,id_tipologia from wf_radicacion where id_radicacion=$Id");
	$row2 = $result2->FetchRow();
	
	while($row = $result->FetchRow()){//imprime las funciones de las actividades
			//echo $row['jerarquia_opcion']." ".$row2['estado'];
		if($row['jerarquia_opcion'] == '4.1.2.1' && ($row2['estado'] == 'En tramite' || $row2['estado'] == 'Suspendido' || $row2['estado'] == 'Re-abierto'))
			$salida.="<img title=\'Cerrar tramite\' src=\'images/good.png\' onClick=\'CerrarTramite()\' style=\'cursor: pointer; margin:0 3px\' width=\'20px\'/>";
			
		if($row['jerarquia_opcion'] == '4.1.2.2' && ($row2['estado'] == 'En tramite' || $row2['estado'] == 'Suspendido'  || $row2['estado'] == 'Re-abierto'))
			$salida.="<img title=\'Anular tramite\' src=\'images/close.png\' onClick=\'AnularTramite()\' style=\'cursor: pointer; margin:0 3px\' width=\'20px\'/>";
			
		if($row['jerarquia_opcion'] == '4.1.2.3' && ($row2['estado'] == 'En tramite' || $row2['estado'] == 'Re-abierto'))
			$salida.="<img title=\'Suspender tramite\' src=\'images/suspender.png\' onClick=\'SuspenderTramite()\' style=\'cursor: pointer; margin:0 3px\' width=\'20px\'/>";
			
		if($row['jerarquia_opcion'] == '4.1.2.4' && 
			$row2['id_tipologia']!=2973 && $row2['id_tipologia']!=2972 &&
			($row2['estado'] == 'Cerrado' || $row2['estado'] == 'Suspendido'))
				$salida.="<img title=\'Reabrir tramite\' src=\'images/refresh.png\' onClick=\'ReabrirTramite(".$Id.")\' style=\'cursor: pointer; margin:0 3px\' width=\'20px\'/>";
	}
	return $salida;
}

function GuardaContinua(){

	$compa = queryQR("SELECT t.id_compania, h.actividad, h.id_historial, r.id_tipologia, 
		t.id_servicio, t.id_tipotramite , r.estado
		FROM wf_radicacion AS r 
		INNER JOIN wf_tipologia AS t ON t.id_tipologia = r.id_tipologia 
		INNER JOIN (SELECT id_radicacion, actividad, id_historial FROM wf_historial WHERE fechahora IS NULL) AS h ON h.id_radicacion = r.id_radicacion
		WHERE r.id_radicacion = '".$_REQUEST['Tramite']."'");
	$activ = $compa->FetchRow();

	if($_REQUEST['Pregunta'] != null){
		$valores="";
		for($i=0; $i<sizeof($_REQUEST['Pregunta']); $i++){
			if($_REQUEST['Respuesta'][$i] != null)
				$valores.="('".$_REQUEST['Pregunta'][$i]."', '".$_REQUEST['Respuesta'][$i]."', '".$_REQUEST['Tramite']."'), ";	
		}
		$valores=substr( $valores ,0,strlen( $valores )-2);
		queryQR("insert into wf_encuesta (pregunta, respuesta, id_radicacion) values $valores");
	}

	if($_FILES['FileRespuesta']['name'] != null){
		GuardaAdjuntos($_REQUEST['Tramite'], 'FileRespuesta', 'Respuesta');
	}
	
	if($_REQUEST['CambiaTipologia'] != null){
		$result = queryQR("update wf_radicacion set id_tipologia='".$_REQUEST['CambiaTipologia']."' where id_radicacion='".$_REQUEST['Tramite']."'");
	}
	
	if($_REQUEST['CartaRespuestaHide'] != null){
		$result = queryQR("update wf_radicacion set carta_respuesta='".str_replace("'", '\"',$_REQUEST['CartaRespuestaHide'])."' where id_radicacion='".$_REQUEST['Tramite']."'");
	}

	if ($_REQUEST['EnviaRespuesta'] == "on"){
		
		if($_REQUEST['MedioRespuesta'] == "Correo electronico"){
		  $result = queryQR("select usuario_correo from adm_usuario where usuario_cod = ".$_SESSION['uscod']."");
			
		  $row = $result->FetchRow();

			EnviaRespuesta($_REQUEST['Tramite'],$row['usuario_correo']);
		}			
			
		if($_REQUEST['MedioRespuesta'] == "Carta"){
			$result = queryQR("select * from wf_radicacion rad, wf_tipologia tip, wf_servicio ser where rad.id_tipologia=tip.id_tipologia and 
							 tip.id_servicio=ser.id_servicio and rad.id_radicacion='".$_REQUEST['Tramite']."'");
			
			$row = $result->FetchRow();
			$TramiteCorrespondencia = GuardaRadicacionCorrespondencia($_REQUEST['Tramite'], "Tramite radicado automáticamente del workflow Quejas y Reclamos", $row['desc_servicio'], $row['id_ciudad'], $row['nombre'], $row['telefono'], $row['direccion'], $row['id_compania'],0);
		
			if($activ['id_compania'] == "4")
				queryQR("UPDATE wf_radarl set radicado_envio ='".$TramiteCorrespondencia."' WHERE id_radicacion='".$_REQUEST['Tramite']."'");
		}
	}
	
	if($_REQUEST['FavorRespuesta'] != null){
		queryQR ("update wf_radicacion set respuesta_favor='".$_REQUEST['FavorRespuesta']."' where id_radicacion='".$_REQUEST['Tramite']."'");
	}

	if($_REQUEST['seguimiento'] != null){
		queryQR ("update wf_radicacion set seguimiento=true,fecha_seguimiento = '".$_REQUEST['fechaseguimiento']."' 
			where id_radicacion='".$_REQUEST['Tramite']."'");
	}
	
	
	if($_REQUEST['CerrarTramiteAuto'] != null){
		
		if($activ['id_tipotramite']!='3'){
			
		  queryQR ("update wf_radicacion set estado='Cerrado', fechahora_estado=now(), causalestado='Termino de tramite correctamente' where id_radicacion='".$_REQUEST['Tramite']."'");
		  queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora, fechahora_limite, observacion, id_workflow, estado_tramite) values 
				('".$_REQUEST['Tramite']."', 'Cerrado', '".$_SESSION['uscod']."', now()+ interval '1 second', now()+ interval '1 second', '<br><br><b>Sistema:</b> El tramite ha sido cerrado automáticamente', (select id_workflow from wf_historial where 
				id_radicacion=".$_REQUEST['Tramite']." order by id_historial desc limit 1), 'Cerrado')");
		}elseif ($activ['actividad']=='Cerrado'){
		  queryQR ("update wf_radicacion set estado='Cerrado', fechahora_estado=now(), causalestado='Termino de tramite correctamente' where id_radicacion='".$_REQUEST['Tramite']."'");			
		}
	}
	
	if($_REQUEST['Observaciones'] != null){
		$Observaciones=str_replace(array("'", "\""), '`',$_REQUEST['Observaciones']);
		$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);
		
		if($TramiteCorrespondencia != null)
			$Observaciones.="<br><br><b>Sistema:</b> El tramite ha sido radicado en correspondencia con el número de tramite ".$TramiteCorrespondencia;
		
		queryQR("update wf_historial set observacion='".$Observaciones."' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
	}
	
	if($_REQUEST['accion'] != null){
		
		$tipress="";
		if($_REQUEST['TipRespuesta'] != '')
			$tipress=",cod_respuesta = '".$_REQUEST['TipRespuesta']."'";

			queryQR("update wf_historial set fechahora=now(), usuario_cod='".$_SESSION['uscod']."'
				$tipress where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
	}


	if( $_REQUEST['PasoProximo'] != NULL && $_REQUEST['Tramite'] != NULL){
		//echo "pepe " . $_REQUEST['PasoProximo'];
		//selecciona la actividad siguiente 
		$result = queryQR("select *,wor.id_actividad acta from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
			act.id_actividad=wor.id_actividad and wor.id_workflow='".$_REQUEST['PasoProximo']."'");
		$row = $result->FetchRow();
		
		$entearl=true;
		if ($activ['id_tipotramite']=='3' && $row['acta']=='5' && $activ['estado']!='Re-abierto')
			$entearl=false;

		if($_REQUEST['UsuarioProximo'] != NULL && $entearl){
			$Usuario=$_REQUEST['UsuarioProximo'];
		}else{//selecciona el usuario para la siguienete actividad, haciendo balanceo de cargas
			$UsuarioRegla=ReglasUsuarioActividad($_REQUEST['PasoProximo'], $_REQUEST['Tramite']);
			if( $UsuarioRegla!= null){
				$Usuario=$UsuarioRegla;
			}else{
				$result2 = queryQR("select *, (select count(*) from wf_historial where usuario_cod=usu.usuario_cod and fechahora is null) as tramites  from wf_workflow wor, wf_workflowusuarios usu,
					adm_usuario adm where adm.usuario_cod=usu.usuario_cod and wor.id_workflow=usu.id_workflow and 
					wor.id_workflow='".$_POST['PasoProximo']."' order by tramites asc limit 1");
				$row2 = $result2->FetchRow();
				$Usuario=$row2['usuario_cod'];
			
			}
		}


		//proyecto de generar caso en la respuesta
		/*if($row['desc_actividad']=='Generar respuesta'){
			$resultcr = queryQR("select * from wf_radicacion rad, wf_tipologia tip, wf_servicio ser where rad.id_tipologia=tip.id_tipologia and 
							 tip.id_servicio=ser.id_servicio and rad.id_radicacion='".$_REQUEST['Tramite']."'");
			
			$rowcr = $resultcr->FetchRow();

			$tt=GuardaRadicacionCorrespondencia($_REQUEST['Tramite'], "Tramite radicado automáticamente del workflow Quejas y Reclamos", $rowcr['desc_servicio'], $rowcr['id_ciudad'], $rowcr['nombre'], $rowcr['telefono'], $rowcr['direccion'], $rowcr['id_compania'],$Usuario);
			
		}*/



		//ojo, actividad asignada en reabrir tramites entes de control ya que son actividades fuera del flujo.
		if ($row['id_actividad'] == null && ($_REQUEST['PasoProximo'] == 99 || $_REQUEST['PasoProximo'] == 98))
			$idactivpfr=($_REQUEST['PasoProximo']==99) ? 9 : 8;
		else
			$idactivpfr=$row['id_actividad'];

		$descacttx=$row['desc_actividad'];
		$idworkflowtx=$row['id_workflow'];
		$fechasegui=null;
		$fechasegui=$_REQUEST['fechaseguimiento'];

			//se inserta la actividad de seguimiento si es necesario.
			$qrcerrado = queryQR("select id_radicacion,fecha_seguimiento from wf_radicacion where id_radicacion=".$_REQUEST['Tramite']." and 
				seguimiento = true and estado = 'Cerrado' ");
			if ($qrcerrado->RecordCount()>0){
				$rowqc = $qrcerrado->FetchRow();
				$fechasegui=$rowqc['fecha_seguimiento'];

				$qrcerrado2 = queryQR("select id_radicacion from wf_historial where id_radicacion=".$_REQUEST['Tramite']." and 
					actividad = 'Seguimiento'");
				if ($qrcerrado2->RecordCount()<1){

					$idactivpfr=13;	
					$descacttx='Seguimiento';

						$result2 = queryQR("select usuario_cod from wf_historial where id_radicacion=".$_REQUEST['Tramite']." and actividad = 'Generar respuesta'
						 order by id_historial desc limit 1");
						$row2 = $result2->FetchRow();
						$Usuario=$row2['usuario_cod'];	
						$idworkflowtx=96;		
				}
			}


		//inserta la segunda actividad de la tipologia
		$TiempoHoras = TiempoHoras($_REQUEST['Tramite'], $idactivpfr);
		$Limite = CalculaTiempoLimite($_REQUEST['Tramite'], $TiempoHoras);

		if ($fechasegui!=null && $idworkflowtx==96){
			$Limite= $fechasegui;
			$TiempoHoras=0;
		}

		$result = queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) values (".$_REQUEST['Tramite'].", '".$descacttx."',
			'".$Usuario."', '$Limite', ".$idworkflowtx.", '$TiempoHoras')");					
		

	}	
	
	if($_REQUEST['accion'] != null){
		if($TramiteCorrespondencia != null)
			$TramiteCorrespondencia = "&codebar=$TramiteCorrespondencia";
		return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."$TramiteCorrespondencia';</script>";
	}
	
}

function calculaLimite($dias){
		$fechaLim = date("Y-m-d");
		$result = queryADM("SELECT fecha FROM festivos WHERE fecha >= '".$fechaLim."'");

		while ($row = $result->FetchRow()){
			$festivos[] = $row['fecha'];
		}

		while ($i < $dias) {
			$fechaLim = date('Y-m-d', strtotime("$fechaLim + 1 day"));
			if(!in_array($fechaLim, $festivos))
				$i++;	
		}
		$fechaLim = $fechaLim." 23:59:59";
		return $fechaLim;
	}

function Guardareasigna(){
	/*if($_REQUEST['UsuarioReasignar'] == null || $_REQUEST['ObservacionesReasigna'] == null)
		return;
		
	$result = queryQR("select * from wf_radicacion rad, wf_historial his where his.id_radicacion=rad.id_radicacion and his.fechahora is null and rad.id_radicacion=".$_REQUEST['Tramite']);
	if($result->RecordCount() > 0){
		$row = $result->FetchRow();
		
		queryQR("update wf_historial set fechahora=now(),usuario_cod='".$_SESSION['uscod']."', observacion='Reasignación: ".$_REQUEST['ObservacionesReasigna']."' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		
		$result2 = queryQR("select * from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
				act.id_actividad=wor.id_actividad and wor.id_workflow='".$row['id_workflow']."' and tie.id_tipotramite='".$row['id_tipotramite']."'");
		$row2 = $result2->FetchRow();
		
		$TiempoHoras = TiempoHoras($_REQUEST['Tramite'], $row2['id_actividad']);
		$Limite = CalculaTiempoLimite($_REQUEST['Tramite'], $TiempoHoras);

		queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) 
					values (".$_REQUEST['Tramite'].", '".$row['actividad']."',
						'".$_REQUEST['UsuarioReasignar']."', '$Limite', ".$row['id_workflow'].", '$TiempoHoras')");
	}
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";	*/
}

function GuardaCerrar(){
	if($_REQUEST['CausalCierre'] == null || $_REQUEST['ObservacionesCierre'] == null)
		return;

	queryQR ("update wf_radicacion set respuesta_favor='".$_REQUEST['FavorRespuesta']."' where id_radicacion='".$_REQUEST['Tramite']."'");
	
	$Observaciones=str_replace(array("'", "\""), '`',$_REQUEST['ObservacionesCierre']);
	$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);
	
	$result = queryQR("select * from wf_historial where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		if($row = $result->FetchRow()){
			queryQR("update wf_historial set fechahora=now(), usuario_cod='".$_SESSION['uscod']."', estado_tramite='Cerrado',  actividad='Cerrado', observacion='Cierre de tramite: $Observaciones' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		}else{
			queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora, fechahora_limite, observacion, id_workflow, estado_tramite) values 
					('".$_REQUEST['Tramite']."', 'Cerrado', '".$_SESSION['uscod']."', now(), now(), 'Cierre de tramite: ".$Observaciones."', (select id_workflow from wf_historial where 
					id_radicacion=".$_REQUEST['Tramite']." order by id_historial desc limit 1), 'Cerrado')");
		}
	queryQR("update wf_radicacion set fechahora_estado=now(), causalestado='".$_REQUEST['CausalCierre']."', estado='Cerrado' where id_radicacion='".$_REQUEST['Tramite']."' ");
	
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
}

function GuardaSuspender(){
	if($_REQUEST['CausalSuspension'] == null || $_REQUEST['ObservacionesSuspension'] == null)
		return;

	$Observaciones=str_replace(array("'", "\""), '`',$_REQUEST['ObservacionesSuspension']);
	$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);
	
	queryQR("update wf_historial set fechahora=now(), fechahora_limite=now(), usuario_cod='".$_SESSION['uscod']."', estado_tramite='Suspendido', actividad='Suspendido', observacion='Tramite suspendido: $Observaciones' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
	queryQR("update wf_radicacion set fechahora_estado=now(), causalestado='".$_REQUEST['CausalSuspension']."', estado='Suspendido' where id_radicacion='".$_REQUEST['Tramite']."' ");
	
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
}

function GuardaAnular(){
	if($_REQUEST['CausalAnulacion'] == null || $_REQUEST['ObservacionesAnulacion'] == null)
		return;

	$Observaciones=str_replace(array("'", "\""), '`',$_REQUEST['ObservacionesAnulacion']);
	$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);
	
	
	$result = queryQR("select * from wf_historial where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		if($row = $result->FetchRow()){
			queryQR("update wf_historial set fechahora=now(), usuario_cod='".$_SESSION['uscod']."', estado_tramite='Anulado',  actividad='Anulado', observacion='Tramite Anulado: $Observaciones' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		}else{
			queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora, fechahora_limite, observacion, id_workflow, estado_tramite) values 
					('".$_REQUEST['Tramite']."', 'Anulado', '".$_SESSION['uscod']."', now(), now(), 'Tramite Anulado: ".$Observaciones."', (select id_workflow from wf_historial where 
					id_radicacion=".$_REQUEST['Tramite']." order by id_historial desc limit 1), 'Anulado')");
		}
	
	queryQR("update wf_radicacion set fechahora_estado=now(), causalestado='".$_REQUEST['CausalAnulacion']."', estado='Anulado' where id_radicacion='".$_REQUEST['Tramite']."' ");
	
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
}

function GuardaDevolucion(){
	if($_REQUEST['ActividadDevolucion'] == null || $_REQUEST['CausalDevolucion'] == null || $_REQUEST['Observaciones'] == null)
		return;
		
	$result = queryQR("select * from wf_radicacion rad, wf_historial his where his.id_radicacion=rad.id_radicacion and his.id_workflow=".$_REQUEST['ActividadDevolucion']." and rad.id_radicacion=".$_REQUEST['Tramite']);
	if($result->RecordCount() > 0){
		$row = $result->FetchRow();
		$result = queryQR("update wf_historial set fechahora=now(), observacion='Causal de devolución: ".$_REQUEST['CausalDevolucion']."<br>Observaciones: ".$_REQUEST['Observaciones']."' where id_radicacion='".$_REQUEST['Tramite']."' and fechahora is null");
		
		$result2 = queryQR("select * from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
				act.id_actividad=wor.id_actividad and wor.id_workflow='".$row['id_workflow']."' and tie.id_tipotramite='".$row['id_tipotramite']."'");
		$row2 = $result2->FetchRow();
				
		$TiempoHoras = TiempoHoras($_REQUEST['Tramite'], $row2['id_actividad']);
		$Limite = CalculaTiempoLimite($_REQUEST['Tramite'], $TiempoHoras);
		
		$result = queryQR("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) values (".$_REQUEST['Tramite'].", '".$row['actividad']."',
						'".$row['usuario_cod']."', '$Limite', ".$row['id_workflow'].", '$TiempoHoras')");
	}
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
}

function GuardaReAbrir(){
	if($_REQUEST['ActividadReabrir'] == null || $_REQUEST['ObservacionesReabrir'] == null )
		return;

	$Observaciones=str_replace(array("'", "\""), '`',$_REQUEST['ObservacionesReabrir']);
	$Observaciones=str_replace(array("\r\n", "\r", "\n"), '<br>',$Observaciones);
		
	$result = queryQR("select * from wf_tiemposactividad tie, wf_workflow wor, wf_actividad act where tie.id_actividad= act.id_actividad and 
					   act.id_actividad=wor.id_actividad and wor.id_workflow='".$_REQUEST['ActividadReabrir']."'");
	$row = $result->FetchRow();
	
	/*$result2 = queryQR("select *, (select count(*) from wf_historial where usuario_cod=usu.usuario_cod and fechahora is null) as tramites  from wf_workflow wor, wf_workflowusuarios usu,
					adm_usuario adm where adm.usuario_cod=usu.usuario_cod and wor.id_workflow=usu.id_workflow and adm.usuario_bloqueado=false and 
					wor.id_workflow='".$_POST['ActividadReabrir']."' order by tramites asc limit 1");
	
	$row2 = $result2->FetchRow();
	$Usuario=$row2['usuario_cod'];*/

	if($_REQUEST['EstadoReabrir'] == 'Suspendido'){
		$result2 = queryQR("select EXTRACT(day FROM (now() - fechahora_estado)) as dias, fechahora_limite + interval '1 second' as fechalimite from wf_radicacion where id_radicacion=".$_REQUEST['Tramite']);
		$row2 = $result2->FetchRow();
		$TiempoLimite = TiempoLimiteTramite(null, ($row2['dias']-1), $row2['fechalimite'], null);//Obtiene tiempo limite del tramite
		
	}else{
		$result2 = queryQR("select tiempo_tramite from wf_radicacion where id_radicacion=".$_REQUEST['Tramite']);
		$row2 = $result2->FetchRow();
		$TiempoLimite = TiempoLimiteTramite(null, $row2['tiempo_tramite'], date('Y/m/d'), null);//Obtiene tiempo limite del tramite
	}
		
	queryQR ("delete from wf_historial where fechahora is null and id_radicacion=".$_REQUEST['Tramite']);
	
	queryQR ("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, estado_tramite, observacion, fechahora) values (".$_REQUEST['Tramite'].", 'Re-abierto',
			'".$_SESSION['uscod']."', now(), ".$row['id_workflow'].", 'Re-abierto','Tramite Re-abierto: ".$Observaciones."', now())");
	
	queryQR ("update wf_radicacion set fechahora_estado=now(), causalestado=null, respuesta_favor=null, estado='Re-abierto', 
			marced='".$_REQUEST['marcaed']. "',
			fechahora_limite='$TiempoLimite' where id_radicacion='".$_REQUEST['Tramite']. "' ");
 	
	$TiempoHoras = TiempoHoras($_REQUEST['Tramite'], $row['id_actividad']);
	$Limite = CalculaTiempoLimite($_REQUEST['Tramite'], $TiempoHoras);

	queryQR ("insert into wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) values (".$_REQUEST['Tramite'].", '".$row['desc_actividad']."',
			'".$_REQUEST['UsuarioReabrir']."', '$Limite', ".$row['id_workflow'].", '$TiempoHoras')");
		
		
	return "<script>location.href='".$_SERVER["SCRIPT_NAME"]."?p=".$_GET["p"]."&Tramite=".$_REQUEST['Tramite']."';</script>";
}

function Horario($horas){
	//echo $horas;
	$dias=0;
	$horas_base=1;

	while($horas>=0){
		$result = queryQR("select (now()+ interval '".$horas_base." hour') as fechahora from wf_horarios hor where  
				\"time\"(now()+ interval '".$horas_base++." hour') BETWEEN hor.horario_desde and hor.horario_hasta");
		if($row = $result->FetchRow())
			$horas--;
	}	
	
	do{
		$result2 = queryQR("select to_timestamp('".$row['fechahora']."', 'YYYY/MM/DD HH24:MI:SS')+ interval '".$dias." day' as fechahora from wf_festivo fes where to_date('".$row['fechahora']."', 'YYYY/MM/DD ')+ interval '".$dias++." day' != fes.festivo");
	}while(($row2 = $result2->FetchRow()) === false);

	return $row2['fechahora'];
}

function GuardaRadicacionCorrespondencia($Tramite, $Observaciones, $TipoTramite, $Cuidad, $Nombre, $Telefono, 
		$Direccion, $Compania, $Usucorr){//Funcion que guarda nueva radicacion	
	$salida="";
	if($Compania == 1)
		$Compania = "SEGUROS GENERALES";
	else
		$Compania  = "SEGUROS DE VIDA";
	
	$conect=new conexion();
	$consulta=$conect->queryequi("select agencia from adm_usuario usu join tblareascorrespondencia are on (usu.area=are.areasid) where usuario_cod=".$_SESSION['uscod']);
	$row = pg_fetch_array($consulta);//consulta la agencia de usuario logeado
	$AgenciaUsu=$row[0];

	if(strlen($AgenciaUsu) != 3)
		exit;

	$consulta=$conect->queryequi("select case when (substring(max(numtramite) from 12 for 4)='9999') 
								THEN 'false' when (substring(max(numtramite) from 12 for 4)!='9999') 
								THEN 'true' end as continua, to_number(max(numtramite),'000000000000000')+1 as 
								siguiente from  radcorrespondencia where numtramite like '".date ( "Ymd" ).$AgenciaUsu."%'
								and length(numtramite) = 15");
	
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

	$Remitente=$_SESSION['uscod'];

	if($_REQUEST['Observaciones']!=null){
		$consulta = $conect->query("select COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') 
			|| ' ' || COALESCE(usuario_segape,'')  from admusuario where usuario_cod='".$_SESSION['uscod']."'");		
		$row = pg_fetch_array($consulta);
		$Observaciones="<b style=''color: #00009B; font-size: 9px''>".date("h:i:s A d-m-Y ").' - '.$row[0].":</b> <div style
		=''margin-left:30px; width:500px''>".str_replace(array("'", "\""), '',$Observaciones).'</div>';// Guarda nombre comentario
	}

$arearr=($Usucorr==0) ? 68 : 118;
$tipodocrr=($Usucorr==0) ? 994 : 71;
$obsrr=($Usucorr==0) ? strtoupper("Respuesta tramite numero ".$Tramite." de ".$TipoTramite) : strtoupper("Tramite WF Quejas y Reclamos numero ".$Tramite." de tipo ".$TipoTramite);

	$conect->queryequi("insert into radcorrespondencia (sr, area, tipodoc, asunto, numguia, observaciones, fecins, 
		remitente, ciudad, numtramite, numfolios, destinatario, radicado) values ((select max(sr)+1 from radcorrespondencia), '$arearr', 
		'$tipodocrr', 
		'$obsrr',
		upper(''), '".$Observaciones."', now(), 
		upper('".str_replace(array("'", "\""), '', $Remitente)."'), '".$Cuidad."', '$NumeroTramite', '1', 
			'$Usucorr', '".$_SESSION['uscod']."')");


	if($Usucorr==0){
		$conect->queryequi("insert into radcorresext (numtramite, destinatario, telefono, direccion,  prioridad, tipo) values ('$NumeroTramite' , upper('".str_replace(array("'", "\""), '',$Nombre)."'), '".
				$Telefono."', upper('".str_replace(array("'", "\""), '',$Direccion)."'), upper('ALTA'), '".$Compania."')");
	}

	$conect->cierracon();


	
	TrazabilidadCorrespondencia($NumeroTramite);
	
	return "$NumeroTramite";
	
}

function TrazabilidadCorrespondencia($NumeroTramite){
	$conect=new conexion();
	
	$inserts="('".$NumeroTramite."', now(), ".$_SESSION['uscod'].", 'RADICADO', ".$_SESSION['area']."), ";
	
	$inserts=substr( $inserts ,0,strlen( $inserts )-2);
	$consulta=$conect->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values $inserts");
	$conect->cierracon();
	
	Trazabilidad($NumeroTramite, $_SESSION['uscod'], null);
}

function TiempoLimiteTramite($Fecha, $DiasHab, $Desde, $TipoTramite){
	if($Fecha !=NULL || $DiasHab !=NULL){//Obtiene la fechalimite
		if($Fecha !=NULL){
			$Festivos=0;			
			while (CantidadFestivos($Fecha, date("Y/m/d", strtotime($Fecha."+". $Festivos." day"))) != $Festivos){
				$Festivos++;
			}		
		
			return  date("Y/m/d", strtotime($Fecha."+". $Festivos." day"))." 23:59:59";
		}else{
			$Hasta = date("Y/m/d", strtotime($Desde."+".$DiasHab." day"));
			$Festivos = CantidadFestivos($Desde, $Hasta);
			
			
			while($Festivos != CantidadFestivos($Desde, date("Y/m/d", strtotime($Hasta."+". $Festivos." day"))))
				$Festivos = CantidadFestivos($Desde, date("Y/m/d", strtotime($Hasta."+". $Festivos." day")));
		
			return  date("Y/m/d", strtotime($Hasta."+". $Festivos." day"))." 23:59:59";
		}
	}else{
		$result = queryQR("select * from wf_tipotramite where id_tipotramite=".$TipoTramite);
		$row = $result->FetchRow();

		$Hasta = date("Y/m/d", strtotime($Desde."+".$row['tiempo_tipotramite']." day"));
		$Festivos = CantidadFestivos($Desde, $Hasta);
		
		
		while($Festivos != CantidadFestivos($Desde, date("Y/m/d", strtotime($Hasta."+". $Festivos." day"))))
			$Festivos = CantidadFestivos($Desde, date("Y/m/d", strtotime($Hasta."+". $Festivos." day")));
	
		return  date("Y/m/d", strtotime($Hasta."+". $Festivos." day"))." 23:59:59";
	}
}

function CantidadFestivos($Inicio, $Fin){
	$result = queryQR("select count(*) as festivos from wf_festivo where festivo BETWEEN '$Inicio' and '$Fin'");
	$row = $result->FetchRow();
	return $row['festivos'];	
}

function ReglasUsuarioActividad($Id_Workflow, $Tramite){
	$result = queryQR("select * from wf_workflow where id_workflow=$Id_Workflow");
	$row = $result->FetchRow();

	//Validacion para entes de control
	if($row['id_actividad'] == 8 || $row['id_actividad'] == 9){ //Revision o Aprobacion
			$result = queryQR ("select id_radicacion from wf_radicacion rad, wf_tipologia tip where 
				id_radicacion=$Tramite and tip.id_tipotramite = 3 and rad.id_tipologia = tip.id_tipologia and rad.estado = 'Re-abierto'");
			if($row = $result->FetchRow()){
			
				$result2 = queryQR ("select * from adm_usuario usu, adm_usumenu usm where 
					usm.jerarquia_opcion = '4.1.5.5' and
					usu.usuario_cod = usm.usuario_cod ");
				$result2->RecordCount();
				if($result2->RecordCount() > 0){
					$rowU = $result2->FetchRow();
					return $rowU['usuario_cod'];
				}
				else
					return null;
			}			
	
	}
	if($row['id_actividad'] == 5){ //Enviar respuesta
			$ActividadCopia=4; //Generar Respuesta
			$result = queryQR ("select his.usuario_cod, tip.id_tipotramite, tip.id_proceso

				from wf_radicacion rad, wf_historial his, wf_tipologia tip

				where his.id_radicacion=$Tramite and 
					rad.id_tipologia = tip.id_tipologia and his.id_radicacion = rad.id_radicacion and
					actividad=(select desc_actividad from wf_actividad where id_actividad=$ActividadCopia)");
			
			if($row = $result->FetchRow()){
				//para cartera no se asigna el mismo usuario que generó respuesta
				if ($row['id_tipotramite']==3 && $row['id_proceso']!=4)
					return $row['usuario_cod'];
			}
	}

	//Validaciones normales
	switch($row['id_actividad']){
		case "8": //Revision
		case "5" : // Enviar respuesta
		case "10" : // Encuesta
			$ActividadCopia=2; //Clasificar
			$result = queryQR ("select usuario_cod from wf_historial where id_radicacion=$Tramite and actividad=(select desc_actividad from wf_actividad where id_actividad=$ActividadCopia)");
			if($row = $result->FetchRow()){
			
				$result2 = queryQR ("select * from wf_workflowusuarios where id_workflow=$Id_Workflow and usuario_cod=".$row['usuario_cod']);
				$result2->RecordCount();
				if($result2->RecordCount() > 0)
					return $row['usuario_cod'];
				else
					return null;
			}else{
				return null;
			}
		break;
		
		case "2": //Clasificar
			$ActividadCopia=1; //radicar
			$result = queryQR ("select usuario_cod from wf_historial where id_radicacion=$Tramite and actividad=(select desc_actividad from wf_actividad where id_actividad=$ActividadCopia 
				order by id_historial desc) ");
			if($row = $result->FetchRow()){
			
				$result2 = queryQR ("select * from wf_workflowusuarios where id_workflow=$Id_Workflow and usuario_cod=".$row['usuario_cod']);
				$result2->RecordCount();
				if($result2->RecordCount() > 0)
					return $row['usuario_cod'];
				else
					return null;
			}else{
				return null;
			}
		break;
		
		case "7": //Control
			$ActividadCopia=6; //Direccionamiento
			$result = queryQR ("select usuario_cod from wf_historial where id_radicacion=$Tramite and actividad=(select desc_actividad from wf_actividad where id_actividad=$ActividadCopia)");
			if($row = $result->FetchRow()){
				
				$result2 = queryQR ("select * from wf_workflowusuarios where id_workflow=$Id_Workflow and usuario_cod=".$row['usuario_cod']);
				$result2->RecordCount();
				if($result2->RecordCount() > 0)
					return $row['usuario_cod'];
				else
					return null;
			}else{
				return null;
			}
		break;
		
		default:
			return null;
		break;
	}	
}

function EnviaRespuesta($Id,$copiac){
	require 'config/phpmailer/class.phpmailer.php';
	$cadena_encriptada = encrypt($Id,"dx");

	$adjuntos = array();
	$result = queryQR("select * from wf_radicacion rad, wf_tipotramite tip, wf_servicio ser where rad.id_tipotramite=tip.id_tipotramite and 
					   tip.id_servicio=ser.id_servicio and id_radicacion=$Id");
	$row = $result->FetchRow();
	
	$servicio=$row['desc_servicio'];

	$body 	= mb_convert_encoding($row['carta_respuesta'], 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
		
	$body .= '<h2><span style="font-size:14px"><span style="font-family:verdana,geneva,sans-serif"><strong><em><span style="color:#327E04">Su opinión es muy importante para nosotros, lo invitamos a diligenciar nuestra encuesta de satisfacci&oacute;n.</span>&nbsp;<a href="https://servicios.laequidadseguros.coop/equidad/Workflow/Encuesta.php?id='.$cadena_encriptada.'">Ir a la encuesta</a></em></strong></span></span></h2>';
	
	$correos =array($row['email']);
	
	$result = queryQR("select * from wf_adjuntos where id_radicacion=$Id and tipo_adjunto='Respuesta'");
	while($row = $result->FetchRow()){
		$adjuntos[]= array("nombre" => $row['desc_adjunto'], "ruta" => $row['ruta_adjunto']);
	}	
	
	try {
		$mail = new PHPMailer(true); 
		
		$mail->IsSMTP();                           
		$mail->SMTPAuth   = false;             
		$mail->Port       = 25;                    
		//$mail->Host       = "outlook.laequidad.com.co"; 
		$mail->Host       = "192.168.241.63";
		$mail->From       = "servicio.cliente@laequidadseguros.coop";
		$mail->FromName   = "Servicio al cliente";
		$mail->Subject  = "Respuesta $servicio tramite No. $Id";	
		//$mail->AddBCC("Paula.Ramirez@laequidadseguros.coop");
		$mail->AddBCC($copiac);
		$mail->MsgHTML($body);
		$mail->IsHTML(true); 
		$intentos=0;
		
		foreach( $adjuntos as $adjunto ) {
			$mail->AddAttachment($adjunto["ruta"], $adjunto["nombre"]);
		} 
		
		foreach( $correos as $destino ) {
			$mail->addAddress( $destino );
		} 
		
		while ((!$mail->Send()) && ($intentos < 5)) {
			sleep(2);
			$intentos=$intentos+1;
		}
		echo "enviado";
	} catch (phpmailerException $e) {
		echo "<script>alert('No se ha podido enviar el e-mail al destinatario debido a un error ');</script>";echo $e->errorMessage();
	}
}

function CalculaTiempoLimite($tramite, $TiempoHoras){
	$TiempoHoras = strtotime( date('Y-m-d H:i:s', 0)."+$TiempoHoras hour");
	$TiempoCalculado= strtotime( "now");
	$TiempoSumado= 0;
	
	$result = queryQR("select to_char(fechahora_limite, 'yyyy-mm-dd')|| ' ' ||(select max(horario_hasta) from wf_horarios) 
						as limite from wf_radicacion where id_radicacion=$tramite");
	$row = $result->FetchRow();
	$fechalimite = strtotime($row['limite']);

	$result = queryQR("select * from wf_horarios");
	$horario = $result->GetArray();
	
	for($i=0; $TiempoSumado < $TiempoHoras && $TiempoCalculado < $fechalimite; $i++){
		$nuevafecha = date ( 'Y-m-d' , strtotime( date('Y-m-d')."+$i day") );
		$result = queryQR("select * from wf_festivo where festivo='$nuevafecha'");
		if(!($row = $result->FetchRow())){
			foreach ($horario as $valor){
				$desde = strtotime($nuevafecha.' '.$valor['horario_desde']);
				$hasta = strtotime($nuevafecha.' '.$valor['horario_hasta']);
				
				if($TiempoCalculado < $desde && $TiempoSumado < $TiempoHoras){
					if($TiempoSumado+($hasta - $desde) > $TiempoHoras){		
						$TiempoCalculado = $desde+($TiempoHoras-$TiempoSumado);
						$TiempoSumado += ($TiempoHoras-$TiempoSumado) ;
					}else{
						$TiempoSumado += ($hasta - $desde);						
						$TiempoCalculado = $hasta;
					}
				}
				
				if($TiempoCalculado >= $desde && $TiempoCalculado < $hasta && $TiempoSumado < $TiempoHoras){
					if($TiempoSumado+($hasta - $TiempoCalculado) > $TiempoHoras){		
						$TiempoCalculado = $TiempoCalculado+($TiempoHoras-$TiempoSumado);
						$TiempoSumado += ($TiempoHoras-$TiempoSumado) ;						
					}else{
						$TiempoSumado += ($hasta - $TiempoCalculado);
						$TiempoCalculado = $hasta;
					}
				}
				
				if($TiempoCalculado > $fechalimite)
					$TiempoCalculado = $fechalimite;
			}
		}
	}
	return date( 'Y-m-d H:i:s', $TiempoCalculado);
}

function TiempoHoras($tramite, $idActividad){
	echo "<br>$tramite<br>";
	echo "<br>$idActividad<br>";
	$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) where id_radicacion=$tramite");
	$row = $result->FetchRow();
	
	if($row['ente_de_control']=='t'){//Es o no es ente de control
		$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) join wf_tiemposactividad 
					USING(id_tipotramite) where id_actividad = $idActividad and id_radicacion=$tramite");
		$row = $result->FetchRow();
		$tiempo = ($row['tiempo']*$row['tiempo_tramite'])/8;
		$tiempo =  round($tiempo, 0, PHP_ROUND_HALF_DOWN);
		return $tiempo;
	}else{
		$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) join wf_tiemposactividad 
					USING(id_tipotramite) where id_actividad = $idActividad and id_radicacion=$tramite");
		$row = $result->FetchRow();
		return $row['tiempo'];
	}
}


function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
   }
   return base64_encode($result);
	}

?>
