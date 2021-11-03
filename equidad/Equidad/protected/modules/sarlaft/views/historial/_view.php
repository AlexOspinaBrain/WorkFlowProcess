<div class="view">

		<b><?php echo CHtml::encode($data->getAttributeLabel('id_historial')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_historial),array('view','id'=>$data->id_historial)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fecha_inicio')); ?>:</b>
	<?php echo CHtml::encode($data->fecha_inicio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fecha_termino')); ?>:</b>
	<?php echo CHtml::encode($data->fecha_termino); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('estado')); ?>:</b>
	<?php echo CHtml::encode($data->estado); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('observacion')); ?>:</b>
	<?php echo CHtml::encode($data->observacion); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_radica')); ?>:</b>
	<?php echo CHtml::encode($data->id_radica); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('usuario_cod')); ?>:</b>
	<?php echo CHtml::encode($data->usuario_cod); ?>
	<br />


</div>