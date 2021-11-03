<div>
<br>
</div>

<div class="row">
	<div class="span2">
		<br>		
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php  echo $this->renderPartial('_menuArl'); ?>	
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
	    'validateOnChange'=>true      		
   	),
    'htmlOptions' => array(
		'class' => 'well', 
		'enctype' => 'multipart/form-data'
	),
)); ?>
		
<fieldset> 
	<legend>Radicación Procesos ARL</legend>
<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*
</span> son obligatorios.</p>

<?php  if ($archval>0){
?>
        <div class="alert alert-danger">Nombre de archivo incorrecto (debe ser sin caracteres especiales).</div>

<?php
    }

?>

</fieldset>



<?php echo $form->errorSummary(array($modelRadica,$modelHistorial)); ?>

<?php echo $form->textFieldRow($modelRadica,'afiliacion', array('class'=>'input-small', 'maxlength'=>15));?>
<?php 


    $proceso = CHtml::listData( Proceso::model()->findAll(array("order"=>"proceso")), 'idproceso', 'proceso');

	unset($proceso[0]);
	echo $form->dropDownListRow($modelRadica,'idproceso', $proceso, 
        array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge',

            'ajax'=>array(
                'type'=>'POST',
                'url'=>array('default/findTipologia'),
                'update'=>'#'.CHtml::activeId($modelRadica,'idtipologia'),
                'beforeSend'=> 'function(){
                    $("#'.CHtml::activeId($modelRadica,'idtipologia').'").html("");
                    
                  }',
            )
        )); 
	
    $tipologia = CHtml::listData( Tipologia::model()->findAll(array("order"=>"tipologia")), 
        'idtipologia', 'tipologia');

    unset($tipologia[0]);
    echo $form->dropDownListRow($modelRadica,'idtipologia', $tipologia, array('prompt' => '', 'includeCurrent' => false,'class'=>'input-xlarge'));
?>

<div id="empresa">
    <?php echo $form->textFieldRow($modelRadica,'intermediario', 
    array('class'=>'input-xlarge', 'maxlength'=>100, 'style' => 'text-transform: uppercase'));?>
    <?php echo $form->textFieldRow($modelRadica,'nitintermediario', 
    array('class'=>'input-xlarge', 'maxlength'=>9));?>
    <?php echo $form->textFieldRow($modelRadica,'ejecutivov', 
    array('class'=>'input-xlarge', 'maxlength'=>100, 'style' => 'text-transform: uppercase'));?>
    <?php echo $form->textFieldRow($modelRadica,'franquicia', 
    array('class'=>'input-xlarge', 'maxlength'=>100));?>
    <?php echo $form->textFieldRow($modelRadica,'vlrcot', 
    array('class'=>'input-small', 'maxlength'=>9));?>
    <?php echo $form->textFieldRow($modelRadica,'vlrnomina', 
    array('class'=>'input-small', 'maxlength'=>9));?>
    <?php echo $form->textFieldRow($modelRadica,'ntrabajadores', 
    array('class'=>'input-small', 'maxlength'=>5));?>
    <?php echo $form->textFieldRow($modelRadica,'representante', 
    array('class'=>'input-xlarge', 'maxlength'=>100, 'style' => 'text-transform: uppercase'));?>
    <?php echo $form->textFieldRow($modelRadica,'nit', 
    array('class'=>'input-small', 'maxlength'=>10));?>
    <?php echo $form->textFieldRow($modelRadica,'razonsocial', 
    array('class'=>'input-xlarge', 'maxlength'=>100, 'style' => 'text-transform: uppercase'));?>
</div>
<div id="independiente">
    <?php echo $form->textFieldRow($modelRadica,'vlrcontrato', 
    array('class'=>'input-small', 'maxlength'=>9));?>
    <?php echo $form->textFieldRow($modelRadica,'vlrmescontrato', 
    array('class'=>'input-small', 'maxlength'=>9));?>

    <?php echo $form->dropDownListRow($modelRadica,'riesgo',array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),
            array('class'=>'input-small')); ?>    
</div>


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

    $(function() {  
        $("#empresa").hide();
        $("#independiente").hide();

            if($("#<?=CHtml::activeId($modelRadica, 'idproceso' )?>").val()==1){
                $("#empresa").show();
                $("#independiente").hide();
            }
            else if($("#<?=CHtml::activeId($modelRadica, 'idproceso' )?>").val()==3){
                $("#empresa").hide();
                $("#independiente").show();
            }else{
                $("#empresa").hide();
                $("#independiente").hide();
            }       

        $("#<?=CHtml::activeId($modelRadica, 'idproceso' )?>").change(function (){
           
            if($("#<?=CHtml::activeId($modelRadica, 'idproceso' )?>").val()==1){
                $("#empresa").show();
                $("#independiente").hide();
            }
            else if($("#<?=CHtml::activeId($modelRadica, 'idproceso' )?>").val()==3){
                $("#empresa").hide();
                $("#independiente").show();
            }else{
                $("#empresa").hide();
                $("#independiente").hide();
            }
            
            
        });
    });

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
	   if(isset($_GET['id_radica']) )
		  echo CHtml::script('detallesTramite("'.$_GET['id_radica'].'", true)');
    
?>