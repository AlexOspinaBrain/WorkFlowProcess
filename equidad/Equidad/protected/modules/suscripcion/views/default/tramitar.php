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
			),
		)); ?>
		
		<fieldset> 
			<legend>Radicación tramite 
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
      		<div class="span5">
      			<?php echo $form->dropDownListRow($modelHistorial,'usuario_cod', array(),array('class'=>'span3')); ?>
      		</div>
    	</div>

    	<div class="row" id="containerForms">
    		<div class="span9" id="formExpedicion" style="display:none">
    			<?php echo $form->textFieldRow($modelRadica,'poliza', array('class'=>'span2', 'style' => 'text-transform: uppercase')); ?>

    			<?php $producto = CHtml::listData( Producto::model()->findAll("active=true"), 'id_producto', 'productoCod');?>	
    			
				<?php echo $form->dropDownListRow($modelRadica,'id_producto', $producto, array('empty'=>'', 'class'=>'input-xlarge')); ?>

				<?php $certificado = CHtml::listData( Tipocertificado::model()->findAll(), 'id_certificado', 'desc_certificado');?>
				<?php echo $form->dropDownListRow($modelRadica,'id_certificado', $certificado, array('empty'=>'', 'class'=>'span2')); ?>

				<?php echo $form->textFieldRow($modelRadica,'cant_doc', array('class'=>'input-mini',  'type'=>"number")); ?>
				
				<?php $canal = CHtml::listData( Canal::model()->findAll(), 'id_canal', 'canal');?>	
    			<?php unset($canal[0]);?>	
				<?php echo $form->dropDownListRow($modelRadica,'id_canal', $canal, array('empty'=>'', 'class'=>'span2')); ?>
		

				<?php $sarlaft = CHtml::listData( Sarlaft::model()->findAll(), 'id_sarlaft', 'desc_sarlaft');?>	
    			<?php unset($sarlaft[0]);?>	

				<?php $garantias = CHtml::listData( Garantias::model()->findAll(), 'id_garantia', 'desc_garantia');?>	
    			<?php unset($garantias[0]);?>	
				<?php echo $form->dropDownListRow($modelRadica,'id_garantia', $garantias, array('empty'=>'', 'class'=>'span3')); ?>

				<?php echo $form->datepickerRow(
            $modelRadica,
            'fecha_garantia',
            array(
                'options' => array('language' => 'es'),                
                'append' => '<i class="icon-calendar"></i>',
                'class'=>'input-small',
                'options'=>array('format'=>'yyyy/mm/dd')
            )
        ); ?>

    		</div>
    		<div class="span9" id="formDevolucion" style="display:none">

    			<?php $causal = CHtml::listData( Causal::model()->findAll(), 'causal', 'causal');?>	
				<?php echo $form->dropDownListRow($modelHistorial,'causal', $causal, array('empty'=>'', 'class'=>'span3')); ?>

    		</div>

    		<div class="span9" id="formComentarios" style="display:none">
    			<?php echo $form->textAreaRow($modelObservacion,'observacion',  array('class' => 'span4', 'rows' => 5)); ?>
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
    		</div>
    	</div>

    	<?php echo $form->hiddenField($modelRadica,'id_agencia'); ?>
    	<?php echo $form->hiddenField($modelRadica, '[pre]status'); ?>
		
		<?php
			$this->endWidget();
		?>		
    </div>          
</div>

<script type="text/javascript">
	$(function() {		
    	viewForm();    	
	});

    function loadUsersWorkflow()
    {    	
        <?php echo CHtml::ajax(array(
          	'url'=>array('default/GetUsersWorkflow'), 
 			'type' => 'POST',
 			'data'=>'js:$("#tramitar-form").serialize()', 
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
		case 'swRadica/recibir_expedicion':
		case 'swRadica/expedicion':
			$("#formDevolucion").fadeOut();
		 	$("#formExpedicion").fadeIn();
    		$("#formComentarios").fadeIn();
		break;
		case 'swRadica/verificar_comercial':
		  	$("#formExpedicion").fadeOut();
		  	$("#formComentarios").fadeIn();
    		$("#formDevolucion").fadeIn();
		break;
		default:
		  $("#containerForms > div.span9").fadeOut();
		}
    } 
</script>