<!--
Este script es llamado dentro del script principal.php
Se de ejecutar 'principal?p=AppAdministracion/Usuarios.php'
paar correcta visualización
-->
<?php
require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript" src="js/jquery.min.js"></script>
	<script src="js/ui/js/jquery.ui.core.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.widget.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.position.js"></script><!-- autocomplete configuration -->
	<script src="js/ui/js/jquery.ui.autocomplete.js"></script><!-- autocomplete configuration -->
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script><!-- validate form configuration -->
	

	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->
	<link rel="stylesheet" href="js/ui/css/base/jquery.ui.all.css"><!-- autocomplete configuration -->	
	<link rel="stylesheet" href="js/ui/css/demos.css"><!-- autocomplete configuration -->
	

	<script>
$(document).ready(function(){
	$("#div1").fadeOut(0);
 	$("a").click(function(){
    $("#div1").fadeToggle();
  });
});
</script>
    <script type="text/javascript">
        $(document).ready(function() {	
			$("#formID").validationEngine();		
			
			$( "#AutoUsuario").autocomplete({
				source: "config/ajax_querys.php?op=buscaNombreYUsuario",
				minLength: 1,
				delay: 200,
				select: function( event, ui ) {
					$("#usuario_cod").val(ui.item.id);
					$("#busqueda").val(ui.item.value);
					$("#formID").validationEngine('detach');
					$("#formID").submit();			
				}
			});
			
			$("#borrar").click(function(){
				location.href='<?=$_SERVER['REQUEST_URI']?>';
			});
			
			$("input").first().focus();
			
			$('#Oficina').change(function() {
				MuestraAreas($(this).val());
			});
        });
		
		function MuestraAreas(agencia, area){
			$('#Area').html("<option>Espere ... </option>");
				$.ajax({
					type: "POST",
					url: "config/ajax_querys.php",
					data: { op: "buscaarea", term: agencia }
				}).done(function( data ) {	
					var obj =$.parseJSON(data);	
					var opciones="<option value=''></option>";
					for(i=0; i<obj.length; i++)
						opciones+="<option value='"+obj[i].id+"' "+((obj[i].id == area)?"selected='selected'":"")+">"+obj[i].value+"</option>";
					
					$('#Area').html(opciones);
				});
		}
    </script>
	
	<style>
		table#mitabla {
    border-collapse: collapse;
    border: 1px solid #CCC;
    font-size: 12px;
}
 
table#mitabla th {
    font-weight: bold;
    background-color: #E1E1E1;
    padding:5px;
}
 
table#mitabla tbody tr:hover td {
    background-color: #F3F3F3;
}
 
table#mitabla td {
    padding: 5px 10px;
}

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
		
		label{
			margin-top:10px;
		}
		
		input[type=text]{
		margin:0px 2px;
			display:inline;			
		}
		
		input[type=checkbox]{
			display:inline;
		}
	</style>
</head>
<body>
<table align="center"><tr><td>
	<div class="formular">
		<fieldset style="width:370px">
			<legend>Busqueda usuario</legend>
		<label>	
			<input type="text" id="AutoUsuario" name="AutoUsuario" style="width:300px" value="<?=$_REQUEST["busqueda"]?>"/>
			<button type="button" id="borrar">Borrar</button>
			<span>Busqueda por nombre o alias de usuario para actualizar</span>
		</label>	
		</fieldset>
	</div>
	<?php
	$conect=new conexion();
