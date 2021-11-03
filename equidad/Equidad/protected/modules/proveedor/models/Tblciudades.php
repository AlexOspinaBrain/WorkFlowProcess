<?php

/**
 * This is the model class for table "tblciudades".
 *
 * The followings are the available columns in table 'tblciudades':
 * @property integer $idciudad
 * @property string $ciudad
 * @property string $codigo
 * @property integer $id_departamento
 *
 * The followings are the available model relations:
 * @property Proveedor[] $proveedors
 * @property WfRadicacion[] $wfRadicacions
 * @property Tbldepartamentos $idDepartamento
 */
class Tblciudades extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tblciudades the static model class
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
		return 'tblciudades';
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
			array('id_departamento', 'numerical', 'integerOnly'=>true),
			array('ciudad', 'length', 'max'=>80),
			array('codigo', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idciudad, ciudad, codigo, id_departamento', 'safe', 'on'=>'search'),
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
			'proveedors' => array(self::HAS_MANY, 'Proveedor', 'idciudad'),
			'wfRadicacions' => array(self::HAS_MANY, 'WfRadicacion', 'id_ciudad'),
			'idDepartamento' => array(self::BELONGS_TO, 'Tbldepartamentos', 'id_departamento'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idciudad' => 'Idciudad',
			'ciudad' => 'Ciudad',
			'codigo' => 'Codigo',
			'id_departamento' => 'Id Departamento',
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

		$criteria->compare('idciudad',$this->idciudad);
		$criteria->compare('ciudad',$this->ciudad,true);
		$criteria->compare('codigo',$this->codigo,true);
		$criteria->compare('id_departamento',$this->id_departamento);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getCiudadDep(){
		return $this->ciudad.' ( '.$this->idDepartamento->desc_departamento." )";
	}

}