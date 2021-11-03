<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/equidad/bootstrap.min.css'); ?>
    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/equidad/jquery-ui-1.10.0.custom.css'); ?>
    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/equidad/bootstrap-datepicker.min.css'); ?>
    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/equidad/bootstrap-fugue-master/css/bootstrap-fugue-min.css'); ?>
   <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	 
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

     <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
        background-image:url('<?php echo Yii::app()->getBaseUrl().'/images/fondo.jpg'; ?>');
        background-repeat:no-repeat;
        background-attachment:fixed;
        background-position:center;
      }
    </style>
</head>
<body>
    
<div style="background-color: white;height: 50px;position: fixed;width: 100%;margin-top: -80px;"></div>

<div class="container" id="page">     
  <?php echo $content; ?>
</div>

</body>
</html>
