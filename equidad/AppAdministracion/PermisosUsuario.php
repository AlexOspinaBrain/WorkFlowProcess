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
    <script type="text/javascript" src="js/ui/js/jquery-ui-1.8.21.custom.js"></script><!-- checkboxTree configuration -->
    <script type="text/javascript" src="js/jquery.checkboxtree.min.js"></script> <!-- checkboxTree configuration -->
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.button.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/jquery.checkboxtree.min.css"/><!-- checkboxTree configuration -->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- autocomplete configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- autocomplete configuration -->
	
    <script type="text/javascript">
        $(document).ready(function() {
			$( "button" ).button();	
			$('#arbol').checkboxTree({
				collapseImage: 'images/minus.png',
				expandImage: 'images/plus.png'	
			});
			
			$( "#AutoUsuario").autocomplete({
				source: "config/newAjax.php?op=buscaNombreYUsuario",
				minLength: 1,
				delay: 200,
				select: function( event, ui ) {
					$("#usuario_cod").val(ui.item.id);
					$("#AutoUsuario").val(ui.item.value);
					$("#formID").submit();			
				}
			});
		
			$("#borrar").click(function(){
				borrar();
			})
			$("input").first().focus();
        });
		
		function AgregaOpcion(padre, opcion, jerarquia, nombre, selec){
			if(padre == ""){
				if(!$("#permisos").length){
					$('#arbol').append("<li><input type='checkbox'> <label>Permisos</label><ul id='permisos'></ul></li>");
				}
				$('#permisos').append("<li id='"+opcion+"li'><input type='checkbox' name='"+opcion+"' "+((selec==1)?"checked":"")+"><label>"+jerarquia+" "+nombre+"</label></li>");
			}else{
				if(!$("#"+padre+"ul").length){
					$("#"+padre+"li").append("<ul id='"+padre+"ul'></ul>");
				}
				$("#"+padre+"ul").append("<li id='"+opcion+"li'><input type='checkbox' name='"+opcion+"' "+((selec==1)?"checked":"")+"><label>"+jerarquia+" "+nombre+"</label></li>");
			}		
		}
		
		function botonGuardar(){
			$("#arbol").append('<br><br>'+
								'<div style="text-align: right">'+
								'<button type="submit" name="guarda" value="true">Guardar cambios</button>'+
								'<button type="button" onClick="location.reload();">Cancelar</button></div>');
		}	
		function borrar(){
			$("#AutoUsuario, #usuario_cod").val("");
			$("#AutoUsuario").focus();
			$("#arbol").remove();
		}
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
		.formular fieldset{
			padding:0px 30px;
		}
		input[type=text]{
			width:300px; 
			display:inline;
			margin:0px 2px;
		}
		
		input[type=checkbox]{
			display:inline;
		}
	</style>
</head>
<body>
<table align="center"><tr><td>
	<form class="formular" action="#" id="formID" method="post">
		<fieldset style="width:400px">
			<legend>
				Busqueda usuario
			</legend>
			<br>
			<label>	
		<input type="text" id="AutoUsuario" name="AutoUsuario" value="<?=$_REQUEST["AutoUsuario"]?>"/>
		<button type="button" id="borrar">Borrar</button>
		<span>Busqueda por nombre o alias de usuario </span>
		</label>
		<input type="hidden" id="usuario_cod" name="usuario_cod" value="<?=$_REQUEST["usuario_cod"]?>"/>
		</fieldset>
		<ul id="arbol" style="border:0px solid #AAA;background: none;"></ul>
	</form>
</td></tr></table>
	<?=MuestraPermisos($_REQUEST["usuario_cod"])?>
	<?=GuardaPermisos($_REQUEST["guarda"])?>
</body>
</html>


<?php
function MuestraPermisos($idUsu){	
	if($idUsu == null)
	return;

	$consulta=queryQR("select * from adm_menu");
	while ($row =  $consulta->FetchRow()){
		$aux=explode(".", $row['jerarquia_opcion']);	
		
		$salida="";		

		for($i=0; $i<sizeof($aux); $i++){
			if(strlen($aux[$i]) == 1)
				$salida.="0".$aux[$i];
			else
				$salida.=$aux[$i];
		}
		
		$consulta2=queryQR("select count(*) from adm_usumenu um where um.jerarquia_opcion='".$row['jerarquia_opcion']."' and um.usuario_cod='$idUsu'");
		$row2 = $consulta2->FetchRow();
		$opciones[$salida]=array('jerarquia_opcion' => $row['jerarquia_opcion'], 'opcion' => $row['opcion'], 'selec' => $row2[0]);	
	}
	
	ksort($opciones);
	
	$salida="";
	foreach($opciones as $c=>$v){
		$padre= str_replace(".", "-", substr($v['jerarquia_opcion'], 0, strrpos($v['jerarquia_opcion'], ".")));
		$opcion= str_replace(".", "-", $v['jerarquia_opcion']);
		$salida.='<script>AgregaOpcion("'.$padre.'", "'.$opcion.'", "'.$v['jerarquia_opcion'].'", "'.$v['opcion'].'", "'.$v['selec'].'")</script>';
	}
	$salida.='<script>botonGuardar()</script>';
	return $salida;
}

function GuardaPermisos($guardar){
	if(!$guardar)	
		return;
		
	$consulta=queryQR("select * from adm_menu");
	$permisos="";
	while ($row = $consulta->FetchRow()){
		$jerarquia=str_replace(".", "-", $row['jerarquia_opcion']);
		if($_REQUEST[$jerarquia]=='on')
			$permisos.="('".$_REQUEST["usuario_cod"]."', '".$row['jerarquia_opcion']."'), ";
	}
	
	$permisos=substr($permisos, 0, strlen($permisos)-2); 
	queryQR("delete from adm_usumenu where usuario_cod='".$_REQUEST["usuario_cod"]."'");
	
	if(strlen($permisos)>0)
		queryQR("INSERT INTO adm_usumenu(usuario_cod, jerarquia_opcion) VALUES ".$permisos);
	
	$salida='<script>borrar();alert("Permisos actualizados");</script>';
	return $salida;
}
?>

