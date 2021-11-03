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
			setTimeout("$('#NumTramite').focus()",1);	
			
			$( "#codigobarras" ).dialog({
				autoOpen: false,
				width:520,
				height: 200,
				modal: true,
				close: function(event, ui) {  }
			});
			
		});
			
		function tables(Area,Numtramite){
			$(".flexme3").flexigrid({
				url : 'config/BusquedaTablesXML.php',
				dataType : 'xml',
				params:		[ { name: 'consulta', value: 'TramitesArea' },
							  { name: 'Area', value: Area},	
							  { name: 'NumTramite', value: Numtramite},	
							  
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
			$('#NumTramite').val($('#NumTramite').val());
						
			if( $('#NumTramite').val().length == 15){
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "SiRedirecionarCor", term: $('#NumTramite').val(), areausu:'<?=$_SESSION['area']?>' }
				}).done(function( data ) {	
				});			
			}
		}
		
		function RedireccionaIcono(Tramite){
		$('#NumTramit').val(Tramite);
		$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscausuariotramite", term: Tramite }
				}).done(function( data ) {			
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option value='"+obj[i].id+"'>"+obj[i].value+"</option>";
					
					$('#Destinatario').html(opciones);
				});
				
			$( "#FormularioRedireccionarinterno").dialog({
							autoOpen: true,
							modal: true,
							width:400,
							height: 150,
							close: function( event, ui ) {$('#NumTramite').val('')}
						});
		}
		
		function MuestraDetalles(Tramite){
		$('#NumTramite').val($('NumTramite').val());
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
											alert (data);
					$("#MuestraDetalles" ).html(' ');
					$("#MuestraDetalles" ).dialog( "destroy" );									
				}
			}
		});
	}
	MuestraCodeBar('<?=$_REQUEST['codebar']?>');
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
<form class="formul" action="#" id="formul" method="post">
<table align='center' style='padding:10px'>
	<tr>
		<td><fieldset style="width:350px; height:50px;" class='filtros' >
				<legend><b>Area destino: </b></legend>			
				<select name="Area" id="Area"><option></option></select>		
				<?=OpcionesSelect('Area', 'tblareascorrespondencia are', ' are.areasid', 'are.area', '')?>
			</fieldset></td><td>
		
			<fieldset style="width:130px;" class='filtros'>
				<legend><b>Numero de tramite: </b></legend>			
				<input type="text" name="NumTramite" id="NumTramite" style='width:120px'/>
			</fieldset></td><td>
			<input type="submit"></td>
	</tr>
</table>
</form>

<table class="flexme3"></table>
<div id="dialog-modal" title="Recibir Correspondencia" style="display:none"></div>
<div id="dialog-espera" title="Recibir Correspondencia" style="display:none"></div>
<div id="MuestraDetalles"></div>

<div id="FormularioRedireccionarinterno" title="Redireccionar" style="display:none">
<table align="center"><tr><td>
	<form class="formular" action="#" id="formID" method="post">
	<fieldset style="width:320px">
		<span>Destinatario:</span> </label>
		<select id="Destinatario" name="Destinatario" class="validate[required]" style="text-transform:uppercase;">
		</select>

		<input type="hidden" name="NumTramit" id="NumTramit" style='width:120px'/>
		<div style="text-align: right">
			<button type="submit" name="Guardar" id="Guardar" value="Redireccionar">Redireccionar</button>
		</div>	
		</fieldset>
	</form>
</td></tr></table></div>
<?php GuardaRedirrecionar(); ?>
<div id="codigobarras"></div>
</body>
</html>



<?php
if(!empty($_POST['Area']) || !empty($_POST['NumTramite']))
{
	echo "<script type='text/javascript'>tables('".$_POST['Area']."','".$_POST['NumTramite']."');
	$('#Area').val(".$_POST['Area'].");
	$('#NumTramite').val(".$_POST['NumTramite'].");
	
	</script>";
}
else if(empty($_POST['Area'])  &&  empty($_POST['NumTramite']) && empty($_REQUEST['Destinatario']))
{
echo "<br /><center><b>Seleccione un campo</center>";
}


function GuardaRedirrecionar(){

	if($_REQUEST['Guardar']==null)
	{
		return;
	}	
	$conect=new conexion();
		$consuli=$conect->queryequi("select * from radcorrespondencia cor join adm_usuario usu on cor.destinatario =cast(usu.usuario_cod as text) where numtramite='".$_REQUEST['NumTramit']."' "); 
		while ($rows = pg_fetch_array($consuli)){
		$usuan=$rows['usuario_nombres'];
		$usuan1=$rows['usuario_priape'];	
		}		
		$conect->queryequi("update trasacorrespondencia set usuario='".$_REQUEST['Destinatario']."' where numtramite='".$_REQUEST['NumTramit']."' and fechahora is null");
		$conect->queryequi("update radcorrespondencia set destinatario='".$_REQUEST['Destinatario']."' where numtramite='".$_REQUEST['NumTramit']."'");			
	    $consul=$conect->queryequi("select usuario_cod, usuario_nombres, usuario_priape, usuario_segape from adm_usuario where usuario_cod='".$_REQUEST['Destinatario']."'");
		while ($row = pg_fetch_array($consul)){
		echo "<center>Se realizo cambio en el tramite ".$_REQUEST['NumTramit']." - ".$usuan." ".$usuan1." Destinatario anterior  nuevo destinatario ".$row['usuario_nombres']." ".$row['usuario_priape']."</center>";
		}
}

function MasOpciones(){
	$conect=new conexion();
	$consulta=$conect->queryequi("select correspondencia from tblareascorrespondencia where areasid='".$_SESSION['area']."'");
	$row = pg_fetch_array($consulta);
	$conect->cierracon();
	
	if($row['correspondencia']=='t')
		return '<option value="4">Por enviar</option>';

}
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