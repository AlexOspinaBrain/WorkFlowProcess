<?php
/* @var $this RadicaController */
/* @var $model Radica */

?>

<div class="row">
	<div class="span2">
		<br>
		<div class="well" style="padding: 8px 0; position:fixed; width:11%">
			<?php echo $this->renderPartial('_menuSuscripcion'); ?>	
		</div>

	</div>

    <div class="span10">
    	<br>
    	<div class="well">
    	<br>
    	<legend>Tramites pendientes por verificar</legend>
    	
    	<?php
    		$usuarios = Yii::app()->db->createCommand()
				->select("usu.usuario_cod,  COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as nombres")		
				->from('tblradofi age')
				->join('tblareascorrespondencia are', 'age.codigo=are.agencia')
				->join('adm_usuario usu', 'are.areasid=usu.area')
				->join('adm_usumenu men', 'men.usuario_cod=usu.usuario_cod')
				->where('id_agencia=:id_agencia and jerarquia_opcion=:jerarquia_opcion', array(':id_agencia'=>Yii::app()->user->idAgencia, ':jerarquia_opcion'=>'4.2.4'))
				->queryAll();
    	?>

		<?php 
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'ajaxUpdate'=>false,
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
			           	array(
			                'class' => 'bootstrap.widgets.TbButtonColumn',
			                'template' => '{update} ',
			                'buttons' => array(
        						'update' => array(
            						'label' => 'Verificar',                 						
            						'url'=>'array("verificarComercial","code"=>$data->code)',            						
        						),	        
    						),

			            ),
			       		array(
	        				'name' => 'code',
	        				'htmlOptions'=>array('width'=>'40px',),
	        				'type'=>'raw',
            				'value'=>'CHtml::ajaxLink(
            						"$data->code", 
            						array("viewDetalle"), 
            						array(
            							"data"=>array("code"=>$data->code),
			       						"success"=>"function(data){\$(\'body\').append(data)}",
			       					)
			       				)',
			       			'htmlOptions'=>array('width'=>'15%'),
	        			),					      
	        			array(
				            'name'=>'id_persona',
				            'value'=>'"( " .$data->idPersona->tipo_doc ." " . $data->idPersona->documento . " ) ". 
				            $data->idPersona->nombre' ,	
				           // 'htmlOptions'=>array('width'=>'40%'),	        			
	        			),
	        			array(
	        				'name' => 'prioridad',
	        				'htmlOptions'=>array('width'=>'10%'),
	        				'filter'=>array('Urgente' => 'Urgente',	'Normal'=>'Normal'), 
	        			),	        			
						array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario',
	        				'value' => '$data->susHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>CHtml::listData($usuarios, 'usuario_cod', 'nombres'),	        				
	        				'htmlOptions'=>array('width'=>'120px'),
	        			),	 
			        ),
			    )
			);
		?>
    	</div>
    </div>  
</div>

<style type="text/css">
	#yw1_c0{width: 30px}
	.grid-view .button-column{width: 30px}
</style>