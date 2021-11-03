<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */

$this->breadcrumbs=array(
	'Proveedors'=>array('index'),
	$model->id_proveedor=>array('view','id'=>$model->id_proveedor),
	'Update',
);

$this->menu=array(
	array('label'=>'Crear proveedor', 'url'=>array('create')),
	array('label'=>'Detalle Proveedor', 'url'=>array('view', 'id'=>$model->id_proveedor)),
	array('label'=>'Listar Proveedores', 'url'=>array('admin')),
	array('label'=>'Salir', 'url'=>'http://imagine.laequidadseguros.coop/'),
);
?>

<h2>Actualizar Proveedor <br><?php echo "( ". $model->tipo_doc . " " . $model->documento." ) ". $model->nombre; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'areas'=>$areas, 'ciudades'=>$ciudades,'ProveedorDoc'=>$ProveedorDoc)); ?>