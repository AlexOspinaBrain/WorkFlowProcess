<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */
/* @var $form CActiveForm */
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'proveedor-form',
    'enableAjaxValidation'=>true,
    'type' => 'horizontal',
    'enableClientValidation'=>true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	  	'validateOnChange' => true, // allow client validation for every field
	),
	'htmlOptions' => array('class' => 'well'),
)); ?>

	<legend><?php echo $proveedor->isNewRecord ? 'Crear un nuevo proveedor' : 'Actualizar proveedor ( '.$persona->tipo_doc.' '.$persona->documento.' ) '.$persona->nombre?></legend>
	<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> obligatorios.</p>
    <?php echo $form->errorSummary(array($persona, $proveedor)); ?>
	 	
	<?php echo $form->checkBoxRow($proveedor, 'estado', array('checked'=>'checked')); ?>
	<?php echo $form->dropDownListRow($persona,'tipo_doc',array('C.C.'=>'C.C.','NIT'=>'NIT'),array('empty'=>'', 'class'=>'span1')); ?>
	<?php echo $form->textFieldRow($persona,'documento', array('append' => '', 'class'=>'span2', 'hint' => 'Escriba el documento sin codigo de verificación')); ?>
	<?php echo $form->textFieldRow($persona,'nombre', array('class'=>'span4', 'maxlength'=>80, 'readonly'=>'readonly')); ?>
	<?php echo $form->textFieldRow($persona,'telefono', array('class'=>'span2', 'maxlength'=>50)); ?>
	<?php echo $form->textFieldRow($persona,'fax', array('class'=>'span2', 'maxlength'=>50)); ?>
	<?php echo $form->textFieldRow($persona,'direccion', array('class'=>'span4', 'maxlength'=>100)); ?>
	<?php $ciudades = CHtml::listData(Ciudades::model()->findAll('idciudad!=1 order by ciudad'), 'idciudad', 'ciudad');?>
	<?php echo $form->dropDownListRow($persona,'idciudad', $ciudades, array('empty'=>'', 'class'=>'span4')); ?>
	<?php echo $form->textFieldRow($persona,'correo', array('class'=>'span4', 'maxlength'=>100)); ?>
	<?php echo $form->textFieldRow($persona,'producto', array('class'=>'span4', 'maxlength'=>100)); ?>
	<?php echo $form->textFieldRow($persona,'representante', array('class'=>'span4', 'maxlength'=>100)); ?>
	<?php echo $form->textFieldRow($proveedor,'acta', array('class'=>'span1', 'maxlength'=>5)); ?>
	<?php echo $form->datepickerRow(
	   		$proveedor,
	   		'fecha_aprobacion', 
	   		array(
          		'append' => '<i class="icon-calendar"></i>',
           		'hint' => 'En formato dd/mm/aaaa',
           		'class'=>'input-small'
       		)
       	); 
    ?>
	
	<?php echo $form->dropDownListRow(
		   	$proveedor,
		   	'tipo', 
		   	array(
		   		'OTROS' => 'OTROS',
				'CRITICO RECURRENTE'=>'CRITICO RECURRENTE',
				'CRITICO NO RECURRENTE' => 'CRITICO NO RECURRENTE',
			), 
			array('class'=>'span2')
		); 
	?>

	<?php echo $form->dropDownListRow(
	    	$proveedor,
	    	'doc_cobro', 
	    	array(
	    		'Factura'=>'Factura',
				'Cuenta de cobro' => 'Cuenta de cobro'
			), 
			array('empty'=>'', 'class'=>'span2')
		); 
	?>

	<?php $areas = CHtml::listData(Area::model()->findAll(), 'areasid', 'area');?>
	<?php echo $form->dropDownListRow($proveedor,'area_id', $areas, array('empty'=>'', 'class'=>'span4')); ?>
	

	<div class="control-group ">
		<label class="control-label" for="Proveedor_proveedorDocs">Documentos del proveedor</label>
		<div class="controls">
			<?php 
				echo CHtml::checkBoxList(
					'proveedorDocs', 
					CHtml::listData($proveedor->proveedorDocs, 'id_documento', 'id_documento'), 
					CHtml::listData(ProveedorDoc::model()->findAll(), 'id_documento', 'desc_documento'), 
					array('labelOptions'=>array('style'=>'display:inline'))
				);	
			?>
		</div>
	</div>
	
	<?php echo $form->hiddenField($proveedor, 'id_persona'); ?>
	
	<div class="form-actions">
		<?php 
    		$this->widget('bootstrap.widgets.TbButton', array(
            	'buttonType'=>'submit',
            	'type'=>'primary',
            	'label'=> 'Guardar' ,
            	'htmlOptions'=>array('class' => 'pull-right'),
  			)); 
  		?>
	</div>

<?php $this->endWidget(); ?>

<style type="text/css">
	.loading {
	    background-image: url('images/loading.gif');
	    background-position:  right center;
	    background-repeat: no-repeat;
	}
</style>

<script type="text/javascript">
    function buscaCifin()
    {
        <?php echo CHtml::ajax(array(
          	'url'=>array('persona/buscaIdentificacion'), 
 			'type' => 'POST',
 			'dataType'=>'json',
 			'beforeSend' => 'function(){ $("#'.CHtml::activeId($persona, 'nombre' ).', #'.CHtml::activeId($persona, 'documento' ).'").addClass("loading");}',
 			'complete' => 'function(){ $("#'.CHtml::activeId($persona, 'nombre' ).', #'.CHtml::activeId($persona, 'documento' ).'").removeClass("loading");}',
			'data'=>array(
				'tipo_doc'=>'js:$("#'.CHtml::activeId($persona, 'tipo_doc' ).'").val()', 
				'documento'=>'js:$("#'.CHtml::activeId($persona, 'documento' ).'").val()'
			), 
			'success'=> 'function(data){
				if(data.encontrado == "SI"){
					if(data.id_proveedor)
						location.href = "'.$this->createUrl('default/update', array('id' => '')).'"+data.id_proveedor;

					$("#'.CHtml::activeId($persona, 'nombre' ).'").val(data.nombre);
					$("#'.CHtml::activeId($persona, 'producto' ).'").val(data.producto);
					$("#'.CHtml::activeId($persona, 'idciudad' ).'").val(data.idciudad);
					$("#'.CHtml::activeId($proveedor, 'id_persona' ).'").val(data.id_persona);
					$("#'.CHtml::activeId($persona, 'nombre' ).'").attr("readonly", true);
				}else{
					$("#'.CHtml::activeId($persona, 'nombre' ).'").removeAttr("readonly");
				}
			}'
      	))?>;
        return false;  
    } 
</script>

<?php 
$script = <<<EOD
  	var x = $("#Persona_documento").parent();
  	x.find("span").remove();

  	var button = $('<button class="btn btn-primary" id="btnSearchCifin" type="button"/>')
    				.append('<i class="icon-search icon-white"></i>')
    				.height(30)
    				.click(buscaCifin);

  	button = x.append(button);

  	$("#Persona_documento").keypress(function(e) {
		if(e.which == 13) 
			buscaCifin();	
	});		

	$("#Persona_documento, #Persona_tipo_doc").change(buscaCifin);	
EOD;
Yii::app()->getClientScript()->registerScript('buttonSearchCifin',$script, CClientScript::POS_END);
?>


