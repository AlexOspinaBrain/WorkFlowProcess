
<div>
<br>
</div>
<div class="row">
	<div class="span2">
		<br>

		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php echo $this->renderPartial('_menuArl'); ?>	
		</div>

	</div>

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Informes ARL</legend>
			

<?php

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-inf1',
    'type'=>'inline',
    //'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnChange'=>false,
        'validateOnSubmit'=>true
        
    ),

));

?>

<?php echo $form->dropDownListRow($modelInformesForm,'informe',array('1'=>'Gestión & Tiempos','2'=>'Radicados Agencia')); ?>

<?php echo $form->error($modelInformesForm, 'informe') ?>	
	
<?php echo $form->labelEx($modelInformesForm,'fechai'); ?>

<?php
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'attribute' => 'fechai',
 								'model'=> $modelInformesForm,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yy-mm-dd',                    				
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
            				));
	echo $form->error($modelInformesForm, 'fechai');

?>


<?php echo $form->labelEx($modelInformesForm,'fechaf'); ?>

<?php

$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				                'attribute' => 'fechaf',
 								'model'=> $modelInformesForm,
				                
				                'language' => 'es',	                   				          
                				'options' => array(                   		
                    				'dateFormat' => 'yy-mm-dd',                    				
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
            				));
echo $form->error($modelInformesForm, 'fechaf');

echo "&nbsp;";


 $this->widget('bootstrap.widgets.TbButton', array(
      	'buttonType'=>'submit',
        'type'=>'primary',
        'label'=> 'Generar' ,
        'htmlOptions'=>array('class' => 'pull-center', 'onclick' => 'this.disabled=true;this.value="Enviando.. .";this.form.submit();' ),
  	));

$this->endWidget();

?>


<div class="form well">
	
