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
	<legend>Actividad estudio Indemnizaciones</legend>
<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>
</fieldset>

<?php echo $form->errorSummary(array($modelRadica)); ?>

		<?php 
			sizeof( SWHelper::nextStatuslistData($modelRadica, array('includeCurrent' => false))) > 1 ?
				$optionsNextStatus = array(	'prompt' => '', 'includeCurrent' => false) :
				$optionsNextStatus = array('includeCurrent' => false);
		?>

		<?php echo $form->dropDownListRow($modelRadica, 'status', SWHelper::nextStatuslistData( Radica::model()->findByAttributes(array('id_radica'=>$modelRadica->id_radica)), $optionsNextStatus )); ?>

		
		<div class="row">
		<div class="control-group span5"><label class="control-label" for="Radica_respuesta">Respuesta</label>
			<div class="controls">
				<?php
			$this->widget('ext.EFineUploader.EFineUploader',
	 			array(
	       			'id'=>"respuesta",
	       			'config'=>array(
			            'autoUpload'=>true,
			            'request'=>array(
				            'endpoint'=>$this->createUrl('upload'),// OR $this->createUrl('files/upload'),
				            'params'=>array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
				      	),
		        		'chunking'=>array('enable'=>true,'partSize'=>100),//bytes
		        		'callbacks'=>array(
		        		'onComplete'=>"js:function(id, name, response){ 
			                    $('#Radica_respuesta').val(response.path);                                        
		              		}",                                    
						),
		               	'validation'=>array(
			            	'allowedExtensions'=>array('doc','docx','pdf'),
			              	'sizeLimit'=>4 * 1024 * 1024,//maximum file size in bytes			                
	                    ),
				        'messages'=>array(
		                	'typeError'=>"El archivo {file} tiene una extensión incorrecta. Se permite solamente los archivos con las siguientes extensiones: {extensions}.",
		                    'sizeError'=>"Tamaño de archivo {archivo} es grande, el tamaño máximo de {sizeLimit}.",
		                    'minSizeError'=>"Tamaño de archivo {archivo} es pequeño, el tamaño mínimo {minSizeLimit}.",
		                    'emptyError'=>"{file} is empty, please select files again without it.",
		                    'onLeave'=>"Los archivos se están cargando, si te vas ahora la carga se cancelará."
		               	),
					)
	      		)
			);
	 	
		 	
		 	echo $form->hiddenField($modelRadica,'respuesta');


		?>
			</div>
		</div>

		</div>
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

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
      	'buttonType'=>'submit',
        'type'=>'primary',
        'label'=> 'Guardar' ,
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
		
	});

	
</script>
