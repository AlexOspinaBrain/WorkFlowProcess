<?php
session_start();
include("../../config/conexion.php");
	
	$conect = new conexion();

	if($_POST['tipo_doc'] == "NIT")
		$tipoDoc = "Nit";
	else
		$tipoDoc = "CC";

	$limite = calculaLimite();

	//GUARDA RADICACIÓN 
	$tramite = $conect->queryequi("INSERT INTO wf_radicacion (tipo_doc, numero_doc, nombre, email, telefono, direccion, id_ciudad, id_agencia, descripcion, fechareal, id_tipologia, id_tipotramite, 
		id_producto, id_recepcion, id_respuesta, estado, fechahora, fechahora_limite, tiempo_tramite)
		VALUES ('".$tipoDoc."', '".$_POST['id']."', '".$_POST['nombre']."', '".$_POST['correo']."', '".$_POST['telefono']."', '".$_POST['direccion']."', '".$_POST['ciudad']."', 
			'".$_POST['agencia']."', '".$_POST['desc']."', '".$_POST['fecha']."', '2091', '6', '0', '".$_POST['recepcion']."', '1', 'En tramite', now(), '".$limite."', '15')");

	// SELECCIONA ID DE ULTIMO RADICADO
	$radica = $conect->queryequi("SELECT id_radicacion FROM wf_radicacion ORDER BY id_radicacion DESC LIMIT 1");
	$id_rad = pg_fetch_row ($radica);

	//GUARDA PETICION ARL (DATOS ADICIONALES A LA RADICACION)
	$peticion = $conect->queryequi("INSERT INTO wf_radarl (empresa, nit, numero_siniestro, tipo_siniestro, radicado_recibido, id_radicacion, tipo_cliente)
			VALUES ('".$_POST['empresa']."', '".$_POST['nit']."', '".$_POST['siniestro']."', '".$_POST['tipo_sin']."', '".$_POST['radicado']."', '".$id_rad[0]."', '".$_POST['tipo_cli']."')");
	
	//SE GRABA PRIMERA ACTIVIDAD (RADICACION ARL)
	$historial = $conect->queryequi("INSERT INTO wf_historial (id_radicacion, actividad, usuario_cod, fechahora, fechahora_limite, id_workflow, tiempo_actividad) 
			VALUES ('".$id_rad[0]."', 'Radicar', '".$_SESSION['uscod']."', now(), now(), '16473', '0')");

	//SE GRABA ACTIVIDAD PENDIENTE -----------------
	$prox = proximoUser();
	$TiempoHoras = tiempoHoras($id_rad[0], 2);

	//SE GRABA SEGUNDA ACTIVIDAD (RADICACION ARL)
	$result = $conect->queryequi("INSERT INTO wf_historial (id_radicacion, actividad, usuario_cod, fechahora_limite, id_workflow, tiempo_actividad) 
		VALUES (".$id_rad[0].", 'Clasificar', '".$prox['usuario_cod']."', '".$limite."', '".$prox['id_flujo']."', '".$TiempoHoras."')");



	if($_FILES['files']['name'] != null){
		
		$pathlocal =  '/vol2/'.date("Ymd").'/DerechosPeticionARL/';

		if(!file_exists ($pathlocal)){	
			if (!file_exists('/vol2/'.date("Ymd"))){
				mkdir( $pathlocal, 0775, true);
				chmod( '/vol2/'.date("Ymd"), 0775);
				chmod( $pathlocal, 0775);
			}else{
				mkdir( $pathlocal, 0775, true);
				chmod( $pathlocal, 0775);
			}
		}

		$NombreArchivo = $pathlocal.$_FILES['files']['name'];
		if(move_uploaded_file($_FILES['files']['tmp_name'], $NombreArchivo)){
			$result = queryQR("insert into wf_adjuntos (desc_adjunto, ruta_adjunto, tipo_adjunto, id_radicacion) values 
					('".$_FILES['files']['name']."', '".realpath($NombreArchivo)."', 'Adicional', '".$id_rad[0]."')");
		}	
	
	}

	function calculaLimite(){
		$fechaLim = date("Y-m-d");
		//$result = queryADM("SELECT fecha FROM festivos WHERE fecha >= '".$fechaLim."'");
		$result = queryQR("SELECT festivo as fecha FROM wf_festivo WHERE festivo >= '".$fechaLim."'");

		while ($row = $result->FetchRow()){
			$festivos[] = $row['fecha'];
		}

		while ($i < 15) {
			$fechaLim = date('Y-m-d', strtotime("$fechaLim + 1 day"));
			if(!in_array($fechaLim, $festivos))
				$i++;	
		}
		$fechaLim = $fechaLim." 23:59:59";
		return $fechaLim;
	}

	function proximoUser(){
		$qry = queryQR("SELECT u.usuario_cod, f.id_flujo FROM wf_workflowusuarios AS u INNER JOIN wf_flujo AS f ON f.id_flujo = u.id_workflow WHERE f.id_workflow = '16473'");
		$row = $qry->FetchRow();
		return $row;
	}

	function tiempoHoras($tramite, $idActividad){
		$result = queryQR("select * from wf_radicacion join wf_tipotramite USING(id_tipotramite) join wf_tiemposactividad 
					USING(id_tipotramite) where id_actividad = $idActividad and id_radicacion=$tramite");
		$row = $result->FetchRow();

		return $row['tiempo'];
	}




	echo $id_rad[0];

?>
