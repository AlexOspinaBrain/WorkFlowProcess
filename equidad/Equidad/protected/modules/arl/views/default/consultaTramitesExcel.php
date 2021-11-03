<?php 

    $colmnm=array(
                        array(   
                            'value' => '$data->id_radica',
                            'header'=>'Id. Radicación',
                            'htmlOptions'=>array('width'=>'40px'),
                            'type'=>'raw',
                            
                        ),  
                        array(
                            'value' => '$data->afiliacion',
                            'name'=>'afiliacion',                           
                            'htmlOptions' => array('style'=>'width:140px')
                        ),
                        array(
                            'name' => 'Fecha Radicación',
                            'value' => '$data->fecha_rad',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Usuario Radicación',
                            'value' => '$data->arlHistorialrad->usuarioCod->usuario_desc',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Agencia Radicación',
                            'value' => '$data->admUsuario->rel_area->agencia0->descrip',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),                                             
                        array(
                            
                            'header'=>'Proceso',
                            'value' => '$data->idProceso->proceso',                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),    
                        array(
                            'value' => '$data->idTipologia->tipologia',                            
                            'name'=>'idtipologia',                           
                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),
                        array(
                            'value' => '$data->swGetStatus()->getLabel()',
                            'name'=>'status',                           
                            'filter'=>SWHelper::allStatuslistData( $model)
                        ),
                        array(
                            'name' => 'Intermediario',
                            'value' => '$data->intermediario',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'ID Intermediario',
                            'value' => '$data->nitintermediario',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Ejecutivo Ventas',
                            'value' => '$data->ejecutivov',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Franquicia',
                            'value' => '$data->franquicia',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Valor Cotización',
                            'value' => '$data->vlrcot',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Valor Nomina',
                            'value' => '$data->vlrnomina',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => '# Trabajadores',
                            'value' => '$data->ntrabajadores',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Representante Legal',
                            'value' => '$data->representante',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'NIT',
                            'value' => '$data->nit',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Razón Social',
                            'value' => '$data->razonsocial',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Valor Total Contrato',
                            'value' => '$data->vlrcontrato',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Valor Mensual Contrato',
                            'value' => '$data->vlrmescontrato',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Riesgo',
                            'value' => '$data->razonsocial',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),


        );



    $this->widget('ext.phpexcel.EExcelView', array(
    'title'=>'Informe Tramites',
    'autoWidth'=>true,
    'grid_mode'=>'export', 
    'sheets'=>array(
        array(
            'sheetTitle'=>'Casos',
            'dataProvider' => $model->search(),
            'columns' => $colmnm,

        ),      
    ),
));
?>
