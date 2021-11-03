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
				'actions'=>array('index'),
				'users'=>array('*'),
			),		
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('upload','viewDetalle', 'viewArchivo','devuelto','estudio','vehi','informeAjaxatendidos'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('radicar','findTipologia'),
				'expression'=>"Yii::app()->user->checkAccess('4.5.1')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('estudio', 'formEstudio' ),
				'expression'=>"Yii::app()->user->checkAccess('4.5.2')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('consultaTramites','consultaTramitesExcel' ),
				'expression'=>"Yii::app()->user->checkAccess('4.5.3')",
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('devuelto','formEstudio' ),
				'expression'=>"Yii::app()->user->checkAccess('4.5.4')",
			),			
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('formReasigna','formCestado','informes','informeEntramitexls'),
				'expression'=>"Yii::app()->user->checkAccess('4.5.5')",
			),						
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('vehi','formEstudio'),
				'expression'=>"Yii::app()->user->checkAccess('4.5.7')",
			),					
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	public function actionIndex()
	{
		if(Yii::app()->user->isGuest){
			$model=new LoginForm;
			$model->setAttributes(array(
				'username'=>$_SESSION['usuario_desc'], 
				'password'=>'1'
			));
			$model->login();
			
		}
		
		
		$this->render('index');
	}



	public function actionRadicar()
	{
		$modelRadica=new Radica('radicar');
		$modelHistorial=new Historial('radicar');
		$modelHistorialee=new Historial;
		$modelFile = new File;
		
		$archval=0;

		$this->performAjaxValidation($modelRadica);
		$this->performAjaxValidation($modelHistorial);

		$modelHistorial->attributes = $_POST['Historial'];
		
        if(isset($_POST['Radica'])){  

			//valida si viene archivo y que venga con nombre valido.
                        $archchsv = CUploadedFile::getInstancesByName('docs_multiple');
                        if (count($archchsv)!=0) {
                        	
    						foreach($archchsv as $archchv => $p) {                	

							$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_. "; 
								   for ($j=0; $j<strlen($p->name); $j++){ 
								      if (strpos($permitidos, substr($p->name,$j,1))===false){ 
								         $archval++;
								      } 
								   } 
	                        }
                    	}//else
                    		//$archval=99;


			$modelRadica->attributes=$_POST['Radica'];
			$modelRadica->intermediario = strtoupper($_POST['Radica']['intermediario']);
			$modelRadica->ejecutivov = strtoupper($_POST['Radica']['ejecutivov']);
			$modelRadica->representante = strtoupper($_POST['Radica']['representante']);
			$modelRadica->razonsocial = strtoupper($_POST['Radica']['razonsocial']);
			$modelRadica->usuario_cod=Yii::app()->user->id;;
			$nextStatus = $modelRadica->swGetNextStatus();
			$modelRadica->swSetStatus($nextStatus[0]);

				$modelHistorial->fecha_termino = new CDbExpression('NOW()');
				$modelHistorial->usuario_cod = Yii::app()->user->id;
				$modelHistorial->estado = "Radicación";
				$modelHistorial->fecha_inicio = new CDbExpression('NOW()');
				$modelHistorial->fecha_limite = new CDbExpression('NOW()');			

		  if($archval==0){
			if ($modelRadica->validate() && $modelHistorial->validate()){
 			 if($modelRadica->save()){	

				
				$modelHistorial->id_radica = $modelRadica->id_radica;
				$modelHistorial->save();

				$modelHistorialee->id_radica = $modelRadica->id_radica;
				$modelHistorialee->usuario_cod = $modelRadica->getUserEnEstudio();
				$modelHistorialee->estado = "En tramite";
				$modelHistorialee->fecha_inicio = new CDbExpression('NOW()');
				$modelHistorialee->save();
					
					//carga los archivos				
					if($archval==0){
                        $archchs = 	CUploadedFile::getInstancesByName('docs_multiple');
                        
                        if (count($archchs)!=0) {
    						foreach($archchs as $archch => $i) {                	
	                        	$modelFilex = new File;
								$modelFilex->path_file=$i->tempName;
								$modelFilex->name_file=$i->name;
	                        	$modelFilex->id_radica = $modelRadica->id_radica;
	                        	$modelFilex->save();
	                        }
                    	}
                    }



				$this->redirect(array('radicar', 'id_radica'=>$modelRadica->id_radica));			
				
			 }
		    }
		  }
        }


		$this->render('radicar',array(
			'modelRadica'=>$modelRadica,			
			'modelHistorial'=>$modelHistorial,	
			'modelFile'=>$modelFile,
			'archval'=>$archval,
			
		));
	}

	public function actionFindTipologia(){


		if(empty($_POST[Radica][idproceso]))
			return;

		$model=Proceso::model()->findByPk($_POST[Radica][idproceso]);
		echo CHtml::tag('option',array(),'',true);

		foreach($model->wfarlTipologias as $value)
			echo CHtml::tag('option',array('value'=>$value->idtipologia),$value->tipologia,true);

	}



	public function actionUpload()
   	{
   		$file = new File;
    	$tempFolder='/tmp/radicador/';
 		
 		if(!is_dir($tempFolder))
	       	mkdir($tempFolder, 0777, TRUE);
 
        	Yii::import("ext.EFineUploader.qqFileUploader");
 
		$uploader = new qqFileUploader();
		$uploader->allowedExtensions = array('pdf', 'jpg', 'jpeg', 'tif', 'doc','docx','xls','xlsx','ppt','pptx');
		$uploader->sizeLimit = 2 * 1024 * 1024;
		$result = $uploader->handleUpload($tempFolder);
		//$result['filename'] = $uploader->getUploadName();
		$ext = end( explode('.', $uploader->getUploadName()) );
		$nombre = $this->cambiaString(basename($uploader->getUploadName(), $ext));

		rename ($tempFolder.$uploader->getUploadName(), $tempFolder.$nombre.$ext);
		$result['filename'] = $nombre.$ext;
				
		$uploadedFile=$tempFolder.$result['filename'];
		$data = pathinfo($_FILES ["qqfile"]['name']);

        $result['path']=$tempFolder.$result['filename'];
        $file->name_file=$data['filename'];
        $file->path_file=$result['path'];        
        $result['model']=  CHtml::activeTextField($file,"[count]name_file");
        $result['model'].= CHtml::tag('button', array("class"=>"btn btn-danger", "type"=>"button", "onClick"=>new CJavaScriptExpression("\$('#File_count_container').remove()")), '<i class="icon-remove icon-white"></i>');        
        $result['model'].= CHtml::activeHiddenField($file,"[count]path_file");
        $result['model'].= CHtml::tag("div",array("class"=>"help-inline error", "id"=>"File_count_name_file_em_", "style"=>"display:none"));       
        $result['model']= CHtml::tag("div",array("id"=>"File_count_container", "style"=>"padding-bottom: 5px;"), $result['model']);       
        $result['model'].= CHtml::script("
        	var settings = $('#formRadica').data('settings');
			settings.attributes.push({
				'id':'File_count_name_file',
				'inputID':'File_count_name_file',
				'errorID':'File_count_name_file_em_',
				'model':'File',
				'name':'name_file',
				'enableAjaxValidation':true,
				'status':1
			});

			$('#formRadica').data('settings', settings);");  

        $result['model']=  htmlspecialchars(str_replace("\"", "'", $result['model']));
        echo json_encode($result);
   	}

   	public function cambiaString($cadena){

    	$cadena = trim($cadena);
		$cadena = strtr($cadena,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
		//$cadena = strtr($cadena,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz");
		$cadena = preg_replace('#([^.a-z0-9]+)#i', '-', $cadena);
        $cadena = preg_replace('#-{2,}#','-',$cadena);
        $cadena = preg_replace('#-$#','',$cadena);
        $cadena = preg_replace('#^-#','',$cadena);

		return $cadena;
    }


	public function actionConsultaTramites(){
		$model=new Radica('search');
		$model->unsetAttributes();  

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];
		

		$this->render('consultaTramites',array(
			'model'=>$model,
		));		
	}

  

	public function actionConsultaTramitesExcel(){
		$model=new Radica('search');
		$model->unsetAttributes();  
		
		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];
		else{
			$model->fechai=$_GET['fechai'];
			$model->fechaf=$_GET['fechaf'];
		}

		$this->render('consultaTramitesExcel',array('model'=>$model,'info'=>$_GET['info']));
	}    

	public function actionDevuelto(){
		$model=new Radica('search');
		$model->unsetAttributes(); 

		if(Yii::app()->user->checkAccess('4.5.5')==false)
			$model->usuarioPend= Yii::app()->user->id;
		
		$model->status= 'swRadica/en_devolucion'; 

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];		

		$this->render('estudio',array(
			'model'=>$model,
		));		
	}

	public function actionEstudio(){
		$model=new Radica('search');
		$model->unsetAttributes(); 
		//var_dump(Yii::app()->user->checkAccess('4.5.5'));
		//Yii::app()->end();

		if(Yii::app()->user->checkAccess('4.5.5')==false )
			$model->usuarioPend= Yii::app()->user->id;

		$model->status = 'swRadica/en_tramite'; 
		//$model->condition =	"id_producto <> '80' and id_Producto <> '81'";
		
		

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];		

		$this->render('estudio',array(
			'model'=>$model,
		));		
	}

	public function actionVehi(){
		$model=new Radica('search');
		$model->unsetAttributes(); 
		

		if(Yii::app()->user->checkAccess('4.5.5')==false )
			$model->usuarioPend= Yii::app()->user->id;

		$model->status = 'swRadica/en_estudio'; 
		$model->condition =	"(id_producto = '80' or id_Producto = '81')";
		
		

		if(isset($_GET['Radica']))
			$model->attributes=$_GET['Radica'];		

		$this->render('estudio',array(
			'model'=>$model,
		));		
	}
	
	public function actionViewDetalle($id_radica, $openDialog=false)
	{
		$model=Radica::model()->findByAttributes(array('id_radica'=>$id_radica));
		//$modelObs=new RadicaObs('search');
		if(!$model->arlHistorialrad->id_radica){
				//var_dump($id_radica);
				//Yii::app()->end();

				$modelHistorial=new Historial;

				
				$modelHistorial->id_radica = $id_radica;
				$modelHistorial->fecha_termino = $model->fecha_rad;
				
				$modelHistorial->usuario_cod = $model->usu;

				$modelHistorial->estado = "Radicación";
				$modelHistorial->fecha_inicio = $model->fecha_rad;
				$modelHistorial->save();

				$modelHistorial=new Historial;

				$modelHistorial->id_radica = $id_radica;
				$modelHistorial->usuario_cod = $model->getUserEnEstudio();
				$modelHistorial->estado = "En estudio";
				$modelHistorial->fecha_inicio = $model->fecha_rad;
				$modelHistorial->save();
		}

        
		$this->renderPartial('_viewDetalle',array(
			'model'=>$model,
			//'modelObs' => $modelObs,
			'openDialog' => $openDialog,

		),false,true);
	}
	
	public function actionFormEstudio($id_radica)
   	{
   		$modelRadica = Radica::model()->findByPk($id_radica);
		$modelRadica->scenario='estudio';
		$archval=0;
 
   		$modelHistorial = Historial::model()->findByAttributes(
   				array(
   					'id_radica'=>$id_radica, 
   					'estado'=>$modelRadica->swGetStatus()->getLabel(),
   					'fecha_termino'=>null
   				));
   		


   		$modelHistorialac = new Historial;
   		$modelFile = new File;


   		$this->performAjaxValidation($modelRadica);

			//valida si viene archivo y que venga con nombre valido.
                        $archchsv = 	CUploadedFile::getInstancesByName('docs_multiple');
                        if (count($archchsv)!=0) {
                        	
    						foreach($archchsv as $archchv => $p) {                	

								$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_. "; 
								   for ($j=0; $j<strlen($p->name); $j++){ 
								      if (strpos($permitidos, substr($p->name,$j,1))===false){ 
								         $archval++;
								      } 
								   } 
	                        }
                    	}

        if ($archval==0){
         if(isset($_POST['Radica'])){


			//cierra la actividad en curso
				$modelHistorial->attributes = $_POST['Historial'];
				$modelHistorial->observacion = $_POST['Historial']['observacion'];
				$modelHistorial->fecha_termino = new CDbExpression('NOW()');
				$modelHistorial->usuario_cod = Yii::app()->user->id;
				if($modelHistorial->validate()){
						$modelHistorial->save();


					$modelRadica->attributes=$_POST['Radica'];
		            //guarda actualizacion de estado
					if ($modelRadica->validate()) $modelRadica->save();


					//carga los archivos				

		            $archchs = 	CUploadedFile::getInstancesByName('docs_multiple');
		            //var_dump(count($archchs) );
					//Yii::app()->end();
		            if (count($archchs)!=0) {
						foreach($archchs as $archch => $i) {                	
		                	$modelFilex = new File;
							$modelFilex->path_file=$i->tempName;
							$modelFilex->name_file=$i->name;
		                	$modelFilex->id_radica = $modelRadica->id_radica;
		                	$modelFilex->id_historial = $modelHistorial->id_historial;
		                	$modelFilex->save();
		                }
		        	}



					//agrega el nuevo historial
					
					$modelHistorialac->id_radica = $modelRadica->id_radica;
					
					$modelHistorialac->usuario_cod = Yii::app()->user->id;
					$modelHistorialac->estado = $modelRadica->swGetStatus()->getLabel();
					$modelHistorialac->fecha_inicio = new CDbExpression('NOW()');
					
					
					if($modelHistorialac->estado == 'Cerrado' || $modelHistorialac->estado == 'Anulado')
						$modelHistorialac->fecha_termino = new CDbExpression('NOW()');

					if($modelHistorialac->estado == 'Devuelto')
						$modelHistorialac->usuario_cod = $modelRadica->arlHistorialrad->usuario_cod;

					if($modelHistorialac->estado == 'En tramite')
						$modelHistorialac->usuario_cod = $modelRadica->getUserEnEstudio();

					if ($modelHistorialac->validate()) $modelHistorialac->save();
						$this->redirect(array('consultaTramites'));
				}

					
		 }
		}else{
		    	$modelRadica->validate();
		}			

   		$this->render('formEstudio',array(
			'modelRadica'=>$modelRadica,
			'modelHistorial'=>$modelHistorial,			
			'modelFile'=>$modelFile,	
			'archval'=>$archval,
		));
   	}

	public function actionFormReasigna($id_radica)	{		



	   		$modelHistorialac = new Historial;
	   		
	   		$modelHistorial = Historial::model()->findByAttributes(
	   				array(
	   					'id_radica'=>$id_radica, 
	   					'fecha_termino'=>null
	   				));

	   		$modelRadica=Radica::model()->findByAttributes(array('id_radica'=>$id_radica));

			if(isset($_POST['Historial'])){
					
				//cierra la actividad en curso
					
					$modelHistorial->fecha_termino = new CDbExpression('NOW()');
					$modelHistorial->observacion = "Tramite re-asignado por el usuario ".Yii::app()->user->getNombreCompleto();
					$modelHistorial->save();


				//agrega el nuevo historial
				
				$modelHistorialac->id_radica = $modelHistorial->id_radica;
				$modelHistorialac->attributes = $_POST['Historial'];
				$modelHistorialac->estado = $modelRadica->swGetStatus()->getLabel();
				$modelHistorialac->fecha_inicio = new CDbExpression('NOW()');

				if ($modelHistorialac->validate()) $modelHistorialac->save();

				$this->redirect(array('estudio'));
			}else{
				
				if ($modelRadica->status === 'swRadica/en_tramite' && Yii::app()->user->checkAccess('4.5.5') != false){

					$users = Yii::app()->db->createCommand()
					    ->select("(usuario_nombres || ' ' || usuario_priape || ' ' || usuario_segape)  as nmcom, usu.usuario_cod"	)
					    ->from('adm_usuario usu')
					    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod')
					    ->join('tblareascorrespondencia tarea', 'usu.area = tarea.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo = tarea.agencia')
					    ->where('not usuario_bloqueado')  
					    //->andWhere('ofi.id_agencia=:agenci',array(':agenci'=>$modelRadica->idAgencia->id_agencia) ) 
					    ->andWhere('um.jerarquia_opcion=:jeqopc',array(':jeqopc'=>'4.5.4' )  )
					    ->queryAll();
				}else{

					$users = Yii::app()->db->createCommand()
					    ->select("(usuario_nombres || ' ' || usuario_priape || ' ' || usuario_segape)  as nmcom, usu.usuario_cod"	)
					    ->from('adm_usuario usu')
					    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod and jerarquia_opcion=:jerarquia_opcion', array(':jerarquia_opcion'=>"4.5.2"))
					    ->where("not usuario_bloqueado")   
					    ->queryAll();

				}

				$this->renderpartial('_formReasigna',array(
							'modelHistorial'=>$modelHistorial,
							'users'=>$users,
						));
				
			}
		
	}   	


	public function actionFormCestado($id_radica)	{		


	   		$modelRadica=Radica::model()->findByAttributes(array('id_radica'=>$id_radica));

			if(isset($_POST['Radica'])){
					
				
				if ($_POST['Radica']['status'] == 'swRadica/en_tramite'){

					
					if ($modelRadica->status == 'swRadica/en_devolucion'){

				   		$modelHistorial = Historial::model()->findByAttributes(
				   				array(
				   					'id_radica'=>$modelRadica->id_radica, 
				   					'fecha_termino'=>null
				   				));

						$modelHistorial->fecha_termino = new CDbExpression('NOW()');
						$modelHistorial->observacion = "Tramite re-asignado por el usuario ".Yii::app()->user->getNombreCompleto();
						$modelHistorial->save();
						$qupstatus=1;
					}else{

				   		$modelHistorial = Historial::model()->findByAttributes(
				   				array(
				   					'id_historial'=>$modelRadica->arlHistorialUlt->id_historial, 
				   					
				   				));

						$modelHistorial->observacion .= "Tramite re-asignado por el usuario ".Yii::app()->user->getNombreCompleto();
						$modelHistorial->save();

						

						//actualiza status por SQL ya que el caso se encontraba cerrado

						$qupstatus = Yii::app()->db->createCommand()->update('wfarl_radica',
							array('status'=>'swRadica/en_tramite'),'id_radica=:id_radica',
								array(':id_radica'=>$modelRadica->id_radica));
						
					}

					if($qupstatus>0){
						//Actualiza el estado en radica
						$modelRadica=Radica::model()->findByAttributes(array('id_radica'=>$id_radica));
						$modelRadica->status=$_POST['Radica']['status'];
						$modelRadica->save();


						//agrega el nuevo historial
						$modelHistorialac = new Historial;
						$modelHistorialac->id_radica = $modelRadica->id_radica;
						$modelHistorialac->estado = $modelRadica->swGetStatus()->getLabel();
						$modelHistorialac->fecha_inicio = new CDbExpression('NOW()');
						$modelHistorialac->usuario_cod = $modelRadica->getUserEnEstudio();

						if ($modelHistorialac->validate()) $modelHistorialac->save();
					}
				}

				$this->redirect(array('consultaTramites'));
			}else{
				
				if ($modelRadica->swIsFinalStatus() || $modelRadica->status == 'swRadica/en_devolucion')
					$estadosp = 1;
				else $estadosp = 0;
				
				$this->renderpartial('_formCestado',array(
							'modelRadica'=>$modelRadica,
							'estadosp'=>$estadosp,
						));
				
			}
		

		
			
	}   	


    protected function performAjaxValidation($model)
    {
    	if(isset($_POST['ajax']) ){
			echo CActiveForm::validate($model);
            Yii::app()->end();
        }
	}  


	public function actionInformes()
	{
		$modelInformesForm=new InformesForm;
		
		if($_POST['InformesForm']){

			$modelInformesForm->attributes = $_POST['InformesForm'];



			if ($modelInformesForm->validate()){
				$fechafhr = $modelInformesForm->fechaf . ' 23:59:59';

				if($modelInformesForm->informe==='1'){

					$informeus = Yii::app()->db->createCommand()
					    ->select("(usuario_nombres || ' ' || usuario_priape || ' ' || usuario_segape)  as usuario , 
					    	usuario_desc as usudesc, 
					    	count(*) cantidad, 
count(case when (select case when (extract(hour from ( hs.fecha_termino - hs.fecha_inicio)) > 0) then 31 else extract(minute from ( hs.fecha_termino - hs.fecha_inicio)) end) < 30 then 1 else NULL end) as instd,

count(case when (select case when (extract(hour from ( hs.fecha_termino - hs.fecha_inicio)) > 0) then 31 else extract(minute from ( hs.fecha_termino - hs.fecha_inicio)) end) >= 30 then 1 else NULL end) as outstd,

							
					    	('".$modelInformesForm->fechai."') as fechaiq,
					    	('".$modelInformesForm->fechaf."') as fechafq,
					    	('".$modelInformesForm->informe."') as infor
					    	")
					    ->from('wfarl_radica rad')
					    ->join('wfarl_historial hs', 'rad.id_radica=hs.id_radica')
					    ->join('adm_usuario uss', 'hs.usuario_cod=uss.usuario_cod')
					    ->where('estado=:estado',array(':estado'=>'En tramite'))
					    ->andWhere('hs.fecha_termino>=:ft', array(':ft'=>$modelInformesForm->fechai))
					    ->andWhere('hs.fecha_termino<=:ftf', array(':ftf'=>$fechafhr))
					    ->group('usuario,usudesc')
					    ->order('usudesc')
					    ->queryAll();
				}elseif($modelInformesForm->informe==='2'){

					$informeus = Yii::app()->db->createCommand()
					    ->select("ofi.descrip as nagencia , ofi.codigo as agencia,

							count(1) as cantidad,
			
							count(case when rad.status='swRadica/cerrado' then 1 else NULL end) as ok,
							count(case when rad.status='swRadica/anulado' then 1 else NULL end) as anu,
							count(case when rad.status='swRadica/en_tramite' or rad.status='swRadica/en_devolucion' then 1 else NULL end) as pend,

					    	('".$modelInformesForm->fechai."') as fechaiq,
					    	('".$modelInformesForm->fechaf."') as fechafq
							
					    	")
					    ->from('wfarl_radica rad')
					    ->join('adm_usuario usu', 'rad.usuario_cod=usu.usuario_cod')
					    ->join('tblareascorrespondencia are', 'usu.area=are.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo=are.agencia')
					    ->where('rad.fecha_rad>=:ft', array(':ft'=>$modelInformesForm->fechai))
					    ->andWhere('rad.fecha_rad<=:ftf', array(':ftf'=>$fechafhr))
					    ->group('ofi.descrip,ofi.codigo')
					    ->order('ofi.descrip')
					    ->queryAll();					
				}				

			}
		}

		$this->render('informes',array(
			'modelInformesForm'=>$modelInformesForm,
			'informeus'=>$informeus,
		));				
	}

	public function actionInformeEntramitexls(){

				//var_dump($_GET['xlsCC']);
				
				//Yii::app()->end();

				$fechafhr = $_GET['fechaf'] . ' 23:59:59';

				$cmpl=false;
				if (isset($_GET['xlsCC']))
					$cmpl=true;
				

				if ($_GET['infor']==='2'){
					if(!$cmpl){
				      $informeusx = Yii::app()->db->createCommand()
					    ->select("rad.*, pr.proceso, tip.tipologia, ofi.descrip, hs.estado, hs.fecha_inicio, hs.fecha_termino,
					    	(hs.fecha_termino - hs.fecha_inicio) as tiempo, 
					    	uss.usuario_nombres, uss.usuario_priape, uss.usuario_desc")
					    ->from('wfarl_radica rad')
					    ->join('wfarl_historial hs', 'rad.id_radica=hs.id_radica')
					    ->join('adm_usuario uss', 'rad.usuario_cod=uss.usuario_cod')
					    ->join('tblareascorrespondencia are', 'uss.area=are.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo=are.agencia')
					    ->join('wfarl_proceso pr', 'pr.idproceso=rad.idproceso')
					    ->join('wfarl_tipologia tip', 'tip.idtipologia=rad.idtipologia')
					    ->where('estado=:estado',array(':estado'=>'En tramite'))
					    ->andWhere('uss.usuario_desc=:usus', array(':usus'=>$_GET['usu']))
					    ->andWhere('hs.fecha_termino>=:ftt', array(':ftt'=>$_GET['fechai']))
					    ->andWhere('hs.fecha_termino<=:ftf', array(':ftf'=>$fechafhr))
					    ->queryAll();	
					}else{
				      $informeusx = Yii::app()->db->createCommand()
					    ->select("rad.*, pr.proceso, tip.tipologia, ofi.descrip, hs.estado, hs.fecha_inicio, hs.fecha_termino,
					    	(hs.fecha_termino - hs.fecha_inicio) as tiempo, 
					    	uss.usuario_nombres, uss.usuario_priape, uss.usuario_desc")
					    ->from('wfarl_radica rad')
					    ->join('wfarl_historial hs', 'rad.id_radica=hs.id_radica')
					    ->join('adm_usuario uss', 'rad.usuario_cod=uss.usuario_cod')
					    ->join('tblareascorrespondencia are', 'uss.area=are.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo=are.agencia')
					    ->join('wfarl_proceso pr', 'pr.idproceso=rad.idproceso')
					    ->join('wfarl_tipologia tip', 'tip.idtipologia=rad.idtipologia')
					    ->where('estado=:estado',array(':estado'=>'En tramite'))
					    ->andWhere('hs.fecha_termino>=:ftt', array(':ftt'=>$_GET['fechai']))
					    ->andWhere('hs.fecha_termino<=:ftf', array(':ftf'=>$fechafhr))
					    ->queryAll();							
					}
				}elseif ($_GET['infor']==='1'){
					if(!$cmpl){
				      $informeusx = Yii::app()->db->createCommand()
					    ->select("rad.*, pr.proceso, tip.tipologia, ofi.descrip, hs.estado, hs.fecha_inicio, hs.fecha_termino,
					    	(hs.fecha_termino - hs.fecha_inicio) as tiempo, 
					    	uss.usuario_nombres, uss.usuario_priape, uss.usuario_desc")
					    ->from('wfarl_radica rad')
					    ->join('wfarl_historial hs', 'rad.id_radica=hs.id_radica')
					    ->join('adm_usuario uss', 'rad.usuario_cod=uss.usuario_cod')
					    ->join('tblareascorrespondencia are', 'uss.area=are.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo=are.agencia')
					    ->join('wfarl_proceso pr', 'pr.idproceso=rad.idproceso')
					    ->join('wfarl_tipologia tip', 'tip.idtipologia=rad.idtipologia')
					    ->where('estado=:estado',array(':estado'=>'En tramite'))
					    ->andWhere('uss.usuario_desc=:usus', array(':usus'=>$_GET['usu']))
					    ->andWhere('hs.fecha_termino>=:ftt', array(':ftt'=>$_GET['fechai']))
					    ->andWhere('hs.fecha_termino<=:ftf', array(':ftf'=>$fechafhr))
					    
					    
					    ->queryAll();	
					}else{
				      $informeusx = Yii::app()->db->createCommand()
					    ->select("rad.*, pr.proceso, tip.tipologia, ofi.descrip, hs.estado, hs.fecha_inicio, hs.fecha_termino,
					    	(hs.fecha_termino - hs.fecha_inicio) as tiempo, 
					    	uss.usuario_nombres, uss.usuario_priape, uss.usuario_desc")
					    ->from('wfarl_radica rad')
					    ->join('wfarl_historial hs', 'rad.id_radica=hs.id_radica')
					    ->join('adm_usuario uss', 'rad.usuario_cod=uss.usuario_cod')
					    ->join('tblareascorrespondencia are', 'uss.area=are.areasid')
					    ->join('tblradofi ofi', 'ofi.codigo=are.agencia')
					    ->join('wfarl_proceso pr', 'pr.idproceso=rad.idproceso')
					    ->join('wfarl_tipologia tip', 'tip.idtipologia=rad.idtipologia')
					    ->where('estado=:estado',array(':estado'=>'En tramite'))
					    ->andWhere('hs.fecha_termino>=:ftt', array(':ftt'=>$_GET['fechai']))
					    ->andWhere('hs.fecha_termino<=:ftf', array(':ftf'=>$fechafhr))
					    
					    
					    ->queryAll();							
					}
				}

		$this->render('informeEntramitexls',array('informeusx'=>$informeusx,'infor'=>$_GET['infor']));
	}  



}


