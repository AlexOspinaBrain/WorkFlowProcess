<?php

/**
 * This is the model class for table "sus_radica".
 *
 * The followings are the available columns in table 'sus_radica':
 * @property string $code
 * @property string $fecha_rad
 * @property integer $id_persona
 * @property integer $id_certificado
 * @property string $prioridad
 * @property string $fecha_cierre
 * @property integer $id_agencia
 * @property string $status
 * @property string $poliza
 * @property integer $id_producto
 * @property integer $cant_doc
 * @property integer $id_canal
 * @property boolean $sarlaft
 * @property integer $id_sarlaft
 * @property string $fecha_garantia
 * @property integer $id_garantia
 * @property string $usuario_osiris
 * @property integer $cant_ordenes
 * @property string $fecha_expe
 * @property string $certificado
 *
 * The followings are the available model relations:
 * @property SusHistorial[] $susHistorials
  * @property SusAdjuntos[] $susAdjuntoses
 * @property SusRadicaObs[] $susRadicaObs
 * @property Persona $idPersona
 * @property SusTipocertificado $idCertificado
 * @property Tblradofi $idAgencia
 * @property SusProducto $idProducto
 * @property SusCanal $idCanal
 * @property SusSarlaft $idSarlaft
 * @property SusGarantias $idGarantia 
 * @property SusRadicaObs[] $susRadicaObs
 */
