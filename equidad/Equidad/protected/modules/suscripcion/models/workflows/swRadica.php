<?php
    return array(
        'initial' => 'radicacion',
        'node' => array(
            array('id'=>'radicacion',   'label'=>'Radicación',  'transition'=>'recibir_tecnico, tramitar'),
            array('id'=>'recibir_tecnico', 'label'=>'Recibir técnico',    'transition'=>'tramitar, anulado'),
            array('id'=>'tramitar','label'=>'Tramitar',    'transition'=>'recibir_expedicion, verificar_comercial, expedicion'),
            //array('id'=>'devolucion_comercial','label'=>'Devolución comercial',  'transition'=>'recibir_tramite'),
            array('id'=>'recibir_expedicion','label'=>'Recibir para expedición', 'transition'=>'expedicion'  ),
            array('id'=>'expedicion','label'=>'Expedición', 'transition'=>'cierre, recibir_tecnico, verificar_comercial' ),
            array('id'=>'cierre','label'=>'Cerrado' ),
            array('id'=>'verificar_comercial','label'=>'Verificar comercial', 'transition'=>'recibir_tecnico, cierre' ),
            array('id'=>'anulado','label'=>'Anulado'),
        )
    )
?>