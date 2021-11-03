<?php

/**
 * This is the model class for table "proveedor".
 *
 * The followings are the available columns in table 'proveedor':
 * @property integer $id_proveedor
 * @property boolean $estado
 * @property string $acta
 * @property string $fecha_aprobacion
 * @property string $tipo
 * @property string $doc_cobro
 * @property integer $area_id
 * @property integer $id_persona
 *
 * The followings are the available model relations:
 * @property Persona $idPersona
 * @property Tblareascorrespondencia $area
 * @property ProveeRelDoc[] $proveeRelDocs
 */
class Proveedor extends CActiveRecord
{
	public $documento;
	public $nombre;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Proveedor the static model class
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
		return 'proveedor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_persona, doc_cobro, area_id, tipo', 'required'),
			array('area_id, id_persona', 'numerical', 'integerOnly'=>true),
			array('fecha_aprobacion', 'date'),
			array('fecha_aprobacion', 'default', 'setOnEmpty' => true, 'value' => null),
			array('acta', 'length', 'max'=>5),
			array('tipo, doc_cobro', 'length', 'max'=>20),
			array('estado', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('documento, nombre, id_proveedor, estado, acta, fecha_aprobacion, tipo, doc_cobro, area_id, id_persona', 'safe', 'on'=>'search'),
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
			'idPersona' => array(self::BELONGS_TO, 'Persona', 'id_persona'),
			'area' => array(self::BELONGS_TO, 'Area', 'area_id'),
			'proveedorDocs' => array(self::MANY_MANY, 'ProveedorDoc', 'provee_rel_doc(id_proveedor, id_documento)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_proveedor' => 'Id Proveedor',
			'estado' => 'Proveedor activo',
			'acta' => 'Acta',
			'fecha_aprobacion' => 'Fecha Aprobacion',
			'tipo' => 'Tipo',
			'doc_cobro' => 'Doc Cobro',
			'area_id' => 'Area',
			'id_persona' => 'Id Persona',
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

		$criteria->compare('id_proveedor',$this->id_proveedor);
		$criteria->compare('estado',$this->estado);
		$criteria->compare('acta',$this->acta,true);
		$criteria->compare('fecha_aprobacion',$this->fecha_aprobacion,true);
		$criteria->compare('tipo',$this->tipo,true);
		$criteria->compare('doc_cobro',$this->doc_cobro,true);
		$criteria->compare('area_id',$this->area_id);
		$criteria->compare('id_persona',$this->id_persona);
		$criteria->with = array( 'idPersona' );
		$criteria->compare('upper("idPersona".nombre)',strtoupper ($this->nombre),true);
		$criteria->compare('"idPersona".documento',$this->documento);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getDocumentos(){
		$salida=null;
		foreach ($this->proveedorDocs as $doc) {
			$salida.= "<li>".$doc->desc_documento."</li>";
		}

		if($salida)
			$salida = "<ul>".$salida."</ul>";

		return $salida;
	}

	public function behaviors(){
        return array('ESaveRelatedBehavior' => array(
         	'class' => 'application.components.ESaveRelatedBehavior')
 	    );
	}
}