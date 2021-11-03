<?php
require 'class.phpmailer.php';

try {
	$mail = new PHPMailer(true); 
	$correos =array("informaticawq@gmail.com", "aassddffaassddff1@gmail.com");
	$body             = file_get_contents('contents.html');	
	$body             = preg_replace('/\\\\/','', $body);
	
	$mail->IsSMTP();                           
	$mail->SMTPAuth   = false;             
	$mail->Port       = 25;                    
	//$mail->Host       = "outlook.laequidad.com.co"; 
	$mail->From       = "correspondencia@laequidadseguros.coop";
	$mail->FromName   = "Correspondencia Equidad";
	$mail->Subject  = "Alerta de correspondencia";	
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
	
	echo 'Message has been sent.'.$intentos;
} catch (phpmailerException $e) {
	echo $e->errorMessage();
}
?>