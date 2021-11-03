<?php

/**
 * This is the model class for table "sus_tipocertificado".
 *
 * The followings are the available columns in table 'sus_tipocertificado':
 * @property integer $id_certificado
 * @property string $desc_certificado
 *
 * The followings are the available model relations:
 * @property SusRadica[] $susRadicas
 */
class Tipocertificado extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tipocertificado the static model class
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
		return 'sus_tipocertificado';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_certificado', 'required'),
			array('desc_certificado', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_certificado, desc_certificado', 'safe', 'on'=>'search'),
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
			'susRadicas' => array(self::HAS_MANY, 'Radica', 'id_certificado'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_certificado' => 'Id Certificado',
			'desc_certificado' => 'Certificado',
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

		$criteria->compare('id_certificado',$this->id_certificado);
		$criteria->compare('desc_certificado',$this->desc_certificado,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}