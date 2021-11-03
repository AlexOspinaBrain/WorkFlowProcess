	<script type="text/javascript">
        $(document).ready(function() {	
			if (<?=$usuyaco?>>0){
				$("#estadod").attr('disabled','disabled');
				$("#obdd").attr('disabled','disabled');
				$("#fecdd").attr('disabled','disabled');
				$("#archdd").attr('disabled','disabled');
			}
		})
	</script>
<div>
<br>
</div>

<div class="row">
	<div class="span2">
		<br>		
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php  echo $this->renderPartial('_menuSarlaft'); ?>	
		</div>
	</div>

    <div class="span10" id="containerFormRadica">    	    	
    	
    	<br>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'formRadica',
	'type' => 'horizontal',
	'enableClientValidation'=>true,
    //'enableAjaxValidation'=>true,
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
	<legend>Actividad <?= $modelRadica->swGetStatus()->getLabel()?> - Identificación <?= $modelRadica->tipo_id . " - " . $modelRadica->identificacion  ?></legend>

<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>


<?php if ($archval>0){
?>
        <div class="alert alert-danger">Nombre de archivo incorrecto (debe ser sin caracteres especiales).</div>

<?php
    } if ($usuyaco>0){

?>
	<div class="alert alert-danger"><H3>TRAMITE SELECCIONADO POR OTRO USUARIO.</H3></div>
<?php
    } 

?>


</fieldset>
<p class="alert alert-info" >Ver detalle del caso  <?= CHtml::link($modelRadica->id_radica,"#", array("onClick"=>"js:detallesTramite($(this).text())")) ?></p>

<br>
<br>

<?php //echo $form->errorSummary(array($modelRadica)); ?>

		<?php 
			sizeof( SWHelper::nextStatuslistData($modelRadica, array('includeCurrent' => false))) > 1 ?
				$optionsNextStatus = array(	'prompt' => '', 'includeCurrent' => false) :
				$optionsNextStatus = array('includeCurrent' => false);
		?>

		<?php echo $form->dropDownListRow($modelRadica, 'status', 
			SWHelper::nextStatuslistData( Radica::model()->findByAttributes(array('id_radica'=>$modelRadica->id_radica)), $optionsNextStatus ),
			array('required'=>'true','id'=>'estadod')); ?>

		<?php if ($modelRadica->status =='swRadica/en_estudio')	{?>
	        <div class="control-group">
	            
	            <?= CHtml::label("Fecha de Formulario", null, array('class'=>'control-label'))?>   
	            <div class="controls">
			
				<?php
					$this->widget('zii.widgets.jui.CJuiDatePicker', array(
									                'attribute' => 'fecha_formulario',
					 								'model'=> $modelRadica,
									                
									                'language' => 'es',	                   				          
					                				'options' => array(                   		
					                    				'dateFormat' => 'yy-mm-dd',                    				
					                    				'showOtherMonths' => true,
					                    				'selectOtherMonths' => true,
					                    				'changeMonth' => true,
					                    				'changeYear' => true,
					                    				'showButtonPanel' => true
					                				),
					                				'htmlOptions'=>array(
					 									'style'=>'height:20px;width:70px',
					 									'readonly'=>'readonly',
					 									'id'=>'fecdd'
					                				),
					            				));
				?>
				<?php	echo $form->error($modelRadica, 'fecha_formulario');

				?>
				</div>
			</div>	
		
		<?php }?>	

        <div class="control-group">
            <?= CHtml::label("Adjunto", null, array('class'=>'control-label'))?>
                
            <div class="controls">
                <?php
                    $this->widget('CMultiFileUpload',
                        array(
                            'model'=>$modelFile,
                            'name'=>'docs_multiple',
                            'attribute'=>'name_file',
                            'accept' => 'jpeg|jpg|gif|png|doc|docx|tif|xls|xlsx|ppt|pptx|pdf', // useful for verifying files
                            'duplicate' => 'Archivo Duplicado!', // useful, i think
                            'denied' => 'El tipo de archivo es invalido!', // useful, i think
                            'max'=> 10,
                            //'denied'=>'Archivo erróneo.',
                            'htmlOptions' => array('multiple' => 'multiple','class'=>'btn-danger','id'=>'archdd'),

                        )
                    );  
                ?>                
                
                            
            </div>
        </div>
   


		<?= 
	$form->textAreaRow(
		$modelHistorial,
		'observacion',  
		array(
			'class' => 'span4', 
			'rows' => 5, 
			'id'=>'obdd',
			'labelOptions'=>array('label'=>'Bitácora <br>(actividad: '.$modelRadica->swGetStatus()->getLabel().')')
	)); 
?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
      	'buttonType'=>'submit',
        'type'=>'primary',
        'label'=> 'Guardar' ,
        'htmlOptions'=>array('class' => 'pull-right'),
        //'htmlOptions'=>array('class' => 'pull-right', 'onclick' => 'this.disabled=true;this.value="Enviando...";this.form.submit();' ),
  	));?>
</div>	

<?php
	$this->endWidget();
?>
	</div>
           
</div>
<script>
    function detallesTramite(id_radica)
    {
	   	<?php echo CHtml::ajax(array(
        	'url'=>array('default/viewDetalle'), 
 			'type' => 'GET',
 			'data'=>array(
				'id_radica'=>'js:id_radica', 				
			), 
			'success'=> "function(data){
				$('body').append(data);
        	}"
      	))?>;
    	
        return false;  
    } 	
</script>