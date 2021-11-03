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
			'id'=>'radicaForm',
    		'enableAjaxValidation'=>false,
    		'type' => 'horizontal',
    		'enableClientValidation'=>true,			
			'htmlOptions' => array('class' => 'well'),
		)); ?>
		
		<fieldset>
 
			<legend>Radicación proceso de suscripción</legend>
			<p class="note"> <span class="label label-important">Importante</span> Los datos con <span class="required">*</span> son obligatorios.</p>
		</fieldset>

		<?php echo $form->errorSummary(array($modelRadica, $modelHistorial, $modelPersona)); ?>

		<?php echo $form->dropDownListRow($modelPersona,'tipo_doc',array('C.C.'=>'C.C.','NIT'=>'NIT'),array('empty'=>'', 'class'=>'span1')); ?>
		<?php echo $form->textFieldRow($modelPersona,'documento', array('maxlength'=>10, 'append' => '', 'class'=>'span2', 'hint' => 'Escriba el documento sin codigo de verificación')); ?>
		<?php echo $form->textFieldRow($modelPersona,'nombre', array('class'=>'span4', 'maxlength'=>80, 'readonly'=>'readonly')); ?>

		<?php $certificado = CHtml::listData( Tipocertificado::model()->findAll(), 'id_certificado', 'desc_certificado');?>
	
		<?php echo $form->dropDownListRow($modelRadica,'id_certificado', $certificado, array('empty'=>'', 'class'=>'span2', 'onChange'=>'cambiaCertificado();')); ?>

		 <?php echo $form->dropDownListRow(
			    	$modelRadica,
			    	'prioridad', 
			    	array(
			    		'Urgente' => 'Urgente',
						'Normal'=>'Normal',
					), 
					array('empty'=>'', 'class'=>'span2','onChange'=>'js:changePrioridad($(this).val())')
				); 
		?>

		<div class="control-group offset1" style="display:none" id="divFecha_cierre">
			<?php echo $form->labelEx($modelRadica,'fecha_cierre', array('class' => 'control-label')); ?>

			<div class="controls">				
			<?php
				$this->widget('ext.timepicker.BJuiDateTimePicker',array(
				    'model'=>$modelRadica,
				    'attribute'=>'fecha_cierre',
				    'type'=>'datetime', 
				    'language'=>'es',
				    'options'=>array( 
				        'timeFormat'=> 'hh:mm:ss tt',
				        'dateFormat'=> 'yy/mm/dd',
				        'hourGrid'=>4,
				        'minuteGrid'=>10,
				     	'numberOfMonths'=> 2,
						'minDate'=> 0,
						'maxDate'=> 10,
				    ),
				    'htmlOptions'=>array(
				        'class'=>'input-medium'
				    )
				));
			?>
			<?php echo $form->error($modelRadica,'fecha_cierre'); ?>
			</div>
		</div>
	

		<?php $agencia = CHtml::listData( Agencia::model()->findAll(array('order'=>'descrip')), 'id_agencia', 'descrip');?>
		<?php echo $form->dropDownListRow($modelRadica,	'id_agencia', $agencia, array('empty'=>'', 'class'=>'span3','disabled'=>'disabled')); ?>
		<?php echo $form->hiddenField($modelRadica,	'id_agencia'); ?>

		<?php 
			sizeof( SWHelper::nextStatuslistData($modelRadica, array('includeCurrent' => false))) > 1 ?
				$optionsNextStatus = array(	'prompt' => '', 'includeCurrent' => false) :
				$optionsNextStatus = array('includeCurrent' => false);
		?>

		<div class="row">
      		<div class="span4">
      			<?php echo $form->dropDownListRow($modelRadica, 'status', SWHelper::nextStatuslistData( new Radica, $optionsNextStatus )); ?>
      		</div>
      		<div class="span5">
      			<?php echo $form->dropDownListRow($modelHistorial,'usuario_cod', array(),array('class'=>'span3')); ?>
      		</div>
    	</div>

    	<?php echo $form->textAreaRow($modelObservacion,'observacion',  array('class' => 'span4', 'rows' => 5)); ?>

		<?php echo $form->hiddenField($modelRadica, 'id_persona'); ?>
		<?php echo $form->hiddenField($modelRadica, '[pre]status'); ?>

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

		
		<?php
			$this->endWidget();
		?>
    </div>          
</div>

