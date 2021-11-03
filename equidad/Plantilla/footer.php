<?php	
require_once ('config/ValidaUsuario.php');
require_once ('config/conexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" /> 
	<style>
		#pie{
			color:gray;
			font-size:12px;
			font-family: arial;
			text-align:right;
		}
		.textfooter{
			padding-right:20px;
			
		}
	</style>

</head>

<body>
<div id="pie">
	<?=MuestraFooter()?>
</div>
</body>
</html>
<?php
function MuestraFooter(){
	$salida="";
	
	$conect=new conexion();
	$consulta=$conect->query("select * from admusuario where usuario_cod='".$_SESSION['uscod']."'");
	$row = pg_fetch_array($consulta);
	$salida.="<span class='textfooter'><b>Usuario:  </b>".$row['usuario_desc']."</span>";
	$salida.="<span class='textfooter'><b>Nombre: </b>".$row['usuario_nombres']." ".$row['usuario_priape']." ".$row['usuario_segape']."</span>";
	
	$consulta=$conect->queryequi("select are.area, ofi.descrip from tblareascorrespondencia are, tblradofi ofi where are.agencia=ofi.codigo and are.areasid='".$row['area']."'");
	$row2 = pg_fetch_array($consulta);
	$salida.="<span class='textfooter'><b>Pertenece a la agencia:  </b>".$row2['descrip']."</span>";
	$salida.="<span class='textfooter'><b>del área:  </b>".$row2['area']."</span>";
	$conect->cierracon();
	return $salida;
}
?>