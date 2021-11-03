<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */

$this->breadcrumbs=array(
	'Proveedors'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Crear Proveedor', 'url'=>array('create')),
	array('label'=>'Salir', 'url'=>'http://imagine.laequidadseguros.coop/'),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#proveedor-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Administración de Proveedores</h1>

<p>
También puede escribir un operador de comparación (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
ó <b>=</b>) al comienzo de cada uno de sus valores de búsqueda para especificar cómo se debe hacer la comparación..
</p>

<?php /* echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
*/?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'proveedor-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id_proveedor',
		'tipo_doc',
		'documento',
		'nombre',
		'telefono',
		'fax',
		/*
		'direccion',
		'correo',
		'estado',
		'producto',
		'representante',
		'observaciones',
		'acta',
		'fecha_aprovacion',
		'fecha_actualizacion',
		'tipo',
		'doc_cobro',
		'areasid',
		*/
		array(
			'class'=>'CButtonColumn',
			'template' => '{view}{update}',
		),
	),

)); ?>

