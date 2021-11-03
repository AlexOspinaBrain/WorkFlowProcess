<?php
$action = Yii::app()->controller->action->id;

$prest=false;
if (Yii::app()->user->checkAccess('4.4.2') == true  || Yii::app()->user->checkAccess('4.4.6') == true || Yii::app()->user->checkAccess('4.4.7') == true) $prest=true;

$this->menu=array(
	array('label' => 'Opciones', 'itemOptions' => array('class' => 'nav-header')),
	'',
	array(
		'label'=>'* Radicar', 
		'url' =>  array('radicar'), 
		'itemOptions'=>array(
			'class'=>($action === 'radicar' || $action === 'viewRadica' ? 'active' : ''),
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.4.1')
	),
	array(
		'label'=>'* En Estudio', 
		'url' => array('estudio'), 
		'itemOptions'=>array(
			'class'=>($action === 'estudio' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>$prest
	),
	array(
		'label'=>'* En Estudio Autos', 
		'url' => array('vehi'), 
		'itemOptions'=>array(
			'class'=>($action === 'vehi' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>$prest
	),
	array(
		'label'=>'* Pendientes', 
		'url' => array('devuelto'), 
		'itemOptions'=>array(
			'class'=>($action === 'devuelto' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.4.4')
	),	
	array(
		'label'=>'* Consulta Casos', 
		'url' => array('consultaTramites'), 
		'itemOptions'=>array(
			'class'=>($action === 'consultaTramites' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.4.3')
	),
	array(
		'label'=>'* Informes', 
		'url' => array('informes'), 
		'itemOptions'=>array(
			'class'=>($action === 'informes' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.4.5')
	),	
);

$this->widget('bootstrap.widgets.TbMenu', array(
	'type'=>'list',
	'encodeLabel'=>false,
	'items' => $this->menu
));


?>

<style type="text/css">
	.badge{position:absolute; right:-10px;}
</style>