class Radica extends SWActiveRecord
{
	private $modelHistorialPre = null;
	private $modelHistorialPos = null;
	public $searchHistorialPend;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Radica the static model class
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
		return 'sus_radica';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, fecha_rad, id_certificado, id_persona, prioridad, id_agencia, status, fecha_cierre', 'required', 'on'=>'radica'),
			array('id_producto, id_canal, cant_doc, id_certificado', 'required',  'on'=>'tramitar'),
			array('poliza, id_sarlaft, fecha_expe, cant_ordenes, usuario_osiris, certificado', 'required',  'on'=>'expedicion'),
			array('fecha_expe', 'date','format'=>'yyyy/mm/dd', 'on'=>'expedicion'),
			//array('id_ramo, id_canal', 'safe',  'on'=>'radicar'),
			array('status', 'SWValidator'),
			array('code', 'unique'),			
			//array('fecha_cierre', 'date','format'=>'yyyy/mm/dd hh:mm:ss', 'on'=>'radica'),
			array('fecha_garantia', 'date','format'=>'yyyy/mm/dd', 'on'=>'tramitar'),
			array('id_persona, id_certificado', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>13),
			array('prioridad', 'length', 'max'=>10),
			array('poliza, certificado', 'length', 'max'=>20),
			array('id_agencia', 'length', 'max'=>3),
			array('fecha_cierre', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('poliza, code, fecha_rad, id_persona, id_certificado, prioridad, fecha_cierre, id_agencia, status, id_producto, cant_doc, sarlaft, id_sarlaft, fecha_garantia, id_garantia, usuario_osiris, fecha_expe, cant_ordenes', 'safe'),
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
			'susHistorials' => array(self::HAS_MANY, 'Historial', 'code', 'order'=>'fecha_termino ASC'),
			'susHistorialPend' => array(self::HAS_ONE, 'Historial', 'code', 'condition'=>'"susHistorialPend".fecha_termino is null'),
			//'susHistorialPend' => array(self::HAS_ONE, 'Historial', 'code', 'condition'=>'id_historial=(select max(id_historial) from sus_historial where code=t.code)'),
			'susRadicaObs' => array(self::HAS_MANY, 'RadicaObs', 'code', 'order'=>'fecha ASC'),
			'susAdjuntos' => array(self::HAS_MANY, 'Adjuntos', 'code'),
			'idPersona' => array(self::BELONGS_TO, 'Persona', 'id_persona'),
			'idCertificado' => array(self::BELONGS_TO, 'Tipocertificado', 'id_certificado'),
			'rel_agencia' => array(self::BELONGS_TO, 'Agencia', 'id_agencia'),
			'idProducto' => array(self::BELONGS_TO, 'Producto', 'id_producto'),
			'idCanal' => array(self::BELONGS_TO, 'Canal', 'id_canal'),
			'idSarlaft' => array(self::BELONGS_TO, 'Sarlaft', 'id_sarlaft'),
			'idGarantia' => array(self::BELONGS_TO, 'Garantias', 'id_garantia'),
			'idAgencia' => array(self::BELONGS_TO, 'Agencia', 'id_agencia'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'code' => 'Radicado',
			'fecha_rad' => 'Fecha radicación',
			'id_persona' => 'Cliente',
			'id_certificado' => 'Tipo de certificado',
			'prioridad' => 'Prioridad',
			'fecha_cierre' => 'Fecha limite',
			'id_agencia' => 'Agencia',
			'status' => 'Proximo paso',
			'poliza' => 'Numero de Poliza',
			'id_producto' => 'Producto',
			'cant_doc' => 'No. de documentos',
			'id_canal' => 'Canal',
			'sarlaft' => 'Sarlaft',
			'id_sarlaft' => 'Estado Sarlaft',
			'fecha_garantia' => 'Fecha limite garantia',
			'id_garantia' => 'Garantias',
			'usuario_osiris' => 'Usuario expidió',
			'cant_ordenes' => 'Cant Ordenes',
			'fecha_expe' => 'Fecha Expedición',
			'certificado' => 'Certificado',
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
		$fechasRadicacion = explode("-", $this->fecha_rad);
		if(sizeof($fechasRadicacion)>1)
			$criteria->addBetweenCondition('fecha_rad', $fechasRadicacion[0], $fechasRadicacion[1], 'AND');
		else
			$criteria->compare('CAST(fecha_rad AS DATE)',$this->fecha_rad);
			
		$fechasCierre = explode("-", $this->fecha_cierre);
		if(sizeof($fechasCierre)>1)
			$criteria->addBetweenCondition('fecha_cierre', $fechasCierre[0], $fechasCierre[1], 'AND');
		else
			$criteria->compare('CAST(fecha_cierre AS DATE)',$this->fecha_cierre);
			
		$fechasGarantia = explode("-", $this->fecha_garantia);
		if(sizeof($fechasGarantia)>1)
			$criteria->addBetweenCondition('fecha_garantia', $fechasGarantia[0], $fechasGarantia[1], 'AND');
		else
			$criteria->compare('CAST(fecha_garantia AS DATE)',$this->fecha_garantia);

		$criteria->order = 'fecha_rad DESC';
		$criteria->compare('t.code',$this->code,true);
		$criteria->compare('id_certificado',$this->id_certificado);
		$criteria->compare('prioridad',$this->prioridad,true);
		//$criteria->compare('fecha_cierre',$this->fecha_cierre,true);
		$criteria->compare('id_agencia',$this->id_agencia);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('poliza',$this->poliza,true);
		$criteria->compare('id_producto',$this->id_producto);
		$criteria->compare('cant_doc',$this->cant_doc);
		$criteria->compare('id_canal',$this->id_canal);
		$criteria->compare('sarlaft',$this->sarlaft);
		$criteria->compare('id_sarlaft',$this->id_sarlaft);
		//$criteria->compare('fecha_garantia',$this->fecha_garantia,true);
		$criteria->compare('id_garantia',$this->id_garantia);
		$criteria->compare('usuario_osiris',$this->usuario_osiris,true);
		$criteria->compare('cant_ordenes',$this->cant_ordenes);
		$criteria->compare('fecha_expe',$this->fecha_expe,true);
		
		$criteria->with=array( 'idPersona'); 
		$criteria->addSearchCondition('"idPersona"."documento" || "idPersona"."nombre"',$this->id_persona, true, 'AND', 'ILIKE');
		if(!empty($this->searchHistorialPend)){
			$criteria->with[] = 'susHistorialPend';
			$criteria->compare('"susHistorialPend"."usuario_cod"',$this->searchHistorialPend);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchHistorials()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$fechasRadicacion = explode("-", $this->fecha_rad);
		if(sizeof($fechasRadicacion)>1)
			$criteria->addBetweenCondition('fecha_rad', $fechasRadicacion[0], $fechasRadicacion[1], 'AND');
		else
			$criteria->compare('CAST(fecha_rad AS DATE)',$this->fecha_rad);
			
		$fechasCierre = explode("-", $this->fecha_cierre);
		if(sizeof($fechasCierre)>1)
			$criteria->addBetweenCondition('fecha_cierre', $fechasCierre[0], $fechasCierre[1], 'AND');
		else
			$criteria->compare('CAST(fecha_cierre AS DATE)',$this->fecha_cierre);
			
		$fechasGarantia = explode("-", $this->fecha_garantia);
		if(sizeof($fechasGarantia)>1)
			$criteria->addBetweenCondition('fecha_garantia', $fechasGarantia[0], $fechasGarantia[1], 'AND');
		else
			$criteria->compare('CAST(fecha_garantia AS DATE)',$this->fecha_garantia);

		$criteria->order = 'fecha_rad DESC';
		$criteria->compare('t.code',$this->code,true);
		//$criteria->compare('CAST(fecha_rad AS DATE)',$this->fecha_rad);
		$criteria->compare('id_certificado',$this->id_certificado);
		$criteria->compare('prioridad',$this->prioridad,true);
		//$criteria->compare('fecha_cierre',$this->fecha_cierre,true);
		$criteria->compare('id_agencia',$this->id_agencia,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('poliza',$this->poliza,true);
		$criteria->compare('id_producto',$this->id_producto);
		$criteria->compare('cant_doc',$this->cant_doc);
		$criteria->compare('id_canal',$this->id_canal);
		$criteria->compare('sarlaft',$this->sarlaft);
		$criteria->compare('id_sarlaft',$this->id_sarlaft);
		//$criteria->compare('fecha_garantia',$this->fecha_garantia,true);
		$criteria->compare('id_garantia',$this->id_garantia);
		$criteria->compare('usuario_osiris',$this->usuario_osiris,true);
		$criteria->compare('cant_ordenes',$this->cant_ordenes);
		$criteria->compare('fecha_expe',$this->fecha_expe,true);
		
		$criteria->with=array('susHistorialPend', 'idPersona'); 
		$criteria->compare('"susHistorialPend"."usuario_cod"',$this->searchHistorialPend);
		$criteria->addSearchCondition('"idPersona"."documento" || "idPersona"."nombre"',$this->id_persona, true, 'AND', 'ILIKE');
			
		
		$codesRadicados = array();
		foreach($this->findAll($criteria) as $data)
    		$codesRadicados[] = $data->code;

    	$modelHistorial=new Historial('search');
		$modelHistorial->code = $codesRadicados;
	

    	return $modelHistorial->search();
	}

	public function getTiempoTramite()
    {
    	if($this->swGetStatus()->getLabel() == 'Cerrado')  {
    		$datetime1 = new DateTime($this->fecha_rad);
			$datetime2 = new DateTime($this->susHistorialPend->fecha_termino);
			$interval = $datetime1->diff($datetime2);
			return $interval->format('%a días');
    	}  		
    		
    }

	public function behaviors()
	{
	    return array(
        	'swBehavior'=>array(
	            'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
	            'workflowSourceComponent' => 'swSuscripcion'
        	),
    	);
	}

	public function beforeValidate() {		
		if($this->isNewRecord){
			$this->fecha_rad = new CDbExpression('NOW()');
			
			if($this->prioridad === 'Normal')
				$this->fecha_cierre =  date("Y-m-d H:i:s", strtotime(" +10 days"));

			$this->code = $this->getConsecutivoSuscripcion();	
		}	

		if($this->sarlaft === false)
			$this->id_sarlaft = 0;

		if(empty($this->fecha_expe))$this->fecha_expe=null;
		if(empty($this->fecha_garantia))$this->fecha_garantia=null;

		$this->poliza = strtoupper($this->poliza);
		return parent::beforeSave();
	}

	public function afterSave(){
		parent::afterSave();

		$this->modelHistorialPre->save();
		$this->modelHistorialPos->save();

		if(!empty($_REQUEST['RadicaObs']['observacion']) || !empty($_REQUEST['Historial']['causal'])){

			$actividad = $this->modelHistorialPre->estado;
			if(isset($actividad))
				$actividad = "( Actividad : " . $actividad . " ) <br>";

			if(!empty($_REQUEST['Historial']['causal']))
				$actividad .= "Causal de devolución: ".$_REQUEST['Historial']['causal']."<br>";

			$modelObservacion=new RadicaObs;
			$modelObservacion->fecha = new CDbExpression('NOW()');
			$modelObservacion->code = $this->code;
			$modelObservacion->usuario_cod = Yii::app()->user->id;
			$modelObservacion->observacion = $actividad.$_REQUEST['RadicaObs']['observacion'];
			$modelObservacion->save();
		}		
	} 

	public function getConsecutivoSuscripcion(){
		$fecha = substr(date('Y'), -2).date('md');

		$criteria=new CDbCriteria;
		$criteria->compare('code', "SUS"."$fecha", true);
		$criteria->order = 'fecha_rad DESC';

		$model = Radica::model()->find($criteria);

		if(isset($model)){
			$consecutivo = $model->code;
			$consecutivo = substr($consecutivo, -4);
			$consecutivo = intval($consecutivo) + 1;
			if($consecutivo > 9999)
				return;
			$consecutivo = str_pad($consecutivo, 4, "0", STR_PAD_LEFT);	
			return "SUS$fecha".$consecutivo;
		}else
			return "SUS$fecha"."0001";		
	}

	public function beforeTransition($event)
	{			
		$this->modelHistorialPre=Historial::model()->findByAttributes(array('code'=>$this->code, 'estado'=>$this->swGetStatus()->getLabel(),'fecha_termino'=>null));

		if(isset($this->modelHistorialPre)){
			$this->modelHistorialPre->fecha_termino = new CDbExpression('NOW()');
			$this->modelHistorialPre->usuario_cod = Yii::app()->user->id;
		}else{
			$this->modelHistorialPre=new Historial;
			$this->modelHistorialPre->code = $this->code;
			$this->modelHistorialPre->fecha_termino = new CDbExpression('NOW()');
			$this->modelHistorialPre->usuario_cod = Yii::app()->user->id;
			$this->modelHistorialPre->estado = $this->swGetStatus()->getLabel();
			$this->modelHistorialPre->fecha_inicio = new CDbExpression('NOW()');
		}	
	}

	public function afterTransition($event)
	{		
		if(!empty($_REQUEST['Historial']['usuario_cod']))
			$user = $_REQUEST['Historial']['usuario_cod'];			
		else
			$user = Yii::app()->user->id;
			
		$this->modelHistorialPos=new Historial;
		$this->modelHistorialPos->code = $this->code;
		$this->modelHistorialPos->fecha_inicio = new CDbExpression('NOW()');
		$this->modelHistorialPos->usuario_cod = $user;
		$this->modelHistorialPos->estado = $this->swGetStatus()->getLabel();
		
		if($this->modelHistorialPos->estado == 'Cerrado' || $this->modelHistorialPos->estado == 'Anulado')
			$this->modelHistorialPos->fecha_termino = new CDbExpression('NOW()');


			
	}
}