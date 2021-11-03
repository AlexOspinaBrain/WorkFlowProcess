<?php
/* @var $this RadicaController */
/* @var $model Radica */

?>

<?php header('Content-Type: text/html; charset=UTF-8'); ?>
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
    	<legend>Recibir tramites radicados</legend>       

    	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    		'type' => 'horizontal',    
    		'htmlOptions'=>array(
               	'onsubmit'=>"return false;",
            ),	
		));

		echo $form->textFieldRow($model,'code', array('labelOptions'=>array('label'=>'Recibir tramite'), 'append' => ''));
		?>
		 <div id="containerAlert"></div>
		<?php
			$this->endWidget();
		?>

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
			                'template' => '{recibir} ',
			                'buttons' => array(
        						'recibir' => array(
            						'label' => 'Recibir',     
            						'icon' => 'icon-ok', 
            						'url'=>'$data->code',
            						'click' => 'js: function(){ recibeTramite($(this).attr("href")); return false; }',              						
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
$script = <<<EOD
  	var x = $("#Radica_code").parent();
  	x.find("span").remove();

  	var button = $('<button class="btn btn-primary" type="button"/>')
    				.append('<i class="icon-ok icon-white"></i>')
    				.height(30)
    				.click(function (){recibeTramite()});

  	x.append(button);

  	$("#Radica_code").keypress(function(e) {
		if(e.which == 13) 
			recibeTramite();	
	});		

EOD;
Yii::app()->getClientScript()->registerScript('buttonSearchCifin',$script, CClientScript::POS_END);
?>

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
          	'url'=>array('default/recibePreRadicados'), 
 			'type' => 'GET',
 			'data'=>array(
				'code'=>'js:tramite', 				
			), 
			'success'=> "function(data){
				data=$.parseJSON(data);
				if(data.recibido === 'true')
	    			$('<div/>', {
	    				'class': 'alert alert-success fade in',	  
	    				'html': 'El tramite <strong>'+tramite+'</strong> ha sido recibido!'  				
					}).append(btnClose)
			  		.appendTo('#containerAlert')
			  	else
			  		$('<div/>', {
	    				'class': 'alert alert-error fade in',
	    				'html': '<strong>Error! </strong> El tramite <strong>'+tramite+'</strong> no existe o no esta disponible para ser recibido.'
					}).append(btnClose)
			  		.appendTo('#containerAlert')

			  	
			  	$.fn.yiiGridView.update('recibirPreRadicados', {data: $(this).serialize()});
			  	$('#Radica_code').val('');
			  	actualizaBadge();
        	}"
      	));

$script = <<<EOD
  	function recibeTramite(code)
    {    
    	var btnClose = $('<button/>', {
    		'type':'button',
    		'class':'close',
    		'data-dismiss':'alert',
    		text:'x'
    	});
    	
    	$('#containerAlert').html('');

    	var tramite =  $('#Radica_code').val();
    	if (code) tramite = code;
	   	
	   	{$c}

	   	return false;  
    } 
EOD;
Yii::app()->getClientScript()->registerScript('recibeTramite',$script, CClientScript::POS_END);
?>