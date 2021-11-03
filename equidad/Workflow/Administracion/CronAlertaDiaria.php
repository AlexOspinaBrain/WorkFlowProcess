<?php
	require '/var/www/equidad/config/phpmailer/class.phpmailer.php';
	require '/var/www/equidad/config/conexion.php';
	

EnvioAlertasResumenDiarioporVencer();
			
		
	function EnvioAlertasResumenDiarioporVencer(){
		$result = queryQR("select qq.id_proceso, qq.prv
		from(
		select pro.id_proceso , case when extract(day from (rad.fechahora_limite-now())) > 1 then 'PV' else 'NO' end as prv

		from wf_historial his, wf_workflow as wor, wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_tipotramite ttr,
		wf_radicacion  rad, wf_compania com
						
		where his.fechahora is null and rad.id_radicacion=his.id_radicacion and ttr.id_tipotramite=rad.id_tipotramite and 
		ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso AND tip.id_tipologia=wor.id_tipologia and 
		wor.id_workflow=his.id_workflow and com.id_compania=tip.id_compania and rad.estado <> 'Cerrado' and tip.id_compania <> 4 and pro.proceso_desc <> 'SIN PROCESO' and
		pro.responsable is not null
		group by pro.id_proceso ,prv
		) as qq

		where qq.prv = 'PV'");	
						
		while($row = $result->FetchRow()){

			$body = file_get_contents('/var/www/equidad/Workflow/Alertas/AlertaPorVencer.html');	

			$result2 = queryQR("select rad.estado , rad.id_radicacion, to_char(rad.fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as felim, his.id_historial, 
				extract(day from (rad.fechahora_limite-now())) as faltadias, his.actividad, pro.proceso_desc, ser.desc_servicio,  com.des_compania, ttr.desc_tipotramite, pro.responsable, usu.usuario_priape, usu.usuario_nombres
				
				from wf_historial his, wf_workflow as wor, wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_tipotramite ttr, wf_radicacion rad, wf_compania com, adm_usuario as usu
				
				where his.fechahora is null and rad.id_radicacion=his.id_radicacion and ttr.id_tipotramite=rad.id_tipotramite and 
				ser.id_servicio=tip.id_servicio and pro.id_proceso=tip.id_proceso AND tip.id_tipologia=wor.id_tipologia and 
				usu.usuario_cod = his.usuario_cod and
				wor.id_workflow=his.id_workflow and com.id_compania=tip.id_compania and rad.estado <> 'Cerrado' and tip.id_compania <> 4 and pro.proceso_desc <> 'SIN PROCESO' and
				pro.id_proceso = ".$row['id_proceso']." and his.actividad not in ('Aprobacion','Revision')
				group by rad.estado , rad.id_radicacion, felim, his.id_historial, 
				faltadias, his.actividad, pro.proceso_desc, ser.desc_servicio,  com.des_compania, ttr.desc_tipotramite, pro.responsable, usu.usuario_priape, usu.usuario_nombres
				having extract(day from (rad.fechahora_limite-now())) = 1
				order by  rad.id_radicacion desc");

			$usucorr="";
			
			while($row2 = $result2->FetchRow()){
			
				if ($usucorr=="") $usucorr = $row2['responsable'];
				$body.= "<tr align='center'>";
				$body.= "<td>".$row2['id_radicacion']."</td><td>".$row2['felim']."</td><td>".$row2['actividad']."</td><td>".$row2['proceso_desc']."</td><td>".$row2['desc_servicio'].
					"</td><td>".$row2['des_compania']."</td><td>".$row2['desc_tipotramite']."</td>
					<td>".$row2['usuario_nombres']." ".$row2['usuario_priape']."</td>";

				$body.= "</tr>";
			}
			//queryQR("insert into wf_log_alertas (desc_alerta, id_historial, fechahora_alerta) values ('".$row['semaforo']."', '".$row['id_historial']."', now())");
			$body.="</table></p><br>";
			$body.="<p><span style='font-family: arial, helvetica, sans-serif; font-size: small;'>Este es un correo automatico.</span></p></div></div>";

			//$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
			$Enviado=EnviaCorreo($body, "Alerta, WF Por Vencer", $usucorr);

			
		}		
	}
	
	function EnviaCorreo($body, $Asunto, $Destinatario){
		try {
			$correos =array($Destinatario);

			$mail = new PHPMailer(true); 
			
			$mail->IsSMTP();                           
			$mail->SMTPAuth   = false;             
			$mail->Port       = 25;                    
			$mail->Host       = "192.168.241.63"; 
			$mail->From       = "servicio.cliente@laequidadseguros.coop";
			$mail->FromName   = "Workflow Quejas & Reclamos";
			$mail->Subject  = $Asunto;	
			$mail->MsgHTML($body);
			$mail->IsHTML(true); 
			$mail->AddBCC("Escalamiento.Casos@laequidadseguros.coop");
			$mail->AddBCC("Gerencia.Sac@laequidadseguros.coop");
			$intentos=0;
			
			foreach( $correos as $destino ) {
				$mail->addAddress( $destino );
			} 
			
			while ((!$mail->Send()) && ($intentos < 5)) {
				sleep(2);
				$intentos=$intentos+1;
			}
			$salida="Enviado";
		} catch (Exception $e) {
			$salida= "Error: ".$e->errorMessage();
		}
		return $salida;
	}

?>
