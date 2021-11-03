<?php

$action = Yii::app()->controller->action->id;

$badgeRadicados = CHtml::tag('span', array('class'=>'badge '.($action != 'viewRadicados' ? 'badge-success' : ''), 'id'=>'badgeRadicados'), '', true);
$badgeRecibirRadicados = CHtml::tag('span', array('class'=>'badge '.($action != 'listRecibirRadicados' ? 'badge-success' : ''), 'id'=>'badgeRecibirRadicados'), '', true);
$badgePorTramitar = CHtml::tag('span', array('class'=>'badge '.($action == 'adminTramitar' || $action == 'tramitar' ? '' : 'badge-success'), 'id'=>'badgePorTramitar'), '', true);
$badgeRecibirTramitados = CHtml::tag('span', array('class'=>'badge '.($action == 'listRecibirTramitados' ? '' : 'badge-success'), 'id'=>'badgeRecibirTramitados'), '', true);
$badgePorExpedir = CHtml::tag('span', array('class'=>'badge '.($action == 'adminExpedicion' || $action == 'expedicion' ? '' : 'badge-success'), 'id'=>'badgePorExpedir'), '', true);
$badgeVerificarComercial = CHtml::tag('span', array('class'=>'badge '.($action == 'adminVerificarComercial' || $action == 'verificarComercial' ? '' : 'badge-success'), 'id'=>'badgeVerificarComercial'), '', true);

$this->menu=array(
	array('label' => 'Opciones', 'itemOptions' => array('class' => 'nav-header')),
	'',
	array(
		'label'=>'Radicar', 
		'url' =>  array('radicar'), 
		'itemOptions'=>array(
			'class'=>($action === 'radicar' || $action === 'viewRadica' ? 'active' : ''),
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.1')
	),
	array(
		'label'=>'Ver radicados' . $badgeRadicados, 
		'url' => array('viewRadicados'), 
		'itemOptions'=>array(
			'class'=>($action === 'viewRadicados' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.1')
	),
	array(
		'label'=>'Recibir radicados' . $badgeRecibirRadicados, 
		'url' => array('listRecibirRadicados'), 
		'itemOptions'=>array(
			'class'=>($action === 'listRecibirRadicados' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.2')
	),
	array(
		'label'=>'Tramitar' . $badgePorTramitar, 
		'url' => array('adminTramitar'), 
		'itemOptions'=>array(
			'class'=>($action === 'adminTramitar' || $action === 'tramitar' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.2')
	),
	array(
		'label'=>'Recibir tramitados'.$badgeRecibirTramitados, 
		'url' => array('listRecibirTramitados'), 
		'itemOptions'=>array(
			'class'=>($action === 'listRecibirTramitados' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.3')
	),
	array(
		'label'=>'Expedición'.$badgePorExpedir, 
		'url' => array('adminExpedicion'), 
		'itemOptions'=>array(
			'class'=>($action === 'adminExpedicion' || $action === 'expedicion' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.3')
	),
	array(
		'label'=>'Verificar comercial'.$badgeVerificarComercial, 
		'url' => array('adminVerificarComercial'), 
		'itemOptions'=>array(
			'class'=>($action === 'adminVerificarComercial' || $action === 'verificarComercial' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.4')
	),
	array(
		'label'=>'Consulta tramites', 
		'url' => array('consultaTramites'), 
		'itemOptions'=>array(
			'class'=>($action === 'consultaTramites' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.2.5')
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

<script type="text/javascript">
	$(function() {
    	actualizaBadge();
  	});

  	function actualizaBadge(){
  		<?php echo CHtml::ajax(array(
          	'url'=>array('default/actualizaBadge'), 
 			'type' => 'POST',
 			'dataType'=>'json',
 			'success'=> 'function(data){				
				for (var k in data.badges)
					$("#"+k).text(data.badges[k]);
			}'
      	))?>;
        return false;  
  	}
</script>