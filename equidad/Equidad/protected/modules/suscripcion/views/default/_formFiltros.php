<div id="dialogFormFiltros">
    <div class="row">
        <script>
            $(function() {
                $( "#dialogFormFiltros" ).dialog({
                    autoOpen:false,
                    modal:true,
                   // width:'auto',
                    hide:'fade',
                    show:'fade',
                    title:'Filtros',
                    buttons: [ { 
                        text: "Buscar", 
                        click: function() {
                            $.fn.yiiGridView.update('gridConsultaTramites', {data: $("#formSearch").serialize()});
                            $( this ).dialog( "close" );  
                        } , 
                        'class':'btn btn-primary'} ],
                    open:function(){
                        $(this).dialog( "option", "height", 500);
                        $(this).dialog( "option", "width", 900);
                       
                        $(this).dialog( "option", "position", ["middle",20] );
                    }
                    
                });

                $("#formSearch").keypress(function(e) {
                    if(e.which == 13) {
                        $.fn.yiiGridView.update('gridConsultaTramites', {data: $("#formSearch").serialize()});
                        $( "#dialogFormFiltros" ).dialog( "close" );  
                        return false;
                    }
                }); 
            });
        </script>

        <div class="span11">   
            <ul class="nav nav-tabs" style="margin-bottom: 0px;">
                <li class="active">
                    <a><i class="icon-info-sign"></i> Filtros datos tramite</a>
                </li>  
            </ul>    
          
            <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array('id' => 'formSearch')); ?>
                <div class="row">
                    <div class="span2">
                        <?php echo $form->textFieldRow($model,'code', array('class'=>'span2')); ?>
                    </div>

                    <div class="span2">                      
                        <?php echo $form->dateRangeRow($model, 'fecha_rad', array('id'=>'fecha_rad2', 'class'=>'span2', 'options'=>array('format' => 'YYYY/MM/DD'))); ?>
                    </div>

                    <div class="span3">
                        <?php echo $form->textFieldRow($model,'id_persona', array('class'=>'span3')); ?>
                    </div>

                    <div class="span2">                      
                        <?php $certificado = CHtml::listData( Tipocertificado::model()->findAll(), 'id_certificado', 'desc_certificado');?>    
                        <?php echo $form->dropDownListRow($model,'id_certificado', $certificado, array('empty'=>'', 'class'=>'span2')); ?>
                    </div>

                    <div class="span1">                      
                        <?php echo $form->dropDownListRow(
                            $model,
                            'prioridad', 
                            array(
                                'Urgente' => 'Urgente',
                                'Normal'=>'Normal',
                            ), 
                            array('empty'=>'', 'class'=>'span1')
                        ); 
                        ?>
                    </div>

                     <div class="span3">  
                        <?php $agencia = CHtml::listData( Agencia::model()->findAll(array('order'=>'descrip')), 'codigo', 'descrip');?>
                        <?php echo $form->dropDownListRow($model,'id_agencia', $agencia, array('empty'=>'', 'class'=>'span3')); ?>
                    </div>   

                     <div class="span2">                      
                        <label for="Radica_status">Estado</label> 
                        <?php 
                            $this->widget('ext.EchMultiSelect.EchMultiSelect', array(
                                'model' => $model,
                                'dropDownAttribute' => 'status',     
                                'data' => SWHelper::allStatuslistData($model),   
                                'options'=>array(
                                    'checkAllText' => "Todos",  
                                    'uncheckAllText' => "Ninguno",
                                    'selectedText' =>"# seleccionado",
                                    'noneSelectedText'=>'-- No seleccionado --',
                                ),         
                                'dropDownHtmlOptions'=> array(        
                                    'id'=>'status2',
                                )                     
                            ));
                        ?>
                    </div> 

                    

                    <div class="span2">                      
                        <?php $producto = CHtml::listData( Producto::model()->findAll(), 'id_producto', 'producto');?>    
                         <label for="Radica_id_producto">Producto</label> 
                        <?php 
                            $this->widget('ext.EchMultiSelect.EchMultiSelect', array(
                                'model' => $model,
                                'dropDownAttribute' => 'id_producto',     
                                'data' => $producto,   
                                'options'=>array(
                                    'checkAllText' => "Todos",  
                                    'uncheckAllText' => "Ninguno",
                                    'selectedText' =>"# seleccionado",
                                    'noneSelectedText'=>'-- No seleccionado --',
                                    'filter'=>true,
                                    'minWidth'=>300,
                                ),         
                                   'dropDownHtmlOptions'=> array(        
                                    'id'=>'producto2',
                                )                  
                            ));
                        ?>
                       
                    </div>

                    <div class="span2">
                        <?php echo $form->textFieldRow($model,'poliza', array('class'=>'span2')); ?>
                    </div>

                    <div class="span2">                      
                        <?php $canal = CHtml::listData( Canal::model()->findAll(), 'id_canal', 'canal');?>    
                        <?php echo $form->dropDownListRow($model,'id_canal', $canal, array('empty'=>'', 'class'=>'span2')); ?>
                    </div>

                    <div class="span2">                      
                        <?php echo $form->dateRangeRow($model, 'fecha_garantia', array('class'=>'span2',  'options'=>array('format' => 'YYYY/MM/DD'))); ?>
                    </div>

                    <div class="span2">         
                        <label for="Radica_id_garantia">Garantia</label>             
                        <?php $garantias = CHtml::listData( Garantias::model()->findAll(), 'id_garantia', 'desc_garantia');?>    
                        <?php 
                            $this->widget('ext.EchMultiSelect.EchMultiSelect', array(
                                'model' => $model,
                                'dropDownAttribute' => 'id_garantia',     
                                'data' => $garantias,    
                                'options'=>array(
                                    'checkAllText' => "Todos",  
                                    'uncheckAllText' => "Ninguno",
                                    'selectedText' =>"# seleccionado",
                                    'noneSelectedText'=>'-- No seleccionado --',                                    
                                ),                              
                            ));
                        ?>
                    </div>

                    <div class="span2">         
                        <label for="Radica_id_sarlaft">Sarlaft</label>             
                        <?php $sarlaft = CHtml::listData( Sarlaft::model()->findAll(), 'id_sarlaft', 'desc_sarlaft');?>    
                        <?php 
                            $this->widget('ext.EchMultiSelect.EchMultiSelect', array(
                                'model' => $model,
                                'dropDownAttribute' => 'id_sarlaft',     
                                'data' => $sarlaft,    
                                'options'=>array(
                                    'checkAllText' => "Todos",  
                                    'uncheckAllText' => "Ninguno",
                                    'selectedText' =>"# seleccionado",
                                    'noneSelectedText'=>'-- No seleccionado --',    
                                    'minWidth'=>300,                                
                                ),                              
                            ));
                        ?>
                    </div>

                    <div class="span2">                      
                        <?php echo $form->dateRangeRow($model, 'fecha_cierre', array('class'=>'span2',  'options'=>array('format' => 'YYYY/MM/DD'))); ?>
                    </div>
                    <div class="span2">
                        <?php echo $form->textFieldRow($model,'searchHistorialPend', array('class'=>'span2')); ?>
                    </div>

                </div>

            <?php
                $this->endWidget();
            ?>
        </div>   
    </div>
</div>

<style type="text/css">
    .daterangepicker{z-index: 1003 !important}
    .ui-multiselect{height:30px;max-width:100%;}
</style>