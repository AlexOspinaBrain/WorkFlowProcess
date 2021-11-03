<?php	
require_once('config/ValidaUsuario.php')
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<head>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" /> 
    <title>Imagine Technologies</title>
 
    <script type="text/javascript" src="js/jquery.min.js"></script>
	
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	
	<script>
		var mensaje="<?=$_SESSION['EstadoSesion']?>";
		$(function(){
			$('#login').css("margin-top",(($(window).height() - $('#login').height())/2)+"px");
			$("#formID").validationEngine();
			$("#Usuario").focus();
			
			if(mensaje.length > 0)
				$('#legendsesion').validationEngine('showPrompt', mensaje, 'error');
		});
	</script>
</head>

<style type="text/css">
body {
	background-image:url('images/equidadseg.jpg');
}
</style>
<body>
<?php
if($_REQUEST['EstadoSesion']=="caduca")
	echo $_SESSION['EstadoSesion'];
?>
<table id="login" align="center"><tr><td>
	<form id="formID" class="formular" method="post">
		<fieldset style="width:270px">
			<legend id="legendsesion">
				Inicio Sesión
			</legend>
			<br>
			<label>	<span>Usuario : </span>
				<input class="validate[required] text-input" type="text" name="Usuario" id="Usuario" />
			</label>
			
			</legend>
			<label>	<span>Password : </span>
				<input class="validate[required] text-input" type="password" name="Password" id="Password" />
			</label>
			<input class="submit" type="submit" value="Entrar" name="EnviaLogin"/>
		</fieldset>
	</form>
</td></tr></table>
</body>
<?=$_SESSION['EstadoSesion']=""?>
</html>

