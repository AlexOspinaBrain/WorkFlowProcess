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
      		<div class="span5" id="rowUsuario" style="display:none">
      			<?php echo $form->dropDownListRow($modelHistorial,'usuario_cod', array(),array('class'=>'span3')); ?>
      		</div>
    	</div>

    	<div class="row" id="containerForms">
    		<div class="span9" id="formExpedicion" style="display:none">   				
    			<?php $producto = CHtml::listData( Producto::model()->findAll("active=true"), 'id_producto', 'productoCod');?>	    			
				<?php echo $form->dropDownListRow($modelRadica,'id_producto', $producto, array('class'=>'input-xlarge', 'onChange'=>'js:$("#dialogPlaseWait").dialog("open");$("#tramitar-form").submit();')); ?>
    			<?php echo $form->textFieldRow($modelRadica,'poliza', array('append' => '', 'class'=>'span2', 'readonly' => true)); ?>
    			<?php echo $form->textFieldRow($modelRadica,'certificado', array('class'=>'span2', 'readonly' => true)); ?>
    			<?php echo $form->textFieldRow($modelRadica,'cant_ordenes', array('class'=>'span1', 'readonly' => true)); ?>
    			<?php echo $form->textFieldRow($modelRadica,'usuario_osiris', array('class'=>'span2', 'readonly' => true)); ?>
    			<?php echo $form->textFieldRow($modelRadica,'fecha_expe', array('class'=>'span2', 'readonly' => true)); ?>
    			
    			<?php $sarlaft = CHtml::listData( Sarlaft::model()->findAll(), 'id_sarlaft', 'desc_sarlaft');?>	
    			<?php unset($sarlaft[0]);?>	

    			<?php $garantias = CHtml::listData( Garantias::model()->findAll(), 'id_garantia', 'desc_garantia');?>	
    			<?php unset($garantias[0]);?>	
				<?php echo $form->dropDownListRow($modelRadica,'id_garantia', $garantias, array('empty'=>'', 'class'=>'span3')); ?>

    			<div class="row">
    				<div class="span3">
						<?php echo $form->checkBoxRow($modelRadica, 'sarlaft',array('onChange'=>'js:changeSarlaft();')); ?>
    				</div>
    				<div class="span5" id="rowEstadoSarlaft" style=	"display:none">
						<?php echo $form->dropDownListRow($modelRadica,'id_sarlaft', $sarlaft, array('empty'=>'', 'class'=>'span4')); ?>
    				</div>
    			</div>
    		</div>

    		<div class="span9" id="formDevolucion" style="display:none">
    			<?php $causal = CHtml::listData( Causal::model()->findAll(), 'causal', 'causal');?>	
				<?php echo $form->dropDownListRow($modelHistorial,'causal', $causal, array('empty'=>'', 'class'=>'span3')); ?>
    		</div>

    		<div class="span9" id="formGeneral" style="display:none">

    			<?php echo $form->textAreaRow($modelObservacion,'observacion',  array('class' => 'span4', 'rows' => 5)); ?>
    			<div class="form-actions">
					<?php 
		    			$this->widget('bootstrap.widgets.TbButton', array(
		            		'buttonType'=>'submit',
		            		'type'=>'primary',
		            		
		            		'label'=> 'Guardar' ,
		            		'htmlOptions'=>array('class' => 'pull-right', 'name'=>'Guardar'),
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

	<?php 
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'dialog_polizas',		
			'options'=>array(
    			'title'=>'Polizas',
    			'width'=>'auto',
    			'modal'=>true,
    			'autoOpen'=>false,
    			'buttons' => array(
        			array('text'=>'Aceptar', 'class'=> 'btn btn-primary','click'=> 'js:function(){$(this).dialog("close");}')        			
    			),
			),
		));

		$this->widget(
		    'bootstrap.widgets.TbGridView',		    
		    //'zii.widgets.grid.CGridView',		    
		    array(
		    	'ajaxUpdate'=>true,
		    	'id'=>'gridPolizas',		    	
		        'dataProvider' => $resultOsiris->search(),
				'filter' => $resultOsiris,
				'type' => 'striped bordered condensed',
				'selectableRows'=>1,
				'selectionChanged'=>"function(){
					var row = $('#gridPolizas').find('input:checked').parent().parent();
					$('#Radica_poliza').val(row.find('.POLIZA').text());
					$('#Radica_certificado').val(row.find('.CERTIF').text());
					$('#Radica_usuario_osiris').val(row.find('.SUCREA').text());
					$('#Radica_cant_ordenes').val(row.find('.CANTIDAD').text());
					$('#Radica_fecha_expe').val(row.find('.FECEXP').text());
				}",
			    'columns' => array(	
			    	array('class'=>'CCheckBoxColumn'), 
					array('name'=>'POLIZA', 'htmlOptions'=>array('class'=>'POLIZA')),
					array('name'=>'CERTIF', 'htmlOptions'=>array('class'=>'CERTIF')),
					array('name'=>'CANTIDAD', 'filter'=>false, 'htmlOptions'=>array('class'=>'CANTIDAD')),
					array('name'=>'FECEXP', 'filter'=>false, 'htmlOptions'=>array('class'=>'FECEXP')),
					array('name'=>'SUCREA', 'filter'=>false, 'htmlOptions'=>array('class'=>'SUCREA'))
				),
			)
		);
 
		$this->endWidget('zii.widgets.jui.CJuiDialog'); 
	?>

	<?php
		$this->beginWidget('zii.widgets.jui.CJuiDialog',
		    array(
		        'id'=>'dialogPlaseWait',
		        'options'=>array(            
		            'modal'=>true,
		            'autoOpen'=>false,// default is true
		            'closeOnEscape'=>false,
		            'open'=>'js:function(){ $(".ui-dialog-titlebar-close").hide(); }'
		        ))
			);
		echo CHtml::tag('h4', array('style'=>'text-align: center;'), "Espere por favor ...");
		$this->endWidget('zii.widgets.jui.CJuiDialog'); 
	?>

<?php 
$script = <<<EOD
  	var x = $("#Radica_poliza").parent();
  	x.find("span").remove();

  	var button = $('<button class="btn btn-primary" id="btnSearchCifin" type="button"/>')
    				.append('<i class="icon-search icon-white"></i>')
    				.height(30)
    				.click(function(){\$("#dialog_polizas").dialog("open")});

  	x.append(button);

  /*	var actualizaTable =  function(){
  		$("#gridPolizas").find(':checkbox').change(function(){alert($(this).parent().parent().html())})
  	}
  	$( document ).ready(actualizaTable);*/

EOD;
Yii::app()->getClientScript()->registerScript('buttonSearchCifin',$script, CClientScript::POS_END);
?>

<script type="text/javascript">
	$(function() {
		loadUsersWorkflow();
    	viewForm();
    	changeSarlaft();
    	//changePoliza(<?=CJSON::encode($resultOsiris)?>);
	});

	$("#<?php echo CHtml::activeId($modelRadica, 'status' )?>").ready(loadUsersWorkflow);

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
		case 'swRadica/cierre':
			$("#rowUsuario").fadeOut();
			$("#formDevolucion").fadeOut();   
		 	$("#formExpedicion").fadeIn();
    		$("#formGeneral").fadeIn();    		
		break;
		case 'swRadica/verificar_comercial':
		case 'swRadica/recibir_tecnico':
			$("#rowUsuario").fadeIn();
			$("#formExpedicion").fadeOut();
    		$("#formGeneral").fadeIn();    		
    		$("#formDevolucion").fadeIn();   
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