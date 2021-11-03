<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'historial-form',
	'enableAjaxValidation'=>false,
)); ?>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'fecha_inicio',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'fecha_termino',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'estado',array('class'=>'span5','maxlength'=>50)); ?>

	<?php echo $form->textAreaRow($model,'observacion',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<?php echo $form->textFieldRow($model,'id_radica',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'usuario_cod',array('class'=>'span5')); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
</div>

<?php $this->endWidget(); ?>
