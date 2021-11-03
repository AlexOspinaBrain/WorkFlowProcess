<?php
/* @var $this RadicaController */
/* @var $model Radica */


?>
<div>
<br>
</div>
<div class="row">
	<div class="span2">
		<br>

		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php echo $this->renderPartial('_menuSarlaft'); ?>	
		</div>

	</div>

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Consulta general SARLAFT</legend>
    	 
    <div class="pull-left">
        <?php   

        	$muestt = true;      
        	if (Yii::app()->user->checkAccess('4.4.5')==true)    
        		$muestt = false;

            $this->widget(
                'bootstrap.widgets.TbButtonGroup',
                array(
                    'type' => 'primary',                    
                    'buttons' => array(
                        array('label' => 'Exportar',               
                            'disabled'=>$muestt,

                            //'htmlOptions' => array('id' => 'btnTapalote'),                            
                            'items' => array(                                
                                array('label' => 'Excel', 'url' => '#', 'icon' => 'icon-download', 'linkOptions' =>array('onClick'=>'js:exportExcel()')),
                            )
                        )
                    )
                )
            );
        ?>
    </div>
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



		
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'id'=>'gridConsultaTramites',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
                        array(
                            'class' => 'bootstrap.widgets.TbButtonColumn',
                            'template' => '{update} ',
                            'htmlOptions' => array('style' => 'width:30px;text-align: center;cursor: pointer',

                                 'onclick'=>'js:relizaActividad($(this).parent())', 'class'=>'id_radica'),
                            'headerHtmlOptions' => array('style' => 'width:30px'),
                            'buttons' => array(
                                'update' => array(
                                    'label' => false,   
                                    'visible'=> '(Yii::app()->user->checkAccess("4.4.5")) ? true : false',

                                    'url'=> '$data->id_radica',                                 
                                    'click' => 'js:function(){return false;}', 
                                    'icon' => 'icon-ok', 
                                ),          
                            ),
                        ),                        
			       		array(			   
			       			'header'=>'Id. Radicación',    			
	        				'name' => 'id_radica',
	        				'htmlOptions'=>array('width'=>'40px',),
	        				'type'=>'raw',
            				'value'=>'CHtml::link($data->id_radica,"#", array("onClick"=>"js:detallesTramite($(this).text())"))',
	        			),		
	        			'identificacion',
	        			'nombre',

      					array(
						 'name'=>'fecha_rad',
						 'filter'=>$dateisOn,
						 
						 'value'=>'Yii::app()->dateFormatter->format("dd/MM/y",strtotime($data->fecha_rad))' ,
						 ),
	        			array(
	        				'header' => 'Estado',
	        				'value' => '$data->swGetStatus()->getLabel()',
	        				'name'=>'status',
	        				//'htmlOptions'=>array('width'=>'40px'),
	        				'filter'=>SWHelper::allStatuslistData($model)
	        			),
	        			/*array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario',
	        				'value' => '$data->sarHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>CHtml::listData(Historial::model()->findAll("fecha_termino is null"), 'usuario_cod','usuarioCod.NombreCompleto'),	        				
	        				//'htmlOptions'=>array('width'=>'120px'),
	        			),*/
			        ),
					'afterAjaxUpdate'=>'function(){
                       	  $("#'.CHtml::activeId($model, 'fechai').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 
                       	  $("#'.CHtml::activeId($model, 'fechaf').'").datepicker(jQuery.extend({showMonthAfterYear:true},jQuery.datepicker.regional["es"],{"dateFormat":"yymmdd","changeMonth":"true","showButtonPanel":"true","changeYear":"true","showOtherMonths":"true","selectOtherMonths":"true"})); 

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

    function exportExcel(){     
        var summary = $(".summary").text();
        var cantidad = parseInt(summary.substring(summary.indexOf("de")+2,summary.indexOf("resultados")));
        if(cantidad > 5000){
            alert("Realice una consulta con menos de 5000 tramites");
            return;
        }
        location.href = '<?=$this->createUrl("default/consultaTramitesExcel")?>?'+ $("#gridConsultaTramites input").serialize();
    }

    function relizaActividad(fila){
       <?php echo CHtml::ajax(array(
            'url'=>array('default/formCestado'), 
            'type' => 'GET',
            'data'=>array(
                'id_radica'=>'js:$(fila).find(".id_radica").find("a").attr("href")+""',              

            ), 
            'success'=> "function(data){
                $('#mydialog').dialog('option', 'title', 'Cambia Estado');
                $( '#bodyDetalles' ).html(data);
                $( '#mydialog' ).dialog('open');

            }"
        ))?>;
        
        return false; 
    }
</script>
