<div class="span6" style="margin-left: 0px; ">
	
<?php 
	$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id' => 'tramitar-form',
		'type' => 'horizontal',
		//'action'=>array('default/formReasigna'),
    	'enableClientValidation'=>false,		
    	'enableAjaxValidation'=>true,	
		'htmlOptions' => array(
			'class' => 'well', 
         ),
		'clientOptions' => array(      
			'validateOnChange' => false,
			'validateOnSubmit' => true,
			'afterValidate'=>'js:afterSave',
			'beforeValidate'=>"js:function(form){               
                $('#botonGuarda').html('Espere...').attr('disabled', 'disabled');
                return true;
            }"
		)	
	)); 
?>

	<div class="row vertical">
		<div class="span2">			
			<?php


			
			

			$listClases = CHtml::listData($users, 'usuario_cod', 'nmcom');

				echo $form->dropDownListRow(
					$modelHistorial,
					'usuario_cod',
					$listClases
				); 

			
			?>
		</div>
	</div>
<?php
	$this->endWidget();
?>

</div>
<script type="text/javascript">
	$(function($) {	
		$('#mydialog').dialog({			
  			'buttons':{
    			"Aceptar":{
      				'text':'Aceptar',
      				'class':'btn btn-primary',
      				'id':'botonGuarda',
      				'click': function() {
          				$( "#tramitar-form" ).submit();
        			}

    			},
    			"Cancelar":{
      				'text':'Cancelar',
      				'class':'btn',
      				'click': function() {
          				$( this ).dialog( "close" );
        			}
    			}
  			}
  		});

	});

	function afterSave(form, data, hasError){
		if(data.save){
			$.fn.yiiGridView.update("gridConsultaTramites");
			$('#botonGuarda').html('Aceptar').attr('disabled', false);
			$( '#mydialog' ).dialog( "close" );
			
			//if(data.id_radica)
			//	detallesTramite(data.id_tramite, data.no_tramite);
		}else
			$('#botonGuarda').html('Aceptar').attr('disabled', false);
	}

</script>

<style type="text/css">
.input-append .btn {height: 30px;}
.input-append input {font-size: 14px;}
</style>
