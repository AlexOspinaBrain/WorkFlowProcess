<?php
require_once ('../config/conexion.php');
$conect=new conexion();
if(!empty($_POST['Respuesta0']) || !empty($_POST['Respuesta1']) || !empty($_POST['Respuesta2']) || !empty($_POST['Respuesta3']) || !empty($_POST['Respuesta4']) || !empty($_POST['Respuesta5']) || !empty($_POST['Respuesta5']))
{
for($i=0;$i<=6;$i++)
{

$inse=$conect->queryequi("insert INTO wf_encuesta(pregunta,respuesta,id_radicacion) VALUES('".$_REQUEST["Pregunta".$i]."','".$_REQUEST["Respuesta".$i]."','".$_REQUEST["id"]."'  )");
$upda=$conect->queryequi("update wf_historial set observacion='Se realizo encuesta desde la pagina web', usuario_cod='0',fechahora=now()  where id_radicacion='".$_REQUEST["id"]."' and fechahora is NULL ");
}
?>
<html>
<head>
<title>ENCUESTA DE SATISFACCIÓN</title>
<style type="text/css">

fieldset {
  padding: 1em;
  font:80%/1 sans-serif;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  border-radius: 8px;
  width: 450px;
  }

fieldset { 
	border:5px solid green;
	  width: 450px;

}

legend {
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  border-radius: 8px;
  padding: 0.2em 0.5em;
  border:3px solid green;
  color:green;
  font-size:90%;
  text-align:center;
  }



#main {
   position: relative;
}
#main:after {
    content : "";
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    background: url("images/equidad.jpg") no-repeat fixed center; 
    width: 100%;
    height: 100%;
    opacity : 0.2;
    z-index: -1;
}

</style>
</head>
<body id="main">
<br>
<center><br><fieldset><legend><br>LA EQUIDAD SEGUROS O.C.</legend>Gracias por sus aportes, trabajamos en búsqueda de la excelencia<br>
<br>Puede Cerrar esta Pagina.
</fieldset><br /></center>

<?php
}
else
{
  echo "Debe llenar todos los campos <a href='Encuesta.php'>Atrás</a>";
}
?>