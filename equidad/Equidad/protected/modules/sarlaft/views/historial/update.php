<?php
$this->breadcrumbs=array(
	'Historials'=>array('index'),
	$model->id_historial=>array('view','id'=>$model->id_historial),
	'Update',
);

	$this->menu=array(
	array('label'=>'List Historial','url'=>array('index')),
	array('label'=>'Create Historial','url'=>array('create')),
	array('label'=>'View Historial','url'=>array('view','id'=>$model->id_historial)),
	array('label'=>'Manage Historial','url'=>array('admin')),
	);
	?>

	<h1>Update Historial <?php echo $model->id_historial; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>