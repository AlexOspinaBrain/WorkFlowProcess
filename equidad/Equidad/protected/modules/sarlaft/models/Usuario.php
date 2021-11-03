<?php

/**
 * This is the model class for table "adm_usuario".
 *
 * The followings are the available columns in table 'adm_usuario':
 * @property integer $usuario_cod
 * @property string $usuario_desc
 * @property string $usuario_contrasena
 * @property string $usuario_nombres
 * @property string $usuario_priape
 * @property string $usuario_segape
 * @property string $usuario_correo
 * @property integer $usuario_numentradas
 * @property boolean $usuario_bloqueado
 * @property integer $area
 * @property string $tipodoc
 * @property string $numerodoc
 * @property string $usuario_ultfecha
 *
 * The followings are the available model relations:
 * @property SusHistorial[] $susHistorials
 * @property AdmMenu[] $admMenus
 * @property FacComprobantepago[] $facComprobantepagos
 * @property FacOrdengiro[] $facOrdengiros
 * @property FacRadica[] $facRadicas
 * @property SusRadicaObs[] $susRadicaObs
 * @property WfComentario[] $wfComentarios
 * @property WfHistorial[] $wfHistorials
 * @property Tblareascorrespondencia $area0
 * @property WfWorkflow[] $wfWorkflows
 */
class Usuario extends CActiveRecord
{
	public $jerarq;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Usuario the static model class
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
		return 'adm_usuario';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usuario_desc', 'required'),
			array('usuario_numentradas, area', 'numerical', 'integerOnly'=>true),
			array('usuario_desc, usuario_contrasena', 'length', 'max'=>20),
			array('usuario_nombres, usuario_priape, usuario_segape', 'length', 'max'=>30),
			array('usuario_correo', 'length', 'max'=>80),
			array('tipodoc', 'length', 'max'=>8),
			array('numerodoc', 'length', 'max'=>40),
			array('usuario_bloqueado, usuario_ultfecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('usuario_cod, usuario_desc, usuario_contrasena, usuario_nombres, usuario_priape, usuario_segape, usuario_correo, usuario_numentradas, usuario_bloqueado, area, tipodoc, numerodoc, usuario_ultfecha, jerarq', 'safe', 'on'=>'search'),
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
			'sarHistorials' => array(self::HAS_MANY, 'Historial', 'usuario_cod'),
			
			'admMenus' => array(self::MANY_MANY, 'AdmMenu', 'Usumenu(usuario_cod, jerarquia_opcion)'),
			'facComprobantepagos' => array(self::HAS_MANY, 'FacComprobantepago', 'usuario_cod'),
			'facOrdengiros' => array(self::HAS_MANY, 'FacOrdengiro', 'usuario_cod'),
			'facRadicas' => array(self::HAS_MANY, 'FacRadica', 'usuario_cod'),
			'susRadicaObs' => array(self::HAS_MANY, 'SusRadicaObs', 'usuario_cod'),
			'wfComentarios' => array(self::HAS_MANY, 'WfComentario', 'usuario_cod'),
			'wfHistorials' => array(self::HAS_MANY, 'WfHistorial', 'usuario_cod'),
			'rel_area' => array(self::BELONGS_TO, 'Area', 'area'),
			'wfWorkflows' => array(self::MANY_MANY, 'WfWorkflow', 'wf_workflowusuarios(usuario_cod, id_workflow)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'usuario_cod' => 'Usuario Cod',
			'usuario_desc' => 'Usuario Desc',
			'usuario_contrasena' => 'Usuario Contrasena',
			'usuario_nombres' => 'Usuario Nombres',
			'usuario_priape' => 'Usuario Priape',
			'usuario_segape' => 'Usuario Segape',
			'usuario_correo' => 'Usuario Correo',
			'usuario_numentradas' => 'Usuario Numentradas',
			'usuario_bloqueado' => 'Usuario Bloqueado',
			'area' => 'Area',
			'tipodoc' => 'Tipodoc',
			'numerodoc' => 'Numerodoc',
			'usuario_ultfecha' => 'Usuario Ultfecha',
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

		$criteria->compare('usuario_cod',$this->usuario_cod);
		$criteria->compare('usuario_desc',$this->usuario_desc,true);
		$criteria->compare('usuario_contrasena',$this->usuario_contrasena,true);
		$criteria->compare('usuario_nombres',$this->usuario_nombres,true);
		$criteria->compare('usuario_priape',$this->usuario_priape,true);
		$criteria->compare('usuario_segape',$this->usuario_segape,true);
		$criteria->compare('usuario_correo',$this->usuario_correo,true);
		$criteria->compare('usuario_numentradas',$this->usuario_numentradas);
		$criteria->compare('usuario_bloqueado',$this->usuario_bloqueado);
		$criteria->compare('area',$this->area);
		$criteria->compare('tipodoc',$this->tipodoc,true);
		$criteria->compare('numerodoc',$this->numerodoc,true);
		$criteria->compare('usuario_ultfecha',$this->usuario_ultfecha,true);

		$criteria->with=array('admMenus'); 
		$criteria->compare('"admMenus"."jerarquia_opcion"',$this->jerarq);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getNombreCompleto()
    {
     	return $this->usuario_nombres. " " 
     		. $this->usuario_priape . " " 
     		. $this->usuario_segape ;
    }
}