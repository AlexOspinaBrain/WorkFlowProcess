
<button type="button" class="btn btn-primary btn-lg btn-block">Work Flow ARL</button>
<?php
$action = Yii::app()->controller->action->id;

$prest=false;
if (Yii::app()->user->checkAccess('4.5.2') == true  || Yii::app()->user->checkAccess('4.5.6') == true)
	$prest=true;

$this->menu=array(
	array('label' => '', 'itemOptions' => array('class' => 'nav-header')),
	'',
	array(
		'label'=>'Radicar', 
		'url' =>  array('radicar'), 
		'itemOptions'=>array(
			'class'=>($action === 'radicar' || $action === 'viewRadica' ? 'active' : ''),
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.5.1')
	),
	array(
		'label'=>'En Tramite', 
		'url' => array('estudio'), 
		'itemOptions'=>array(
			'class'=>($action === 'estudio' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>$prest
	),
	/*array(
		'label'=>'* En Estudio Autos', 
		'url' => array('vehi'), 
		'itemOptions'=>array(
			'class'=>($action === 'vehi' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>$prest
	),*/
	array(
		'label'=>'Devolución', 
		'url' => array('devuelto'), 
		'itemOptions'=>array(
			'class'=>($action === 'devuelto' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.5.4')
	),	
	array(
		'label'=>'Consulta Tramites', 
		'url' => array('consultaTramites'), 
		'itemOptions'=>array(
			'class'=>($action === 'consultaTramites' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.5.3')
	),
	array(
		'label'=>'Informes', 
		'url' => array('informes'), 
		'itemOptions'=>array(
			'class'=>($action === 'informes' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.5.5')
	),	
);

$this->widget('bootstrap.widgets.TbMenu', array(
	'type'=>'list',
	//'encodeLabel'=>false,
	'items' => $this->menu
));


?>

<style type="text/css">
	.badge{position:absolute; right:-10px;}
</style>

