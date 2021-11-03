<html>
<head>
<title>ENCUESTA DE SATISFACCIÓN</title>
    <script src="../js/jquery.min.js" type="text/javascript" ></script>	
	<script src="../js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="../css/validationEngine.jquery.css" type="text/css"/><!-- validate form configuration -->

<style type="text/css">

.button {

background: green;

color: white;

border: 1px solid #eee;

border-radius: 20px;

box-shadow: 5px 5px 5px #eee;

}

.button:hover {

background: white;

color: green;

border: 1px solid white;

border-radius: 20px;

box-shadow: 5px 5px 5px #eee;

}



		#Respuesta4,#Respuesta5,#Respuesta3,#Respuesta6{
		 				padding:10px;
		 				border:solid 1px ;
						border-radius:5px;
		 			   }
 		#input1,#correo,#sele:focus{box-shadow:0 0 15px ;}

 		#input2,#correo,#sele{
		 				padding:10px;
		 				border:solid 1px ;
						border-radius:5px;
		 			   }
 		#input2,#correo:focus{box-shadow:0 0 15px ;}

		fieldset 	{
					  padding: 1em;
					  font:80%/1 sans-serif;
					  -webkit-border-radius: 8px;
					  -moz-border-radius: 8px;
					  border-radius: 8px;
					  width: 550px;
					}

		fieldset    { 	border:5px solid green;
				 		width: 550px;
					}


		legend 		{  -webkit-border-radius: 8px;
					   -moz-border-radius: 8px;
					   border-radius: 8px;
					   padding: 0.2em 0.5em;
					   border:3px solid green;
					   color:green;
					   font-size:90%;
					   text-align:center;
					}

		#main   	{	position: relative;}				
		#main:after {	content : "";
					    display: block;
					    position: absolute;
					    top: 0;
					    left: 0;
					    background: url("../images/equidad.jpg") no-repeat fixed center; 
					    width: 100%;
					    height: 100%;
					    opacity : 0.2;
					    z-index: -1;
					}
</style>

<script type="text/javascript">

 $(document).ready(function() {	

//$('#myForm').validate();

$("#myForm").validationEngine();	
});
</script>
</head>


