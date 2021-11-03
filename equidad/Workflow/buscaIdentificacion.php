<?php

require_once ('../config/conexion.php');

	$documento = $_REQUEST['documento'];
    $tipoIdent = $_REQUEST['tipo_doc'];

    if (empty($documento) || empty($tipoIdent)){            
        echo CJSON::encode(array(
            'error' => 'true',
            'status' => 'HPI check failed, please enter a registration number '
        ));
        exit;
    }else{

    	//Consultar tabla persona
    	$sql = "SELECT tipo_doc, documento, nombre, telefono, direccion, idciudad FROM persona WHERE tipo_doc = '".$tipoIdent."' AND documento = '".$documento."' ";
 
		$rta = queryQR($sql);

		$row = $rta->FetchRow();
        
        if($row != false){ 

			$salida = '{"rta": "base", "tipoDoc": "'.$row['tipo_doc'].'", "numDoc": "'.$row['documento'].'", "nombre": "'.$row['nombre'].'", "telefono": "'.$row['telefono'].'", "direccion": "'.$row['direccion'].'", "ciudad": "'.$row['idciudad'].'"}';     

        }else{
        	// Consulta Cifin
            $url = 'http://webapp.laequidadseguros.coop:8080/WSGetDatosCifinService/WSGetDatosCifin?WSDL';
            $soapClient = new SoapClient($url,  array('exceptions' => true));
            $respuesta = $soapClient->getDatosCifin( array('identificacion'=>$documento , 'tipoIdent'=>($tipoIdent == 'C.C.') ? 1 : 2 ));                    
            $respuesta = $respuesta->return;

            if($respuesta->encontrado === "SI" && $respuesta->nombre1 !== "DOCUMENTO NO EX"){
      
                if( $respuesta->nombre1 !=null && trim($respuesta->nombre1) !== ''){
                    $nombre = $respuesta->nombre1 . ' ' . $respuesta->nombre2 . ' ' . $respuesta->apellido1 . ' ' . $respuesta->apellido2 ;
                }else{  
                    $nombre = $respuesta->nombre;
                }
                
                //$sqlGuarda = "INSERT INTO (tipo_doc, documento, nombre, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido)";

                $salida ='{"rta": "cifin", "tipoDoc": "'.$respuesta->tipoDoc.'", "numDoc": "'.$respuesta->numDoc.'", "actEcono": "'.$respuesta->actEconomica.'", "nombre": "'.$nombre.'", "nombre1": "'.$respuesta->nombre1.'", "nombre2": "'.$respuesta->nombre2.'", "apell1": "'.$respuesta->apellido1.'", "apell2": "'.$respuesta->apellido2.'"}';

            }else{
            	$salida ='{"rta": "ninguna"}';
            }
        }

        echo utf8_encode($salida);
        //$return["json"] = json_encode($return);
  		//echo json_encode($salida);
    }

?>
