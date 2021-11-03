<?php

/**
 * This is the model class for table "idm_amparo".
 *
 * The followings are the available columns in table 'idm_amparo':
 * @property integer $id_amparo
 * @property string $desc_amparo
 *
 * The followings are the available model relations:
 * @property IdmClasifiDoc[] $idmClasifiDocs
 */
class Amparo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Amparo the static model class
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
		return 'idm_amparo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_amparo', 'required'),
			array('desc_amparo', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_amparo, desc_amparo', 'safe', 'on'=>'search'),
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
			'idmClasifiDocs' => array(self::HAS_MANY, 'IdmClasifiDoc', 'id_amparo'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_amparo' => 'Id Amparo',
			'desc_amparo' => 'Desc Amparo',
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

		$criteria->compare('id_amparo',$this->id_amparo);
		$criteria->compare('desc_amparo',$this->desc_amparo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}