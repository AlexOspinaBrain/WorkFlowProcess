<?php
    return array(
        'initial' => 'radicacion',
        'node' => array(
            array('id'=>'radicacion',   'label'=>'Radicación',  'transition'=>'en_estudio'),
           // array('id'=>'por_asignar_a_estudio', 'label'=>'Por asignar a estudio',    'transition'=>'en_estudio'),
            array('id'=>'en_estudio','label'=>'En estudio', 'transition'=>'en_juicio, solicitud_documentos, ofrecimiento, honorarios, objetado, cerrado, pagado'),
            array('id'=>'en_juicio','label'=>'En juicio'),
            array('id'=>'solicitud_documentos','label'=>'Solicitud de documentos'),
            array('id'=>'objetado','label'=>'Objetado'),
            array('id'=>'cerrado','label'=>'Cerrado'),
            array('id'=>'pagado','label'=>'Pagado'),
            array('id'=>'honorarios','label'=>'Honorarios'),
            array('id'=>'ofrecimiento','label'=>'Ofrecimiento'),
        )
    )
?>