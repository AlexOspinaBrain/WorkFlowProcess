<?php

/**
 * This is the model class for table "tbldepartamentos".
 *
 * The followings are the available columns in table 'tbldepartamentos':
 * @property integer $id_departamento
 * @property string $desc_departamento
 * @property string $indicativo
 *
 * The followings are the available model relations:
 * @property Tblciudades[] $tblciudades
 */
class Tbldepartamentos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tbldepartamentos the static model class
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
		return 'tbldepartamentos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_departamento', 'required'),
			array('id_departamento', 'numerical', 'integerOnly'=>true),
			array('desc_departamento', 'length', 'max'=>20),
			array('indicativo', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_departamento, desc_departamento, indicativo', 'safe', 'on'=>'search'),
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
			'tblciudades' => array(self::HAS_MANY, 'Tblciudades', 'id_departamento'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_departamento' => 'Id Departamento',
			'desc_departamento' => 'Desc Departamento',
			'indicativo' => 'Indicativo',
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

		$criteria->compare('id_departamento',$this->id_departamento);
		$criteria->compare('desc_departamento',$this->desc_departamento,true);
		$criteria->compare('indicativo',$this->indicativo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}