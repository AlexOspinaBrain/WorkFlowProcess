<?php
class InformesForm extends CFormModel{
    public $fechai;
    public $fechaf;
    public $informe;

    public function rules()
    {
        return array(
            array('fechai, fechaf, informe', 'required'),
            array('fechai, fechaf', 'date', 'format' => 'yyyy-MM-dd'),
            array('fechai, fechaf', 'length', 'max'=>10, 'min'=>10),
            array('informe', 'numerical'),
        );
    }
    public function attributeLabels()
    {
        return array(
            'fechai'=>'Fecha Inicial',
            'fechaf'=>'Fecha Final',
            'informe'=>'Tipo de Informe',
        );
    }
} 
?>