<?php

/**
 * This is the model class for table "sus_radica_obs".
 *
 * The followings are the available columns in table 'sus_radica_obs':
 * @property integer $id_observacion
 * @property string $observacion
 * @property string $fecha
 * @property integer $usuario_cod
 * @property string $code
 *
 * The followings are the available model relations:
 * @property AdmUsuario $usuarioCod
 * @property SusRadica $code0
 */
class RadicaObs extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RadicaObs the static model class
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
		return 'sus_radica_obs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code', 'required'),
			array('usuario_cod', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_observacion, observacion, fecha, usuario_cod, code', 'safe'),
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
			'usuarioCod' => array(self::BELONGS_TO, 'Usuario', 'usuario_cod'),
			'code0' => array(self::BELONGS_TO, 'SusRadica', 'code'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_observacion' => 'Id Observación',
			'observacion' => 'Observación',
			'fecha' => 'Fecha',
			'usuario_cod' => 'Usuario Cod',
			'code' => 'Code',
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

		$criteria->compare('id_observacion',$this->id_observacion);
		$criteria->compare('observacion',$this->observacion,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('usuario_cod',$this->usuario_cod);
		$criteria->compare('code',$this->code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}