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

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Pendientes por tramitar</legend>
    	 
		<?php 

	$dateisOn = $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'name' => 'Radica[fechai]',
 								'value' => $model->fechai,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yymmdd',                    				
                    				'showOtherMonths' => true,
                    				'selectOtherMonths' => true,
                    				'changeMonth' => true,
                    				'changeYear' => true,
                    				'showButtonPanel' => true
                				),
                				'htmlOptions'=>array(
 									'style'=>'height:20px;width:70px',
 									'readonly'=>'readonly'
                				),
            				),true) . ' a ' .
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'name' => 'Radica[fechaf]',
 								'value' => $model->fechaf,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yymmdd',                    				
                    				'showOtherMonths' => true,
                    				'selectOtherMonths' => true,
                    				'changeMonth' => true,
                    				'changeYear' => true,
                    				'showButtonPanel' => true
                				),
                				'htmlOptions'=>array(
 									'style'=>'height:20px;width:70px',
 									'readonly'=>'readonly'
                				),
                				
            				),true);

	$dateisOnu = $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'name' => 'Radica[fechaiu]',
 								'value' => $model->fechaiu,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yymmdd',                    				
                    				'showOtherMonths' => true,
                    				'selectOtherMonths' => true,
                    				'changeMonth' => true,
                    				'changeYear' => true,
                    				'showButtonPanel' => true
                				),
                				'htmlOptions'=>array(
 									'style'=>'height:20px;width:70px',
 									'readonly'=>'readonly'
                				),
            				),true) . ' a ' .
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'name' => 'Radica[fechafu]',
 								'value' => $model->fechafu,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yymmdd',                    				
                    				'showOtherMonths' => true,
                    				'selectOtherMonths' => true,
                    				'changeMonth' => true,
                    				'changeYear' => true,
                    				'showButtonPanel' => true
                				),
                				'htmlOptions'=>array(
 									'style'=>'height:20px;width:70px',
 									'readonly'=>'readonly'
                				),
                				
            				),true);


        	
        	if (Yii::app()->user->checkAccess('4.5.5')==true)    {
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
	        				'header' => 'Afiliacion',
	        				'value'=>'$data->afiliacion' ,
	        				'name'=>'afiliacion' ,
							'type'=>'raw',
	        			),  
	        			array(
	        				'header' => 'Proceso',
	        				'value'=>'$data->idProceso->proceso' ,
	        				//'filter'=>false ,
							'type'=>'raw',
	        			), 	        			
	        			array(
	        				'header' => 'Tipología',
	        				'value'=>'$data->idTipologia->tipologia' ,
	        				//'filter'=>false,
							'type'=>'raw',
	        			), 

      					array(
							 'name'=>'fecha_rad',
							 'filter'=>$dateisOn,
							 'htmlOptions'=>array('style'=>'text-align: center',),
							 'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->fecha_rad))' ,
							 	
						 ),
	        			array(
	        				'name'=>'fecha_ult',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->arlHistorialPend->fecha_inicio))' ,
	        				
	        				'filter'=>$dateisOnu   
	        			),     
						array(
	        				'header'=>'Fecha Limite',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'$data->arlHistorialPend->fecha_limite?Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->arlHistorialPend->fecha_limite)):null' ,
	        				
	        				//'filter'=>$dateisOnu   
	        			),  
	        			array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario (consulte por usuario ej: aospina)',
	        				'value' => '$data->arlHistorialPend->usuarioCod->NombreCompleto',	        				
	        				//'filter'=>true,	        
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
	        				'header' => 'Afiliacion',
	        				'value'=>'$data->afiliacion' ,
	        				'name'=>'afiliacion' ,
							'type'=>'raw',
	        			),  
	        			array(
	        				'header' => 'Proceso',
	        				'value'=>'$data->idProceso->proceso' ,
	        				//'filter'=>false ,
							'type'=>'raw',
	        			), 	        			
	        			array(
	        				'header' => 'Tipología',
	        				'value'=>'$data->idTipologia->tipologia' ,
	        				//'filter'=>false,
							'type'=>'raw',
	        			), 
      					array(
						 'name'=>'fecha_rad',
						 'filter'=>$dateisOn,
						 'htmlOptions'=>array('style'=>'text-align: center',),
						 'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->fecha_rad))' ,
						 ),  	        			
	        			array(
	        				'name'=>'fecha_ult',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->arlHistorialPend->fecha_inicio))' ,	        				
	        				
	        				'filter'=>$dateisOnu   
	        			),    
	        			array(
	        				'header'=>'Fecha Limite',
	        				'htmlOptions'=>array('style'=>'text-align: center',),
	        				'value'=>'$data->arlHistorialPend->fecha_limite?Yii::app()->dateFormatter->format("dd/MM/y hh:mm:ss",strtotime($data->arlHistorialPend->fecha_limite)):null' ,
	        				
	        				//'filter'=>$dateisOnu   
	        			),  
	        			array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario (consulte por usuario ej: aospina)',
	        				'value' => '$data->arlHistorialPend->usuarioCod->NombreCompleto',	        				
	        				//'filter'=>true,
	        			),	        				        			
			        );        		
        	}

			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'id'=>'gridConsultaTramites',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'condensed hover',
			        'rowCssClassExpression'=>'$data->semaforo()',
			        'columns' => $muestt,
					'afterAjaxUpdate'=>'function(){
                       	

                       	  $("#'.CHtml::activeId($model, 'fechai').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 
                       	  $("#'.CHtml::activeId($model, 'fechaf').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 

                       	  $("#'.CHtml::activeId($model, 'fechaiu').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 
                       	  $("#'.CHtml::activeId($model, 'fechafu').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 

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
