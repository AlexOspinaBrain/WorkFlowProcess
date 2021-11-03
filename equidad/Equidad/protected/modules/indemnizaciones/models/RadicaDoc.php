<?php

/**
 * This is the model class for table "idm_radica_doc".
 *
 * The followings are the available columns in table 'idm_radica_doc':
 * @property integer $id_radica_doc
 * @property string $ruta
 * @property integer $paginas
 * @property integer $id_radica
 * @property integer $id_clasificacion
 *
 * The followings are the available model relations:
 * @property IdmRadica $idRadica
 * @property IdmClasifiDoc $idClasificacion
 */
class RadicaDoc extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RadicaDoc the static model class
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
		return 'idm_radica_doc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('paginas, id_radica, id_clasificacion, ruta', 'required'),
			
			array('paginas, id_radica, id_clasificacion', 'numerical', 'integerOnly'=>true),
			//array('ruta', 'length', 'max'=>256),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_radica_doc, ruta, paginas, id_radica, id_clasificacion', 'safe', 'on'=>'search'),
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
			'idRadica' => array(self::BELONGS_TO, 'IdmRadica', 'id_radica'),
			'idClasificacion' => array(self::BELONGS_TO, 'ClasificaDoc', 'id_clasificacion'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_radica_doc' => 'Id Radica Doc',
			'ruta' => 'Archivo',
			'paginas' => 'Paginas',
			'id_radica' => 'Id Radica',
			'id_clasificacion' => 'Id Clasificacion',
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

		$criteria->compare('id_radica_doc',$this->id_radica_doc);
		$criteria->compare('ruta',$this->ruta,true);
		$criteria->compare('paginas',$this->paginas);
		$criteria->compare('id_radica',$this->id_radica);
		$criteria->compare('id_clasificacion',$this->id_clasificacion);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}