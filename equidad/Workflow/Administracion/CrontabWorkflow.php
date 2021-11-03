<?php
	require '/var/www/equidad/config/phpmailer/class.phpmailer.php';
	require '/var/www/equidad/config/conexion.php';
	

EnvioAlertasActividad();
EnvioAlertasTramite();
EnvioAlertasSeguimiento();
	
	//if(intval(date("H")) < 7 || intval(date("H")) > 17)
//		IngresaPersonas();
//		IngresaProductos();
		//CantidadRegistros();
		
		
	function EnvioAlertasTramite(){
		$result = queryQR("select rad.id_radicacion, to_char(rad.fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as fechahora_limite, cast((EXTRACT(EPOCH FROM (rad.fechahora_limite-now()))/3600) as bigint)
						   ||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',rad.fechahora_limite-now()))) || ' sec' as 
						   tiemporestante, his.actividad, COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as  nombres, 
						   ser.desc_servicio, pro.proceso_desc, com.des_compania, ttr.desc_tipotramite, usu.usuario_correo from wf_radicacion rad,wf_historial his, adm_usuario usu, wf_tipologia tip, 
						   wf_servicio ser, wf_proceso pro, wf_compania com, wf_tipotramite ttr where ttr.id_tipotramite=rad.id_tipotramite and com.id_compania=tip.id_compania and 
						   pro.id_proceso=tip.id_proceso and ser.id_servicio=tip.id_servicio and tip.id_tipologia=rad.id_tipologia and his.id_radicacion=rad.id_radicacion and 
						   usu.usuario_cod=his.usuario_cod and his.fechahora is null and estado='En tramite' and rad.fechahora_limite<now()");
		while($row = $result->FetchRow()){
			$result2 = queryQR("select *,  date_part('hours', now()-fechahora_alerta) as transcurso from wf_log_alertas where id_radicacion='".$row['id_radicacion']."' and 
								desc_alerta='Vencida' order by fechahora_alerta desc");
								
			if(!($row2 = $result2->FetchRow()) || (($st_time < $cur_time && $end_time > $cur_time) || ($st_time2 < $cur_time && $end_time2 > $cur_time))){	
				$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #ff0000;"><strong>Estado:</strong>&nbsp;Vencido</span></p>';
			
				$body	= file_get_contents('/var/www/equidad/Workflow/Alertas/AlertaTramite.html');				
				$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
				$body   = str_replace('{id_radicado}',$row['id_radicacion'], $body);
				$body   = str_replace('{fechahora_limite}',$row['fechahora_limite'], $body);
				$body   = str_replace('{tiempo_restante}',$row['tiemporestante'], $body);
				$body   = str_replace('{actividad}',$row['actividad'], $body);
				$body   = str_replace('{persona}',$row['nombres'], $body);
				$body   = str_replace('{proceso}',$row['proceso_desc'], $body);
				$body   = str_replace('{servicio}',$row['desc_servicio'], $body);
				$body   = str_replace('{compania}',$row['des_compania'], $body);
				$body   = str_replace('{tipo_tramite}',$row['desc_tipotramite'], $body);
				$body   = str_replace('{estado}',$estado, $body);
				
				$result3 = queryQR("select usuario_correo, actividad from wf_historial his, adm_usuario usu where usu.usuario_cod=his.usuario_cod and 
									his.id_radicacion='".$row['id_radicacion']."' and (his.actividad='Direccionamiento' or his.actividad='Canalizar') order by actividad desc");
				if($row3 = $result3->FetchRow()){
					$Correo=$row3['usuario_correo'];
					//$Correo="Paula.Ramirez@laequidadseguros.coop";
				}else{					
					$Correo="Escalamiento.Casos@laequidadseguros.coop";
				}
				
				queryQR("insert into wf_log_alertas (desc_alerta, id_radicacion, fechahora_alerta) values ('Vencida', '".$row['id_radicacion']."', now())");
				$Enviado=EnviaCorreo($body, "Alerta, Tramite vencido No. ".$row['id_radicacion'], $Correo);
				$fp = fopen("/var/www/equidad/Workflow/Administracion/LogCrontab.txt","a");
				fwrite($fp, "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."\n");
				fclose($fp);
				echo "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."<br>";
			}
		}
	}
	
	function EnvioAlertasSeguimiento(){
		$result = queryQR("select rad.id_radicacion, to_char(rad.fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as fechahora_limite, cast((EXTRACT(EPOCH FROM (rad.fechahora_limite-now()))/3600) as bigint)
						   ||' hrs '|| abs(date_part('minutes',rad.fechahora_limite-now()))||' min '|| ceiling(abs(date_part('seconds',rad.fechahora_limite-now()))) || ' sec' as 
						   tiemporestante, his.actividad, COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as  nombres, 
						   ser.desc_servicio, pro.proceso_desc, com.des_compania, ttr.desc_tipotramite, usu.usuario_correo 
						   from wf_radicacion rad,wf_historial his, adm_usuario usu, wf_tipologia tip, 
						   wf_servicio ser, wf_proceso pro, wf_compania com, wf_tipotramite ttr 
						   where his.fechahora is null and his.actividad='Seguimiento' and his.fechahora_limite<now() and ttr.id_tipotramite=rad.id_tipotramite and com.id_compania=tip.id_compania and 
						   pro.id_proceso=tip.id_proceso and ser.id_servicio=tip.id_servicio and tip.id_tipologia=rad.id_tipologia and his.id_radicacion=rad.id_radicacion and 
						   usu.usuario_cod=his.usuario_cod");
		while($row = $result->FetchRow()){
			$result2 = queryQR("select *,  date_part('hours', now()-fechahora_alerta) as transcurso from wf_log_alertas where id_radicacion='".$row['id_radicacion']."' and 
								desc_alerta='Seguimiento' order by fechahora_alerta desc");
								
			if(!($row2 = $result2->FetchRow()) || (($st_time < $cur_time && $end_time > $cur_time) || ($st_time2 < $cur_time && $end_time2 > $cur_time))){	
				$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #ff0000;"><strong>Estado:</strong>&nbsp;Vencido</span></p>';
			
				$body	= file_get_contents('/var/www/equidad/Workflow/Alertas/AlertaSeguimiento.html');				
				$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
				$body   = str_replace('{id_radicado}',$row['id_radicacion'], $body);
				$body   = str_replace('{fechahora_limite}',$row['fechahora_limite'], $body);
				$body   = str_replace('{tiempo_restante}',$row['tiemporestante'], $body);
				$body   = str_replace('{actividad}',$row['actividad'], $body);
				$body   = str_replace('{persona}',$row['nombres'], $body);
				$body   = str_replace('{proceso}',$row['proceso_desc'], $body);
				$body   = str_replace('{servicio}',$row['desc_servicio'], $body);
				$body   = str_replace('{compania}',$row['des_compania'], $body);
				$body   = str_replace('{tipo_tramite}',$row['desc_tipotramite'], $body);
				$body   = str_replace('{estado}',$estado, $body);
				

					//$Correo=$row['usuario_correo'];
					//$Correo="contactossygocolombia@gmail.com";
					//$Correo="Paula.Ramirez@laequidadseguros.coop";
				$Correo="nidia.munoz@laequidadseguros.coop";

				
				queryQR("insert into wf_log_alertas (desc_alerta, id_radicacion, fechahora_alerta) values ('Seguimiento', '".$row['id_radicacion']."', now())");
				$Enviado=EnviaCorreo($body, "Alerta, Tramite en Seguimiento final No. ".$row['id_radicacion'], $Correo);
				$fp = fopen("/var/www/equidad/Workflow/Administracion/LogCrontab.txt","a");
				fwrite($fp, "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."\n");
				fclose($fp);
				echo "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."<br>";
			}
		}
	}

	function EnvioAlertasActividad(){
		$result = queryQR("select rad.id_radicacion, to_char(his.fechahora_limite,'yyyy-MM-dd HH:MI:SS AM') as fechahora_limite, his.id_historial, 
				cast((EXTRACT(EPOCH FROM (his.fechahora_limite-now()))/3600) as bigint)||' hrs '|| abs(date_part('minutes',his.fechahora_limite-now()))||' min '|| 
				ceiling(abs(date_part('seconds',his.fechahora_limite-now()))) || ' sec' as tiemporestante,
				his.actividad, pro.proceso_desc, ser.desc_servicio,  com.des_compania, ttr.desc_tipotramite,
				( case when (EXTRACT(EPOCH FROM(his.fechahora_limite-now()))/3600)>(his.tiempo_actividad/2) then 'Asignado' 
				ELSE(case when (EXTRACT(EPOCH FROM(his.fechahora_limite-now()))/3600)>0 then 'Proxima a vencer' else 'Vencida' end) end )  as 
				semaforo, usuario_correo 
				from wf_historial his, wf_workflow wor, wf_tipologia tip, wf_proceso pro, wf_servicio ser, wf_tipotramite ttr,
				wf_radicacion rad, wf_compania com, adm_usuario usu 
				where his.usuario_cod=usu.usuario_cod and rad.id_radicacion=his.id_radicacion and ttr.id_tipotramite=rad.id_tipotramite and ser.id_servicio=tip.id_servicio 
				and pro.id_proceso=tip.id_proceso AND tip.id_tipologia=wor.id_tipologia and wor.id_workflow=his.id_workflow and com.id_compania=tip.id_compania and his.fechahora is null");	
						
		while($row = $result->FetchRow()){
			$result2 = queryQR("select *,  date_part('hours', now()-fechahora_alerta) as transcurso from wf_log_alertas where id_historial='".$row['id_historial']."' and desc_alerta='".$row['semaforo']."' order by fechahora_alerta desc");
			if(!($row2 = $result2->FetchRow())){	
				
				if($row['semaforo'] == "Asignado")
					$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #1fc129;"><strong>Estado:</strong>&nbsp;'.$row['semaforo'].'</span></p>';
					
				if($row['semaforo'] == "Proxima a vencer")
					$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #ff6600;"><strong>Estado:</strong>&nbsp;'.$row['semaforo'].'</span></p>';
				
				if($row['semaforo'] == "Vencida")
					$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #ff0000;"><strong>Estado:</strong>&nbsp;'.$row['semaforo'].'</span></p>';
			
				
				$body	= file_get_contents('/var/www/equidad/Workflow/Alertas/AlertaActividad.html');				
				$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
				$body   = str_replace('{id_radicado}',$row['id_radicacion'], $body);
				$body   = str_replace('{fechahora_limite}',$row['fechahora_limite'], $body);
				$body   = str_replace('{tiempo_restante}',$row['tiemporestante'], $body);
				$body   = str_replace('{actividad}',$row['actividad'], $body);
				$body   = str_replace('{proceso}',$row['proceso_desc'], $body);
				$body   = str_replace('{servicio}',$row['desc_servicio'], $body);
				$body   = str_replace('{compania}',$row['des_compania'], $body);
				$body   = str_replace('{tipo_tramite}',$row['desc_tipotramite'], $body);
				$body   = str_replace('{estado}',$estado, $body);
				queryQR("insert into wf_log_alertas (desc_alerta, id_historial, fechahora_alerta, id_radicacion)
				 values ('".$row['semaforo']."', '".$row['id_historial']."', now(), '".$row['id_radicacion']."')");
				$Enviado=EnviaCorreo($body, "Alerta, actividad pendiente del tramite No. ".$row['id_radicacion'], $row['usuario_correo']);
				$fp = fopen("/var/www/equidad/Workflow/Administracion/LogCrontab.txt","a");
				fwrite($fp, "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."\n");
				fclose($fp);
				echo "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."<br>";
			}else{
				if((($st_time < $cur_time && $end_time > $cur_time) || ($st_time2 < $cur_time && $end_time2 > $cur_time)) && $row['semaforo']=="Vencida"){
					if($row['semaforo'] == "Vencida")
						$estado = '<p><span style="font-family: arial, helvetica, sans-serif; font-size: small; background-color: #ff0000;"><strong>Estado:</strong>&nbsp;'.$row['semaforo'].'</span></p>';			
				
					$body	= file_get_contents('/var/www/equidad/Workflow/Alertas/AlertaActividad.html');				
					$body 	= mb_convert_encoding($body, 'ISO-8859-1', mb_detect_encoding($body, 'UTF-8, ISO-8859-1', true));
					$body   = str_replace('{id_radicado}',$row['id_radicacion'], $body);
					$body   = str_replace('{fechahora_limite}',$row['fechahora_limite'], $body);
					$body   = str_replace('{tiempo_restante}',$row['tiemporestante'], $body);
					$body   = str_replace('{actividad}',$row['actividad'], $body);
					$body   = str_replace('{proceso}',$row['proceso_desc'], $body);
					$body   = str_replace('{servicio}',$row['desc_servicio'], $body);
					$body   = str_replace('{compania}',$row['des_compania'], $body);
					$body   = str_replace('{tipo_tramite}',$row['desc_tipotramite'], $body);
					$body   = str_replace('{estado}',$estado, $body);
					queryQR("insert into wf_log_alertas (desc_alerta, id_historial, fechahora_alerta, id_radicacion) values ('".$row['semaforo']."', '".$row['id_historial']."', now(), '".$row['id_radicacion']."')");
					$Enviado=EnviaCorreo($body, "Alerta, actividad pendiente del tramite No. ".$row['id_radicacion'], $row['usuario_correo']);
					$fp = fopen("/var/www/equidad/Workflow/Administracion/LogCrontab.txt","a");
					fwrite($fp, "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."\n");
					fclose($fp);
					echo "[ ".date("d-m-Y H:i:s")." ] : Envia alerta ".$row['semaforo']." tramite no.".$row['id_radicacion']." Status: ".$Enviado."<br>";
				}
			}
		}		
	}
	
	function EnviaCorreo($body, $Asunto, $Destinatario){
		try {
			$correos =array($Destinatario);

			$mail = new PHPMailer(true); 
			
			$mail->IsSMTP();                           
			$mail->SMTPAuth   = false;             
			$mail->Port       = 25;                    
			//$mail->Host       = "outlook.laequidad.com.co"; 
			$mail->Host       = "192.168.241.63"; 
			$mail->From       = "servicio.cliente@laequidadseguros.coop";
			$mail->FromName   = "Workflow Quejas & reclamos";
			$mail->Subject  = $Asunto;	
			$mail->MsgHTML($body);
			$mail->IsHTML(true); 
			
			$mail->AddBCC("Escalamiento.Casos@laequidadseguros.coop");
			$mail->AddBCC("Gerencia.Sac@laequidadseguros.coop");
			//$mail->AddBCC("paula.ramirez@laequidadseguros.coop");
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

	function IngresaPersonas(){		
		$db = NewADOConnection("oci8");
		$db->charSet = 'we8iso8859p1';
		$ls=$db->Connect("(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.200.12)(PORT = 1521))) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME= osiris)))", "wfimagine", "wfimagine");
		$query="select ltrim(rtrim(pmolano.fc_traer500(codigo,'nit'),' '),'0') as nit ,nombre, osiris.fc_traerdet502(codigo, '630') as Direccion,
				osiris.fc_traerdet502(codigo, '700') as Telefono, osiris.fc_traerdet502(codigo, '831') as Correo from S03500";
				
		$result = queryQR("select * from wf_log_osiris where desc_table='Personas' order by id_log desc");
		$row = $result->FetchRow();
		if ($row['fecha_fin'] != null){
			queryQR("insert into wf_log_osiris (desc_table, num_inicio, num_fin, fecha_inicio) values ('Personas', '".$row['num_fin']."','".(intval($row['num_fin'])+5000)."', now())");
			
			$rs = $db->SelectLimit($query, 5000,$row['num_fin']);
			if (!$rs) {
				print $db->ErrorMsg(); // Displays the error message if no results could be returned
			} else {
				while ($row = $rs->FetchRow()) {
					$result=queryQR("select * from wf_persona where identificacion='".str_replace(array("'", "\"", "\\"), '`',$row['NIT'])."'");
					if($result->RecordCount() == 0){
						queryQR("insert into wf_persona (identificacion, nombres, telefono, direccion, correo) values
								('".str_replace(array("'", "\"", "\\"), '`',$row['NIT'])."', '".
									str_replace(array("'", "\"", "\\"), '`',$row['NOMBRE'])."', '".
									str_replace(array("'", "\"", "\\"), '`',$row['TELEFONO'])."', '".
									str_replace(array("'", "\"", "\\"), '`',$row['DIRECCION'])."', '".
									str_replace(array("'", "\"", "\\"), '`',$row['CORREO'])."')");
								
						echo("insert into wf_persona (identificacion, nombres, telefono, direccion, correo) values
							('".str_replace(array("'", "\"", "\\"), '`',$row['NIT'])."', '".
								str_replace(array("'", "\"", "\\"), '`',$row['NOMBRE'])."', '".
								str_replace(array("'", "\"", "\\"), '`',$row['TELEFONO'])."', '".
								str_replace(array("'", "\"", "\\"), '`',$row['DIRECCION'])."', '".
								str_replace(array("'", "\"", "\\"), '`',$row['CORREO'])."')<br><br>");	
					}
				}  // end while
			} // end else
			queryQR("update wf_log_osiris set fecha_fin=now() where desc_table='Personas' and fecha_fin is null");
		}
	}
	
	function IngresaProductos(){		
		$db = NewADOConnection("oci8");
		$db->charSet = 'we8iso8859p1';
		$ls=$db->Connect("(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.200.12)(PORT = 1521))) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME= osiris)))", "wfimagine", "wfimagine");
		$query= "select 
				fecren as InicioTecnico,
				fecini as InicioCertificado,
				osiris.fc_codpla(a.codpla) as Descripcion,
				poliza,
				certif, 
				orden,
				SUCUR,
				a.tomador,
				pmolano.fc_traer500(substr(a.sucur,2),'nombre') as Radicada,
				ltrim(rtrim(pmolano.fc_traer500(a.tomador,'nit'),' '),'0') as NitTomador,
				pmolano.fc_traer500(a.tomador,'nombre') as NombreTomador,
				ltrim(rtrim(pmolano.fc_traer500(a.asegurado,'nit'),' '),'0') as NitAsegurado,
				pmolano.fc_traer500(a.asegurado,'nombre') as NombreAsegurado,
				ltrim(rtrim(pmolano.fc_traer500(a.beneficiario,'nit'),' '),'0') as NitBeneficiario,
				pmolano.fc_traer500(a.beneficiario,'nombre') as NombreBeneficiario,
				ltrim(rtrim(pmolano.fc_traer500(a.agente,'nit'),' '),'0') as NitIntermediario,
				pmolano.fc_traer500(a.agente,'nombre') as NombreIntermediario,
				CODPLA AS PRODUCTO,
				pmolano.fc_traer500(substr(a.sucur,2),'nombre') as DescripcionPlan,
				fecter as FinTecnico,
				Fecter as FinCertificado,
				decode( substr(sucur,1,1),'1','Generales','Vida') as Compania,
				tipcer,
				estado,
				osiris.fc_traerdet502(a.tomador, '00000060') as cod_ciudad,
				osiris.fc_traer105('00000060', osiris.fc_traerdet502(a.tomador, '00000060')) as Ciudad,
				osiris.fc_traer105('00000065', osiris.fc_traerdet502(a.tomador, '00000065')) as Departamento,
				osiris.fc_traerdet502(a.tomador, '630') as Direccion,
				osiris.fc_traerdet502(a.tomador, '700') as Telefono,
				osiris.fc_traerdet502(a.tomador, '831') as CorreoElectronico,
				osiris.fc_traerdet502(a.beneficiario, '630') as Direccionben,
				osiris.fc_traerdet502(a.beneficiario, '700') as Telefonoben,
				osiris.fc_traerdet502(a.beneficiario, '831') as CorreoElectronicoben,
				osiris.fc_traerdet502(a.asegurado, '630') as Direccionasegu,
				osiris.fc_traerdet502(a.asegurado, '700') as Telefonoasegu,
				osiris.fc_traerdet502(a.asegurado, '831') as CorreoElectronicoasegu
				FROM OSIRIS.S03020 A";	
				
		$result = queryQR("select * from wf_log_osiris where desc_table='Productos' order by id_log desc");
		$row = $result->FetchRow();
		if ($row['fecha_fin'] != null){
			queryQR("insert into wf_log_osiris (desc_table, num_inicio, num_fin, fecha_inicio) values ('Productos', '".$row['num_fin']."','".(intval($row['num_fin'])+5000)."', now())");
			
			$rs = $db->SelectLimit($query, 5000,$row['num_fin']);
			if (!$rs) {
				print $db->ErrorMsg(); // Displays the error message if no results could be returned
			} else {
				while ($row = $rs->FetchRow()) {
					$result=queryQR("select * from wf_producto where poliza='".$row['POLIZA']."' AND nittomador='".$row['NITTOMADOR']."' 
						and nitasegurado='".str_replace(array("'", "\""), '`',$row['NITASEGURADO'])."' and nitbeneficiario='".str_replace(array("'", "\""), '`',$row['NITBENEFICIARIO'])."'");
					
					if($result->RecordCount() == 0){
						queryQR("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
							nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, ciudad, departamento, iniciocertificado, fincertificado) 
							values ('".$row['POLIZA']."', '".$row['INICIOTECNICO']."', '".$row['FINTECNICO']."', '".$row['DESCRIPCION']."', '".$row['RADICADA']."', '".$row['NITTOMADOR']."',
							'".$row['NOMBRETOMADOR']."', '".str_replace(array("'", "\""), '`',$row['NITASEGURADO'])."', '".$row['NOMBREASEGURADO']."', '".str_replace(array("'", "\""), '`',$row['NITBENEFICIARIO'])."', '".$row['NOMBREBENEFICIARIO']."', 
							'".$row['NITINTERMEDIARIO']."', '".$row['NOMBREINTERMEDIARIO']."', '".$row['COMPANIA']."', '".$row['TIPOCER']."', '".$row['ESTADO']."', '".$row['CIUDAD']."', 
							'".$row['DEPARTAMENTO']."', '".$row['INICIOTECNICO']."', '".$row['FINTECNICO']."')");
						
			
						echo("insert into wf_producto (poliza, iniciotecnico, fintecnico, descripcion, radicada, nittomador, nombretomador, nitasegurado, nombreasegurado, 
							nitbeneficiario, nombrebeneficiario, nitintermediario, nombreintermediario, compania, tipocer, estado, ciudad, departamento, iniciocertificado, fincertificado) 
							values ('".$row['POLIZA']."', 
							'".$row['INICIOTECNICO']."', '".$row['FINTECNICO']."', '".$row['DESCRIPCION']."', '".$row['RADICADA']."', '".$row['NITTOMADOR']."', '".$row['NOMBRETOMADOR']."', 
							'".$row['NITASEGURADO']."', '".$row['NOMBREASEGURADO']."', '".str_replace(array("'", "\""), '`',$row['NITBENEFICIARIO'])."', '".$row['NOMBREBENEFICIARIO']."', '".$row['NITINTERMEDIARIO']."', 
							'".$row['NOMBREINTERMEDIARIO']."', '".$row['COMPANIA']."', '".$row['TIPOCER']."', '".$row['ESTADO']."', '".$row['CIUDAD']."', '".$row['DEPARTAMENTO']."', '".
							$row['INICIOTECNICO']."', '".$row['FINTECNICO']."')<br><br>");
					}
				}  // end while
			} // end else
			queryQR("update wf_log_osiris set fecha_fin=now() where desc_table='Productos' and fecha_fin is null");
		}
	}
	
	function CantidadRegistros(){		
		$db = NewADOConnection("oci8");
		$db->charSet = 'we8iso8859p1';
		$ls=$db->Connect("(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.200.12)(PORT = 1521))) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME= osiris)))", "wfimagine", "wfimagine");
		$rs = $db->Execute( "select count(*) FROM OSIRIS.S03020 ");
		$row = $rs->FetchRow();
		echo "<br>Cantidad de productos: ".$row[0];
		
		$rs = $db->Execute( "select count(*) FROM OSIRIS.S03500 ");
		$row = $rs->FetchRow();
		echo "<br>Cantidad de personas: ".$row[0];
	}
?>
