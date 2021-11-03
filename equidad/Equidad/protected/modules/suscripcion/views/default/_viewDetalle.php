<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<div id="dialogViewDetalle" style="display:none">
<div class="row" id="nnn">
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
            
                    if(height > $(window).height ())
                        $("#dialogViewDetalle").dialog( "option", "height", $(window).height()-10 );
                        
                    $("#dialogViewDetalle").dialog( "option", "width", $("#dialogViewDetalle" ).width() +50);
                    $(this).dialog( "option", "position", { my: "center", at: "center" } );
                }
            });

            $( "#dialogCodebar" ).dialog({
                autoOpen:false,
                modal:true,
               // width:'auto',
                hide:'fade',
                show:'fade',
                title:'Codigo de barras',
                buttons: [ { text: "Imprimir", click: function(){$("#codeBar").jqprint()} , 'class':'btn btn-primary'},
                        { text: "Cerrar", click: function() { $( this ).dialog( "close" ); } , 'class':'btn'}  ]    ,
                open:function(){                   
                    $("#dialogCodebar").dialog( "option", "height", 220 );                        
                    $("#dialogCodebar").dialog( "option", "width", 380);
                    $(this).dialog( "option", "position", { my: "center", at: "center" } );
                }      
            });

        });
    </script>

    <?php
        if(($openDialog))
            echo CHtml::script(' $( "#dialogCodebar" ).dialog("open")');
    ?>

    <div class="span7 offset1" >
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-info-sign"></i> Detalles generales</a>
            </li>  
        </ul>
        <table class="detail-view table table-bordered table-striped table-condensed">
            <tbody>
                <tr class="odd">
                    <th><?= $model->getAttributeLabel('code')?></th>
                    <td><b class="text-success"><?= $model->code?></b>  </td>
                    <th>Código de barras</th>
                    <td>
                        <?php
                            $this->widget(
                                'bootstrap.widgets.TbButton',
                                array(
                                    'size' => 'small',
                                    'type'=>'primary',
                                    'icon' => 'icon-barcode icon-white',              
                                    'id' => 'buttonCodeBar',
                                    'htmlOptions'=>array(
                                        'onClick'=>'js:alert()'
                                    )
                                )
                            )
                        ?>

                    </td>               
                </tr>
                <tr class="even">
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
                    <th><?= $model->getAttributeLabel('poliza')?></th><td><?=$model->poliza?$model->poliza:"<span class='null'>No asignado</span>" ?></td>
                    <th><?= $model->getAttributeLabel('cant_ordenes')?></th><td><?=$model->cant_ordenes?$model->cant_ordenes:"<span class='null'>No asignado</span>"?></td>
                </tr>
                 <tr class="odd">
                    <th><?= $model->getAttributeLabel('id_producto')?></th><td colspan="3"><?=$model->id_producto!=0?$model->idProducto->codigo_osiris.' - '.$model->idProducto->producto:"<span class='null'>No asignado</span>" ?></td>
                    
                </tr>
                <tr class="even">
                    <th><?= $model->getAttributeLabel('id_agencia')?></th>
                    <td colspan="3"><?=$model->idAgencia->descrip?></td>
                </tr>
            </tbody>
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
                <th >Estado</th><th >Fecha Inicio</th><th >Fecha Termino</th><th >Usuario</th></tr>
        
            <?php foreach ($model->susHistorials as $i => $his) : ?>        
            <tr>
                <td><?=$his->estado?></td>
                <td><?= Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($his->fecha_inicio))?></td>
                <td><?=$his->fecha_termino != null ? Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($his->fecha_termino)) : null?></td>
                <td><?=$his->usuarioCod->nombreCompleto?></td>
            </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'historial-form',
    'enableAjaxValidation'=>false
)); ?>
<?php 
    $modelObs->code = $model->code;
?>

 <?php echo $form->hiddenField($modelObs, 'code'); ?>
