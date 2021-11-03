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
    	<legend>Tramites pendientes por entregar</legend>
    	<div id="containerAlert"></div>
		<?php 
			$this->widget(
			    'bootstrap.widgets.TbGridView',
			    array(
			        'dataProvider' => $model->search(),
			        'id'=>'gridRadicados',
			        'ajaxUpdate'=>false,
			        'filter' => $model,
			        'type' => 'striped bordered condensed',			    
			        'columns' => array(	
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
			                'class' => 'bootstrap.widgets.TbButtonColumn',
			                'header'=>'Anular',
			                'template' => '{anular}',			               	
			                'buttons' => array(
        						'anular' => array(
            						'label' => 'Anular',     
            						'icon' => 'icon-remove', 
            						'url'=>'$data->code',
            						'click' => 'js: function(){ anularTramite($(this).attr("href")); return false; }',              						
        						),	        
    						),
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

<?php 
$c = CHtml::ajax(array(
        'url'=>array('default/anular'), 
 		'type' => 'GET',
 		'data'=>array(
			'code'=>'js:code', 				
		), 
		'success'=> "function(data){
			data=$.parseJSON(data);
			
			if(data.anulado === 'true')
				$('<div/>', {
	    			'class': 'alert alert-success fade in',	  
	    			html: 'El tramite <strong>'+code+'</strong> ha sido anulado!'  				
				}).append(btnClose)
			      .appendTo('#containerAlert')
			else
				$('<div/>', {
		    		'class': 'alert alert-error fade in',
	    			html: '<strong>Error! </strong> El tramite <strong>'+code+'</strong> no ha sido eliminado.'
				}).append(btnClose)
			  	.appendTo('#containerAlert')

			  	
			$.fn.yiiGridView.update('gridRadicados', {data: $(this).serialize()});			  	
				actualizaBadge();
        	}"
      	));

$script = <<<EOD
	function anularTramite(code)
    {
    	var btnClose = $('<button/>', {
    		type:'button',
    		'class':'close',
    		'data-dismiss':'alert',
    		text:'x'
    	});
    	
    	$('#containerAlert').html('');

    	if(confirm("Seguro que desea anular el tramite "+code+" ?"))
	    	{$c}
    	
        return false;  
    }          
EOD;

Yii::app()->getClientScript()->registerScript('anularTramite',$script, CClientScript::POS_END);
?>