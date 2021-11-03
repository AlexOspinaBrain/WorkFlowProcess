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

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Tramite pendientes tramitar</legend>
    	 
		<?php 

        	
        	if (Yii::app()->user->checkAccess('4.4.5')==true)    {
        		$muestt = array(	
						array(
			                'class' => 'bootstrap.widgets.TbButtonColumn',
			                'template' => '{update} ',
			                'htmlOptions' => array('style' => 'width:30px;text-align: center;cursor: pointer', 'onclick'=>'js:relizaActividad($(this).parent())', 'class'=>'id_radica'),
			                'headerHtmlOptions' => array('style' => 'width:30px'),
			                'buttons' => array(
			                    'update' => array(
			                        'label' => false,   
			                        'url'=> '$data->id_radica',                                 
			                        'click' => 'js:function(){return false;}', 
			                        'icon' => 'icon-ok', 
			                    ),          
		                	),
		                ),
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
	        			
	        			array(
	        				'header' => 'Identificación',
	        				'value'=>'$data->identificacion' ,
	        				'name'=>'identificacion' ,
							'type'=>'raw',
	        			),  
	        			array(
	        				'header' => 'Nombre',
	        				'value'=>'$data->nombre' ,
	        				'filter'=>false,
							'type'=>'raw',
	        			), 
	        			array(
	        				'header' => 'Producto',
	        				'value'=>'$data->idProducto->ramo' ,
	        				'filter'=>false ,
							'type'=>'raw',
	        			), 

	        			array(
	        				'header' => 'Fecha Ultima Tarea',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->sarHistorialPend->fecha_inicio))' ,
	        				'name'=>'fecha_ult',
	        				'filter'=>false   
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
	        				'value' => '$data->sarHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>false,	        
	        			),	        				        			
			        );
        	}else {
				$muestt = array(	

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
	        			
	        			array(
	        				'header' => 'Identificación',
	        				'value'=>'$data->identificacion' ,
	        				'name'=>'identificacion' ,
							'type'=>'raw',
	        			),  
	        			array(
	        				'header' => 'Nombre',
	        				'value'=>'$data->nombre' ,
	        				'filter'=>false,
							'type'=>'raw',
	        			), 
	        			array(
	        				'header' => 'Producto',
	        				'value'=>'$data->idProducto->ramo' ,
	        				'filter'=>false ,
							'type'=>'raw',
	        			), 
	        			'fecha_rad',
	        			array(
	        				'header' => 'Fecha Ultima Tarea',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->sarHistorialPend->fecha_inicio))' ,
	        				'name'=>'fecha_ult',
	        				'filter'=>false   
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
	        				'value' => '$data->sarHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>false,	        
	        			),	        				        			
			        );        		
        	}

			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'id'=>'gridConsultaTramites',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => $muestt,
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

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
'id'=>'mydialog',
'options'=>array(
    'modal' => true,
    'hide' => 'fade',
    'show' => 'fade',
    'autoOpen'=>false,
    'buttons' => array(
        array('text'=>'Aceptar','click'=> 'js:function(){$(this).dialog("close");}', 'class'=>'btn btn-primary')        
    ),
    'open'=>"js:function(){        
            $('.qq-upload-button').removeClass('qq-upload-button').addClass('btn btn-primary');        
            $(this).dialog('option', 'width', $('#bodyDetalles > div').width()+40);
            $(this).dialog('option', 'height', 'auto');

            if($(this).height()+190 > $(window).height())
                $(this).dialog('option', 'height', $(window).height()-80);
                   
            $(this).dialog({ position: ['center', 60] })
        }"
    ),
));
 
echo CHtml::tag('div', array('id'=>'bodyDetalles'));

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

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


    function relizaActividad(fila){
       <?php echo CHtml::ajax(array(
            'url'=>array('default/formReasigna'), 
            'type' => 'GET',
            'data'=>array(
                'id_radica'=>'js:$(fila).find(".id_radica").find("a").attr("href")+""',              

            ), 
            'success'=> "function(data){
                $('#mydialog').dialog('option', 'title', 'Reasigna');
                $( '#bodyDetalles' ).html(data);
                $( '#mydialog' ).dialog('open');

            }"
        ))?>;
        
        return false; 
    }



</script>