if($_REQUEST['usuario_cod']==""){
 $rows=0;
                $filas=0;        
}else{
		$consulta=$conect->queryequi("SELECT * from radcorrespondencia rad join trasacorrespondencia tra USING(numtramite)
where destinatario ='".$_REQUEST['usuario_cod']."' and fechahora is null"); 
 
		$rows = pg_fetch_array($consulta);      

                $filas=pg_num_rows($consulta);        
}
	?>
		<br>
		<form class="formular" action="#" id="formID" method="post">
		<input type="hidden" id="busqueda" name="busqueda" value="<?=$_REQUEST["busqueda"]?>"/>
		<input type="hidden" id="usuario_cod" name="usuario_cod" value="<?=$_REQUEST["usuario_cod"]?>"/>
		<fieldset style="width:350px; margin-left:10px">
			<legend id="tituloform">Nuevo usuario</legend>
			<br>
		<label>	
			<span>Usuario..: </span><br>
			<input type="text" id="Usuario" name="Usuario" class="validate[required] text-input" style="text-transform:lowercase; width:150px"/>
		</label>
		
		<label>	
			<span>Nombres: </span><br>
			<input type="text" id="Nombres" name="Nombres" class="validate[required] text-input" style="text-transform:uppercase;width:300px"/>
		</label>
		
		<label>	
			<span>Primer apellido: </span><br>
			<input type="text" id="PrimeApe" name="PrimeApe" class="validate[required] text-input" style="text-transform:uppercase; width:200px"/>
		</label>
		
		<label>	
			<span>Segundo apellido: </span><br>
			<input type="text" id="SegApe" name="SegApe" style="text-transform:uppercase;width:200px"/>
		</label>
		
		<label>	
			<span>Correo electronico: </span><br>
			<input type="text" id="Correo" name="Correo" class="validate[custom[email]] text-input" style="text-transform:lowercase;width:300px"/>
		</label>
		
		<label>	
			<span>Agencia: </span><br>
			<select id="Oficina" name="Oficina" class="validate[required]" style="text-transform:uppercase;">
				<option> </option>
			</select>			
		</label>		
		<?=OpcionesSelect('Oficina', 'tblradofi', 'codigo', 'descrip', '')?>
		
		<label>	
			<span>Area: </span><br>
			<select id="Area" name="Area" class="validate[required]" style="text-transform:uppercase;"></select>
		</label>
		
		<label>	
			<span>Usuario Activo: </span><br>
			<input type="checkbox" id="Activo" name="Activo">
		</label>
		
		<div style="text-align: right">
		<?php
		//$d=pg_num_rows($consulta);
		$d= $filas;
		$e=$d-1;?>
			<input type='hidden' name='Accion' id='Accion' value="Guardar"><br>
			<button type="submit" name="GuardarNuevo" id="GuardarNuevo" value="Guardar">Guardar</button>
			<button type="reset" id="Reseter">Borrar</button><br>	<br>	
			</div>
			</td></tr><?
		if($d==1 || $d==0){?>
		<?php
		}else{
		?>
		<center>
		<?php 
		echo "<center><h3>No puede realizar cambios porque tiene pendientes ".$e." tramites de correspondencia</h3>";
		echo "<a href='#'' ><h3>Mostrar Tramites</h3></a>";
		$i=1;
		echo "<div id='div1'><table id='mitabla' border='1' ><tr><th align='center'>#</th><th align='center'>Tramites</th></tr>";
		while ($row = pg_fetch_array($consulta))
		{
		echo "<tr><td align='center'>".$i."</td><td align='center'>".$row['numtramite']."</td></tr>";
		$i++;
		}echo "</table><br>";}?>

</table></form></fieldset></div></center>
<?=GuardaNuevo($_REQUEST['GuardarNuevo'])?>
<?=MuestraUsuario($_REQUEST['usuario_cod'])?>
</body>
</html>

<?php
function OpcionesSelect($IdSelect, $Tabla, $Id, $Value, $Where){
	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select $Id, $Value from $Tabla $Where order by $Value");
	
	while ($row = pg_fetch_array($consulta)){
		$salida.='<script>$("#'.$IdSelect.'").append("<option id=\"'.$IdSelect.$row[$Id].'\" value=\"'.$row[$Id].'\">'.$row[$Value].'</option>");</script>';
	}
	$conect->cierracon();
	return $salida;
}

function GuardaNuevo($Guardar){
	if(!strlen($Guardar)>0)
	return;
	
	$salida="";
	$conect=new conexion();
	if($_REQUEST['Accion'] == 'Guardar'){
		$consulta=$conect->queryequi("select * from adm_usuario where usuario_desc='".$_REQUEST['Usuario']."'");
		if ($row = pg_fetch_array($consulta)){
			$salida.="<script>$('#Usuario').validationEngine('showPrompt', 'Este usuario ya existe', 'error');</script>";
			$salida.="<script>$('#Usuario').val('".$_REQUEST['Usuario']."');</script>";
			$salida.="<script>$('#Nombres').val('".$_REQUEST['Nombres']."');</script>";
			$salida.="<script>$('#PrimeApe').val('".$_REQUEST['PrimeApe']."');</script>";
			$salida.="<script>$('#SegApe').val('".$_REQUEST['SegApe']."');</script>";
			$salida.="<script>$('#Correo').val('".$_REQUEST['Correo']."');</script>";
			$salida.='<script>$("#Area'.$_REQUEST['Area'].'").attr("selected", "selected");</script>';
			$salida.='<script>$("#Oficina'.$_REQUEST['Oficina'].'").attr("selected", "selected");</script>';
		}else{
			$Activo=(($_REQUEST['Activo'] != null)? "false" : "true" );
			$conect->queryequi("INSERT INTO adm_usuario(usuario_cod, usuario_desc, usuario_nombres, usuario_priape, usuario_segape, 
					usuario_correo, area, usuario_contrasena, usuario_bloqueado) VALUES ".
					"((SELECT max(usuario_cod)+1 from adm_usuario), lower('".$_REQUEST['Usuario']."'), upper('".$_REQUEST['Nombres']."'), upper('".
					$_REQUEST['PrimeApe']."'), upper('".$_REQUEST['SegApe']."'),'".$_REQUEST['Correo']."','".$_REQUEST['Area']."', '1', $Activo)");
					
			$consulta = $conect->queryequi("select usuario_cod from adm_usuario where usuario_desc=lower('".$_REQUEST['Usuario']."')");
			$row = pg_fetch_array($consulta);
			$conect->queryequi("INSERT INTO adm_usumenu (jerarquia_opcion, usuario_cod ) values ('2', ".$row['usuario_cod']."), 
																						  ('2.1', ".$row['usuario_cod']."), 
																						  ('2.2', ".$row['usuario_cod']."), 
																						  ('2.3', ".$row['usuario_cod']."), 
																						  ('2.4', ".$row['usuario_cod']."), 
																						  ('6', ".$row['usuario_cod']."), 
																						  ('6.1', ".$row['usuario_cod']."), 
																						  ('4', ".$row['usuario_cod']."), 
																						  ('4.1', ".$row['usuario_cod']."), 
																						  ('4.1.1', ".$row['usuario_cod']."), 
																						  ('4.1.2', ".$row['usuario_cod']."), 
																						  ('4.1.3', ".$row['usuario_cod']."), 
																						  ('4.1.6', ".$row['usuario_cod'].")");
			$salida.='<script>alert("Usuario ingresado correctamente");</script>';
		}
	}
	
	if($_REQUEST['Accion'] == 'Actualiza'){

		$flujos = $conect->queryequi("select wfs.id_workflow, count(usuario_cod) as cct
			from wf_workflowusuarios wfs inner join
				(select wfusu.id_workflow
				from wf_workflowusuarios wfusu inner join wf_workflow using (id_workflow) inner join wf_tipologia using (id_tipologia)
				where wfusu.usuario_cod = '".$_REQUEST['usuario_cod']."' and wf_tipologia.eliminado_tipologia = false) ss using (id_workflow)
			group by wfs.id_workflow
			having count(usuario_cod) < 2");
		
		if (pg_num_rows($flujos)>1 && $_REQUEST['Activo'] == null){
			$salida.='<script>alert("IMPOSIBLE DESACTIVAR el usuario, tiene flujos asignados en WF PQRS, debe ejecutar el cambio de usuario masivo antes.");</script>';
		}else{

			$Activo=(($_REQUEST['Activo'] != null)? ", usuario_bloqueado=false" : ", usuario_bloqueado=true" );
			$conect->queryequi("UPDATE adm_usuario SET usuario_nombres='".strtoupper($_REQUEST['Nombres'])."', usuario_priape='".
					strtoupper($_REQUEST['PrimeApe'])."', usuario_segape='".strtoupper($_REQUEST['SegApe']).
					"', usuario_correo='".strtolower($_REQUEST['Correo'])."', area=".$_REQUEST['Area']." $Activo WHERE usuario_cod='".$_REQUEST['usuario_cod']."'");
					
			$salida.='<script>alert("Usuario actualizado correctamente");</script>';
		}
	}
	$conect->cierracon();
	return  $salida;
}

function MuestraUsuario($IdUsu){
	if(!strlen($IdUsu) > 0)
	return;

	$salida="";
	$conect=new conexion();
	$consulta=$conect->queryequi("select * from adm_usuario usu join tblareascorrespondencia are on are.areasid=usu.area where usuario_cod='".$IdUsu."' ");
	if ($row = pg_fetch_array($consulta)){
		$salida.="<script>MuestraAreas('".$row['agencia']."', '".$row['areasid']."')</script>";
		$salida.="<script>$('#Usuario').val('".$row['usuario_desc']."');</script>";
		$salida.="<script>$('#Usuario').attr('readonly', true);</script>";
		$salida.="<script>$('#Activo').attr('checked', ".(($row['usuario_bloqueado']==t)?"false":"true").");</script>";
		$salida.="<script>$('#Nombres').val('".$row['usuario_nombres']."');</script>";
		$salida.="<script>$('#PrimeApe').val('".$row['usuario_priape']."');</script>";
		$salida.="<script>$('#SegApe').val('".$row['usuario_segape']."');</script>";
		$salida.="<script>$('#Correo').val('".$row['usuario_correo']."');</script>";
		$salida.="<script>$('#Accion').val('Actualiza');</script>";
		$salida.="<script>$('#tituloform').text('Actualización usuario');</script>";
		$salida.='<script>$("#Oficina'.$row['agencia'].'").attr("selected", "selected");</script>';
	}	
		
	$conect->cierracon();
	return $salida;
}
?>
