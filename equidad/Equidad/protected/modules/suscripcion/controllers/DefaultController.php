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
				'actions'=>array('alertas'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('getUsersWorkflow', 'ViewDetalle', 'ActualizaBadge', 'codebar', 'viewFormSearch', 'index', 'exportarExcel'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('radicar','viewRadicados', 'viewRadica', 'Anular', 'usersTecnicos'),
				'expression'=>"Yii::app()->user->checkAccess('4.2.1')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('listRecibirRadicados', 'RecibePreRadicados', 'adminTramitar', 'tramitar'),
				'expression'=>"Yii::app()->user->checkAccess('4.2.2')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('listRecibirTramitados' , 'recibeTramitados', 'adminExpedicion', 'expedicion'),
				'expression'=>"Yii::app()->user->checkAccess('4.2.3')",
			),	
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('adminVerificarComercial', 'verificarComercial'),
				'expression'=>"Yii::app()->user->checkAccess('4.2.4')",
			),	
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('consultaTramites', 'prueba'),
				'expression'=>"Yii::app()->user->checkAccess('4.2.5')",
			),			
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionRadicar()
	{
		$modelRadica=new Radica('radica');
		$modelHistorial=new Historial;
		$modelPersona=new Persona;
		$modelObservacion=new RadicaObs;

		$modelRadica->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_POST['Radica'])){
			$modelRadica->attributes=$_POST['Radica'];
			$modelHistorial->attributes=$_POST['Historial'];
			$modelPersona->attributes=$_POST['Persona'];
			$modelObservacion->attributes=$_POST['RadicaObs'];

			if(empty($modelRadica->id_persona) && $modelPersona->validate() ){
				$modelPersona->save();
				$modelRadica->id_persona=$modelPersona->id_persona;
			}

			if($modelRadica->validate() && $modelHistorial->validate()){		
				$modelRadica->save();				
				$this->redirect(array('radicar', 'code'=>$modelRadica->code));				
			}
		}

		$this->render('radicar',array(
			'modelRadica'=>$modelRadica,
			'modelPersona'=>$modelPersona,
			'modelHistorial'=>$modelHistorial,
			'modelObservacion'=>$modelObservacion,
		));
	}

	/**
	 * Listado de ultimos radicados
	 */
	public function actionViewRadicados()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values
		$model->status="swRadica/recibir_tecnico";
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];

		$this->render('viewRadicados',array(
			'model'=>$model,
		));
	}

	/**
	 * Listado de ultimos radicados
	 */
	public function actionViewDetalle($code, $openDialog=false)
	{
		$model=Radica::model()->findByAttributes(array('code'=>$code));
		$modelObs=new RadicaObs('search');

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery-ui.css'] = false;
        
		$this->renderPartial('_viewDetalle',array(
			'model'=>$model,
			'modelObs' => $modelObs,
			'openDialog' => $openDialog,
		),false,true);
	}


	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionViewRadica($code)
	{
		$model=Radica::model()->findByAttributes(array('code'=>$code));

		$modelHistorial=new Historial('search');
		$modelHistorial->code=$model->code;

		$this->render('viewRadica',array(
			'model'=>$model,
			'modelHistorial'=>$modelHistorial,
		));
	}

	public function actionListRecibirRadicados()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values
		$model->status="swRadica/recibir_tecnico";
		$model->searchHistorialPend=Yii::app()->user->id;
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica'])){
			$model->attributes=$_GET['Radica'];
			$model->searchHistorialPend = $_GET['Radica']['searchHistorialPend'];			
		}

		$this->render('ListRecibirRadicados',array(
			'model'=>$model,
		));
	}

	public function actionListRecibirTramitados()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values

		$model->status="swRadica/recibir_expedicion";
		$model->searchHistorialPend=Yii::app()->user->id;
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica'])){
			$model->attributes=$_GET['Radica'];
			$model->searchHistorialPend = $_GET['Radica']['searchHistorialPend'];
		}

		$this->render('ListRecibirTramitados',array(
			'model'=>$model,
		));
	}

	public function actionRecibePreRadicados($code)
	{
		$model=Radica::model()->findByAttributes(array('code'=>$code));

		if( isset ($model)){
			if($model->swIsNextStatus('swRadica/tramitar')){
				$model->swSetStatus('swRadica/tramitar'); 
				$model->save();
				
		 		echo CJSON::encode(array('recibido' => 'true'));
		 	}else
		 		echo CJSON::encode(array('recibido' => 'false'));
		}else
		 	echo CJSON::encode(array('recibido' => 'false'));
	}

	public function actionAnular($code)
	{
		$model=Radica::model()->findByAttributes(array('code'=>$code));

		if( isset ($model)){
			if($model->swIsNextStatus('swRadica/anulado')){
				$model->swSetStatus('swRadica/anulado'); 
				$model->save();
				
		 		echo CJSON::encode(array('anulado' => 'true'));
		 	}else
		 		echo CJSON::encode(array('anulado' => 'false'));
		}else
		 	echo CJSON::encode(array('anulado' => 'false'));
	}

	public function actionRecibeTramitados($code)
	{
		$model=Radica::model()->findByAttributes(array('code'=>$code));

		if( isset ($model)){
			$proximosPaso = $model->swGetNextStatus();
			if($model->swGetStatus()->getLabel() ==  "Recibir para expedición" && sizeof($proximosPaso) == 1){
				
				$model->swSetStatus($proximosPaso[0]); 
				$model->save();
				
		 		echo CJSON::encode(array('recibido' => 'true'));
		 	}else
		 		echo CJSON::encode(array('recibido' => 'false'));
		}else
		 	echo CJSON::encode(array('recibido' => 'false'));
	}

	/**
	 * List tag opcions users activities workflow
	 */
	public function actionGetUsersWorkflow()
    {  
    	if(empty($_POST['Radica']['status']) || empty($_POST['Radica']['id_agencia'])){
    		echo CHtml::tag('option', array(''), '',true);
    		return;
    	}

    	if(($_POST['Radica']['pre']['status']=='swRadica/tramitar' && $_POST['Radica']['status']=='swRadica/expedicion') || 
    		($_POST['Radica']['pre']['status']=='swRadica/radicacion' && $_POST['Radica']['status']=='swRadica/tramitar') ){
    		$user = Yii::app()->db->createCommand()
			->select()		
			->from('adm_usuario usu')
			->where('usuario_cod=:usuario_cod and not usuario_bloqueado', array(':usuario_cod'=>Yii::app()->user->id))
			->queryRow();
		
			echo CHtml::tag(
			 	'option', 
			 	array('value'=>$user['usuario_cod']),
			 	CHtml::encode('@'.$user['usuario_desc'].' '.$user['usuario_nombres'] . ' ' .$user['usuario_priape'] . ' ' .$user['usuario_segape']),
			 	true
			);

			Yii::app()->end();
    	}
    	
    	switch ($_POST['Radica']['status']) {
    		case 'swRadica/recibir_tecnico':
    			$itemName = '4.2.2';
    			break;
    		case 'swRadica/recibir_expedicion':
    			$itemName = '4.2.3';
    			break;
    		case 'swRadica/verificar_comercial':
    			$itemName = '4.2.4';
    			break;    		
    		default:
    			return;
    			break;
    	}

		
		$users = Yii::app()->db->createCommand()
			->select()		
			->from('tblradofi age')
			->join('tblareascorrespondencia are', 'age.codigo=are.agencia')
			->join('adm_usuario usu', 'are.areasid=usu.area')
			->join('adm_usumenu men', 'men.usuario_cod=usu.usuario_cod and not usuario_bloqueado')
			->where('id_agencia=:id_agencia and jerarquia_opcion=:itemname', array(':id_agencia'=>$_POST['Radica']['id_agencia'], ':itemname'=>$itemName))
			->queryAll();

	 	echo CHtml::tag('option', array(''), '',true);

		foreach($users as $user){
			if($user['usuario_cod'] == Yii::app()->user->id && ($itemName == '4.2.3' || $itemName == '4.2.2'))
				continue;
			
			 echo CHtml::tag(
			 	'option', 
			 	array('value'=>$user['usuario_cod']),
			 	CHtml::encode('@'.$user['usuario_desc'].' '.$user['usuario_nombres'] . ' ' .$user['usuario_priape'] . ' ' .$user['usuario_segape']),
			 	true
			);
        }
    }

    public function actionAdminTramitar()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values
		$model->status="swRadica/tramitar";
		$model->searchHistorialPend=Yii::app()->user->id;
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica'])){
			$model->attributes=$_GET['Radica'];
			$model->searchHistorialPend = $_GET['Radica']['searchHistorialPend'];			
		}

		$this->render('adminTramitar',array(
			'model'=>$model,
		));
	}

	public function actionAdminExpedicion()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values
		$model->status="swRadica/expedicion";
		$model->searchHistorialPend=Yii::app()->user->id;
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica'])){
			$model->attributes=$_GET['Radica'];
			$model->searchHistorialPend = $_GET['Radica']['searchHistorialPend'];
		}

		$this->render('adminExpedicion',array(
			'model'=>$model,
		));
	}

	public function actionAdminVerificarComercial()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  // clear any default values
		$model->status="swRadica/verificar_comercial";
		$model->searchHistorialPend=Yii::app()->user->id;
		$model->id_agencia=Yii::app()->user->idAgencia;

		if(isset($_GET['Radica'])){
			$model->attributes=$_GET['Radica'];
			$model->searchHistorialPend = $_GET['Radica']['searchHistorialPend'];
		}

		$this->render('adminVerificarComercial',array(
			'model'=>$model,
		));
	}

	public function actionTramitar($code)
	{
		$modelRadica=Radica::model()->findByAttributes(array('code'=>$code));
		$modelHistorial=new Historial;
		$modelObservacion=new RadicaObs;
		$modelRadica->scenario='tramitar';
		$modelHistorial->scenario='devolucion';

		if(isset($_POST['Radica']) && isset($_POST['Historial'])){
			$modelRadica->status=$_POST['Radica']['status'];
			$modelHistorial->attributes=$_POST['Historial'];
			$modelObservacion->attributes=$_POST['RadicaObs'];

			if($modelRadica->status ==  "swRadica/recibir_expedicion" || $modelRadica->status ==  "swRadica/expedicion"){
				$modelHistorial->scenario=null;
				$modelRadica->attributes=$_POST['Radica'];
				if($modelRadica->validate() && $modelHistorial->validate()){				    
				    $modelRadica->save();
					$this->redirect(array('adminTramitar'));
				}
			}

			if($modelRadica->status ==  "swRadica/verificar_comercial"){
				$modelRadica->scenario=null;
				
				if($modelRadica->validate() && $modelHistorial->validate()){
				  	$modelRadica->save();					
					$this->redirect(array('adminTramitar'));
				}
			}
		}
	
		$this->render('tramitar',array(
			'modelRadica'=>$modelRadica,
			'modelHistorial'=>$modelHistorial,
			'modelObservacion'=>$modelObservacion,
		));
	}

	public function actionExpedicion($code)
	{
		$modelRadica=Radica::model()->findByAttributes(array('code'=>$code));
		$modelHistorial=new Historial;
		$modelObservacion=new RadicaObs;
		$resultOsiris = new PolizasOsiris;
		$modelHistorial->scenario='devolucion';
		$modelRadica->scenario='expedicion';
			
     	if(isset($_POST['Radica']))
			$modelRadica->attributes=$_POST['Radica'];

		if(isset($_POST['Historial']))
			$modelHistorial->attributes=$_POST['Historial'];

		if(isset($_POST['RadicaObs']))
			$modelObservacion->attributes=$_POST['RadicaObs'];
			
		if(isset($_GET['PolizasOsiris']))
			$resultOsiris->attributes=$_GET['PolizasOsiris'];

 		$compania = Yii::app()->dbOsiris->createCommand()
	 		->select('CIA')
  			->from('(osiris.v_vigia_productos)')
  			->where('codigo_producto = :codigo', array(':codigo'=>$modelRadica->idProducto->codigo_osiris))
  			->queryScalar();
	
  		$sql="Begin pr_pruebadocumento('".$compania.$modelRadica->idAgencia->codigo_osiris."',
  									   '".$modelRadica->idProducto->codigo_osiris."',
  									   '".$modelRadica->idPersona->documento."',
  									   '".$modelRadica->poliza."', ''); 
			end;";//sucursal, codigo producto, documento cliente, poliza
		
  		Yii::app()->dbOsiris->createCommand($sql)->execute();
  		
		if(isset($_POST['Radica']) && isset($_POST['Historial']) && isset($_POST['Guardar'])){

			if($modelRadica->status == 'swRadica/cierre'){
				$modelHistorial->usuario_cod = Yii::app()->user->id;
				$modelRadica->sarlaft=$_POST['Radica']['sarlaft']=="1" ? true : false;
				$modelHistorial->scenario=null;				
			}

			if($modelRadica->status == 'swRadica/verificar_comercial' || $modelRadica->status == 'swRadica/recibir_tecnico'){
				$modelRadica->scenario=null;
				$modelRadica->validate();
				$modelHistorial->validate();
			}

			if($modelRadica->validate() && $modelHistorial->validate()){
				$modelRadica->save();					
				$this->redirect(array('adminExpedicion', 'code'=>$modelRadica->code));				
			}
		}
	
		$this->render('expedicion',array(
			'modelRadica'=>$modelRadica,
			'modelHistorial'=>$modelHistorial,
			'modelObservacion'=>$modelObservacion,
			'resultOsiris'=>$resultOsiris,
		));
	}

	public function actionVerificarComercial($code)
	{
		$modelRadica=Radica::model()->findByAttributes(array('code'=>$code));
		$modelHistorial=new Historial;
		$modelObservacion=new RadicaObs;		

		if(isset($_POST['Radica']))
			$modelRadica->attributes=$_POST['Radica'];

		if(isset($_POST['Historial']))
			$modelHistorial->attributes=$_POST['Historial'];

		if(isset($_POST['RadicaObs']))
			$modelObservacion->attributes=$_POST['RadicaObs'];
		


		if(isset($_POST['Radica'])){
			if($filez=$this->uploadMultifile($modelRadica,'susAdjuntos','/tmp/'.date('Ymd').'/Suscripcion/')){
  			 	foreach ($filez as $file) {
  			 		$modelAdjuntos=new Adjuntos;
  			 		$modelAdjuntos->nombre=$file['nombre'];
  			 		$modelAdjuntos->ruta_adjunto=$file['ruta'];
  			 		$modelAdjuntos->code=$modelRadica->code;
  			 		$modelAdjuntos->save();  			 		
  			 	} 	
   			}

			if($modelRadica->status == 'swRadica/cierre')
				$modelHistorial->usuario_cod = Yii::app()->user->id;

			
			if($modelRadica->validate() && $modelHistorial->validate()){		
			    if($modelRadica->save())
					$this->redirect(array('adminVerificarComercial'));
			}
		}
	
		$this->render('verificarComercial',array(
			'modelRadica'=>$modelRadica,
			'modelHistorial'=>$modelHistorial,
			'modelObservacion'=>$modelObservacion,
		));
	}

	public function actionConsultaTramites()
	{
		$model=new Radica('search');
		$model->unsetAttributes();  

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];

		$this->render('consultaTramites',array(
			'model'=>$model,
		));		
	}

	public function actionActualizaBadge(){
		$badges = array();
		$badges['badgeRadicados'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/recibir_tecnico', 'id_agencia'=>Yii::app()->user->idAgencia)));
		$badges['badgeRecibirRadicados'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/recibir_tecnico', 'id_agencia'=>Yii::app()->user->idAgencia)));
		$badges['badgePorTramitar'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/tramitar', 'id_agencia'=>Yii::app()->user->idAgencia)));
		$badges['badgeRecibirTramitados'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/recibir_expedicion', 'id_agencia'=>Yii::app()->user->idAgencia)));
		$badges['badgePorExpedir'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/expedicion', 'id_agencia'=>Yii::app()->user->idAgencia)));
		$badges['badgeVerificarComercial'] = count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/verificar_comercial', 'id_agencia'=>Yii::app()->user->idAgencia)));
		echo CJSON::encode(array("badges"=>$badges));
	}

	public function actionExpedir()
	{
		$this->render('expedir');

	}

	public function actionViewFormSearch($model)
	{
		$this->renderPartial('_formFiltros', array('model'=>$model));
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionExportarExcel(){
		$model=new Radica('search');
		$model->unsetAttributes();  

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];

		$this->renderPartial('_consultaTramitesExcel',array(
			'model'=>$model,
		));				
	}

	public function actionAlertas(){
		/*$model= new LogAlertas;
		$model->code = 'SUS1312160003';
		$model->save();*/

		/*Yii::import('application.extensions.phpmailer.JPhpMailer');
		$mail = new JPhpMailer;
		$mail->IsSMTP();
		$mail->Host = 'outlook.laequidad.com.co';
		$mail->Port = 25;
		$mail->SetFrom('laequidadseguros@laequidadseguros.coop', 'La Equidad Seguros');
		$mail->Subject = 'PHPMailer Test Subject via smtp, basic with authentication';
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
		$mail->MsgHTML('<h1>JUST A TEST!</h1>');
		$mail->AddAddress("William.QuitianExt@laequidadseguros.coop");
		$mail->Send();*/

		$model=Radica::model()->findByPk('SUS1312160006');
		try {
			echo $model->susHistorialPend->estado;
		} catch (Exception $e) {
		echo $e;	
		}
		
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Radica the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Radica::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Radica $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tramitar-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}    

	public function uploadMultifile ($model,$attr,$path)
	{
		if (!is_dir($path))
			mkdir($path, 0777, true);
		
		if($sfile=CUploadedFile::getInstances($model, $attr)){
  			foreach ($sfile as $i=>$file){  
  				$x='';
  				$y=0;
  				$nameFile = $model->code.'.'.$file->getExtensionName();
  				while (file_exists($path.$model->code.$x.'.'.$file->getExtensionName())){
  					$x='('.++$y.')';
  				}
  				
     			$file->saveAs($path.$model->code.$x.'.'.$file->getExtensionName());
     			$ffile[]= array('nombre'=>$file->getName(), 'ruta'=>$path.$model->code.$x.'.'.$file->getExtensionName());
     		}     		
    		return ($ffile);
   		}   		
 	}

 	public function actionUsersTecnicos (){
		$tecnicos = Yii::app()->db->createCommand()
			->select('*')		
			->from('tblradofi age')
			->join('tblareascorrespondencia are', 'age.codigo=are.agencia')
			->join('adm_usuario usu', 'are.areasid=usu.area')
			->join('adm_ usu', 'are.areasid=usu.area')
			->where('id_agencia=:id_agencia', array(':id_agencia'=>$_POST['Radica']['id_agencia']))
			->queryAll();

		/*foreach($data as $value=>$name)
    	{
        echo CHtml::tag('option',
                   array('value'=>$value),CHtml::encode($name),true);
    }*/

		var_dump($tecnicos);
 	}

 	
}
