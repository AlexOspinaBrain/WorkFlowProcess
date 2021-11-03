<?php

/**
 * This is the model class for table "wf_festivo".
 *
 * The followings are the available columns in table 'wf_festivo':
 * @property integer $id_festivo
 * @property string $festivo
 */
class Festivo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Festivo the static model class
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
		return 'wf_festivo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('festivo', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_festivo, festivo', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_festivo' => 'Id Festivo',
			'festivo' => 'Festivo',
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

		$criteria->compare('id_festivo',$this->id_festivo);
		$criteria->compare('festivo',$this->festivo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}