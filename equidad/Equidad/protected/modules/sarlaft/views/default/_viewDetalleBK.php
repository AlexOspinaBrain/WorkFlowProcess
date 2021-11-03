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
                    <th><?= $model->getAttributeLabel('status')?></th><td><?= CHtml::tag('span', array('class'=>'label label-success'), $model->swGetStatus()->getLabel())?></td>
                </tr>
                 <tr class="odd">                     
                    <th><?= $model->getAttributeLabel('tipo_id')?></th><td><?= $model->tipo_id . " - " . $model->identificacion?></td>   
                    <th><?= $model->getAttributeLabel('nombre')?></th><td><?= $model->nombre ?></td>   

                </tr>
                <tr class="even">
                    
                    <th><?= $model->getAttributeLabel('id_agencia')?></th><td><?= $model->idAgencia->descrip ?></td>
                    <th><?= $model->getAttributeLabel('id_producto')?></th><td><?=$model->idProducto->ramo?></td>
                </tr>

            </tbody>
        </table>

    </div>
</div>

    <?php if($model->sarFiles):  ?>
         <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-upload"></i>Adjuntos</a>
            </li>  
        </ul>

        <table class="items table table-striped table-bordered table-condensed">
            <tr><th>Archivo</th><th >Actividad </th><th >Fecha</th><th >Usuario</th></tr>
                
            <?php foreach ($model->sarFiles as $file) : ?>        
                <tr>
  
                    <td><?=CHtml::link(CHtml::image(Yii::app()->baseUrl.'/images/adjunto.png','',array('width'=>'20')) .' '. $file->name_file, 
                        $_SERVER['HTTP_HOST'] === '192.168.241.87' ?  
                            str_replace('http://imagine.laequidadseguros.coop/','https://servicios.laequidadseguros.coop/Imagine/',$file->path_file)
                            : 
                            $file->path_file , 
                        array("target"=>"_blank")) ?></td> 

                    <td><?= !$file->idHistorial->estado ? 'Radicación' : $file->idHistorial->estado ?></td>               
                    <td><?=$file->fecha?></td>   
                    <td><?= !$file->idHistorial->usuarioCod->nombreCompleto ?  $model->sarHistorialrad->usuarioCod->nombreCompleto : $file->idHistorial->usuarioCod->nombreCompleto ?></td>
                </tr>
            <?php endforeach ?>
        </table> 
    <?php endif ?>

<div class="row">
    <div class="span9">
        <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-tasks"></i>Historial del tramite</a>
            </li>  
        </ul>

        <table class="items table table-striped table-bordered table-condensed">
            <tr>
                <th >Actividad</th><th >Fecha Inicio</th><th >Fecha Termino</th><th >Usuario</th><th >Bitácora</th></tr>
        
            <?php foreach ($model->sarHistorials as $i => $his) : ?>        
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