<?php

/**
 * This is the model class for table "idm_clasifi_doc".
 *
 * The followings are the available columns in table 'idm_clasifi_doc':
 * @property integer $id_clasificacion
 * @property integer $id_documento
 * @property integer $id_producto
 * @property integer $id_ramo
 *
 * The followings are the available model relations:
 * @property IdmDocumento $idDocumento
 * @property SusProducto $idProducto
 * @property EquiRamo $idRamo
 * @property IdmRadicaDoc[] $idmRadicaDocs
 */
class ClasificaDoc extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClasificaDoc the static model class
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
		return 'idm_clasifi_doc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_documento, id_producto, id_ramo, id_amparo', 'required'),
			array('id_documento, id_producto, id_ramo, id_amparo', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_clasificacion, id_documento, id_producto, id_ramo, id_amaparo', 'safe', 'on'=>'search'),
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
			'idDocumento' => array(self::BELONGS_TO, 'Documento', 'id_documento'),
			'idProducto' => array(self::BELONGS_TO, 'Producto', 'id_producto'),
			'idRamo' => array(self::BELONGS_TO, 'Ramo', 'id_ramo'),
			'idAmparo' => array(self::BELONGS_TO, 'Amparo', 'id_amparo'),
			'idmRadicaDocs' => array(self::HAS_MANY, 'RadicaDoc', 'id_clasificacion'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_clasificacion' => 'Id Clasificacion',
			'id_documento' => 'Documento',
			'id_producto' => 'Producto',
			'id_ramo' => 'Ramo',
			'id_amparo' => 'Amparo',
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

		$criteria->compare('id_clasificacion',$this->id_clasificacion);
		$criteria->compare('id_documento',$this->id_documento);
		$criteria->compare('id_producto',$this->id_producto);
		$criteria->compare('id_ramo',$this->id_ramo);
		$criteria->compare('id_amparo',$this->id_amparo);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}