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

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Tramite pendientes por estudio</legend>
    	 
		<?php 
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'id'=>'gridConsultaTramites',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
			        	array(			       				        			
            				'value'=>'$data->isEditEstudio', 
            				'type'=>'raw',
	        			),	
			       		array(			       			
	        				'name' => 'id_radica',
	        				'htmlOptions'=>array('width'=>'40px',),
	        				'type'=>'raw',
            				'value'=>'CHtml::link($data->id_radica,"#", array("onClick"=>"js:detallesTramite($(this).text())"))',
	        			),		
	        			'no_sinestro',
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
				            'header'=>'Producto',
				            'value'=>'$data->idProducto->producto' ,
	        			),	       
	        			array(
	        				'header' => 'Estado',
	        				'value' => '$data->swGetStatus()->getLabel()',
	        				'name'=>'status',
	        				'filter'=>false
	        			),
	        			array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario',
	        				'value' => '$data->idmHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>false,	        
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


<script type="text/javascript">	
	var activeFocus;

    function detallesTramite(id_radica)
    {
	   	<?php echo CHtml::ajax(array(
        	'url'=>array('default/viewDetalle'), 
 			'type' => 'GET',
 			'data'=>array(
				'id_radica'=>'js:id_radica', 				
			), 
			'success'=> "function(data){
				$('body').append(data);
        	}"
      	))?>;
    	
        return false;  
    } 
</script>
