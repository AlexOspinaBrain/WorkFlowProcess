<?php

class PersonaController extends Controller
{
	
	public function actionBuscaIdentificacion()
    {
        if (Yii::app()->request->isAjaxRequest && !empty($_POST['tipo_doc']) && !empty($_POST['documento'])) {
            $respuesta = array();
            $documento = Yii::app()->request->getParam('documento');
            $tipoIdent = Yii::app()->request->getParam('tipo_doc');

            $persona=Persona::model()->find('tipo_doc=:tipo_doc and documento=:documento', array('tipo_doc'=>$tipoIdent, 'documento'=>$documento));

            if($persona && $persona->proveedors){
                echo CJSON::encode(array('encontrado' => 'SI', 'id_proveedor' => $persona->proveedors->id_proveedor));
                Yii::app()->end();
            }elseif ($persona) {
                echo CJSON::encode(array(
                    'encontrado' => 'SI', 
                    'id_persona' => $persona->id_persona, 
                    'nombre' => $persona->nombre,
                    'producto' => $persona->producto,
                    'idciudad' => $persona->idciudad
                ));
                Yii::app()->end();
            }

            $url = 'http://webapp.laequidadseguros.coop:8080/WSGetDatosCifinService/WSGetDatosCifin?WSDL';
            $soapClient = new SoapClient($url,  array('exceptions' => true));
            $respuesta = $soapClient->getDatosCifin( array('identificacion'=>$documento , 'tipoIdent'=>($tipoIdent == 'C.C.') ? 1 : 2 ));                    
            $respuesta = $respuesta->return;

            if($respuesta->encontrado === 'SI'){
                $person=new Persona;
                $person->tipo_doc = $respuesta->tipoDoc;
                $person->documento = $respuesta->numDoc;
                $person->producto = $respuesta->actEconomica;
                $person->idciudad = 1;
                $person->fecha_actualizacion = new CDbExpression('NOW()');

                if($respuesta->nombre1 !== null){
                    $person->primer_nombre = $respuesta->nombre1;
                    $person->segundo_nombre = $respuesta->nombre2;
                    $person->primer_apellido = $respuesta->apellido1;
                    $person->segundo_apellido = $respuesta->apellido2;
                    $person->nombre = $respuesta->nombre1 . ' ' . $respuesta->nombre2 . ' ' . $respuesta->apellido1 . ' ' . $respuesta->apellido2 ;
                }else{  
                    $person->nombre = $respuesta->nombre;
                }
                $person->save();  
                echo CJSON::encode(array(
                    'encontrado' => 'SI', 
                    'id_persona' => $persona->id_persona, 
                    'nombre' => $persona->nombre,
                    'producto' => $persona->producto,
                    'idciudad' => $persona->idciudad
                ));
                Yii::app()->end();                      
            }else{
                echo CJSON::encode(array('encontrado' => 'NO'));
                Yii::app()->end();
            }      
   	    }else{
            echo CJSON::encode(array('encontrado' => 'NO', 'error'=>'Datos invalidos'));
            Yii::app()->end();
        }
    }
}