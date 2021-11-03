<?php

/**
 * This is the model class for table "WF_POLIZAS".
 *
 * The followings are the available columns in table 'WF_POLIZAS':
 * @property string $SUCUR
 * @property string $POLIZA
 * @property double $CANTIDAD
 * @property string $SUCREA
 * @property string $FECEXP
 * @property string $ASEGURADO
 * @property string $CERTIF
 */
class PolizasOsiris extends CActiveRecord
{

	public function getDbConnection(){
		return Yii::app()->dbOsiris;
	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PolizasOsiris the static model class
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
		return 'WF_POLIZAS';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CANTIDAD', 'numerical'),
			array('SUCUR', 'length', 'max'=>6),
			array('POLIZA, CERTIF', 'length', 'max'=>8),
			array('SUCREA', 'length', 'max'=>20),
			array('ASEGURADO', 'length', 'max'=>12),
			array('FECEXP', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('SUCUR, POLIZA, CANTIDAD, SUCREA, FECEXP, ASEGURADO, CERTIF', 'safe', 'on'=>'search'),
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
			'SUCUR' => 'Sucur',
			'POLIZA' => 'Poliza',
			'CANTIDAD' => 'Cantidad',
			'SUCREA' => 'Sucrea',
			'FECEXP' => 'Fecexp',
			'ASEGURADO' => 'Asegurado',
			'CERTIF' => 'Certif',
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

		$criteria->compare('SUCUR',$this->SUCUR,true);
		$criteria->compare('POLIZA',$this->POLIZA,true);
		$criteria->compare('CANTIDAD',$this->CANTIDAD);
		$criteria->compare('SUCREA',$this->SUCREA,true);
		$criteria->compare('FECEXP',$this->FECEXP,true);
		$criteria->compare('ASEGURADO',$this->ASEGURADO,true);
		$criteria->compare('CERTIF',$this->CERTIF,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function afterFind()
	{
	    // Format dates based on the locale
	   $this->FECEXP = date("Y/m/d", strtotime($this->FECEXP));
	    return parent::afterFind();
	}
}