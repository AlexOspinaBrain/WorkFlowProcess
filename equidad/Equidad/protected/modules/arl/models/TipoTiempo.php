<?php

/**
 * This is the model class for table "wfarl_tipo_tiempo".
 *
 * The followings are the available columns in table 'wfarl_tipo_tiempo':
 * @property integer $idtipologia
 * @property integer $diatramite
 * @property integer $diadevolucion
 * @property integer $diatramite2
 *
 * The followings are the available model relations:
 * @property WfarlTipologia $idtipologia0
 */
class TipoTiempo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TipoTiempo the static model class
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
		return 'wfarl_tipo_tiempo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idtipologia', 'required'),
			array('idtipologia, diatramite, diadevolucion, diatramite2', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idtipologia, diatramite, diadevolucion, diatramite2', 'safe', 'on'=>'search'),
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
			'idtipologia0' => array(self::BELONGS_TO, 'Tipologia', 'idtipologia'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idtipologia' => 'Idtipologia',
			'diatramite' => 'Diatramite',
			'diadevolucion' => 'Diadevolucion',
			'diatramite2' => 'Diatramite2',
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
		$criteria->compare('diatramite',$this->diatramite);
		$criteria->compare('diadevolucion',$this->diadevolucion);
		$criteria->compare('diatramite2',$this->diatramite2);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}