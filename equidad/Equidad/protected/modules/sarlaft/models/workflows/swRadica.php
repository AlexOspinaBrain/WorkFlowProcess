<?php
    return array(
        'initial' => 'radicacion',
        'node' => array(
            array('id'=>'radicacion',   'label'=>'Radicación',  'transition'=>'en_estudio'),
            array('id'=>'en_estudio','label'=>'En estudio', 'transition'=>'en_devolucion,cerrado,no_vincular,
                anulado,compromiso'),
            array('id'=>'en_devolucion','label'=>'Caso pendiente', 'transition'=>'en_estudio'),
            array('id'=>'no_vincular','label'=>'No vincular'),
            array('id'=>'cerrado','label'=>'Caso OK'),
            array('id'=>'anulado','label'=>'Anulado'),
            array('id'=>'compromiso','label'=>'Compromiso', 'transition'=>'en_estudio'),

        )
    )
?>