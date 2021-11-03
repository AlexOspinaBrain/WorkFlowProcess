<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */

?>

<div class="row">
	<div class="span2">
		<br>		
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php  echo $this->renderPartial('_menuIndemnizaciones'); ?>	
		</div>
	</div>

    <div class="span10" id="containerFormRadica">    	    	
    	<?php //$this->renderPartial('_form2', array('modelRadica' => $modelRadica, 'modelClasificaDoc' => $modelClasificaDoc));  ?>    
    	<br>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'formRadica',
	'type' => 'horizontal',
	'enableClientValidation'=>true,
    'enableAjaxValidation'=>true,
    'clientOptions' => array(    			
	    'validateOnSubmit'=>true,
	    'validateOnChange'=>true,        		
   	),
    'htmlOptions' => array(
		'class' => 'well', 
		'enctype' => 'multipart/form-data'
	),
)); ?>
		
<fieldset> 
	<legend>Radicación proceso de Indemnizaciones</legend>
<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>
</fieldset>

<?php echo $form->errorSummary(array($modelRadica)); ?>

<?php echo $form->textFieldRow($modelRadica,'no_sinestro', array('class'=>'input-small', 'maxlength'=>8));?>

<?php 
	$agencia = CHtml::listData( Agencia::model()->findAll(array('order'=>'descrip')), 'id_agencia', 'descrip');
	unset($agencia[0]);
	echo $form->dropDownListRow($modelRadica,'id_agencia_tramita', $agencia, array('class'=>'input-xlarge')); 
	echo $form->dropDownListRow($modelRadica,'id_agencia_expide', $agencia, array('empty'=>'', 'class'=>'input-xlarge')); 
?>

<?php 
	$ramo = CHtml::listData( Ramo::model()->findAll(array('order'=>'desc_ramo')), 'id_ramo', 'desc_ramo');
	unset($ramo[0]);
	echo $form->dropDownListRow($modelClasificaDoc, 'id_ramo', $ramo, array('onChange'=>'cambiaRamo()')); 
?>

<?= $form->dropDownListRow($modelRadica,'id_producto', array(), array('empty'=>'', 'class'=>'span3')); ?>

<?php 
	$amparo = CHtml::listData( Amparo::model()->findAll(array('order'=>'desc_amparo')), 'id_amparo', 'desc_amparo');
	unset($amparo[0]);
	echo $form->dropDownListRow($modelClasificaDoc,'id_amparo', $amparo, array('empty'=>'','class'=>'span4')); 
?>

<?= 
	$form->textAreaRow(
		$modelHistorial,
		'observacion',  
		array(
			'class' => 'span4', 
			'rows' => 5, 
			'labelOptions'=>array('label'=>'Bitácora <br>(actividad: '.$modelRadica->swGetStatus()->getLabel().')')
	)); 
?>

<div id="listDocumentos"></div>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
      	'buttonType'=>'submit',
        'type'=>'primary',
        'label'=> 'Radicar' ,
        'htmlOptions'=>array('class' => 'pull-right'),
  	));?>
</div>	

<?php
	$this->endWidget();
?>
    </div>          
</div>

<script type="text/javascript">
	$(function() {	
		cambiaRamo();

		$("#<?=CHtml::activeId($modelRadica, 'id_producto' )?>, #<?=CHtml::activeId($modelClasificaDoc, 'id_ramo' )?>, #<?=CHtml::activeId($modelClasificaDoc, 'id_amparo' )?>").change(function(){
			<?=CHtml::ajax(array(       				
			        'type'=>'POST',
			        'url'=>CController::createUrl('default/loadListDocuments'),
			        'update'=>'#listDocumentos',
			      	'data'=>'js:$("#formRadica").serialize()',
			    ));
			?>
		});
	});

	function cambiaRamo(){
		<?=
			CHtml::ajax(array(
				'type'=>'POST',
				'data'=>'js:$("#formRadica").serialize()',
				'url'=>CController::createUrl('default/ramoProducto'), 
				'update'=>'#'.CHtml::activeId($modelRadica, 'id_producto'), 
			))
		?>
	}
</script>
