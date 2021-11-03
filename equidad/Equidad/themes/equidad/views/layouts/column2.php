
<?php $this->beginContent('//layouts/main'); ?>


<?php
    $this->widget('bootstrap.widgets.TbNavbar', array(
        'brand' => CHtml::image(Yii::app()->getBaseUrl().'/images/logo.jpg','',array('style'=>'width: 100px;position: absolute;margin-top: -30px;')),
        'brandOptions' => array('style'=>'width:auto;margin-left: 0px;'),
        'htmlOptions' => array('style' => 'margin-top:20px'),
        'items' => array(
        array(
            'class' => 'bootstrap.widgets.TbMenu',
            'htmlOptions' => array('style' => 'margin-left: 100px'),
            //'items' => Yii::app()->user->getMenu(array('1', '2', '3', '4', '5'), array('4.1', '4.2')),          
            'items'=>array(
                        array('label'=>'Volver a Imagine', 'url'=>'/equidad/default.php'),

                    )
            ),
          array(
            'class' => 'bootstrap.widgets.TbMenu',
            'htmlOptions' => array('class' => 'pull-right'),
            //'items' => Yii::app()->user->getMenu(array('6'))
            'items'=>array(
                array('label'=>'Cerrar Sesión', 'url'=>'/equidad/Equidad/site/logout'),

              )
            )
        ),

    ));
?>

    <div class="row">
        <div class="span8 offset2">
            <?php if(isset($this->breadcrumbs)):?>
                <?php $this->widget('zii.widgets.CBreadcrumbs', array(
                    'links'=>$this->breadcrumbs,
                )); ?><!-- breadcrumbs -->
            <?php endif?>
        </div>
    </div>

     <?php echo $content; ?>
<?php if(Yii::app()->user->id):?>
<div style="position: fixed;right: 0px;z-index:100;bottom:0px;color: gray;background-color: white; padding:5px;font-size:12px">
    <div style="padding:0 20px;display: inline;"><b>Usuario: </b> <?=Yii::app()->user->descUsuario?></div>
    <div style="padding:0 20px;display: inline;"><b>Nombre: </b> <?=Yii::app()->user->nombreCompleto?></div>
    <div style="padding:0 20px;display: inline;"><b>Pertenece a la agencia: </b> <?=Yii::app()->user->agencia?></div>
    <div style="padding:0 20px;display: inline;"><b>del área: </b> <?=Yii::app()->user->area?></div>
</div>
<?php endif ?>
   
<?php $this->endContent(); ?>
<style>
    .navbar {z-index:500 !important;}
</style>
