<?php

/**
 * This is the model class for table "persona".
 *
 * The followings are the available columns in table 'persona':
 * @property integer $id_persona
 * @property string $tipo_doc
 * @property integer $documento
 * @property string $nombre
 * @property string $primer_nombre
 * @property string $segundo_nombre
 * @property string $primer_apellido
 * @property string $segundo_apellido
 * @property string $telefono
 * @property string $fax
 * @property string $direccion
 * @property string $correo
 * @property string $producto
 * @property string $representante
 * @property string $fecha_actualizacion
 * @property integer $idciudad
 *
 * The followings are the available model relations:
 * @property Tblciudades $idciudad0
 * @property SusRadica[] $susRadicas
 * @property Proveedor[] $proveedors
 * @property FacRadica[] $facRadicas
 * @property PersonaComment[] $personaComments
 */
class Persona extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Persona the static model class
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
		return 'persona';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tipo_doc, documento, nombre, idciudad', 'required'),
			array('correo', 'email'),
			array('documento', 'unique', 'on' => 'create'),
			array('documento, idciudad', 'numerical', 'integerOnly'=>true),
			array('tipo_doc', 'length', 'max'=>4),
			array('nombre', 'length', 'max'=>80),
			array('primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, telefono, fax', 'length', 'max'=>50),
			array('direccion, correo, producto, representante', 'length', 'max'=>100),
			array('fecha_actualizacion', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_persona, tipo_doc, documento, nombre, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, telefono, fax, direccion, correo, producto, representante, fecha_actualizacion, idciudad', 'safe', 'on'=>'search'),
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
			'idciudad0' => array(self::BELONGS_TO, 'Ciudades', 'idciudad'),
			'susRadicas' => array(self::HAS_MANY, 'SusRadica', 'id_persona'),
			'proveedors' => array(self::HAS_ONE, 'Proveedor', 'id_persona'),
			'facRadicas' => array(self::HAS_MANY, 'FacRadica', 'id_persona'),
			'personaComments' => array(self::HAS_MANY, 'PersonaComment', 'id_persona'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_persona' => 'Id Persona',
			'tipo_doc' => 'Tipo documento',
			'documento' => 'Documento',
			'nombre' => 'Nombre',
			'primer_nombre' => 'Primer nombre',
			'segundo_nombre' => 'Segundo nombre',
			'primer_apellido' => 'Primer apellido',
			'segundo_apellido' => 'Segundo apellido',
			'telefono' => 'Telefono',
			'fax' => 'Fax',
			'direccion' => 'Direccion',
			'correo' => 'Correo',
			'producto' => 'Producto',
			'representante' => 'Representante',
			'fecha_actualizacion' => 'Fecha Actualizacion',
			'idciudad' => 'Ciudad',
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

		$criteria->compare('id_persona',$this->id_persona);
		$criteria->compare('tipo_doc',$this->tipo_doc,true);
		$criteria->compare('documento',$this->documento);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('primer_nombre',$this->primer_nombre,true);
		$criteria->compare('segundo_nombre',$this->segundo_nombre,true);
		$criteria->compare('primer_apellido',$this->primer_apellido,true);
		$criteria->compare('segundo_apellido',$this->segundo_apellido,true);
		$criteria->compare('telefono',$this->telefono,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('direccion',$this->direccion,true);
		$criteria->compare('correo',$this->correo,true);
		$criteria->compare('producto',$this->producto,true);
		$criteria->compare('representante',$this->representante,true);
		$criteria->compare('fecha_actualizacion',$this->fecha_actualizacion,true);
		$criteria->compare('idciudad',$this->idciudad);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}