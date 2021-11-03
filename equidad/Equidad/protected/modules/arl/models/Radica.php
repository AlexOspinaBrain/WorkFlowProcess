<?php

/**
 * This is the model class for table "wfarl_radica".
 *
 * The followings are the available columns in table 'wfarl_radica':
 * @property integer $id_radica
 * @property string $tipo_id
 * @property string $identificacion
 * @property string $nombre
 * @property string $fecha_rad
 * @property string $status
 * @property integer $id_agencia
 * @property integer $id_producto
 *
 * The followings are the available model relations:
 * @property Tblradofi $idAgencia
 * @property ArlHistorial[] $arlHistorials
 */
class Radica extends CActiveRecord
{
	private $modelHistorialPre = null;
	private $modelHistorialPos = null;
	private $tramiteEstudio = null;

	public  $searchHistorialPend;
	public  $fechai;
	public  $fechaf;

	public $usuarioPend;
	
	public $fecha_ult;
	public $fechaiu;
	public $fechafu;	

	public $condition;
	
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
		return 'wfarl_radica';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'SWValidator'),
			array('idproceso','validaempind', 'on'=>'radicar'),
			array('idproceso, idtipologia, usuario_cod', 'numerical', 'integerOnly'=>true),
			array('afiliacion', 'length', 'max'=>12),
			array('nitintermediario, vlrcot, vlrnomina, ntrabajadores, nit, vlrcontrato, vlrmescontrato, riesgo',
			  'numerical', 'integerOnly'=>true),
			//array('identificacion','findcasodia','on'=>'radicar'),
			//array('id_producto','nonit','on'=>'radicar'),
			array('fecha_rad', 'safe'),
			array('fecha_rad', 'date', 'format'=>'yyyyMMdd', 'on'=>'search'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_radica, idproceso, idtipologia, afiliacion, fecha_rad, status, usuario_cod, usuarioPend, fechai, fechaf,fechaiu, fechafu, fecha_ult, searchHistorialPend', 'safe', on=>'search'),
			
			array('fechai, fechaf, fechaiu, fechafu', 'date', 'format'=>'yyyyMMdd', on=>'search'),

			array('id_radica, idproceso, idtipologia, afiliacion, fecha_rad, status, intermediario, nitintermediario, ejecutivov, franquicia, vlrcot, vlrnomina, ntrabajadores, representante, nit, razonsocial, vlrcontrato, vlrmescontrato, riesgo', 'safe'),
			array('idproceso, idtipologia, afiliacion, status', 'required', 'on'=>'radicar'),
			array('status', 'required', 'on'=>'estudio'),

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
			'idProceso' => array(self::BELONGS_TO, 'Proceso', 'idproceso'),
			'idTipologia' => array(self::BELONGS_TO, 'Tipologia', 'idtipologia'),
			'admUsuario' => array(self::BELONGS_TO, 'Usuario', 'usuario_cod'),
			'arlFiles' => array(self::HAS_MANY, 'File', 'id_radica'),
			'arlHistorialrad' => array(self::HAS_ONE, 'Historial', 'id_radica', 'order' => 'id_historial ASC'),
			'arlHistorials' => array(self::HAS_MANY, 'Historial', 'id_radica', 'order' => 'fecha_inicio ASC'),

			'arlHistorialcount' => array(self::STAT, 'Historial', 'id_radica'),

			'arlHistorialPend' => array(self::HAS_ONE, 'Historial', 'id_radica', 'on'=>'"arlHistorialPend".fecha_termino is null', 'order' => 'id_historial DESC'),
			'arlHistorialUlt' => array(self::HAS_ONE, 'Historial', 'id_radica', 'on'=>'"arlHistorialUlt".fecha_termino is not null', 'order' => 'id_historial DESC'),
			'arlHistorialPest' => array(self::HAS_ONE, 'Historial', 'id_radica', 
				'condition'=>"estado = 'En tramite'", 'order' => 'id_historial ASC'),


		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_radica' => 'Id Radica',
			'idproceso' => 'Proceso',
			'idtipologia' => 'Tipología',
			'afiliacion' => 'Afiliación',
			'fecha_rad' => 'Fecha Radicación',
			'status' => 'Estado',
			'usuario_cod' => 'Usuario Radica',

  			'intermediario' => 'Intermediario',
  			'nitintermediario'=> 'Id Intermediario',
  			'ejecutivov' => 'Ejecutivo Ventas',
  			'franquicia' => 'Franquicia',
  			'vlrcot'=> 'Valor Contrato',
  			'vlrnomina'=> 'Valor Nomina',
  			'ntrabajadores'=> 'Cantidad Trabajadores',
  			'representante' => 'Representante Legal',
  			'nit' => 'NIT',
  			'razonsocial' => 'Razón Social',
  			'vlrcontrato' => 'Valor Total Contrato',
  			'vlrmescontrato' => 'Valor mensual Contrato',
  			'riesgo' => 'Riesgo',
  			'fecha_ult'=> 'Fecha Ultima Tarea',

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

		$criteria->compare('t.id_radica',$this->id_radica);
		$criteria->compare('idproceso',$this->idproceso,true);
		$criteria->compare('idtipologia',$this->idtipologia,true);
		$criteria->compare('afiliacion',$this->afiliacion,true);

		
		if((isset($this->fechai) && trim($this->fechai) != "") && (isset($this->fechaf) && trim($this->fechaf) != "")){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime (  $this->fechaf )));

			$criteria->addBetweenCondition('fecha_rad', $this->fechai, $rangob);
		}else if((isset($this->fechai) && trim($this->fechai) != "") && (isset($this->fechaf) || trim($this->fechaf) == null)){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime ( $this->fechai)));
			$criteria->addBetweenCondition('fecha_rad', $this->fechai, $rangob);
		}

		if((isset($this->fechaiu) && trim($this->fechaiu) != "") && (isset($this->fechafu) && trim($this->fechafu) != "")){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime (  $this->fechafu )));

			$criteria->addBetweenCondition('"arlHistorialPend"."fecha_inicio"', $this->fechaiu, $rangob);
		}else if((isset($this->fechaiu) && trim($this->fechaiu) != "") && (isset($this->fechafu) || trim($this->fechafu) == null)){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime ( $this->fechaiu)));
			$criteria->addBetweenCondition('"arlHistorialPend"."fecha_inicio"', $this->fechaiu, $rangob);
		}		

		if($this->fecha_rad){			
			$rango = explode(" - ", $this->fecha_rad);
			if(!$rango[1])
				$rango[1] = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime ( $this->fecha_rad )));
			else
				$rango[1] = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime (  $rango[1] )));
			
			$criteria->addBetweenCondition('fecha_rad', $rango[0], $rango[1]);
		}
		
		if($this->condition)
			$criteria->addCondition($this->condition, 'AND');
		
		$criteria->compare('status',$this->status,true);



		$criteria->with=array('arlHistorialPend'=>array('with'=>array('usuarioCod'))); 
		$criteria->together=true;
		$criteria->compare('usuario_desc',$this->searchHistorialPend);

		$criteria->compare('t.usuario_cod',$this->usuarioPend);
		


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'"arlHistorialPend"."fecha_inicio"'),
			'pagination'=>array('pagesize'=>30),
		));
	}

	public function validaempind($attribute, $params){
		if($this->idproceso==1){
			if(empty($this->intermediario))
				$this->addError('intermediario',$this->getAttributeLabel('intermediario').' es obligatorio');
			if(empty($this->nitintermediario))
				$this->addError('nitintermediario',$this->getAttributeLabel('nitintermediario').' es obligatorio');
			if(empty($this->ejecutivov))
				$this->addError('ejecutivov',$this->getAttributeLabel('ejecutivov').' es obligatorio');
			if(empty($this->franquicia))
				$this->addError('franquicia',$this->getAttributeLabel('franquicia').' es obligatorio');
			if(empty($this->vlrcot))
				$this->addError('vlrcot',$this->getAttributeLabel('vlrcot').' es obligatorio');
			if(empty($this->vlrnomina))
				$this->addError('vlrnomina',$this->getAttributeLabel('vlrnomina').' es obligatorio');
			if(empty($this->ntrabajadores))
				$this->addError('ntrabajadores',$this->getAttributeLabel('ntrabajadores').' es obligatorio');
			if(empty($this->representante))
				$this->addError('representante',$this->getAttributeLabel('representante').' es obligatorio');
			if(empty($this->nit))
				$this->addError('nit',$this->getAttributeLabel('nit').' es obligatorio');
			if(empty($this->razonsocial))
				$this->addError('razonsocial',$this->getAttributeLabel('razonsocial').' es obligatorio');
		}
		if($this->idproceso==3){
			if(empty($this->vlrcontrato))
				$this->addError('vlrcontrato',$this->getAttributeLabel('vlrcontrato').' es obligatorio');
			if(empty($this->vlrmescontrato))
				$this->addError('vlrmescontrato',$this->getAttributeLabel('vlrmescontrato').' es obligatorio');
			if(empty($this->riesgo))
				$this->addError('riesgo',$this->getAttributeLabel('riesgo').' es obligatorio');

		}		
	}

	public function findcasodia($attribute, $params){
		if(!$this->identificacion)
			return;

		
		$result = Yii::app()
					->db
					->createCommand()
					->from('wfarl_radica')
					->where("identificacion=:identificacion and to_char(fecha_rad,'yyyymmdd') = to_char(now(),'yyyymmdd') and 
						status <> 'swRadica/anulado'", 
						array("identificacion"=>$this->identificacion))
					->queryRow();
		if($result)
			$this->addError($attribute, 'El caso con ID. "'.$this->$attribute.'" ya fue radicado el dia de hoy');
		
		
    }

	public function getUserEnEstudio(){	

		$users = Yii::app()->db->createCommand()
		    ->select('distinct(usu.usuario_cod), count(id_historial) as cantidad')
		    ->from('adm_usuario usu')
		    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod and jerarquia_opcion=:jerarquia_opcion', array(':jerarquia_opcion'=>"4.5.2"))
		    ->leftJoin('wfarl_historial hi', 'hi.usuario_cod=usu.usuario_cod and fecha_termino is null and estado=:estado', array(':estado'=>"En tramite"))
		    ->where("not usuario_bloqueado ")   
		    ->order('cantidad asc')		    
		    ->group('usu.usuario_cod')		    
		    ->queryAll();	  	
	  
		return $users[0]['usuario_cod'];
	}

	public function getIsEditEstudio(){
		/*return $this->tramiteEstudio == $this->id_radica ? 
			CHtml::link(
				'<i class="icon-pencil"></i>', //texto link
				array('formEstudio', 'id_radica'=>$this->id_radica), //url
				array('data-toggle'=>'tooltip', 'data-placement'=>'top', 'data-original-title'=>'Realizar estudio')//html options
			) : 
			'';		*/
		return CHtml::link(
				'<i class="icon-pencil"></i>', //texto link
				array('formEstudio', 'id_radica'=>$this->id_radica), //url
				array('data-toggle'=>'tooltip', 'data-placement'=>'top', 'data-original-title'=>'Realizar actividad')//html options
			);
	}

	public function getDiasHoras(){
		$ctatiempo = null;
		if ($this->status=='swRadica/no_vincular' || $this->status=='swRadica/cerrado'){
			$interval = date_diff(date_create($this->fecha_rad),date_create($this->arlHistorialUlt->fecha_termino));
			$ctatiempo = $interval->format('%a días %h horas %i minutos');
		}else{
			$elahora= strtotime(date('Y-m-d h:i:s:u'));

			$interval = date_diff(date_create($this->fecha_rad),date_create($elahora) );
			
			$ctatiempo = $interval->format('%a días %h horas %i minutos');
		}
		return $ctatiempo;
	}
	public function getDiasHorasUT(){
		$ctatiempo = null;
		if ($this->status!=='swRadica/no_vincular' && $this->status!=='swRadica/cerrado'){
			$elahora= strtotime(date('Y-m-d h:i:s:u'));

			$interval = date_diff(date_create($this->arlHistorialPend->fecha_inicio),date_create($elahora) );
			
			$ctatiempo = $interval->format('%a días %h horas %i minutos');
		}
		return $ctatiempo;
	}

	public function getUltimaFecha(){
		$ultimafecha = null;
		if ($this->status=='swRadica/no_vincular' || $this->status=='swRadica/cerrado' || $this->status=='swRadica/anulado')
			$ultimafecha = $this->arlHistorialUlt->fecha_termino;

		
		return $ultimafecha;
	}

	public function semaforo(){
		$statuscolor="";
		if ($this->arlHistorialPend->fecha_limite==null) {
			$statuscolor="alert-danger"; //rojo
		}else{

			$fecha1= new DateTime(date('Y-m-d'));
			$fecha2= new DateTime($this->arlHistorialPend->fecha_limite);

			$nueva = $fecha1->diff($fecha2);
			$masomenos = trim($nueva->format('%R'));
			$diasp = trim($nueva->format('%a'));

			if ($masomenos == '-'){
				$statuscolor="alert-danger"; //rojo
			}else{
				switch ($diasp) {
					case '0':
					case '1':
					case '2':
					case '3':
						$statuscolor="alert alert-warning"; // amarillo
						break;
					
					default:
						$statuscolor="alert-success"; // verde
						break;
				}

			}
		}

		return $statuscolor;
		
	}

	public function behaviors()
	{
	    return array(
        	'swBehavior'=>array(
	            'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
	            'workflowSourceComponent' => 'swArl'
        	),
    	);
	}


	public function beforeValidate() {
		if($this->isNewRecord)
			$this->fecha_rad = new CDbExpression('NOW()');


		return parent::beforeSave();
		
	}

	public function afterSave() {
		
		return parent::afterSave();		

		if(isset($this->modelHistorialPre)){
			if($this->modelHistorialPre->id_radica === null)
				$this->modelHistorialPre->id_radica=$this->id_radica;

			$this->modelHistorialPre->save();
		}

		if(isset($this->modelHistorialPos)){
			if($this->modelHistorialPos->id_radica === null)
				$this->modelHistorialPos->id_radica=$this->id_radica;

			$this->modelHistorialPos->save();			
		}
		
		
	}



	
	public function beforeTransition($event)
	{			


		$this->modelHistorialPre=Historial::model()->findByAttributes(array('id_radica'=>$this->id_radica, 'estado'=>$this->swGetStatus()->getLabel(),'fecha_termino'=>null));

		if(isset($this->modelHistorialPre)){
			$this->modelHistorialPre->fecha_termino = new CDbExpression('NOW()');
			$this->modelHistorialPre->usuario_cod = Yii::app()->user->id;
		}else{
			$this->modelHistorialPre=new Historial;
			$this->modelHistorialPre->id_radica = $this->id_radica;
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
		$this->modelHistorialPos->id_radica = $this->id_radica;
		$this->modelHistorialPos->fecha_inicio = new CDbExpression('NOW()');
		$this->modelHistorialPos->usuario_cod = $user;
		$this->modelHistorialPos->estado = $this->swGetStatus()->getLabel();
		
		if($this->modelHistorialPos->estado == 'Cerrado')
			$this->modelHistorialPos->fecha_termino = new CDbExpression('NOW()');

		if($this->modelHistorialPos->estado == 'Devuelto')
			$this->modelHistorialPos->usuario_cod = $this->tramiteHistorialUlt->usuario_cod ;

		if($this->modelHistorialPos->estado == 'En tramite')
			$this->modelHistorialPos->usuario_cod = $modelRadica->getUserEnEstudio();	

			
	}



}