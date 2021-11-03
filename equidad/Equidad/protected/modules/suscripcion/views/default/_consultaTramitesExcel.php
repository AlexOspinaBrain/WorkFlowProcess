<?php 
	$this->widget('ext.phpexcel.EExcelView', array(
	'title'=>'Informe suscripción -'. date("d-m-Y h:i A"),
    'autoWidth'=>true,
    'grid_mode'=>'export', 
	'sheets'=>array(
		array(
			'sheetTitle'=>'Radicados',
			'dataProvider' => $model->search(),
			'columns' => array(	
				'code',
				array(
					'name'=>'fecha_rad',
					'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_rad))' ,
			   	),
			   	array(
					'name'=>'fecha_cierre',
					'value'=>'Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_cierre))' ,
			   	),
			   	array(
					'header'=>'Tiempo tramite',
					'value'=>'$data->tiempoTramite' ,
			   	),
			   	array(
	        		'header' => 'Estado',
	        		'value' => '$data->swGetStatus()->getLabel()',	        				
	        	),
			   	'prioridad',
			   	array(
					'name'=>'id_agencia',
					'value'=>'$data->idAgencia->descrip' ,
			   	),
			   	array(
					'name'=>'id_certificado',
					'value'=>'$data->idCertificado->desc_certificado' ,
			   	),
			   	array(
					'name'=>'id_canal',
					'value'=>'$data->idCanal->canal' ,
			   	),
			   	array(
					'name'=>'id_sarlaft',
					'value'=>'$data->idSarlaft->desc_sarlaft' ,
			   	),
			   	array(
					'name'=>'id_garantia',
					'value'=>'$data->idGarantia->desc_garantia' ,
			   	),
			   	array(
					'name'=>'fecha_garantia',
					'value'=>'($data->fecha_garantia !=null)?Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_garantia)):""' ,
			   	),
			    array(
					'name'=>'id_persona',
					'value'=>'"( " .$data->idPersona->tipo_doc . $data->idPersona->documento . " ) ". 
						($data->idPersona->primer_nombre ? 
							$data->idPersona->primer_nombre . " " . 
						    $data->idPersona->segundo_nombre . " " . 
						    $data->idPersona->primer_apellido . " " . 
						    $data->idPersona->segundo_apellido : 
						    $data->idPersona->nombre)' ,
			    ),
			    'poliza',
			    'cant_ordenes',	
			    array(
					'name'=>'fecha_garantia',
					'value'=>'($data->fecha_expe !=null)?Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_expe)):""' ,
			   	),		  	
			   	'usuario_osiris'
			),
		),
		array(
			'sheetTitle'=>'Historial',
			'dataProvider' => $model->searchHistorials(),
			'columns' => array(	
			   'code',
			   'estado',		   
			    array(
					'name'=>'fecha_inicio',
					'value'=>'($data->fecha_inicio !=null)?Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_inicio)):""' ,				
			   	),
			   	array(
					'name'=>'fecha_termino',
					'value'=>'($data->fecha_termino !=null)?Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($data->fecha_termino)):""' ,					
			   	),
			  	array(
					'name'=>'usuario_cod',
					'value'=>'$data->usuarioCod->nombreCompleto' ,					
			   	),
			),
		),
	),
));
?>
