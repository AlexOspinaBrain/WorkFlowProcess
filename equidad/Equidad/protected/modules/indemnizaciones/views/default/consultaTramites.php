<?php
/* @var $this RadicaController */
/* @var $model Radica */


?>

<div class="row">
	<div class="span2">
		<br>
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php echo $this->renderPartial('_menuIndemnizaciones'); ?>	
		</div>

	</div>

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Consulta general tramites indemnizaciones</legend>
    	 
    	 <?php /*
    	<button class="btn btn-primary" type="button" id="btnFiltros"><i class="icon-filter icon-white"></i> Busqueda avanzada</button>    	

    	<?php echo CHtml::link('<i class="icon-download-alt icon-white"></i> Informe Excel',"#", array('class'=>'btn btn-primary', 'onclick'=>'js:descargaInforme();')); */?>

    	<?php 
			//$this->renderPartial('_formFiltros', array('model'=>$model));
		?>

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
	        				//'htmlOptions'=>array('width'=>'40px'),
	        				'filter'=>SWHelper::allStatuslistData( $model)
	        			),
	        			array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario',
	        				'value' => '$data->idmHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>CHtml::listData(Historial::model()->findAll("fecha_termino is null"), 'usuario_cod','usuarioCod.NombreCompleto'),	        				
	        				//'htmlOptions'=>array('width'=>'120px'),
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

	$(function() {
		$("#btnFiltros").click(function(){
			$( "#dialogFormFiltros" ).dialog('open');
		});
  		
	});

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

    function descargaInforme(){  
	   	window.location = "<?=$this->createUrl('default/exportarExcel')?>&"+$("#formSearch").serialize(); 
    } 
</script>