<?php
function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);
   for($i=0; $i<strlen($string); $i++) {
   $char = substr($string, $i, 1);
   $keychar = substr($key, ($i % strlen($key))-1, 1);
   $char = chr(ord($char)-ord($keychar));
   $result.=$char;
   }
   return $result;
}
$cadena_desencriptada = decrypt($_REQUEST["id"],"dx");
require_once ('../config/conexion.php');
$conect=new conexion();
$consulta=$conect->queryequi("select id_radicacion,actividad from wf_historial where actividad='Encuesta' and id_radicacion='".$cadena_desencriptada."' and fechahora is null");
$row = @pg_fetch_array($consulta);
	if(!empty($row))
{
?>

<body id="main">
<center>
<fieldset>
<legend><h3>ENCUESTA DE SATISFACCIÓN ATENCIÓN DE QUEJAS O RECLAMOS <br>LA EQUIDAD SEGUROS O.C.</h3></legend>

	<div style=" font-size:14px;">
	<form id="myForm" action="encuesta1" method="post">
	<table border="0">
	<tr>
		<td>
		<p style="text-align:justify; font-weight: bold;"> 
		1.	Indique el periodo de tiempo en el que se dio respuesta a su 
		requerimiento: (Nota: Tenga en cuenta que los días se empiezan a contar 
		a partir del día siguiente hábil de recibida la solicitud)
 		</p>
		<input type="radio" name="Respuesta0" id="Respuesta0" value="Entre 8 y 15 días hábiles." class="validate[required] radio">
		Entre 8 y 15 días hábiles.<br>
		<input type="radio" name="Respuesta0" id="Respuesta0" value="Entre 15 y 20 días hábiles." class="validate[required] radio">
		Entre 15 y 20 días hábiles.<br>
		<input type="radio" name="Respuesta0" id="Respuesta0" value="Entre 20 y 30 días hábiles." class="validate[required] radio">
		Entre 20 y 30 días hábiles.<br>
		<input type="radio" name="Respuesta0" id="Respuesta0" value="Más de 30 días hábiles." class="validate[required] radio">
		Más de 30 días hábiles.<br>
		<br></td>
	</tr>
	<tr>
		<td>
 		<p style="text-align:justify; font-weight: bold;"> 
		2.	En términos de calidad, como califica usted la respuesta<br> a su requerimiento: 
		*(Nota: Calidad se refiere a la claridad<br> y suficiencia en la respuesta recibida.)
 		</p>	
		<input  type="radio" name="Respuesta1" id="Respuesta1" value="Excelente." class="validate[required] radio">
		Excelente.<br>
		<input  type="radio" name="Respuesta1" id="Respuesta1" value="Buena." class="validate[required] radio">
		Buena.<br>
		<input  type="radio" name="Respuesta1" id="Respuesta1" value="Regular." class="validate[required] radio">
		Regular.<br>
		<input  type="radio" name="Respuesta1" id="Respuesta1" value="Deficiente." class="validate[required] radio">
		Deficiente.<br>
		</td>
	</tr>
	<tr>
		<td>
		<p style="text-align:justify;  font-weight: bold;"> 
		3.	Califique la atención recibida por parte del área encargada<br> de la gestión de 
		su requerimiento
 		</p>
		<input  type="radio" name="Respuesta2" id="Respuesta2" value="Excelente." class="validate[required] radio">
		Excelente.<br>
		<input  type="radio" name="Respuesta2" id="Respuesta2" value="Buena." class="validate[required] radio">
		Buena.<br>
		<input  type="radio" name="Respuesta2" id="Respuesta2" value="Regular." class="validate[required] radio">
		Regular.<br>
		<input  type="radio" name="Respuesta2" id="Respuesta2" value="Deficiente." class="validate[required] radio">
		Deficiente.<br>
		</td>
	</tr>
	<tr>
		<td>
		<p style="text-align:justify;  font-weight: bold;"> 
		4.	Sugerencias y/o comentarios
 		</p>
		<textarea placeholder="Digita tu Sugerencia" id="Respuesta3" name="Respuesta3" rows="4" cols="50" class="validate[required]"></textarea>
		</td>
	</tr>
	<tr>
		<td>	
 		<p style="text-align:justify;  font-weight: bold;"> 
		5.	Nombre Completo
 		</p>
		<input placeholder="Digita tu Nombre Completo" type="text" name="Respuesta4" id="Respuesta4" class="validate[required] text-input" />
		<br> 
		</td>
	</tr>
	<tr>
		<td>
 		<p style="text-align:justify; font-weight: bold;"> 6.	Correo Electrónico: </p>
		<input placeholder="Digita tu Correo" id="Respuesta5" type="text" name="Respuesta5" class="validate[required,custom[email]] text-input" /><br>
		</td>
	</tr>
	<tr>
		<td>
		<p style="text-align:justify; font-weight: bold;">	7.	Tipo de Seguro: </p>
 		<select  name="Respuesta6" id="Respuesta6" class="validate[required]">
 		<option> </option>
		<?php
		$conect=new conexion();
		$consulta=$conect->queryequi("select des_compania from wf_compania");
		while($row = pg_fetch_array($consulta))
		{?>

		<option> <?php echo $row['des_compania']; ?></option>
		<?php 
		} ?>
		</select><br>
		</td>
	</tr>


	<input type="hidden" name="id" value=<?=$cadena_desencriptada?> />
	<input type="hidden" name="Pregunta0" value="Indique el periodo de tiempo en el que se dio respuesta a su requerimiento: (Nota: Tenga en cuenta que los días se empiezan a contar a partir del día siguiente hábil de recibida la solicitud)"/>
	<input type="hidden" name="Pregunta1" value="En términos de calidad, como califica usted la respuesta a su requerimiento: 	*(Nota: Calidad se refiere a la claridad y suficiencia en la respuesta recibida.)"/>
	<input type="hidden" name="Pregunta2" value="Califique la atención recibida por parte del área encargada de la gestión de su requerimiento"/>
	<input type="hidden" name="Pregunta3" value="Sugerencias y/o comentarios"/>
	<input type="hidden" name="Pregunta4" value="Nombre Completo"/>
	<input type="hidden" name="Pregunta5" value="Correo Electrónico"/>
	<input type="hidden" name="Pregunta6" value="Tipo de Seguro"/>

<br>
</table>
<br><input type="submit" id="sub" class="button" value="Enviar"><br>
</form>
</fieldset>
</div>
</center>
</body>
</html>


<?php 
}else{
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

</body>
</html>
<body id='main'>
<center><br>
<br><fieldset>
<legend>LA EQUIDAD</legend>
<p style="text-align:justify;  font-weight: bold;"> 
Lo sentimos la encuesta ya no se encuentra disponible, por favor cierre esta pagina.</fieldset>
</center>
</body>
</html>
<?php
}
?>