<?php

/**
 * This is the model class for table "sus_producto".
 *
 * The followings are the available columns in table 'sus_producto':
 * @property integer $id_producto
 * @property string $producto
 * @property string $codigo_osiris
 * @property boolean $active
 *
 * The followings are the available model relations:
 * @property IdmClasifiDoc[] $idmClasifiDocs
 * @property EquiRamo[] $equiRamos
 * @property IdmRadica[] $idmRadicas
 * @property SusRadica[] $susRadicas
 */
class Producto extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Producto the static model class
	 */
	public $id_ramo;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sus_producto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('producto, codigo_osiris, active, id_ramo', 'required'),
			array('producto', 'length', 'max'=>128),
			array('codigo_osiris', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_producto, producto, codigo_osiris, active, id_ramo', 'safe', 'on'=>'search'),
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
			'idmClasifiDocs' => array(self::HAS_MANY, 'IdmClasifiDoc', 'id_producto'),
			'equiRamos' => array(self::MANY_MANY, 'EquiRamo', 'equi_ramo_prod(id_producto, id_ramo)'),
			'Ramo' => array(self::MANY_MANY, 'EquiRamo', 'equi_ramo_prod(id_producto, id_ramo)'),
			'idmRadicas' => array(self::HAS_MANY, 'IdmRadica', 'id_producto'),
			'susRadicas' => array(self::HAS_MANY, 'SusRadica', 'id_producto'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_producto' => 'Id Producto',
			'producto' => 'Producto',
			'codigo_osiris' => 'Codigo Osiris',
			'active' => 'Active',
			'id_ramo' => 'Ramo',
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

		$criteria->compare('id_producto',$this->id_producto);
		$criteria->compare('producto',$this->producto,true);
		$criteria->compare('codigo_osiris',$this->codigo_osiris,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	function getProductoCod()
	{
		return $this->codigo_osiris.' - ('.$this->producto.')';
	}
}