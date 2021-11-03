<?php

/**
 * This is the model class for table "sus_adjuntos".
 *
 * The followings are the available columns in table 'sus_adjuntos':
 * @property integer $id_adjunto
 * @property string $nombre
 * @property string $ruta_adjunto
 * @property string $code
 *
 * The followings are the available model relations:
 * @property SusRadica $code0
 */
class Adjuntos extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Adjuntos the static model class
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
		return 'sus_adjuntos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nombre, ruta_adjunto, code', 'required'),
			array('nombre', 'length', 'max'=>128),
			array('ruta_adjunto', 'length', 'max'=>256),
			array('code', 'length', 'max'=>13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_adjunto, nombre, ruta_adjunto, code', 'safe', 'on'=>'search'),
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
			'id_adjunto' => 'Id Adjunto',
			'nombre' => 'Nombre',
			'ruta_adjunto' => 'Ruta Adjunto',
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

		$criteria->compare('id_adjunto',$this->id_adjunto);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('ruta_adjunto',$this->ruta_adjunto,true);
		$criteria->compare('code',$this->code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}