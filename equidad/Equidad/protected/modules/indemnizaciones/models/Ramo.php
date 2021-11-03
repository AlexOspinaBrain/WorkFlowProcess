<?php

/**
 * This is the model class for table "equi_ramo".
 *
 * The followings are the available columns in table 'equi_ramo':
 * @property integer $id_ramo
 * @property string $desc_ramo
 * @property boolean $active
 *
 * The followings are the available model relations:
 * @property IdmClasifiDoc[] $idmClasifiDocs
 * @property SusProducto[] $susProductos
 */
class Ramo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Ramo the static model class
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
		return 'equi_ramo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_ramo', 'required'),
			array('desc_ramo', 'length', 'max'=>50),
			array('active', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_ramo, desc_ramo, active', 'safe', 'on'=>'search'),
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
			'idmClasifiDocs' => array(self::HAS_MANY, 'IdmClasifiDoc', 'id_ramo'),
			'Productos' => array(self::MANY_MANY, 'Producto', 'equi_ramo_prod(id_ramo, id_producto)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_ramo' => 'Id Ramo',
			'desc_ramo' => 'Desc Ramo',
			'active' => 'Active',
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

		$criteria->compare('id_ramo',$this->id_ramo);
		$criteria->compare('desc_ramo',$this->desc_ramo,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}