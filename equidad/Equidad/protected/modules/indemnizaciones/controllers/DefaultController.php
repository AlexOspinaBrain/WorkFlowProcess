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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('upload', 'viewDetalle', 'index', 'ramoProducto', 'loadListDocuments', 'test', 'viewArchivo'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('radicar'),
				'expression'=>"Yii::app()->user->checkAccess('4.3.1')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('estudio', 'formEstudio'),
				'expression'=>"Yii::app()->user->checkAccess('4.3.2')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('consultaTramites' ),
				'expression'=>"Yii::app()->user->checkAccess('4.3.3')",
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionRadicar()
	{
		$modelRadica=new Radica('radicar');
		$modelClasificaDoc=new ClasificaDoc;
		$modelHistorial=new Historial;
		$modelRadica->id_agencia_tramita=Yii::app()->user->idAgencia;

		$this->performAjaxValidation($modelRadica);

        if(isset($_POST['Radica'])){        	
			$modelRadica->attributes=$_POST['Radica'];	
			$nextStatus = $modelRadica-> swGetNextStatus();
			$modelRadica->swSetStatus($nextStatus[0]);
					
			if($modelRadica->save()){				
				if(isset($_POST['RadicaDoc'])){
					foreach ($_POST['RadicaDoc'] as $documento) {
						if(empty($documento['ruta']))
							continue;

						$modelRadicaDoc = new RadicaDoc;
						$modelRadicaDoc->attributes=$documento;
						$tempFolder='/vol2/indemnizaciones/'.$modelRadica->id_radica;
												
						if(!is_dir($tempFolder))
					       	mkdir($tempFolder, 0775, true);	

						$moveFile = rename($modelRadicaDoc->ruta, '/vol2/indemnizaciones/'.$modelRadica->id_radica.'/'.basename($modelRadicaDoc->ruta));				 		
					    if($moveFile){
					    	$modelRadicaDoc->id_radica=$modelRadica->id_radica;	
							$modelRadicaDoc->paginas=1;	
							$modelRadicaDoc->ruta='/vol2/indemnizaciones/'.$modelRadica->id_radica.'/'.basename($modelRadicaDoc->ruta);	
							$modelRadicaDoc->save();
					    }
					}
				}				
				$this->redirect(array('consultaTramites'));
			}			
        }

		$this->render('radicar',array(
			'modelRadica'=>$modelRadica,			
			'modelClasificaDoc'=>$modelClasificaDoc,			
			'modelHistorial'=>$modelHistorial,			
		));
	}

	public function actionConsultaTramites(){
		$model=new Radica('search');
		$model->unsetAttributes();  

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];

		//$model->searchHistorialPend= 123;

		//var_dump($model->searchHistorialPend);

		$this->render('consultaTramites',array(
			'model'=>$model,
		));		
	}

	public function actionEstudio(){
		$model=new Radica('search');
		$model->unsetAttributes(); 
		$model->searchHistorialPend= Yii::app()->user->id; 
		$model->status= 'swRadica/en_estudio'; 

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];		

		$this->render('estudio',array(
			'model'=>$model,
		));		
	}

	public function actionViewDetalle($id_radica)
	{
		$model=Radica::model()->findByAttributes(array('id_radica'=>$id_radica));
		//$modelObs=new RadicaObs('search');

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.css'] = false;
        
		$this->renderPartial('_viewDetalle',array(
			'model'=>$model,
			//'modelObs' => $modelObs,
		),false,true);
	}

	public function actionGetProductos($id_ramo)
    {    	
    	if($id_ramo == null)
    		return;

    	$Ramo=Ramo::model()->findByAttributes(array('id_ramo'=>$id_ramo));
    	//var_dump($Ramo->Productos);
    	
	 	echo CHtml::tag('option', array(''), '',true);

		foreach($Ramo->Productos as $producto){
			 echo CHtml::tag(
			 	'option', 
			 	array('value'=>$producto->id_producto),
			 	CHtml::encode($producto->productoCod),
			 	true
			);
        }
    }

    public function actionUpload()
   	{
    	$tempFolder='/tmp/temp_indemnizaciones/';
 		
 		if(!is_dir($tempFolder))
	       	mkdir($tempFolder, 0777, TRUE);
 
        Yii::import("ext.EFineUploader.qqFileUploader");
 
		$uploader = new qqFileUploader();
		$uploader->allowedExtensions = array('jpg','jpeg', 'pdf', 'doc', 'docx');
		$uploader->sizeLimit = 4 * 1024 * 1024;//maximum file size in bytes
		$uploader->setUploadName(uniqid().$uploader->getName ());		
		$result = $uploader->handleUpload($tempFolder);
		$result['filename'] = $uploader->getUploadName();
				
		$uploadedFile=$tempFolder.$result['filename'];
 
        header("Content-Type: text/plain");
        $result['path']=$tempFolder.$result['filename'];
        $result=json_encode($result);
        echo $result;
        Yii::app()->end();
   	}

   	public function actionViewArchivo()
   	{
   		if(isset($_POST['model']) && $_POST['model']=='radica'){
   			$model = Radica::model()->findByPk($_POST['id']);
   			$file = $model->respuesta;
   		}else{
   			$model = RadicaDoc::model()->findByPk($_POST['id']);
   			$file = $model->ruta;
   		}

   		if (file_exists($file)){
   			ob_clean();
   			if(mime_content_type ( $file ) == 'application/pdf' || mime_content_type ( $file )=='image/jpeg')
		    	header('Content-Type: ' .$mimeType[pathinfo($file, PATHINFO_EXTENSION)]);
		    else{
		    	header('Content-Type: application/octet-stream');        		
		    	header('Content-Disposition: attachment; filename='.substr(basename($file), 13) );
			}

		    readfile($file);
        }else{
          	return "El archivo no existe!!!";
        }
       

	    Yii::app()->end();
   	}

   	public function actionFormEstudio($id_radica)
   	{
   		$modelRadica = Radica::model()->findByPk($id_radica);
   		$modelHistorial = new Historial;
   		$modelRadica->scenario='estudio';

   		$this->performAjaxValidation($modelRadica);

        if(isset($_POST['Radica'])){        	
			$modelRadica->attributes=$_POST['Radica'];

			if(!empty($modelRadica->respuesta))	{
				$folder='/vol2/indemnizaciones/'.$modelRadica->id_radica;
												
				if(!is_dir($folder))
					mkdir($folder, 0775, true);	

				$moveFile = rename($modelRadica->respuesta, $folder.'/'.basename($modelRadica->respuesta));
				if ($moveFile) 
					$modelRadica->respuesta= $folder.'/'.basename($modelRadica->respuesta);
				else
					unset($modelRadica->respuesta);				
			}

			$modelRadica->save();
			$this->redirect(array('estudio'));
		}

   		$this->render('formEstudio',array(
			'modelRadica'=>$modelRadica,
			'modelHistorial'=>$modelHistorial,			
		));
   	}

   	public function actionRamoProducto(){//dropDownlist RamoProducto
   		$data=Ramo::model()->findByPk($_POST['ClasificaDoc']['id_ramo']);

   		if(sizeof($data->Productos) > 1)
   			echo CHtml::tag('option', array(), '', true);

	    foreach($data->Productos as $value)
	        echo CHtml::tag('option', array('value'=>$value->id_producto), $value->producto, true);
   	}

   	public function actionLoadListDocuments(){//
   		$modelDocuments=array();
   		$modelRadicaDoc=new RadicaDoc;

   		if(!empty($_POST['Radica']['id_producto']) && !empty($_POST['ClasificaDoc']['id_ramo']) && !empty($_POST['ClasificaDoc']['id_amparo']))
  		$modelDocuments = ClasificaDoc::model()->findAllByAttributes(array(
  			'id_producto'=>$_POST['Radica']['id_producto'],
  			'id_ramo'=>$_POST['ClasificaDoc']['id_ramo'],
  			'id_amparo'=>$_POST['ClasificaDoc']['id_amparo']
  		));  		

  		$this->renderPartial('_formRadicaDocs',array('modelDocuments'=>$modelDocuments, 'modelRadicaDoc'=>$modelRadicaDoc), false, true);   		
   	}   	

    protected function performAjaxValidation($model)
    {
    	if(isset($_POST['ajax']) && $_POST['ajax']==='formRadica'){
			echo CActiveForm::validate($model);
            Yii::app()->end();
        }
	}     

	public function actionTest(){
		
	}
}