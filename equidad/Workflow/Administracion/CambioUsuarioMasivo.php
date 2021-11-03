<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=AppAdministracion/PermisosUsuario.php'
paar correcta visualización
-->
<?php
require_once ('config/conexion.php');
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
						
			$( "#AutoUsuario").autocomplete({
				source: "config/newAjax.php?op=buscaNombreYUsuario",
				minLength: 1,
				delay: 200,
				select: function( event, ui ) {
					$("#usuario_cod").val(ui.item.id);
					$("#AutoUsuario").val(ui.item.value);
					//$("#formID").submit();			
				},
				change: function (event, ui) {
                if(!ui.item){
                    
                    $("#AutoUsuario").val("");
                }

            }
			});
			
			$( "#AutoUsuario1").autocomplete({
				source: "config/newAjax.php?op=buscaNombreYUsuario",
				minLength: 1,
				delay: 200,
				select: function( event, ui ) {
					$("#usuario_cod1").val(ui.item.id);
					$("#AutoUsuario1").val(ui.item.value);
					//$("#formID").submit();			
				},
				change: function (event, ui) {
                if(!ui.item){
                    
                    $("#AutoUsuario").val("");
				}
			}
			});
			
			$("#cambiar").click(function(){
			cambiarper();
			});
			
			$("input").first().focus();
		
			$("input").first().focus();
			
			$("#formi").validationEngine('attach', {promptPosition : "topLeft"});	
        });

    </script>
	
		<style>
		.ui-autocomplete-loading { 
			background: white url('js/ui/css/base/images/ui-anim_basic_16x16.gif') right center no-repeat; 
		}
		.ui-autocomplete {
			overflow-y: auto;
			overflow-x: hidden;
			padding-right: 20px;
			height: 100px;
		}

		input[type=text]{
			width:300px; 
			display:inline;
			margin:0px 2px;
		}
		
	</style>
</head>
<body>
<table align="center"><tr><td>
	<form action="#" id="formi" method="post">
		<fieldset style="width:300px" align="center">
			<legend align="center">
				<b>BUSCAR USUARIOS 
			</legend>
			<br>
			<label>	
		<input type="text" id="AutoUsuario" name="AutoUsuario" class="validate[required]"/><br />
		<br /><span>USUARIO ACTUAL </span><br />
		<br /><input type="text" id="AutoUsuario1" name="AutoUsuario1" class="validate[required]"/><br />
		<br /><span>USUARIO NUEVO </span><br />
		<br /><input type="submit" value="CAMBIAR" id="cambiar"><br />
		<br />Se realizara el cambio de permisos y actividades pendientes.
		</label>
		<br />
		<br /><input type="hidden" id="usuario_cod" name="usuario_cod" /> 
		<br /><input type="hidden" id="usuario_cod1" name="usuario_cod1"/>
		</fieldset>
	</form>
	</td>
	</tr>
	</table>
</body>
</html>
<?php 
if (isset($_POST['AutoUsuario']))
{
 if($_POST['usuario_cod1'] != $_POST['usuario_cod']){
	//$update=$conect->queryequi("update wf_workflowusuarios SET usuario_cod=".$_POST['usuario_cod1']." where usuario_cod=".$_POST['usuario_cod']);
	$consultawfu=queryQR("select id_workflow from wf_workflowusuarios 
			where usuario_cod=".$_POST['usuario_cod']);
	if ($consultawfu->RecordCount()!=0){

	    while ($row = $consultawfu->FetchRow()){
	    	$segcon =queryQR("select id_workflow from wf_workflowusuarios 
			where id_workflow = ".$row['id_workflow']." and usuario_cod = ".$_POST['usuario_cod1']);

			if ($segcon->RecordCount()==0){
				
				queryQR("insert into wf_workflowusuarios (id_workflow,usuario_cod) values 
					(".$row['id_workflow'].",".$_POST['usuario_cod1'].")");
			}
			queryQR("delete from wf_workflowusuarios where usuario_cod = ".$_POST['usuario_cod'] . " and 
						id_workflow = ".$row['id_workflow']);
	    }
	    
	}

	$consulta=queryQR("select count (usuario_cod) from wf_historial where fechahora is null and 
			usuario_cod=".$_POST['usuario_cod']);
	$row = $consulta->FetchRow();

	if($consulta->RecordCount()!=0){
		queryQR("update wf_historial SET usuario_cod=".$_POST['usuario_cod1']." where usuario_cod=".$_POST['usuario_cod']." and fechahora is null");
	}

	echo "<center><br />Se asignaron los permisos del usuario ".$_POST['AutoUsuario']." al usuario ".$_POST['AutoUsuario1']." y  ".$row['count']." actividades pendientes";
 }else{

	echo "<center><br />El usuario actual y el nuevo son iguales, no se generaron cambios. Usuario : 
		".$_POST['AutoUsuario']."";

 }
}
?>










