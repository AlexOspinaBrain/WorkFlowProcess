<?php

class PersonaController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */

	public function accessRules()
    {
        return array(
            array('allow','actions'=>array('buscaIdentificacion'),'users'=>array('@')),
            array('deny',  'users'=>array('*'),)
        );
    }
	
	public function actionBuscaIdentificacion()
    {
        if (Yii::app()->request->isAjaxRequest) {
            // get the parameter passed via ajax from the _form.php
            $documento = Yii::app()->request->getParam('documento');
            $tipoIdent = Yii::app()->request->getParam('tipo_doc');
 
            if (empty($documento) || empty($tipoIdent)){            
                echo CJSON::encode(array(
                    'error' => 'true',
                    'status' => 'HPI check failed, please enter a registration number '
                ));

                Yii::app()->end();
            }else{
                $person = Persona::model()->find(
                    'tipo_doc=:tipo_doc and documento=:documento', 
                    array(
                        ':documento'=> $documento,
                        ':tipo_doc'=> $tipoIdent,
                    )
                );
                
                if(!$person){                
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
                        $person->primer_nombre = $respuesta->nombre1;
                       
                        if( $respuesta->nombre1 !=null && trim($respuesta->nombre1) !== ''){
                            $person->primer_nombre = $respuesta->nombre1;
                            $person->segundo_nombre = $respuesta->nombre2;
                            $person->primer_apellido = $respuesta->apellido1;
                            $person->segundo_apellido = $respuesta->apellido2;
                            $person->nombre = $respuesta->nombre1 . ' ' . $respuesta->nombre2 . ' ' . $respuesta->apellido1 . ' ' . $respuesta->apellido2 ;
                        }else{  
                            $person->nombre = $respuesta->nombre;
                        }
                        $person->save();  
                    }
                }

                if(isset($person->id_persona)){
                    $attributes = $person->getAttributes();
                    $attributes['encontrado'] = 'true';
                    echo CJSON::encode( $attributes);
                }else{
                    echo CJSON::encode(array(
                        'encontrado' => 'false',
                    ));
                }
            }
        }
    }


        /*$data = Persona::model()->find('documento=:documento', array(':documento'=>(int) $_POST['Persona']['documento']));
        $data = $data->attributes;
        var_dump("asdasd");
        /*$data = CHtml::listData($data,'REQ_Id','REQ_id');
            foreach($data as $id => $value)
            {
                echo CHtml::tag('option',array('value' => $id),CHtml::encode($value),true);
            }*/
    
       
   
}