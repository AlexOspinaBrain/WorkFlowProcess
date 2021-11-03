<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		
		//echo serialize(array('url'=>'sadas', 'option_yii'=>false));
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		//$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;
		$model->setAttributes(array(
			
			'username'=>Yii::app()->session->get('usuario_desc'), 
			'password'=>'1'
		));
		
		if($model->validate() && $model->login())
			$this->redirect(Yii::app()->user->returnUrl);
		else
			//$this->redirect("../");
			//header ("Location http://" . $_SERVER['HTTP_HOST'] . "/default.php");
			$this->redirect("/equidad/default.php");
			

			//header ("Location: http://imagine.laequidadseguros.coop/default.php");
		/*else
			$this->redirect(Yii::app()->homeUrl);*/
		/*$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			echo "<pre>";
			var_dump($_POST);
			echo "</pre>";
			Yii::app()->end();
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));*/
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{

		Yii::app()->user->logout();
		
		
		$urldereferencia = explode("?", Yii::app()->request->urlReferrer);
		$essecc = explode(":", $urldereferencia[0]);
		if ($essecc[0] == "https")
			header ("Location: https://servicios.laequidadseguros.coop/equidad/");
		else
			$this->redirect("/equidad/default.php");
		
	}
}
