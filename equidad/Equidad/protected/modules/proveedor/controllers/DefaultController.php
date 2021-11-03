<?php

class DefaultController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'crear', 'update', 'admin', 'buscaIdentificacion'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCrear()
	{
		$proveedor=new Proveedor;
		$persona=new Persona;		

		$this->performAjaxValidation($proveedor);

		if(isset($_POST['Proveedor']))
		{
			$proveedor->attributes=$_POST['Proveedor'];

			if(!empty($_POST['Proveedor']['id_persona']))
				$persona = $persona->findByPk($_POST['Proveedor']['id_persona']);

			$persona->attributes = $_POST['Persona'];			

			if($persona->save()){	
				$proveedor->id_persona = $persona->id_persona;
				if($proveedor->save()){				
					$proveedor->proveedorDocs = $_POST['proveedorDocs'];
					$proveedor->saveWithRelated('proveedorDocs');
					$this->redirect(array('view','id'=>$proveedor->id_proveedor));
				}
			}
		}

		$this->render('crear',array('persona'=>$persona, 'proveedor'=>$proveedor));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$proveedor=$this->loadModel($id);

		$this->performAjaxValidation($proveedor);

		if(isset($_POST['Proveedor']))
		{
			$proveedor->attributes=$_POST['Proveedor'];	
			if($proveedor->save()){	
				$proveedor->proveedorDocs = $_POST['proveedorDocs'];
				$proveedor->saveWithRelated('proveedorDocs');
				$this->redirect(array('view','id'=>$proveedor->id_proveedor));
			}
		}

		$this->render('crear',array('persona'=>$proveedor->idPersona, 'proveedor'=>$proveedor));
	}

	

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		//var_dump(Yii::app()->user->checkAccess('deletePostas'));
		$model=new Proveedor('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Proveedor']))
			$model->attributes=$_GET['Proveedor'];

        $this->render('index',array('model'=>$model));

	}

	public function actionBuscaIdentificacion()
    {
		$persona=new Persona();

		if(Yii::app()->getRequest()->getIsAjaxRequest()) {
			$persona->tipo_doc=$_POST['tipo_doc'];
			$persona->documento=$_POST['documento'];
			echo CActiveForm::validate( array( $persona)); 
			Yii::app()->end(); 
		}
        /*if (Yii::app()->request->isAjaxRequest) {
            // get the parameter passed via ajax from the _form.php
            $identificacion = Yii::app()->request->getParam('identificacion');
            $tipoIdent = Yii::app()->request->getParam('tipo_doc');
 
            if ($identificacion == ''){            
                echo CJSON::encode(array(
                    'error' => 'true',
                    'status' => 'HPI check failed, please enter a registration number '
                ));
                // exit;
                Yii::app()->end();
            }else{
                $person = Persona::model()->find('documento=:documento', array(':documento'=> $identificacion));
                
                if(!$person){
                     // url for the soap service
                
                    $url = 'http://webapp.laequidadseguros.coop:8080/WSGetDatosCifinService/WSGetDatosCifin?WSDL';
                    $soapClient = new SoapClient($url,  array('exceptions' => true));
                    $respuesta = $soapClient->getDatosCifin( array('identificacion'=>$identificacion , 'tipoIdent'=>($tipoIdent == 'CC') ? 1 : 2 ));                    

                    if($respuesta->return->encontrado === 'SI'){
                        $nombre = $respuesta->return->nombre;
                        $actEconomica = $respuesta->return->actEconomica;
                        $person=new Persona;
                        $person->tipo_doc = $tipoIdent;
                        $person->documento = $identificacion;
                        $person->nombre = $nombre;  
                        $person->producto = $actEconomica;
                        $person->estado = true;
                        $person->area_id = 40;
                        $person->idciudad = 1;
                        $person->save();
                    }
                }

                if($person){
                    echo CJSON::encode(array(
                        'encontrado' => 'true',
                        'id_persona' => $person->id_persona,
                        'nombre' => $person->nombre,
                    ));
                }else{
                    echo CJSON::encode(array(
                        'encontrado' => 'false',
                    ));
                }
            }*/
        }


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Proveedor the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Proveedor::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Proveedor $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='proveedor-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}