<?php 


if ($info=='S'){
    $colmnm=array(
                        array(   
                            'value' => '$data->id_radica',
                            'header'=>'Id. Radicación',
                            'htmlOptions'=>array('width'=>'40px'),
                            'type'=>'raw',
                            
                        ),  
                        array(
                            'value' => '$data->tipo_id." - ".$data->identificacion',
                            'name'=>'identificacion',                           
                            'htmlOptions' => array('style'=>'width:140px')
                        ),
                        'nombre', 
                        array(
                            'name' => 'Fecha Radicación',
                            'value' => '$data->fecha_rad',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'name' => 'Usuario Radicación',
                            'value' => '$data->sarHistorialrad->usuarioCod->usuario_desc',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                                             
                        array(
                            
                            'header'=>'Of Rad',
                            'value' => '$data->idAgencia->descrip',                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),    
                        array(
                            'value' => '$data->idProducto->ramo',                            
                            'name'=>'id_producto',                           
                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),
                        array(
                            'value' => '$data->swGetStatus()->getLabel()',
                            'name'=>'status',                           
                            'filter'=>SWHelper::allStatuslistData( $model)
                        ),
        );
}else{
    $colmnm=array(
                        array(   
                            'value' => '$data->id_radica',
                            'header'=>'Id. Radicación',
                            'htmlOptions'=>array('width'=>'40px'),
                            'type'=>'raw',
                            
                        ),  
                        array(
                            'value' => '$data->tipo_id." - ".$data->identificacion',
                            'name'=>'identificacion',                           
                            'htmlOptions' => array('style'=>'width:140px')
                        ),
                        'nombre', 
                        array(
                            'name' => 'Fecha Radicación',
                            'value' => '$data->fecha_rad',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                        array(
                            'value' => '$data->sarHistorialPest->fecha_termino',
                            'header'=>'Fecha primera atencion',
                            
                        ),
                        array(
                            'name' => 'Usuario Radicación',
                            'value' => '$data->sarHistorialrad->usuarioCod->usuario_desc',
                            'htmlOptions' => array('style'=>'width:180px; text-align: center')
                        ),
                                             
                        array(
                            
                            'header'=>'Of Rad',
                            'value' => '$data->idAgencia->descrip',                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),    
                        array(
                            'value' => '$data->idProducto->ramo',                            
                            'name'=>'id_producto',                           
                            
                            'htmlOptions'=>array('width'=>'50px', 'style' => 'text-align: center;'),
                        ),
                        array(
                            'value' => '$data->swGetStatus()->getLabel()',
                            'name'=>'status',                           
                            'filter'=>SWHelper::allStatuslistData( $model)
                        ),

                       /*array(
                            'value' => '$data->getDiasHorasUT()',
                            'name'=>'Tiempo Ultima Tarea Pendiente',                           
                        ),                        
                        array(
                            'value' => '$data->getUltimaFecha()',
                            'name'=>'Fecha cierre del Caso',                           
                        ),

                       array(
                            'value' => '$data->getDiasHoras()',
                            'name'=>'Tiempo Total',                           
                        ),*/


        );
}

    $this->widget('ext.phpexcel.EExcelView', array(
    'title'=>'Informe Casos',
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
