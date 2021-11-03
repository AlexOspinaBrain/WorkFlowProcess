<?php	
require_once ('config/ValidaUsuario.php');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
 
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" /> 
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
	<title>Imagine</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	
<script type="text/javascript">
	$(document).ready(function() {
		$('#contenido').height(($(window).height()-($('#contenido').offset().top + $("#footer").height()) - 15)+"px");
		$("body").css("overflow", "hidden");
		$("#contenido").css("overflow", "auto");
    });
</script>

<style type="text/css">
body {
	background-image:url('images/equidadseg.jpg');
}
</style
</head>
<body>
<?php include 'Plantilla/menu.php'; ?>

<div id="contenido">
	<?php 
//echo $Pagina. "aaa";
		if($Pagina !=NULL)
			if(substr ($Pagina, -3 ) == 'php')
				 include $Pagina;  
			else
				echo '<script type="text/javascript"> window.location = "'.$Pagina.'"</script>';
	?>
</div>

<div id="footer">
	<?php include 'Plantilla/footer.php'; ?>
</div>
</body>
</html>
