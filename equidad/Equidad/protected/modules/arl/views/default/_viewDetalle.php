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
                <tr>
                    <th><?= $model->getAttributeLabel('afiliacion')?></th><td><?= $model->afiliacion?></td>
                </tr>
                <tr class="odd">                     
                    <th><?= $model->getAttributeLabel('idproceso')?></th><td><?= $model->idProceso->proceso?></td>   
                    <th><?= $model->getAttributeLabel('idtipologia')?></th><td><?= $model->idTipologia->tipologia ?></td>   

                </tr>
            </tbody>
            <tbody id="emp" style="display:none;">
                <tr>
                    <th><?= $model->getAttributeLabel('nit')?></th><td><?= $model->nit?></td>
                    <th><?= $model->getAttributeLabel('razonsocial')?></th><td><?= $model->razonsocial?></td>
                </tr>                 
                <tr  class="odd">                     
                    <th><?= $model->getAttributeLabel('intermediario')?></th><td><?= $model->intermediario?></td>   
                    <th><?= $model->getAttributeLabel('nitintermediario')?></th><td><?= $model->nitintermediario ?></td>   
                </tr>
                <tr>
                    <th><?= $model->getAttributeLabel('ejecutivov')?></th><td><?= $model->ejecutivov?></td>
                    <th><?= $model->getAttributeLabel('franquicia')?></th><td><?= $model->franquicia?></td>
                </tr>          
                <tr>
                    <th><?= $model->getAttributeLabel('vlrcot')?></th><td><?= $model->vlrcot?></td>
                    <th><?= $model->getAttributeLabel('vlrnomina')?></th><td><?= $model->vlrnomina?></td>
                </tr> 
                <tr>
                    <th><?= $model->getAttributeLabel('ntrabajadores')?></th><td><?= $model->ntrabajadores?></td>
                    <th><?= $model->getAttributeLabel('representante')?></th><td><?= $model->representante?></td>
                </tr> 
                                                      
             </tbody>   
            <tbody id="ind" style="display:none;">    
                <tr  class="odd">                     
                    <th><?= $model->getAttributeLabel('vlrcontrato')?></th><td><?= $model->vlrcontrato?></td>   
                    <th><?= $model->getAttributeLabel('vlrmescontrato')?></th><td><?= $model->vlrmescontrato ?></td>   
                    
                </tr>
                <tr>
                    <th><?= $model->getAttributeLabel('riesgo')?></th><td><?= $model->riesgo?></td>
                    </td>
                </tr>                   
                </div>


            </tbody>
        </table>

    </div>
</div>

    <?php if($model->arlFiles):  ?>
         <ul class="nav nav-tabs" style="margin-bottom: 0px;">
            <li class="active">
                <a><i class="icon-upload"></i>Adjuntos</a>
            </li>  
        </ul>

        <table class="items table table-striped table-bordered table-condensed">
            <tr><th>Archivo</th><th >Actividad </th><th >Fecha</th><th >Usuario</th></tr>
                
            <?php foreach ($model->arlFiles as $file) : ?>        
                <tr>
                    <td><?=CHtml::link(CHtml::image(Yii::app()->baseUrl.'/images/adjunto.png','',array('width'=>'20')) .' '. $file->name_file, strrpos($file->path_file, "http")===false ? array('default/viewFile', "id"=>$file->id_file) : $file->path_file , array("target"=>"_blank")) ?></td>                    
                    <td><?= !$file->idHistorial->estado ? 'Radicación' : $file->idHistorial->estado ?></td>               
                    <td><?=$file->fecha?></td>   
                    <td><?= !$file->idHistorial->usuarioCod->nombreCompleto ?  $model->arlHistorialrad->usuarioCod->nombreCompleto : $file->idHistorial->usuarioCod->nombreCompleto ?></td>
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
        
            <?php foreach ($model->arlHistorials as $i => $his) : ?>        
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
<script type="text/javascript">
    
    $(function() {  
       
        
            if('<?= $model->idproceso ?>'==1)
                $("#emp").show();

            else if('<?= $model->idproceso ?>'==3)
                $("#ind").show();

    });    
</script>