<?php
function Trazabilidad($NumTramite, $Usuario, $Devolucion){
	$conect=new conexion();	
	$consulta=$conect->queryequi("select estado from trasacorrespondencia where numtramite='$NumTramite' and (estado='REDIRECCIONADO' OR estado='DEVUELTO') order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	
	if(strlen($NumTramite) != 15)
		return; 
		
	if($row['estado']=='DEVUELTO')
		Devolucion($NumTramite, $Usuario, $UltArea);
	else
		TrazaTramite($NumTramite, $Usuario, $UltArea);
	
}

function TrazaTramite($NumTramite, $Usuario){
	$conect=new conexion();
	
	$consulta=$conect->queryequi("select tra.area, agencia, tra.estado from trasacorrespondencia tra , tblareascorrespondencia are where are.areasid=tra.area and numtramite='$NumTramite' order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	$UltArea = $row['area'];
	$UltAgencia = $row['agencia'];
	$UltEstado = $row['estado'];
	
	$consulta=$conect->queryequi("select rad.area, are.agencia, rad.destinatario from radcorrespondencia rad, tblareascorrespondencia are where are.areasid=rad.area and numtramite='$NumTramite'");
	$row = pg_fetch_array($consulta);
	$AreaDest = $row['area'];
	$AgenciaDest = $row['agencia'];
	$Destinatario = $row['destinatario'];
	
	if($UltArea == $AreaDest){// si es la misma area
		if($Destinatario == $Usuario){
			$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area, usuario) values ('$NumTramite', 'CERRADO', '".$AreaDest."', '$Destinatario')");
		}else{
			$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area, usuario) values ('$NumTramite', 'RECIBIDO DESTINATARIO', '".$AreaDest."', '$Destinatario')");
		}
	}else{
		if($UltAgencia == $AgenciaDest){//si es la misma agencia
			$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$NumTramite', 'RECIBIDO', '".$AreaDest."')");
		}else{//si es diferente agencia
			if($UltEstado == 'ENVIADO'){//si se envia el documento de una agencia a otra
				if($AgenciaDest=='999'){
					$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area, fechahora, usuario) values ('$NumTramite', 'DISTRIBUCION EXTERNA', '".$AreaDest."', now()+'00:00:01', '".$Usuario."')");
					$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$NumTramite', 'CERRADO', '".$UltArea."')");
				}else{
					$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where agencia='$AgenciaDest' and correspondencia='t'");
					$row = pg_fetch_array($consulta);
					$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$NumTramite', 'RECIBIDO CORRESPONDENCIA', '".$row['areasid']."')");
				}					
			}else{
				$consulta=$conect->queryequi("select correspondencia from tblareascorrespondencia where areasid='$UltArea'");
				$row = pg_fetch_array($consulta);
				if($row['correspondencia'] == 't'){// si esta en el area de correspondencia
					$consulta=$conect->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values ('".$NumTramite."', null, null, 'ENVIADO', ".$UltArea.")");
				}else{// si no esta en el area de correspondencia
					$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where agencia='".$UltAgencia."' and correspondencia='t'");
					$row = pg_fetch_array($consulta);
					$consulta=$conect->queryequi("insert into trasacorrespondencia (numtramite, fechahora, usuario, estado, area) values ('".$NumTramite."', null, null, 'RECIBIDO CORRESPONDENCIA', ".$row['areasid'].")");
				}
			}
		}
	}
}

function Devolucion($NumTramite, $Usuario){
	$conect=new conexion();
	
	$consulta=$conect->queryequi("select tra.area, agencia, tra.estado from trasacorrespondencia tra , tblareascorrespondencia are where are.areasid=tra.area and numtramite='$NumTramite' order by fechahora desc limit 1");
	$row = pg_fetch_array($consulta);
	$UltArea = $row['area'];
	$UltAgencia = $row['agencia'];
	$UltEstado = $row['estado'];
	
	$consulta=$conect->queryequi("select are.agencia from trasacorrespondencia tra, tblareascorrespondencia are where are.areasid=tra.area and numtramite='$NumTramite' and estado='RADICADO'");
	$row = pg_fetch_array($consulta);
	$AgenciaDest = $row['agencia'];
	
	if($UltEstado == 'ENVIADO'){//si se envia el documento de una agencia a otra
		$consulta=$conect->queryequi("select areasid from tblareascorrespondencia where agencia='$AgenciaDest' and correspondencia='t'");
		$row = pg_fetch_array($consulta);
		$conect->queryequi("insert into trasacorrespondencia (numtramite, estado, area) values ('$NumTramite', 'RECIBIDO CORRESPONDENCIA', '".$row['areasid']."')");				
	}
}
?>