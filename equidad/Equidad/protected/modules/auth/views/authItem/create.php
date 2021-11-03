<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $model AuthItemForm */
/* @var $form TbActiveForm */

$this->breadcrumbs = array(
    $this->capitalize($this->getTypeText(true)) => array('index'),
    Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())),
);
?>

    <h1><?php echo Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())); ?></h1>

<?php $form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'type' => 'horizontal',
        'htmlOptions' => array('class' => 'well'),
        //'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
    )
); ?>
<?php echo $form->hiddenField($model, 'type'); ?>
<?php echo $form->textFieldRow($model, 'name'); ?>
<?php echo $form->textFieldRow($model, 'description'); ?>
<?php echo $form->textFieldRow($model, 'data'); ?>

    <div class="form-actions">
        <?php 
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'submit',
                    'type' => 'primary',
                    'label' => 'Crear'
                )
            ); 
        ?>
        <?php
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'label' => 'Cancelar',
                    'url' => array('index'),
                )
            );
        ?>
    </div>

<?php $this->endWidget(); ?>