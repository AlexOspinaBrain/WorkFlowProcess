<?php

/**
 * This is the model class for table "wfarl_tipologia".
 *
 * The followings are the available columns in table 'wfarl_tipologia':
 * @property integer $idtipologia
 * @property string $tipologia
 * @property integer $idproceso
 *
 * The followings are the available model relations:
 * @property WfarlProceso $idproceso0
 */
class Tipologia extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tipologia the static model class
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
		return 'wfarl_tipologia';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tipologia', 'required'),
			array('idproceso', 'numerical', 'integerOnly'=>true),
			array('tipologia', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idtipologia, tipologia, idproceso', 'safe', 'on'=>'search'),
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
			'idproceso0' => array(self::BELONGS_TO, 'Proceso', 'idproceso'),
			'idtipotiempo0' => array(self::BELONGS_TO, 'TipoTiempo', 'idtipologia'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idtipologia' => 'Idtipologia',
			'tipologia' => 'Tipologia',
			'idproceso' => 'Idproceso',
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

		$criteria->compare('idtipologia',$this->idtipologia);
		$criteria->compare('tipologia',$this->tipologia,true);
		$criteria->compare('idproceso',$this->idproceso);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}