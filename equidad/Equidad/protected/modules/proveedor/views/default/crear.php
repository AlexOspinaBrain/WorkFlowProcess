<?php

$this->menu=array(
	array('label'=>'Opciones', 'itemOptions'=>array('class'=>'nav-header')),
	array('label'=>'Listar proveedores', 'url'=>array('index')),
	array('label'=>'Crear proveedor', 'url' => '#','itemOptions'=>array('class'=>'active')),
	'',
	array('label'=>'Salir', 'url'=>'http://imagine.laequidadseguros.coop/'),
);
?>

<div class="row">
	<div class="span2">
		<br>
		<div class="well" style="padding: 8px 0;position:fixed">
			<?php 
				$this->widget('bootstrap.widgets.TbMenu', array(
	    			'type'=>'list',
	    			'items' => $this->menu
				));
			?>
		</div>

	</div>

    <div class="span10">
		<?php echo $this->renderPartial('_form',array('persona'=>$persona, 'proveedor'=>$proveedor)); ?>           
    </div>
</div>