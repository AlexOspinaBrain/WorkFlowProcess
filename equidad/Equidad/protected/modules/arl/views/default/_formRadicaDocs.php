<?php 
if(!empty($modelDocuments)):?>
	<div class="row">
		<div class="span8 offset1">
			<table class="table table-hover table-bordered table-striped">
				<tr><th colspan="2">Documentos requeridos</th></tr>
	 			<?php foreach ($modelDocuments as $i=>$RadicaDoc) :?>
					<tr class="<?= ($i%2==0)?'success':'' ?>">
						<td>
							<?=$RadicaDoc->idDocumento->desc_documento?><br>
							<?php $modelRadicaDoc->id_clasificacion=$RadicaDoc->id_clasificacion ?><br>
						</td>
						<td>			
							<?php
			    				$this->widget('ext.EFineUploader.EFineUploader',
	 							array(
	       							'id'=>$i."_archivo",
	       							'config'=>array(
		                       			'autoUpload'=>true,
		                       			'request'=>array(
		                                	'endpoint'=>$this->createUrl('upload'),// OR $this->createUrl('files/upload'),
		                                    'params'=>array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
		                              	),
		                       			'chunking'=>array('enable'=>true,'partSize'=>100),//bytes
		                       			'callbacks'=>array(
		                                	'onComplete'=>"js:function(id, name, response){ 
		                                    	var id = $(this._element).attr('id');
		                                        id = id.substring(0, id.indexOf('_'));
		                                        $('#RadicaDoc_'+id+'_ruta').val(response.path);                                        
		                                   	}",                                    
										),
		                       			'validation'=>array(
			                                'allowedExtensions'=>array('jpg','jpeg','pdf'),
			                                'sizeLimit'=>4 * 1024 * 1024,//maximum file size in bytes
			                                //'minSizeLimit'=>2*1024*1024,// minimum file size in bytes
	                            		),
				                       	'messages'=>array(
		                                	'typeError'=>"El archivo {file} tiene una extensión incorrecta. Se permite solamente los archivos con las siguientes extensiones: {extensions}.",
		                                   	'sizeError'=>"Tamaño de archivo {archivo} es grande, el tamaño máximo de {sizeLimit}.",
		                                 	'minSizeError'=>"Tamaño de archivo {archivo} es pequeño, el tamaño mínimo {minSizeLimit}.",
		                                  	'emptyError'=>"{file} is empty, please select files again without it.",
		                                   	'onLeave'=>"Los archivos se están cargando, si te vas ahora la carga se cancelará."
		                                ),
	                      			)
	      						));
	 
	 							echo CHtml::activeHiddenField($modelRadicaDoc, "[$i]id_clasificacion");
	 							echo CHtml::activeHiddenField($modelRadicaDoc, "[$i]ruta");
							?>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	</div>
<?php endif ?>	