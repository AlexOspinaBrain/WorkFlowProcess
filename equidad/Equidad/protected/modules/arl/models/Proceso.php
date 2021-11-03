<?php

/**
 * This is the model class for table "wfarl_proceso".
 *
 * The followings are the available columns in table 'wfarl_proceso':
 * @property integer $idproceso
 * @property string $proceso
 *
 * The followings are the available model relations:
 * @property WfarlTipologia[] $wfarlTipologias
 */
class Proceso extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Proceso the static model class
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
		return 'wfarl_proceso';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('proceso', 'required'),
			array('proceso', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idproceso, proceso', 'safe', 'on'=>'search'),
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
			'wfarlTipologias' => array(self::HAS_MANY, 'Tipologia', 'idproceso'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idproceso' => 'Idproceso',
			'proceso' => 'Proceso',
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

		$criteria->compare('idproceso',$this->idproceso);
		$criteria->compare('proceso',$this->proceso,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}