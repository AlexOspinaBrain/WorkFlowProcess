<?php
/* @var $this ProveedorController */
/* @var $model Proveedor */

?>
<div class="row">
	<div class="span2">
		<br>		
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php echo $this->renderPartial('_menuSuscripcion'); ?>	
		</div>
	</div>

	<div class="span10">
		<br>
		<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
			'id'=>'tramitar-form',
    		'type' => 'horizontal',
    		'enableClientValidation'=>true,			
			'htmlOptions' => array(
				'class' => 'well',
				'enctype' => 'multipart/form-data'
			),
		)); ?>
		
		<fieldset> 
			<legend>Expedición tramite
				<?= CHtml::ajaxLink(
						$modelRadica->code,             						
						array("viewDetalle"), 
            			array(
            				"data"=>array("code"=>$modelRadica->code),
			       			"success"=>"function(data){\$('body').append(data)}",
			       		)
			       	)?>
			</legend>

			<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>
		</fieldset>

		<?php echo $form->errorSummary( array($modelRadica, $modelHistorial)); ?>
		

		<?php 
			sizeof( SWHelper::nextStatuslistData($modelRadica, array('includeCurrent' => false))) > 1 ?
				$optionsNextStatus = array(	'prompt' => '', 'includeCurrent' => false) :
				$optionsNextStatus = array('includeCurrent' => false);
		?>

		<div class="row">
      		<div class="span4">
      			<?php echo $form->dropDownListRow($modelRadica, 'status', SWHelper::nextStatuslistData( Radica::model()->findByAttributes(array('code'=>$modelRadica->code)), $optionsNextStatus ), array('onChange'=>'js:loadUsersWorkflow();viewForm();')); ?>

      		</div>
      		<div class="span5" id="rowUsuario">
      			<?php echo $form->dropDownListRow($modelHistorial,'usuario_cod', array(),array('class'=>'span3')); ?>
      		</div>
    	</div>

    	<div class="row" id="containerForms">
    		<div class="span9" id="formVerificaComercial" style="display:none">
    			<div class="control-group ">
    				<label class="control-label" for="RadicaObs_observacion">Adjuntos</label>
    				<div class="controls">
					<?php
	    				$this->widget('CMultiFileUpload', array(
	    					'model'=>$modelRadica,
			                'attribute' => 'susAdjuntos',
			               // 'accept' => 'pdf|doc|docx|jpg|rar', 
			                'duplicate' => 'Este archivo ya ha sido seleccionado!', 
			                'denied' => 'El tipo de archivo no es permitido',
							'remove'=>'[Borrar]',

			               	'options'=>array(
                				'afterFileSelect'=>'function(e ,v ,m){                					
                					var fileSize = e.files[0].size;

                     				if(fileSize>5*1024*1024) 
                        				alert("Este archivo es demasiado grande, seleccione un archivo de 5 Mb");
                      
                     				return false;
                     			}',                                                       
             				),

			            ));
	    			?>
	    			
    				</div>
    			</div>

    			<?php echo $form->textAreaRow($modelObservacion,'observacion',  array('class' => 'span4', 'rows' => 5)); ?>
    			<div class="form-actions">
					<?php 
		    			$this->widget('bootstrap.widgets.TbButton', array(
		            		'buttonType'=>'submit',
		            		'type'=>'primary',
		            		'label'=> 'Radicar' ,
		            		'htmlOptions'=>array('class' => 'pull-right'),
		  				)); 
		  			?>
				</div>
    		</div>
    	</div>
		
		<?php
			$this->endWidget();
		?>
    </div>          
</div>

<script type="text/javascript">
	$(function() {
		loadUsersWorkflow();
    	viewForm();
    	changeSarlaft();
	});
	$("#<?php echo CHtml::activeId($modelRadica, 'status' )?>").ready(loadUsersWorkflow);
    function loadUsersWorkflow()
    {    	
        <?php echo CHtml::ajax(array(
          	'url'=>array('default/GetUsersWorkflow'), 
 			'type' => 'POST',
 			'data'=>array(
				'itemname'=>'js:$("#'.CHtml::activeId($modelRadica, 'status' ).'").val()', 				
			), 
			'success' => 'js:function(data){
				$("#'.CHtml::activeId($modelHistorial, 'usuario_cod' ).'").html(data);
				$("#'.CHtml::activeId($modelHistorial, 'usuario_cod' ).'").val('.$modelHistorial->usuario_cod.');
			} '
      	))?>;
        return false;  
    } 

    function viewForm()
    {   
    	switch($("#Radica_status").val())
		{
		case 'swRadica/cierre':
			$("#rowUsuario").fadeOut();
		 	$("#formVerificaComercial").fadeIn();	
		break;
		case 'swRadica/recibir_tecnico':
    		$("#rowUsuario").fadeIn();
    		$("#formVerificaComercial").fadeIn();	
		break;
		default:
		  $("#containerForms > div.span9").fadeOut();
		}
    } 

    function changeSarlaft()
    {   
    	if($("#<?=CHtml::activeId($modelRadica, 'sarlaft' )?>").is(':checked'))
    		$("#rowEstadoSarlaft").fadeIn();
    	else
    		$("#rowEstadoSarlaft").fadeOut();
    } 
</script>