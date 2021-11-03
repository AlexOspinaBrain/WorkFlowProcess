<?php

/**
 * This is the model class for table "sar_historial".
 *
 * The followings are the available columns in table 'sar_historial':
 * @property integer $id_historial
 * @property string $fecha_inicio
 * @property string $fecha_termino
 * @property string $estado
 * @property string $observacion
 * @property integer $id_radica
 * @property integer $usuario_cod
 *
 * The followings are the available model relations:
 * @property SarRadica $idRadica
 * @property AdmUsuario $usuarioCod
 */
class Historial extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Historial the static model class
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
		return 'sar_historial';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fecha_inicio, estado, id_radica, usuario_cod', 'required'),
			array('id_radica, usuario_cod', 'numerical', 'integerOnly'=>true),
			array('estado', 'length', 'max'=>50),
			array('fecha_termino, observacion', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_historial, fecha_inicio, fecha_termino, estado, observacion, id_radica, usuario_cod', 'safe', 'on'=>'search'),
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
			'idRadica' => array(self::BELONGS_TO, 'Radica', 'id_radica'),
			'usuarioCod' => array(self::BELONGS_TO, 'Usuario', 'usuario_cod'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_historial' => 'Id Historial',
			'fecha_inicio' => 'Fecha Inicio',
			'fecha_termino' => 'Fecha Termino',
			'estado' => 'Estado',
			'observacion' => 'Observacion',
			'id_radica' => 'Id Radica',
			'usuario_cod' => 'Usuario Cod',
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

		$criteria->compare('id_historial',$this->id_historial);
		$criteria->compare('fecha_inicio',$this->fecha_inicio,true);
		$criteria->compare('fecha_termino',$this->fecha_termino,true);
		$criteria->compare('estado',$this->estado,true);
		$criteria->compare('observacion',$this->observacion,true);
		$criteria->compare('id_radica',$this->id_radica);
		$criteria->compare('usuario_cod',$this->usuario_cod);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'fecha_inicio'),
		));
	}
}