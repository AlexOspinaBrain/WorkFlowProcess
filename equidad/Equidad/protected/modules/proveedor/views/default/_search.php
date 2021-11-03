<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id_proveedor'); ?>
		<?php echo $form->textField($model,'id_proveedor'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tipo_doc'); ?>
		<?php echo $form->textField($model,'tipo_doc',array('size'=>3,'maxlength'=>3)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'documento'); ?>
		<?php echo $form->textField($model,'documento'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'nombre'); ?>
		<?php echo $form->textField($model,'nombre',array('size'=>60,'maxlength'=>80)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'telefono'); ?>
		<?php echo $form->textField($model,'telefono',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'direccion'); ?>
		<?php echo $form->textField($model,'direccion',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'correo'); ?>
		<?php echo $form->textField($model,'correo',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'estado'); ?>
		<?php echo $form->checkBox($model,'estado'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'producto'); ?>
		<?php echo $form->textField($model,'producto',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'representante'); ?>
		<?php echo $form->textField($model,'representante',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'observaciones'); ?>
		<?php echo $form->textArea($model,'observaciones',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'acta'); ?>
		<?php echo $form->textField($model,'acta',array('size'=>5,'maxlength'=>5)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fecha_aprovacion'); ?>
		<?php echo $form->textField($model,'fecha_aprovacion'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'fecha_actualizacion'); ?>
		<?php echo $form->textField($model,'fecha_actualizacion'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tipo'); ?>
		<?php echo $form->textField($model,'tipo',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'doc_cobro'); ?>
		<?php echo $form->textField($model,'doc_cobro',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'areasid'); ?>
		<?php echo $form->textField($model,'areasid'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->