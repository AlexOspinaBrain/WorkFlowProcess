<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */

$this->breadcrumbs=array(
	'Proveedors'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Listar Proveedores', 'url'=>array('admin')),
	array('label'=>'Salir', 'url'=>'http://imagine.laequidadseguros.coop/'),
);
?>

<h1>Crear Proveedor</h1>

<?php echo $this->renderPartial('_form',array('model'=>$model, 'areas'=>$areas, 'ciudades'=>$ciudades)); ?>