<div class="row">
    <div class="span7 offset1">
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-comment"></i>  Observaciones</a>
            </li>  
        </ul>       
        
        <table class="detail-view table table-striped table-condensed"><tbody id="Observaciones">
        <?php foreach ($model->susRadicaObs as $i => $obs) : ?>
            <tr>
                <th class="text-right"><?=$obs->usuarioCod->nombreCompleto?><br><?=Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($obs->fecha))?></th>
                <td><?=$obs->observacion?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tbody>
            <tr>
                <th class="text-right"><?php echo $form->labelEx($modelObs,'observacion',  array("style"=>"font-weight:bold")); ?></th>
                <td>
                    <?php echo $form->textArea($modelObs, 'observacion', array('rows' => 2, 'style'=>'width:70%')); ?>
                    <?php echo CHtml::ajaxSubmitButton(
                        'Guardar',
                        array("radicaObs/guardaObservacion"), 
                        array(
                            'update'=>'#Observaciones',
                            'success'=>'js:function(data){
                                $("#Observaciones").html(data);
                                $("#'.CHtml::activeId($modelObs, 'observacion' ).'").val("");
                            }',
                        ),  
                        array('class'=>'btn btn-primary')
                    ); ?>
                </td>

            </tr>
        </tbody></table>
    </div>   
</div>

<?php $this->endWidget(); ?>

<div id="dialogCodebar">
    <?php
        Yii::app()->clientScript->registerScriptFile('js/jquery.jqprint-0.3.js', CClientScript::POS_HEAD);
        $urlCodebar = dirname(Yii::app()->getBaseUrl()).
                '/config/barcode/image.php?filetype=PNG&dpi=72&thickness=30&scale=1&rotation=0&font_family=Arial.ttf&font_size=10&text='.
                $model->code.'&code=BCGcode128';
    ?>
 <table id="codeBar" style="font-size:8px; width:340px; height:80px">
        <tr>
            <td rowspan="3" style="padding: 0px;" >
                <div style="line-height: 8px;">
                    <table>
                        <tr>
                            <th style="padding: 1px 5px; vertical-align: top;font-size:8px"><?= $model->getAttributeLabel('id_persona')?>:</th>
                            <td style="padding: 1px;5px;font-size:8px"><?="(" .$model->idPersona->tipo_doc . $model->idPersona->documento . ") ". 
                                    ($model->idPersona->primer_nombre ? 
                                        $model->idPersona->nombreCompleto : 
                                        $model->idPersona->nombre)?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <th style="padding: 1px 5px;font-size:8px"><?= $model->getAttributeLabel('fecha_rad')?>:</th>
                            <td style="padding: 1px 5px;font-size:8px"><?=Yii::app()->dateFormatter->format("dd/MM/y hh:mm a",strtotime($model->fecha_rad))?></td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <th style="padding: 1px 5px;font-size:8px"><?= $model->getAttributeLabel('prioridad')?>:</th>
                            <td style="padding: 1px 5px;font-size:8px"><?=$model->prioridad?></td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <th style="padding: 1px 5px;font-size:8px"><?= $model->getAttributeLabel('idCertificado.desc_certificado')?>:</th>
                            <td style="padding: 1px 5px;font-size:8px"><?=$model->idCertificado->desc_certificado?></td>
                        </tr>
                    </table>

                    <table>
                        <tr>
                            <th style="padding: 1px 5px;font-size:8px"><?= $model->getAttributeLabel('id_agencia')?>:</th>
                            <td style="padding: 1px 5px;font-size:8px"><?=$model->idAgencia->descrip?></td>
                        </tr>
                    </table>
                </div> 
            </td>
            <th style="text-align: center;padding: 0px;">LA EQUIDAD SEGUROS O.C.</th>
        </tr>
        <tr><td style="padding: 0px;text-align: center;"><img src="<?=$urlCodebar?>" width="145px"></td></tr>
        <tr><th style="text-align: center;padding: 0px;">Suscripciones</th></tr>
    </table>
</div>
<?php 
$script = <<<EOD
    $("#buttonCodeBar").click(function(){
        $("#dialogCodebar").dialog("open");
    });
EOD;
Yii::app()->getClientScript()->registerScript('buttonCodeBar',$script, CClientScript::POS_END);
?>

</div>