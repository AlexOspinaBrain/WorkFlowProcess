<?php
    return array(
        'initial' => 'radicacion',
        'node' => array(
            array('id'=>'radicacion',   'label'=>'Radicación',  'transition'=>'en_tramite'),
            array('id'=>'en_tramite','label'=>'En tramite', 'transition'=>'en_devolucion,cerrado,anulado'),
            array('id'=>'en_devolucion','label'=>'Devuelto', 'transition'=>'en_tramite'),
            array('id'=>'cerrado','label'=>'Cerrado'),
            array('id'=>'anulado','label'=>'Anulado'),
        )
    )
?>