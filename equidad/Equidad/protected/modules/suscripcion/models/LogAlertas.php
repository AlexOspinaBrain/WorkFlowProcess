<?php

/**
 * This is the model class for table "sus_log_alertas".
 *
 * The followings are the available columns in table 'sus_log_alertas':
 * @property integer $id_alerta
 * @property string $desc_alerta
 * @property string $fecha
 * @property string $code
 *
 * The followings are the available model relations:
 * @property SusRadica $code0
 */
class LogAlertas extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogAlertas the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sus_log_alertas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code', 'required'),
			array('code', 'length', 'max'=>13),
			array('desc_alerta, fecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_alerta, desc_alerta, fecha, code', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'code0' => array(self::BELONGS_TO, 'SusRadica', 'code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_alerta' => 'Id Alerta',
			'desc_alerta' => 'Desc Alerta',
			'fecha' => 'Fecha',
			'code' => 'Code',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_alerta',$this->id_alerta);
		$criteria->compare('desc_alerta',$this->desc_alerta,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('code',$this->code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}