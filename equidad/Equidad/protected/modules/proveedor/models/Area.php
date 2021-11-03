<?php

/**
 * This is the model class for table "tblareascorrespondencia".
 *
 * The followings are the available columns in table 'tblareascorrespondencia':
 * @property integer $areasid
 * @property string $area
 * @property string $area_general
 * @property string $responsable
 * @property string $prefijo
 * @property string $agencia
 * @property string $correspondencia
 *
 * The followings are the available model relations:
 * @property Proveedor[] $proveedors
 * @property Tbltiposdoccorresp[] $tbltiposdoccorresps
 * @property FacRadica[] $facRadicas
 * @property Persona[] $personas
 * @property Radcorrespondencia[] $radcorrespondencias
 * @property Tblradofi $agencia0
 * @property Trasacorrespondencia[] $trasacorrespondencias
 * @property AdmUsuario[] $admUsuarios
 */
class Area extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Area the static model class
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
		return 'tblareascorrespondencia';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('area, area_general, responsable', 'length', 'max'=>80),
			array('prefijo', 'length', 'max'=>10),
			array('agencia', 'length', 'max'=>5),
			array('correspondencia', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('areasid, area, area_general, responsable, prefijo, agencia, correspondencia', 'safe', 'on'=>'search'),
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
			'proveedors' => array(self::HAS_MANY, 'Proveedor', 'area_id'),
			'tbltiposdoccorresps' => array(self::HAS_MANY, 'Tbltiposdoccorresp', 'area'),
			'facRadicas' => array(self::HAS_MANY, 'FacRadica', 'id_area'),
			'personas' => array(self::HAS_MANY, 'Persona', 'area_id'),
			'radcorrespondencias' => array(self::HAS_MANY, 'Radcorrespondencia', 'area'),
			'agencia0' => array(self::BELONGS_TO, 'Tblradofi', 'agencia'),
			'trasacorrespondencias' => array(self::HAS_MANY, 'Trasacorrespondencia', 'area'),
			'admUsuarios' => array(self::HAS_MANY, 'AdmUsuario', 'area'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'areasid' => 'Areasid',
			'area' => 'Area',
			'area_general' => 'Area General',
			'responsable' => 'Responsable',
			'prefijo' => 'Prefijo',
			'agencia' => 'Agencia',
			'correspondencia' => 'Correspondencia',
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

		$criteria->compare('areasid',$this->areasid);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('area_general',$this->area_general,true);
		$criteria->compare('responsable',$this->responsable,true);
		$criteria->compare('prefijo',$this->prefijo,true);
		$criteria->compare('agencia',$this->agencia,true);
		$criteria->compare('correspondencia',$this->correspondencia,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}