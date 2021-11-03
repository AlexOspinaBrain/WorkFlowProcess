<?php 

$gridDataProvider = new CArrayDataProvider($informeusx
/*        array(
            'sort'=>array(
                'attributes'=>array(
                    'usuario', 'cantidad'
                ),
            ),

            'pagination'=>array('pageSize'=>99999999)
        )
*/
    );
if ($infor==4) {
    $columns=array( 
                        array(
                            'header' => '# Caso',
                            'name'=>'id_radica',                           
                        ),
                        array(
                            'header' => 'Tipo de Identificacion',
                            'name'=>'tipo_id',                           
                        ),
                        array(
                            'header' => 'Numero de Identificacion',
                            'name'=>'identificacion',                           
                        ),
                        array(
                            'header' => 'Nombre',
                            'name'=>'nombre',                           
                        ),
                        array(
                            'header' => 'Fecha Radicacion',
                            'name'=>'fecha_rad',                           
                        ),
                        array(
                            'header' => 'Estado actual del caso',
                            'name'=>'status',                           
                        ),
                        array(
                            'header' => 'Oficina',
                            'name'=>'nagencia',                           
                        ),
                        array(
                            'header' => 'Producto',
                            'name'=>'ramo',                           
                        ),
                        array(
                            'header' => 'Fecha de Gestion',
                            'name'=>'atencion',                           
                        ),
                        array(
                            'header' => 'Tiempo Gestion',
                            'name'=>'dias',                           
                        ),                        
                        array(
                            'header' => 'Usuario de Gestion',
                            'name'=>'usu',                           
                        ),

                    );
}else{
    $columns=array( 
                        array(
                            'header' => '# Caso',
                            'name'=>'id_radica',                           
                        ),
                        array(
                            'header' => 'Tipo de Identificacion',
                            'name'=>'tipo_id',                           
                        ),
                        array(
                            'header' => 'Numero de Identificacion',
                            'name'=>'identificacion',                           
                        ),
                        array(
                            'header' => 'Nombre',
                            'name'=>'nombre',                           
                        ),
                        array(
                            'header' => 'Fecha Radicacion',
                            'name'=>'fecha_rad',                           
                        ),
                        array(
                            'header' => 'Estado actual del caso',
                            'name'=>'status',                           
                        ),
                        array(
                            'header' => 'Oficina',
                            'name'=>'descrip',                           
                        ),
                        array(
                            'header' => 'Producto',
                            'name'=>'ramo',                           
                        ),
                        array(
                            'header' => 'Estado Gestionado',
                            'name'=>'estado',                           
                        ),
                        array(
                            'header' => 'Fecha de Asignacion',
                            'name'=>'fecha_inicio',                           
                        ),
                        array(
                            'header' => 'Fecha de Gestion',
                            'name'=>'fecha_termino',                           
                        ),
                        array(
                            'header' => 'Tiempo Gestion',
                            'name'=>'tiempo',                           
                        ),                        
                        array(
                            'header' => 'Usuario de Gestion',
                            'name'=>'usuario_desc',                           
                        ),
                        array(
                            'header' => 'Nombres Usuario',
                            'name'=>'usuario_nombres',                           
                        ),
                        array(
                            'header' => 'Apellido Usuario',
                            'name'=>'usuario_priape',                           
                        ),


                    );
}

    $this->widget('ext.phpexcel.EExcelView', array(
    'title'=>'Informe por usuario',
    'autoWidth'=>true,
    'grid_mode'=>'export', 
    'sheets'=>array(
        array(
            'sheetTitle'=>'Casos',
            'dataProvider' => $gridDataProvider,
            'columns' => $columns
        ),      
    ),
));
?>


