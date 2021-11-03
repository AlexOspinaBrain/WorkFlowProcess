<?php

/**
 * This is the model class for table "sus_sarlaft".
 *
 * The followings are the available columns in table 'sus_sarlaft':
 * @property integer $id_sarlaft
 * @property string $desc_sarlaft
 *
 * The followings are the available model relations:
 * @property SusRadica[] $susRadicas
 */
class Sarlaft extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sarlaft the static model class
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
		return 'sus_sarlaft';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_sarlaft', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_sarlaft, desc_sarlaft', 'safe', 'on'=>'search'),
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
			'susRadicas' => array(self::HAS_MANY, 'SusRadica', 'id_sarlaft'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_sarlaft' => 'Id Sarlaft',
			'desc_sarlaft' => 'Desc Sarlaft',
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

		$criteria->compare('id_sarlaft',$this->id_sarlaft);
		$criteria->compare('desc_sarlaft',$this->desc_sarlaft,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}