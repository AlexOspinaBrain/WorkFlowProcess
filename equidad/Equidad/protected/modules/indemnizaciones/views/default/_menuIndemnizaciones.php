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
		'visible'=>Yii::app()->user->checkAccess('4.3.1')
	),
	array(
		'label'=>'Estudio', 
		'url' => array('estudio'), 
		'itemOptions'=>array(
			'class'=>($action === 'estudio' || $action === 'formEstudio' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.3.2')
	),
	array(
		'label'=>'Consulta tramites', 
		'url' => array('consultaTramites'), 
		'itemOptions'=>array(
			'class'=>($action === 'consultaTramites' ? 'active' : ''), 
			'style'=>'position:relative'
		),
		'visible'=>Yii::app()->user->checkAccess('4.3.3')
	),
);

$this->widget('bootstrap.widgets.TbMenu', array(
	'type'=>'list',
	'encodeLabel'=>false,
	'items' => $this->menu
));

$badge = $this->widget('bootstrap.widgets.TbBadge', array(
    'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
    'label'=>count ( Radica::model()->findAllByAttributes(array('status'=>'swRadica/recibir_radicacion'))),
), true); 
?>

<style type="text/css">
	.badge{position:absolute; right:-10px;}
</style>

<script type="text/javascript">
	$(function() {
    	actualizaBadge();
  	});

  	function actualizaBadge(){
  		/*<?php echo CHtml::ajax(array(
          	'url'=>array('default/actualizaBadge'), 
 			'type' => 'POST',
 			'dataType'=>'json',
 			'success'=> 'function(data){				
				for (var k in data.badges)
					$("#"+k).text(data.badges[k]);
			}'
      	))?>;*/
        return false;  
  	}
</script>