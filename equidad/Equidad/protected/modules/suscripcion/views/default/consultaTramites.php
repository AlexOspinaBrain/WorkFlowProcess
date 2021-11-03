<?php
/* @var $this RadicaController */
/* @var $model Radica */


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
    	<div class="well">
    	<br>
    	<legend>Consulta general tramites suscripción</legend>
    	 
    	<button class="btn btn-primary" type="button" id="btnFiltros"><i class="icon-filter icon-white"></i> Busqueda avanzada</button>    	

    	<?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Informe Excel',"#", array('class'=>'btn btn-primary', 'onclick'=>'js:descargaInforme();')); ?>

    	<?php 
			$this->renderPartial('_formFiltros', array('model'=>$model));
		?>

		<?php 
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'ajaxUpdate'=>false,
			    	'id'=>'gridConsultaTramites',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
			       		array(
	        				'name' => 'code',
	        				'htmlOptions'=>array('width'=>'40px',),
	        				'type'=>'raw',
            				'value'=>'CHtml::ajaxLink(
            						"$data->code", 
            						array("viewDetalle"), 
            						array(
            							"data"=>array("code"=>$data->code),
			       						"success"=>"function(data){\$(\'body\').append(data)}",
			       					)
			       				)',
			       			'htmlOptions'=>array('width'=>'15%'),
	        			),		
				       	array(
				            'name'=>'fecha_rad',
				            'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_rad))' ,
				            'htmlOptions'=>array('width'=>'150px'),
				            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'model'=>$model, 
				                'attribute'=>'fecha_rad', 
				                'language' => 'es',	                   				          
                				'defaultOptions' => array(                   		
                    				'dateFormat' => 'yy/mm/dd',                    				
                    				'showOtherMonths' => true,
                    				'selectOtherMonths' => true,
                    				'changeMonth' => true,
                    				'changeYear' => true,
                    				'showButtonPanel' => true,
                				),
                				
            				),  true), 
	        			),
	        			array(
				            'name'=>'id_persona',
				            'value'=>'"( " .$data->idPersona->tipo_doc ." " . $data->idPersona->documento . " ) ". 
				            $data->idPersona->nombre' ,	
				           // 'htmlOptions'=>array('width'=>'40%'),	        			
	        			),
	        			array(
	        				'name' => 'prioridad',
	        				'htmlOptions'=>array('width'=>'80px'),
	        				'filter'=>array('Urgente' => 'Urgente',	'Normal'=>'Normal'), 
	        			),
	        			array(
	        				'header' => 'Estado',
	        				'value' => '$data->swGetStatus()->getLabel()',
	        				'name'=>'status',
	        				'htmlOptions'=>array('width'=>'40px'),
	        				'filter'=>SWHelper::allStatuslistData( $model)
	        			),
			        ),
					'afterAjaxUpdate'=>'function(){
                       	$("#'.CHtml::activeId($model, 'fecha_rad').'").datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional["es"],[]));                       	
                       	$(".filters").find("td").each(function(){
                       		var id=$(this).find("input:text, select").attr("id");                       		
                       		$("#dialogFormFiltros").find("#"+id).val($(this).find("input:text, select").val());
                       	});
                       
                       	$("#"+activeFocus).focus();
                    }',
                    'beforeAjaxUpdate'=>'function(){
                       	activeFocus = $(":focus").attr("id");
                    }',
			    )
			);
		?>
		
    	</div>
    </div>  
</div>

<style type="text/css">
	.ui-datepicker{z-index: 600 !important}
</style>


<?php 
$c = $this->createUrl('default/exportarExcel');
$script = <<<EOD
  	var activeFocus;

	$(function() {
		$("#btnFiltros").click(function(){
			$( "#dialogFormFiltros" ).dialog('open');
		});
  		
	});

    function descargaInforme(){  
	   	window.location = "{$c}&"+$("#formSearch").serialize(); 
    } 
EOD;
Yii::app()->getClientScript()->registerScript('filtros',$script, CClientScript::POS_END);
?>