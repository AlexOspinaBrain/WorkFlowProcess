<?php

$this->menu=array(
	array('label'=>'Opciones', 'itemOptions'=>array('class'=>'nav-header')),
	array('label'=>'Listar proveedores', 'url'=>array('index')),
	array('label'=>'Crear proveedor', 'url' => '#','itemOptions'=>array('class'=>'active')),
	'',
	array('label'=>'Salir', 'url'=>'http://imagine.laequidadseguros.coop/'),
);
?>

<div class="row">
	<div class="span2">
		<br>
		<div class="well" style="padding: 8px 0;position:fixed">
			<?php 
				$this->widget('bootstrap.widgets.TbMenu', array(
	    			'type'=>'list',
	    			'items' => $this->menu
				));
			?>
		</div>

	</div>

    <div class="span8 offset1">
		<h4>
			Detalle Proveedor <br><?php echo "( ". $model->idPersona->tipo_doc . " " . $model->idPersona->documento." ) ". $model->idPersona->nombre; ?>
			<?php 
			$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label' => 'Actualizar',
        'type' => 'primary',
        'url' => $this->createUrl('default/update', array('id' => $model->id_proveedor)),

            	'htmlOptions'=>array('class' => 'pull-right'),
    )
);
?>
		</h4>


<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'type' => 'striped bordered condensed',
	'attributes'=>array(
		array(
          	'label'=>'Docuemnto',
           	'value'=> $model->idPersona->tipo_doc . " " . $model->idPersona->documento
        ),
        'idPersona.nombre',
		array(
          	'name'=>'estado',
           	'value'=>$model->estado?'Activo':'Inactivo'
        ),
		'idPersona.telefono',
		'idPersona.fax',
		'idPersona.direccion',		
		'idPersona.idciudad0.ciudad',		
		'idPersona.correo',		
		'idPersona.producto',
		'idPersona.representante',		
		'acta',
		'fecha_aprobacion',		
		'tipo',
		'doc_cobro',
		array(
          	'name'=>'area_id',
           	'value'=>$model->area->area
        ),
        array(
          	'name'=>'documentos',
           	'type'=>'raw'
        )
	)
)); ?>
    
    </div>
</div>