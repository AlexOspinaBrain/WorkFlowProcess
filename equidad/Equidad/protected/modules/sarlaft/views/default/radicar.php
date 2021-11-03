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
<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*
</span> son obligatorios.</p>

<?php
    if ($archval==99){
?>
        <div class="alert alert-danger">Aun no selecciona un archivo.</div>
<?php }else if ($archval>0){
?>
        <div class="alert alert-danger">Nombre de archivo incorrecto (debe ser sin caracteres especiales).</div>

<?php
    }

?>

</fieldset>



<?php echo $form->errorSummary(array($modelRadica,$modelHistorial)); ?>

<?php echo $form->dropDownListRow($modelRadica,'tipo_id',array('CC'=>'Cédula Ciudadania','NI'=>'NIT','CE'=>'Cédula Extrangería','PA'=>'Pasaporte'),array('empty'=>'')); ?>

<?php echo $form->textFieldRow($modelRadica,'identificacion', array('class'=>'input-small', 'maxlength'=>15));?>
<?php echo $form->textFieldRow($modelRadica,'nombre', array('class'=>'input-xlarge', 'maxlength'=>60, 'style'=>'text-transform:uppercase;'));?>

<?php 


/*$agencia = CHtml::listData( Agencia::model()->findAll(array("order"=>"descrip",
    "condition"=>"eliminado=:eliminado", "params"=>array(':eliminado'=>'0'))), 'id_agencia', 'descrip');

	unset($agencia[0]);
	echo $form->dropDownListRow($modelRadica,'id_agencia', $agencia, array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge')); 
*/
	
$producto = CHtml::listData( Ramo::model()->findAll(array("order"=>"ramo","condition"=>"eliminado=:eliminado", 
        "params"=>array(':eliminado'=>'0'))), 'id_ramo', 'ramo');

    unset($producto[0]);
    echo $form->dropDownListRow($modelRadica,'id_producto', $producto, array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge'));
?>

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
                            'remove'=>'Quitar....',
                            
                            'htmlOptions' => array('multiple' => 'multiple',),

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
			'labelOptions'=>array('label'=>'Bitácora <br>(actividad: '.$modelRadica->swGetStatus()->getLabel().')')
	)); 
	
?>

<div id="listDocumentos"></div>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
      	'buttonType'=>'submit',
        'type'=>'primary',
        'label'=> 'Radicar' ,
        'htmlOptions'=>array('class' => 'pull-right', 'onclick' => 'this.disabled=true;this.value="Enviando.. .";this.form.submit();' ),
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
	   if(isset($_GET['id_radica']) && $archval==0)
		  echo CHtml::script('detallesTramite("'.$_GET['id_radica'].'", true)');
    
?>