<?php
	include ("conexion.php");
	
    $url=$_GET["img"];  
	$document=array();
	if($_GET["down"] == 'true'){
		$opcion =  $_GET["tipo_consulta"];	
		$nombre =$_GET["nombre"];
		$sr_todo = $_GET["sr_todo_tramite"];
		
		if($opcion=='pintermediarios'){
			$result=queryQR("select * from planillasintermediarios where srtodo='$sr_todo' order by sr");
			$rows=$result->getArray();
			$document = getAllDocument($rows, $_GET["sr_tramite"]);
			$nombre = getNameDocument($rows, $_GET["sr_tramite"]).".pdf";
			$url = writeImagen($document, $nombre);
			muestraImagen($url, $nombre);
			deletefile($url);
		}
		
		if($opcion=='ptesoreria'){
			$result=queryQR("select * from planillastesoreria where srtodo='$sr_todo' order by sr");
			$rows=$result->getArray();
			$document = getAllDocument($rows, $_GET["sr_tramite"]);
			$nombre = getNameDocument($rows, $_GET["sr_tramite"]).".pdf";
			$url = writeImagen($document, $nombre);
			muestraImagen($url, $nombre);
			deletefile($url);
		}

		if($opcion=='pcontabilidad'){
			$result=queryQR("select * from planillascontabilidad where srtodo='$sr_todo' order by sr");
			$rows=$result->getArray();
			$document = getAllDocument($rows, $_GET["sr_tramite"]);
			$nombre = getNameDocument($rows, $_GET["sr_tramite"]).".pdf";
			$url = writeImagen($document, $nombre);
			muestraImagen($url, $nombre);
			deletefile($url);
		}

		if($opcion=='phojasvida'){
			$result=queryQR("select * from planillashojasdevida where srtodo='$sr_todo' order by sr");
			$rows=$result->getArray();
			$document = getAllDocument($rows, $_GET["sr_tramite"]);
			$nombre = getNameDocument($rows, $_GET["sr_tramite"]).".pdf";
			$url = writeImagen($document, $nombre);
			muestraImagen($url, $nombre);
			deletefile($url);
		}
		
		if($opcion=='ImagenesTramite'){
			muestraImagen($url, $nombre);
		}
	}
	
	if($_GET['rot'] != null){
		header('Content-type: image/jpeg');
		$imagen = new Imagick();
		$imagen->setResolution( 200, 200 ); 
		$imagen->readimage($url.'['.$_GET['pag'].']');		
		$imagen->setImageFormat('jpg');		
		$imagen->rotateimage('#FFFFFF', $_GET['rot']);		 
	
		echo $imagen;
		
		$imagen->clear();
		$imagen->destroy();
	}
	
	if($_POST['op'] == 'MuestraPaginas'){
		$imagen = new Imagick();
		$imagen->readimage($url.'['.$_GET['pag'].']');
		if($imagen->getImageFormat() == 'TIFF')
			echo getNumberPagesTIF($url);
			
		if($imagen->getImageFormat() == 'PDF')
			echo getNumberPagesPDF($url);
		
		$imagen->clear();
		$imagen->destroy();
	}
	
	
function getNumberPagesTIF($url){
	$im = new Imagick();
	$im->pingImage($url);
	return $im->getNumberImages();
}
function getNumberPagesPDF($filepath){
	$fp = @fopen(preg_replace("/\[(.*?)\]/i", "",$filepath),"r");
    $max=0;
    while(!feof($fp)) {
            $line = fgets($fp,255);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)){
                    preg_match('/[0-9]+/',$matches[0], $matches2);
                    if ($max<$matches2[0]) $max=$matches2[0];
            }
    }
    fclose($fp);
    if($max==0){
        $im = new imagick($filepath);
        $max=$im->getNumberImages();
    }
    return $max;
}

function getAllDocument($array, $sr){
	$key=null;
	$ruta=array();

	foreach ($array as $clave=>$valor){
		if ($valor['sr'] == $sr )
			$key=$clave;
	}
	
	for($i=$key; $i<sizeof($array); $i++){
		if ($array[$key]['descripcion'] == $array[$i]['descripcion']){			
			$ruta[$i] = $array[$i]['path'];
			$ruta[$i]=str_replace("\\", "/", $ruta[$i]);
			$ruta[$i] = substr($ruta[$i], strrpos($ruta[$i],'/vol'),strlen($ruta[$i]));
		}else
			break;
	}
	
	for($i=$key; $i>=0; $i--){
		if ($array[$key]['descripcion'] == $array[$i]['descripcion']){
			$ruta[$i] = $array[$i]['path'];
			$ruta[$i]=str_replace("\\", "/", $ruta[$i]);
			$ruta[$i] = substr($ruta[$i], strrpos($ruta[$i],'/vol'),strlen($ruta[$i]));
		}else
			break;
	}
	sort($ruta);
	return $ruta;
}

function getNameDocument($array, $sr){
	$key=null;
	$ruta=array();

	foreach ($array as $clave=>$valor){
		if ($valor['sr'] == $sr )
			return $valor['descripcion'];
	}
	
}

function muestraImagen($url, $name){
	header("Content-Disposition: attachment; filename=".$name);
	$fp=fopen($url, "r");
	fpassthru($fp);
}

function writeImagen($document, $nombre){
	$imagen = new Imagick($document);					
	$imagen->setImageFormat('pdf');	
	$imagen->writeImages('/tmp/'.$nombre.'.pdf', true);	
	$imagen->clear();
	$imagen->destroy();
	return '/tmp/'.$nombre.'.pdf';
}

function deletefile($url){
	unlink($url);
}
?>