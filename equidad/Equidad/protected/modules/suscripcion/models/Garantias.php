<?php

/**
 * This is the model class for table "sus_garantias".
 *
 * The followings are the available columns in table 'sus_garantias':
 * @property integer $id_garantia
 * @property string $desc_garantia
 *
 * The followings are the available model relations:
 * @property SusRadica[] $susRadicas
 */
class Garantias extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Garantias the static model class
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
		return 'sus_garantias';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_garantia', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_garantia, desc_garantia', 'safe', 'on'=>'search'),
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
			'susRadicas' => array(self::HAS_MANY, 'SusRadica', 'id_garantia'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_garantia' => 'Id Garantia',
			'desc_garantia' => 'Desc Garantia',
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

		$criteria->compare('id_garantia',$this->id_garantia);
		$criteria->compare('desc_garantia',$this->desc_garantia,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}