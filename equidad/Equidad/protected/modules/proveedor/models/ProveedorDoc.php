<?php

/**
 * This is the model class for table "proveedor_doc".
 *
 * The followings are the available columns in table 'proveedor_doc':
 * @property integer $id_documento
 * @property string $desc_documento
 *
 * The followings are the available model relations:
 * @property ProveeRelDoc[] $proveeRelDocs
 */
class ProveedorDoc extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProveedorDoc the static model class
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
		return 'proveedor_doc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('desc_documento', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_documento, desc_documento', 'safe', 'on'=>'search'),
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
			'proveeRelDocs' => array(self::HAS_MANY, 'ProveeRelDoc', 'id_documento'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_documento' => 'Id Documento',
			'desc_documento' => 'Desc Documento',
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

		$criteria->compare('id_documento',$this->id_documento);
		$criteria->compare('desc_documento',$this->desc_documento,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}