<?php
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		'id'=>'dialogCertificado',
		'options'=>array(
			'title'=>'Información',
			'modal'=>true,
			'autoOpen'=>false,
			'buttons' => array(
				array('text'=>'Aceptar', 'class'=> 'btn btn-primary','click'=> 'js:function(){$(this).dialog("close");}'),				
			),
		),
	));
 
	echo 'Opción para la generación de canets, certificaciones y reimpresiones.';
 
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<style type="text/css">
	.loading {
	    background-image: url('images/loading.gif');
	    background-position:  right center;
	    background-repeat: no-repeat;
	}
</style>

<?php
$script = <<<EOD
    function changePrioridad(valor){
    	if(valor == 'Urgente'){
    		$('#divFecha_cierre').show();
    		$('#Radica_fecha_cierre').val('');
    	}else{
    		$('#divFecha_cierre').hide();
    		$('#Radica_fecha_cierre').val(new Date());
    	}
    }
EOD;
Yii::app()->getClientScript()->registerScript('changePrioridad',$script, CClientScript::POS_END);
?>

<?php 
$script = <<<EOD
  	var x = $("#Persona_documento").parent();
  	x.find("span").remove();

  	var button = $('<button class="btn btn-primary" id="btnSearchCifin" type="button"/>')
    				.append('<i class="icon-search icon-white"></i>')
    				.height(30)
    				.click(buscaCifin);

  	x.append(button);

  	$("#Persona_documento").keypress(function(e) {
		if(e.which == 13) {
			buscaCifin();	
			return false;
		}

	});		

	$("#Persona_documento, #Persona_tipo_doc").change(buscaCifin);	
EOD;
Yii::app()->getClientScript()->registerScript('buttonSearchCifin',$script, CClientScript::POS_END);
?>

<script type="text/javascript">
    function buscaCifin()
    {
        <?php echo CHtml::ajax(array(
          	'url'=>array('persona/buscaIdentificacion'), 
 			'type' => 'POST',
 			'dataType'=>'json',
 			'beforeSend' => 'function(){ $("#'.CHtml::activeId($modelPersona, 'nombre' ).', #'.CHtml::activeId($modelPersona, 'documento' ).'").addClass("loading");}',
 			'complete' => 'function(){ $("#'.CHtml::activeId($modelPersona, 'nombre' ).', #'.CHtml::activeId($modelPersona, 'documento' ).'").removeClass("loading");}',
			'data'=>array(
				'tipo_doc'=>'js:$("#'.CHtml::activeId($modelPersona, 'tipo_doc' ).'").val()', 
				'documento'=>'js:$("#'.CHtml::activeId($modelPersona, 'documento' ).'").val()'
			), 
			'success'=> 'function(data){
				$("#'.CHtml::activeId($modelPersona, 'nombre' ).'").val(data.nombre);
				$("#'.CHtml::activeId($modelRadica, 'id_persona' ).'").val(data.id_persona);

				if(data.id_persona)
					$("#'.CHtml::activeId($modelPersona, 'nombre' ).'").attr("readonly","readonly");
				else
					$("#'.CHtml::activeId($modelPersona, 'nombre' ).'").removeAttr("readonly");
			}'
      	))?>;
        return false;  
    } 
</script>

<script type="text/javascript">
	$("#<?php echo CHtml::activeId($modelRadica, 'status')?>").change(loadUsersWorkflow);

    function loadUsersWorkflow()
    {
        <?php echo CHtml::ajax(array(
          	'url'=>array('default/GetUsersWorkflow'), 
 			'type' => 'POST',
 			'update'=> '#'.CHtml::activeId($modelHistorial, 'usuario_cod' ),
 			'data'=>'js:$("#radicaForm").serialize()' 
      	))?>;
        return false;  
    } 
</script>

<script type="text/javascript">	
    function detallesTramite(code, openDialog)
    {
	   	<?php echo CHtml::ajax(array(
        	'url'=>array('default/viewDetalle'), 
 			'type' => 'GET',
 			'data'=>array(
				'code'=>'js:code', 				
				'openDialog'=>'js:openDialog', 				
			), 
			'success'=> "function(data){
				$('body').append(data);
        	}"
      	))?>;
    	
        return false;  
    } 
</script>

<script type="text/javascript">	
    function cambiaCertificado()
    {
		if($("#Radica_id_certificado option:selected").text() == 'OTROS')
			$("#dialogCertificado").dialog("open"); return false;
		
        return false;  
    } 
</script>

<?php 
	if(isset($_GET['code']))
		echo CHtml::script('detallesTramite("'.$_GET['code'].'", true)');
?>