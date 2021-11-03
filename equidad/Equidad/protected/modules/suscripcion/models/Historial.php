<?php

/**
 * This is the model class for table "sus_historial".
 *
 * The followings are the available columns in table 'sus_historial':
 * @property integer $id_historial
 * @property string $fecha_inicio
 * @property string $fecha_termino
 * @property string $estado
 * @property string $code
 * @property integer $usuario_cod
 *
 * The followings are the available model relations:
 * @property SusRadica $radica_rel
 * @property AdmUsuario $usuarioCod
 */
class Historial extends CActiveRecord
{
	public $causal;
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
		return 'sus_historial';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usuario_cod', 'required'),
			array('causal', 'required', 'on'=>'devolucion'),
			array('usuario_cod', 'numerical', 'integerOnly'=>true),
			array('estado', 'length', 'max'=>50),
			array('code', 'length', 'max'=>13),
			array('fecha_termino', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_historial, fecha_inicio, fecha_termino, estado, code, usuario_cod', 'safe', 'on'=>'search'),
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
			'radica_rel' => array(self::BELONGS_TO, 'Radica', 'code'),
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
			'code' => 'Radicado',
			'usuario_cod' => 'Usuario',
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
		$criteria->order = 'code DESC, fecha_inicio asc';
		$criteria->compare('id_historial',$this->id_historial);
		$criteria->compare('fecha_inicio',$this->fecha_inicio,true);
		$criteria->compare('fecha_termino',$this->fecha_termino,true);
		$criteria->compare('estado',$this->estado,true);
		$criteria->compare('code',$this->code, true);
		$criteria->compare('usuario_cod',$this->usuario_cod);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>false ,
			'sort'=>array(
				'defaultOrder'=>array(
      				'id_historial'=>false,
      				'fecha_inicio'=>false
    			)
    		)
		));
	}
}