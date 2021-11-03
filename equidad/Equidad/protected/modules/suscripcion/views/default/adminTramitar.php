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
    	<?php
    		$usuarios = Yii::app()->db->createCommand()
				->select("usu.usuario_cod,  COALESCE(usuario_nombres,'')  || ' ' || COALESCE(usuario_priape,'') || ' ' || COALESCE(usuario_segape,'') as nombres")		
				->from('tblradofi age')
				->join('tblareascorrespondencia are', 'age.codigo=are.agencia')
				->join('adm_usuario usu', 'are.areasid=usu.area')
				->join('adm_usumenu men', 'men.usuario_cod=usu.usuario_cod')
				->where('id_agencia=:id_agencia and jerarquia_opcion=:jerarquia_opcion', array(':id_agencia'=>Yii::app()->user->idAgencia, ':jerarquia_opcion'=>'4.2.2'))
				->queryAll();
    	?>

    	<br>
    	<div class="well">
    	<br>
    	<legend>Tramitar proceso suscripción</legend>
    	 
		<?php 
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			    	'id'=>'recibirPreRadicados',
			        'dataProvider' => $model->search(),
			        'filter' => $model,
			        'ajaxUpdate'=>false,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
			           	array(
			                'class' => 'bootstrap.widgets.TbButtonColumn',
			                'template' => '{update} ',
			                'buttons' => array(
        						'update' => array(
            						'label' => 'Tramitar',                 						
            						'url'=>'array("tramitar","code"=>$data->code)',            						
        						),	        
    						),
			            ),
			       		array(			       			
	        				'name' => 'code',
	        				'htmlOptions'=>array('width'=>'40px',),
	        				'type'=>'raw',	        				
            				'value'=>'CHtml::link($data->code,"#", array("onClick"=>"js:detallesTramite($(this).text())"))',
	        			),		
				       	array(
				            'name'=>'fecha_rad',
				            'value'=>'Yii::app()->dateFormatter->format("y-MM-dd hh:mm a",strtotime($data->fecha_rad))' ,
	        			),
	        			array(
				            'name'=>'id_persona',
				            'value'=>'"( " .$data->idPersona->tipo_doc ." " . $data->idPersona->documento . " ) ". 
				            $data->idPersona->nombre' ,	
				           // 'htmlOptions'=>array('width'=>'40%'),	        			
	        			),
	        			array(
	        				'name' => 'prioridad',
	        				'htmlOptions'=>array('width'=>'40px'),	        				
	        			),
	        			array(
	        				'name' => 'searchHistorialPend',
	        				'header' => 'Usuario',
	        				'value' => '$data->susHistorialPend->usuarioCod->NombreCompleto',	        				
	        				'filter'=>CHtml::listData($usuarios, 'usuario_cod','nombres'),	        				
	        				'htmlOptions'=>array('width'=>'120px'),
	        			),	  
			        ),
			    )
			);
		?>
    	</div>
    </div>  
</div>

<?php 
$c = CHtml::ajax(array(
        	'url'=>array('default/viewDetalle'), 
 			'type' => 'GET',
 			'data'=>array(
				'code'=>'js:code', 				
			), 
			'success'=> "function(data){
				$('body').append(data);
        	}"			
      	));

$script = <<<EOD
  	function detallesTramite(code)
    {    
	   {$c}
    	return false;        
    } 
EOD;
Yii::app()->getClientScript()->registerScript('detallesTramite',$script, CClientScript::POS_END);
?>