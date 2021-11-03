<html>
<head>
</head>
<body> 
<?php
require '../config/phpmailer/class.phpmailer.php';
require_once ('../config/conexion.php');
$conect=new conexion();
$consulta=$conect->queryequi("select *, (select fechahora from trasacorrespondencia where fechahora is not null and numtramite = tra.numtramite order by sr desc limit 1) as ultimafecha,
	(select usuario from trasacorrespondencia where usuario is not null and numtramite = tra.numtramite order by sr desc limit 1)as usu 
from trasacorrespondencia tra where fechahora is null  order by numtramite");

	$mails = array();

while ($row = pg_fetch_array($consulta))
{
$fechaini=strtotime($row['ultimafecha']);
$fechaactu=strtotime("now");
$resta = $fechaactu - $fechaini;
$dias_diferencia = $resta / (60 * 60 * 24); 
$consult=$conect->queryequi("select * from cor_estados where nom_estado='".$row['estado']."' ");
$rows = pg_fetch_array($consult);
$co=$conect->queryequi("select usuario_nombres, usuario_priape, usuario_correo from adm_usuario where usuario_cod='".$row['usu']."' ");
$r = pg_fetch_array($co);
	
	if ($dias_diferencia>$rows["tiempo_estado"] && $rows["tiempo_estado"]!=0)
	{
	$nn = "<td align='center'>".$row['numtramite']."</td><td align='center'>". $row['estado']."</td><td align='center'>". (int)$dias_diferencia."</td></tr>";
	$mails[$row['usu']][]=$nn;
	$rr[$row['usu']] ="<br><font face='verdana'>Buenas D&iacute;as <br><br>Se&ntilde;or(a):<br> <br><b>".$r['usuario_nombres']." ".$r['usuario_priape']."</b>,
		usted tienen pendientes los siguientes N&uacute;meros de tramite de correspondencia:<br><br><table border='1'><tr><th>N&uacute;mero de Tramite</th>
		<th>Actividad Pendiente</th><th>D&iacute;as</th></tr><tr>";
	$mm[$row['usu']] = $r['usuario_correo'];
	}
}
	foreach($mails as $key => $m)
	{
		$i=0;	
		while ($i<=count($key))
		{
			$t[$i]= $mails[$key][$i];
			$i++;	
		}
		$p = implode(" ", $t);


		//print_r($rr);
		$body =$rr[$key];
		$body .=$p;
		$body .='</table><br>Por favor realizar la debida gesti&oacute;n lo antes posible ingresando <a href="http://imagine.laequidadseguros.coop/equidad/">Aqui</a>.<br><br>Atn,<br><br>Correspondencia<br><br><br><b>Este es un mensaje enviado autom&aacute;ticamente por el sistema, por favor no responda a este correo.</b><br>';
		//$correos = array("dxmefisto@gmail.com");
		$correos = array($mm[$key]);
	
	try {
		$mail = new PHPMailer(true); 
		
		$mail->IsSMTP();                           
		$mail->SMTPAuth   = false;             
		$mail->Port       = 25;                   
		//$mail->Host       = "outlook.laequidad.com.co"; 
		$mail->Host       = "192.168.241.63"; 
		$mail->From       = "noreply@laequidadseguros.coop";
		$mail->FromName   = "Sistema";
		$mail->Subject  = "Alerta: tramites pendientes de correspondencia";	
	    $mail->AddBCC("william.quitianext@laequidadseguros.coop");
		$mail->MsgHTML($body);
		$mail->IsHTML(true); 
		$intentos=0;
		
		
		foreach( $correos as $destino ) {
			$mail->addAddress( $destino );
		} 
		
		while ((!$mail->Send()) && ($intentos < 5)) {
			sleep(2);
			$intentos=$intentos+1;
		}
		echo "";
	} catch (phpmailerException $e) {
		echo "<script>alert('No se ha podido enviar el e-mail al destinatario debido a un error ');</script>";echo $e->errorMessage();
	}
	}
?>
</table>
</body>
</html>

