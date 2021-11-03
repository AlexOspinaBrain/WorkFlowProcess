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
	<legend>Radicación proceso SARLAFT</legend>
<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>
</fieldset>

<?php echo $form->errorSummary(array($modelRadica,$modelHistorial)); ?>

<?php echo $form->dropDownListRow($modelRadica,'tipo_id',array('CC'=>'Cédula Ciudadania','NI'=>'NIT','CE'=>'Cédula Extrangería','PA'=>'Pasaporte'),array('empty'=>'')); ?>

<?php echo $form->textFieldRow($modelRadica,'identificacion', array('class'=>'input-small', 'maxlength'=>15));?>
<?php echo $form->textFieldRow($modelRadica,'nombre', array('class'=>'input-xlarge', 'maxlength'=>60, 'style'=>'text-transform:uppercase;'));?>

<?php 
	$agencia = CHtml::listData( Agencia::model()->findAll(array('order'=>'descrip')), 'id_agencia', 'descrip');
	unset($agencia[0]);
	echo $form->dropDownListRow($modelRadica,'id_agencia', $agencia, array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge')); 
	
    $producto = CHtml::listData( Ramo::model()->findAll(array('order'=>'ramo')), 'id_ramo', 'ramo');
    unset($producto[0]);
    echo $form->dropDownListRow($modelRadica,'id_producto', $producto, array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge'));
?>

        <div class="control-group">
            <?= CHtml::label("Adjunto", null, array('class'=>'control-label'))?>
                
            <div class="controls">
                <?php
                    $this->widget('ext.EFineUploader.EFineUploader',
                        array(
                            'id'=>"adjunto",
                            'config'=>array(
                                'autoUpload'=>true,
                                'request'=>array(
                                    'endpoint'=>$this->createUrl('upload'),// OR $this->createUrl('files/upload'),
                                    'params'=>array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
                                ),
                                'chunking'=>array('enable'=>true,'partSize'=>100),//bytes
                                'callbacks'=>array(
                                    'onComplete'=>"js:function(id, name, response){                                     	
                                    	$('#File_path_file').val(response.path);
                                    }",                                    
                                ),
                                'validation'=>array(
                                    'allowedExtensions'=>array('pdf', 'jpg', 'jpeg', 'tif', 'doc','docx','xls','xlsx','ppt','pptx'),
                                    'sizeLimit'=>2 * 1024 * 1024,//maximum file size in bytes
                                ),
                                'messages'=>array(
                                    'typeError'=>"El archivo {file} tiene una extensión incorrecta. Se permite solamente los archivos con las siguientes extensiones: {extensions}.",
                                    'sizeError'=>"Tamaño de archivo {file} es grande, el tamaño máximo de {sizeLimit}.",
                                    'minSizeError'=>"Tamaño de archivo {file} es pequeño, el tamaño mínimo {minSizeLimit}.",
                                    'emptyError'=>"{file} is empty, please select files again without it.",
                                    'onLeave'=>"Los archivos se están cargando, si te vas ahora la carga se cancelará."
                                ),
                            )
                        )
                    );  
                ?>                
                
                <?= $form->hiddenField($modelFile, 'path_file')?>             
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
    function detallesTramite(id_radica, openDialog)
    {
	   	<?php echo CHtml::ajax(array(
        	'url'=>array('default/viewDetalle'), 
 			'type' => 'GET',
 			'data'=>array(
				'id_radica'=>'js:id_radica', 				
				'openDialog'=>'js:openDialog', 				
			), 
			'success'=> "function(data){
				$('body').append(data);
        	}"
      	))?>;
    	
        return false;  
    } 
</script>


<?php 
	if(isset($_GET['id_radica']))
		echo CHtml::script('detallesTramite("'.$_GET['id_radica'].'", true)');
?>