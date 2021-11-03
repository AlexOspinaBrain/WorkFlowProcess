<div id="dialogViewDetalle" style="display:none">
<div class="row">
    <script>
        $(function() {
            $( "#dialogViewDetalle" ).dialog({
                modal:true,
                width:'auto',
                hide:'fade',
                show:'fade',
                title:'Detalles tramite',
                buttons: [ { text: "Aceptar", click: function() { $( this ).dialog( "close" ); } , 'class':'btn btn-primary'} ],
                close:function(){
                    $(this).remove();
                    $('#dialogCodebar').remove();
                },
                open:function(){
                    var height =  $(this).height()+80;    
            
                    if(height > $(window).height ()){
                        $(this).css("height",$(window).height()-160);
                        $(this).css("width",$(this).width()+30);
                    }

                    $(this).dialog( "option", "position", { my: "center", at: "center" } );
                }
            });

            $( "#dialogCodebar" ).dialog({
                autoOpen:false,
                modal:true,
                width:'auto',
                hide:'fade',
                show:'fade',
                title:'Codigo de barras',
                buttons: [ { text: "Imprimir", click: function(){$("#codeBar").jqprint()} , 'class':'btn btn-primary'},
                        { text: "Cerrar", click: function() { $( this ).dialog( "close" ); } , 'class':'btn'}  ],            
            });
        });
    </script>

    <div class="span7 offset1">
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-info-sign"></i> Detalles generales</a>
            </li>  
        </ul>

        <table class="detail-view table table-bordered table-striped table-condensed">
            <tbody>
                <tr class="odd">
                    <th><?= $model->getAttributeLabel('id_radica')?></th><td><?= CHtml::tag('span', array('class'=>'label label-success'), $model->id_radica)?></td>                      
                    <th>Estado</th><td><?= $model->swGetStatus()->getLabel() ?></td>     
                </tr>
                 <tr class="odd">                     
                    <th><?= $model->getAttributeLabel('no_sinestro')?></th><td><?= $model->no_sinestro ?></td>   
                    <th><?= $model->getAttributeLabel('fecha_rad')?></th><td><?=Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($model->fecha_rad))?></td>  
                </tr>
                <tr class="even">
                    <th><?= $model->getAttributeLabel('id_agencia_expide')?></th>
                    <td><?=$model->idAgenciaExpide->descrip?></td>
                    <th><?= $model->getAttributeLabel('id_agencia_tramita')?></th>
                    <td><?=$model->idAgenciaTramita->descrip?></td>
                </tr>
              <?php /* <tr class="even">
                    <th><?= $model->getAttributeLabel('prioridad')?></th>
                    <td><?= CHtml::tag('span', array('class'=>(($model->prioridad === 'Normal')?'label label-info':'label label-important')), $model->prioridad)?></td>
                    <th>Estado</th>
                    <td><?= $model->swGetStatus()->getLabel() ?></td>
                </tr>
                <tr class="odd">
                    <th><?= $model->getAttributeLabel('fecha_rad')?></th><td><?=Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($model->fecha_rad))?></td>
                    <th><?= $model->getAttributeLabel('fecha_cierre')?></th><td><?=Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($model->fecha_cierre))?></td>
                </tr>
                <tr class="odd">
                    <th><?= $model->getAttributeLabel('idCertificado.desc_certificado')?></th><td colspan="3"><?=$model->idCertificado->desc_certificado?></td>
                </tr>       
                <tr class="even">
                    <th><?= $model->getAttributeLabel('id_persona')?></th>
                    <td colspan="3"><?="( " .$model->idPersona->tipo_doc . $model->idPersona->documento . " ) ". 
                                    ($model->idPersona->primer_nombre ? 
                                        $model->idPersona->nombreCompleto : 
                                        $model->idPersona->nombre)?>
                    </td>
                </tr>
                <tr class="odd">
                    <th><?= $model->getAttributeLabel('poliza')?></th><td><?=$model->poliza?></td>
                    <th><?= $model->getAttributeLabel('cant_ordenes')?></th><td><?=$model->cant_ordenes?></td>
                </tr>
                <tr class="even">
                    <th><?= $model->getAttributeLabel('id_agencia')?></th>
                    <td colspan="3"><?=$model->idAgencia->descrip?></td>
                </tr>*/ ?>
            </tbody>
        </table>

    </div>
</div>
<?php $icon=array('jpg'=>'icon-f-document-image', 'pdf'=>'icon-f-blue-document-pdf-text', 'doc'=>'icon-f-blue-document-word-text', 'docx'=>'icon-f-blue-document-word-text'); ?>
<div class="row">
    <div class="span9">
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-tasks"></i>  Documentos <?php echo '('.(sizeof($model->RadicaDocs)).')'?></a>
            </li>  
        </ul>

        <table class="items table table-striped table-bordered table-condensed">
            <tr>
                <th>Documento</th><th style="width:110px"></th>
            </tr>
            <?php if($model->respuesta): ?>
                <tr>
                    <td>Carta de respuesta </td>
                    <td style="vertical-align: middle">
                        <?= CHtml::link('<i class="'.$icon[pathinfo($model->respuesta, PATHINFO_EXTENSION)].' gray"></i> Ver archivo', '', 
                            array(
                                'class'=>'btn btn-small', 
                                'target'=>'_blank',
                                'submit'=>array('default/viewArchivo'), 
                                'params'=>array('id' => $model->id_radica, 'model'=>'radica')))
                        ?>                   
                    </td>
                </tr>
            <?php endif ?>
            
            <?php foreach ($model->RadicaDocs as $documento) : ?>        
            <tr>
                <td><?=$documento->idClasificacion->idDocumento->desc_documento?> </td>
                <td style="vertical-align: middle">
                    <?= CHtml::link('<i class="'.$icon[pathinfo($documento->ruta, PATHINFO_EXTENSION)].' gray"></i> Ver archivo', '', 
                        array(
                            'class'=>'btn btn-small', 
                            'target'=>'_blank',
                            'submit'=>array('default/viewArchivo'), 
                            'params'=>array('id' => $documento->id_radica_doc)))
                    ?>
                   
                </td>
            </tr>
            <?php endforeach ?>
        </table>  
    </div>
</div>

<div class="row">
    <div class="span9">
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-tasks"></i>  Historial del tramite</a>
            </li>  
        </ul>

        <table class="items table table-striped table-bordered table-condensed">
            <tr>
                <th >Actividad</th><th >Fecha Inicio</th><th >Fecha Termino</th><th >Usuario</th><th >Bitácora</th></tr>
        
            <?php foreach ($model->idmHistorials as $i => $his) : ?>        
            <tr>
                <td><?=$his->estado?></td>
                <td><?= Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($his->fecha_inicio))?></td>
                <td><?=$his->fecha_termino != null ? Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($his->fecha_termino)) : null?></td>
                <td><?=$his->usuarioCod->nombreCompleto?></td>
                <td><?=$his->observacion?></td>
            </tr>
            <?php endforeach ?>
        </table>  
    </div>
</div>

</div>