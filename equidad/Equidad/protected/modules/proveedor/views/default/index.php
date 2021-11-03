<?php 

$this->menu=array(
	array('label'=>'Opciones', 'itemOptions'=>array('class'=>'nav-header')),
	array('label'=>'Listar Proveedores', 'url' => '#','itemOptions'=>array('class'=>'active')),
	array('label'=>'Crear Proveedor', 'url'=>array('crear')),
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

    <div class="span10">
		<?php 

			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',
			        'columns' => array(
			        	array(
			        		'name' => 'documento', 
			        		'value'=>'" ( ".$data->idPersona->tipo_doc." ) ".$data->idPersona->documento',
			        	),
			        	array( 'name'=>'nombre', 'value'=>'$data->idPersona->nombre'),			            		            
			            array(
			            	'name' => 'doc_cobro',
			            	'filter'=>array(
	    						'Factura'=>'Factura',
								'Cuenta de cobro' => 'Cuenta de cobro'
							),  
			            ),
			            array(
			            	'name' => 'tipo',
			            	'filter'=>array(
		   						'OTROS' => 'OTROS',
								'CRITICO RECURRENTE'=>'CRITICO RECURRENTE',
								'CRITICO NO RECURRENTE' => 'CRITICO NO RECURRENTE',
							), 
			            ),
			            array(
			                'class' => 'bootstrap.widgets.TbButtonColumn',
			                'template' => '{view} {update}',
			            ),
			        ),
			    )
			);
		?>
    </div>
</div>