<?php
if ($informeus && ($modelInformesForm->informe === '1')){



            $this->widget(
                'bootstrap.widgets.TbButtonGroup',
                array(
                    'type' => 'primary',                    
                    'buttons' => array(
                        array('label' => 'Exportar',               
                            
                            'items' => array(                                
                                array('label' => 'Excel', 'url' => '#', 'icon' => 'icon-download', 'linkOptions' =>array('onClick'=>'js:exportExcelG()')),
                            )
                        )
                    )
                )
            );

	$outt = $modelInformesForm->informe === '1'?'Atendidos (> 30 minutos)':'Atendidos (> 3 dias)';
	$inn = $modelInformesForm->informe === '1'?'Atendidos (<= 30 minutos)':'Atendidos (<= 3 dias)';

	$gridDataProvider = new CArrayDataProvider($informeus,
			array(
				'sort'=>array(
	        		'attributes'=>array(
	             		'usuario', 'cantidad', 'usudesc', 'instd', 'outstd', 'fechaiq', 'fechaiq', 'infor'
	        		),
	    		),
				'pagination'=>array('pageSize'=>99999999)
			)

		);

	$this->widget(
	    'bootstrap.widgets.TbGridView',
	    array(
	        'type' => 'striped bordered condensed',
	        'dataProvider' => $gridDataProvider,
	        'htmlOptions'=> array('style'=>'font-size:11px'),
	        'enableSorting'=>true,
	        'columns' => array(
	        		/*array(
	        				'header'=>'Usuario', 
	        				'name'=>'usudesc',
	        				'type' => 'raw',
	        				'value'=>'CHtml::link($data["usudesc"],"#", array("onClick"=>"js:exportExcel($(this).text())"),false)',
	        		),*/
	        		array(
			       			'header'=>'Nombre',    			
            				'name'=>'usuario',
	        			),	 
	        		array(
			       			'header'=>'Numero de Casos Atendidos',    			
            				'name'=>'cantidad',
            				
	        			),	 
	        		array(
			       		'header'=>$modelInformesForm->informe === '1'?'Atendidos (<= 30 minutos)':'Atendidos (<= 3 dias)',
			       		'name'=>'instd',
            				
	        			),	        
	        		array(
	        			'header'=>$outt,
			       		'name'=>'outstd',
            				
	        			),		        		
	        	array(
	        		'header'=>'Ver Excel',
					'htmlOptions' => array('nowrap'=>'nowrap'),
					'class'=>'bootstrap.widgets.TbButtonColumn',
					'template' => '{view}',

			        'buttons' => array(
			            'view' => array(
			                'label' => false,                            
			                //'url'=> '$data->usudesc',
			                'icon' => 'icon-eye-open',  
			                'url'=>'Yii::app()->createUrl("arl/default/informeEntramitexls", 
			                			array("usu"=>$data[usudesc],"fechai"=>$data[fechaiq],"fechaf"=>$data[fechafq],
			                			"infor"=>$data[infor])

			                		)',
			                
			            ),  
			        ),
			        'htmlOptions'=>array('width'=>'5%', 'style'=>'text-align: center'),					
					
				)
			),
	    )
	);
}elseif ($informeus && $modelInformesForm->informe === '2'){

    ?><div class="pull-left">
        <?php   

            $this->widget(
                'bootstrap.widgets.TbButtonGroup',
                array(
                    'type' => 'primary',                    
                    'buttons' => array(
                        array('label' => 'Exportar',               
                            //'disabled'=>$muestt,

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
	$gridDataProvider = new CArrayDataProvider($informeus,
		array(
			'sort'=>array(
        		'attributes'=>array(
             		'nagencia', 'cantidad', 'agencia', 'ok',  'anu', 'pend', 'fechaiq', 'fechaiq'
        		),
    		),
			'pagination'=>array('pageSize'=>99999999)
		)

	);

	$this->widget(
	    'bootstrap.widgets.TbGridView',
	    array(
	        'type' => 'striped bordered condensed',
	        'dataProvider' => $gridDataProvider,
	        'htmlOptions'=> array('style'=>'font-size:10px'),
	        'enableSorting'=>true,
	        'columns' => array(
	        		array(
			       			'header'=>'Nombre Agencia',    			
            				'name'=>'nagencia',
	        			),	 
	        		array(
			       			'header'=>'Casos Radicados',    			
            				'name'=>'cantidad',
            				
	        			),	
	        		array(
			       			'header'=>'Cerrados OK',    			
            				'name'=>'ok',
	        			),	 
	        		array(
			       			'header'=>'Pendientes',    			
            				'name'=>'pend',
            				
	        			),	
	        		array(
			       			'header'=>'Anulados',    			
            				'name'=>'anu',
            				
	        			),	
	        	)
	    )
	);
}
?>
</div>

</div>
</div>

<script type="text/javascript">

    function exportExcelG(){     

 		var fechai='<?=$modelInformesForm->fechai;?>';
 		var fechaf='<?=$modelInformesForm->fechaf;?>';
 		var infor='<?=$modelInformesForm->informe;?>';

        location.href = '<?=$this->createUrl("default/informeEntramitexls")?>?fechai='+fechai+'&fechaf='+fechaf+'&xlsCC=true&infor='+infor;

    }
    function exportExcelG4(){     

 		var fechai='<?=$modelInformesForm->fechai;?>';
 		var fechaf='<?=$modelInformesForm->fechaf;?>';
 		var infor='<?=$modelInformesForm->informe;?>';

        location.href = '<?=$this->createUrl("default/informeEntramitexls")?>?fechai='+fechai+'&fechaf='+fechaf+'&xlsCC=true&infor='+infor;

    }
    function exportExcel(){     
 
 		var fechai='<?=$modelInformesForm->fechai;?>';
 		var fechaf='<?=$modelInformesForm->fechaf;?>';

        location.href = '<?=$this->createUrl("default/consultaTramitesExcel")?>?fechai='+fechai+'&fechaf='+fechaf+"&info=S";
    }    
 </script>

