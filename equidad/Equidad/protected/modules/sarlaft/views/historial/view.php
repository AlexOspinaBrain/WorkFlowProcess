<?php
$this->breadcrumbs=array(
	'Historials'=>array('index'),
	$model->id_historial,
);

$this->menu=array(
array('label'=>'List Historial','url'=>array('index')),
array('label'=>'Create Historial','url'=>array('create')),
array('label'=>'Update Historial','url'=>array('update','id'=>$model->id_historial)),
array('label'=>'Delete Historial','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id_historial),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage Historial','url'=>array('admin')),
);
?>

<h1>View Historial #<?php echo $model->id_historial; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
'data'=>$model,
'attributes'=>array(
		'id_historial',
		'fecha_inicio',
		'fecha_termino',
		'estado',
		'observacion',
		'id_radica',
		'usuario_cod',
),
)); ?>
