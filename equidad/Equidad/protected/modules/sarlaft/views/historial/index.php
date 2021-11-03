<?php
$this->breadcrumbs=array(
	'Historials',
);

$this->menu=array(
array('label'=>'Create Historial','url'=>array('create')),
array('label'=>'Manage Historial','url'=>array('admin')),
);
?>

<h1>Historials</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
'dataProvider'=>$dataProvider,
'itemView'=>'_view',
)); ?>
