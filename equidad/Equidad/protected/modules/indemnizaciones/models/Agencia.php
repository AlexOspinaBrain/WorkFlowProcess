<?php

/**
 * This is the model class for table "tblradofi".
 *
 * The followings are the available columns in table 'tblradofi':
 * @property string $codigo
 * @property string $descrip
 * @property string $codigo_osiris
 * @property integer $id_agencia
 *
 * The followings are the available model relations:
 * @property WfRadicacion[] $wfRadicacions
 * @property Tblareascorrespondencia[] $tblareascorrespondencias
 * @property SusRadica[] $susRadicas
 * @property WfTipologia[] $wfTipologias
 * @property IdmRadica[] $idmRadicas
 */
class Agencia extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Agencia the static model class
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
		return 'tblradofi';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('codigo', 'required'),
			array('codigo', 'length', 'max'=>5),
			array('descrip', 'length', 'max'=>100),
			array('codigo_osiris', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('codigo, descrip, codigo_osiris, id_agencia', 'safe', 'on'=>'search'),
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
			'wfRadicacions' => array(self::HAS_MANY, 'WfRadicacion', 'id_agencia'),
			'tblareascorrespondencias' => array(self::HAS_MANY, 'Tblareascorrespondencia', 'agencia'),
			'susRadicas' => array(self::HAS_MANY, 'SusRadica', 'id_agencia'),
			'wfTipologias' => array(self::HAS_MANY, 'WfTipologia', 'id_agencia'),
			'idmRadicas' => array(self::HAS_MANY, 'IdmRadica', 'id_agencia'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'codigo' => 'Codigo',
			'descrip' => 'Descrip',
			'codigo_osiris' => 'Codigo Osiris',
			'id_agencia' => 'Agencia',
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

		$criteria->compare('codigo',$this->codigo,true);
		$criteria->compare('descrip',$this->descrip,true);
		$criteria->compare('codigo_osiris',$this->codigo_osiris,true);
		$criteria->compare('id_agencia',$this->id_agencia);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}