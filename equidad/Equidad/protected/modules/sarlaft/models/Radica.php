<?php

/**
 * This is the model class for table "sar_radica".
 *
 * The followings are the available columns in table 'sar_radica':
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
 * @property SarHistorial[] $sarHistorials
 */
class Radica extends CActiveRecord
{
	private $modelHistorialPre = null;
	private $modelHistorialPos = null;
	private $tramiteEstudio = null;

	public $searchHistorialPend;
	public  $fechai;
	public  $fechaf;

	public $usuarioPend;

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
		return 'sar_radica';
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
			
			array('id_agencia, id_producto', 'numerical', 'integerOnly'=>true),
			array('tipo_id', 'length', 'max'=>2),
			array('identificacion, nombre', 'length', 'max'=>100),
			array('identificacion','findcasodia','on'=>'radicar'),
			array('identificacion', 'numerical'),
			array('id_producto','nonit','on'=>'radicar'),
			array('fecha_rad', 'safe'),
			array('fecha_rad', 'date', 'format'=>'yyyyMMdd', 'on'=>'search'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_radica, tipo_id, identificacion, nombre, fecha_rad, status, id_agencia, id_producto, usu, usuarioPend, fechai, fechaf', 'safe', on=>'search'),
			
			array('fechai, fechaf, fecha_formulario', 'date', 'format'=>'yyyyMMdd', on=>'search'),

			array('id_radica, tipo_id, identificacion, nombre, fecha_rad, status, id_agencia, id_producto, fecha_formulario', 'safe'),
			array('tipo_id, identificacion, nombre, status, id_producto', 'required', 'on'=>'radicar'),
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
			'idAgencia' => array(self::BELONGS_TO, 'Agencia', 'id_agencia'),
			'idProducto' => array(self::BELONGS_TO, 'Ramo', array('id_producto'=>'id_ramo')),
			'sarFiles' => array(self::HAS_MANY, 'File', 'id_radica'),
			'sarHistorialrad' => array(self::HAS_ONE, 'Historial', 'id_radica', 'order' => 'id_historial ASC'),
			'sarHistorials' => array(self::HAS_MANY, 'Historial', 'id_radica', 'order' => 'fecha_inicio ASC'),
			//'sarHistorialPend' => array(self::HAS_ONE, 'Historial', 'id_radica', 'condition'=>'id_historial=(select max(id_historial) from sar_historial where id_radica=t.id_radica)'),
			'sarHistorialPend' => array(self::HAS_ONE, 'Historial', 'id_radica', 'on'=>'"sarHistorialPend".fecha_termino is null', 'order' => 'id_historial DESC'),
			'sarHistorialUlt' => array(self::HAS_ONE, 'Historial', 'id_radica', 'on'=>'"sarHistorialUlt".fecha_termino is not null', 'order' => 'id_historial DESC'),
			'sarHistorialPest' => array(self::HAS_ONE, 'Historial', 'id_radica', 
				'condition'=>"estado = 'En estudio'", 'order' => 'id_historial ASC'),


		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_radica' => 'Id Radica',
			'tipo_id' => 'Tipo Identificación',
			'identificacion' => 'Identificación',
			'nombre' => 'Nombre',
			'fecha_rad' => 'Fecha Radicación',
			'status' => 'Estado',
			'id_agencia' => 'Agencia',
			'id_producto' => 'Producto',
			'usu' => 'Usuario Radica',
			'en_trabajo' => 'Fecha Inicio Trabajo',
			'usuario_cod_trb' => 'Usuario Trabajo',
			'fecha_formulario' => 'Fecha de Formulario',
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
		$criteria->compare('tipo_id',$this->tipo_id,true);
		$criteria->compare('identificacion',$this->identificacion,true);
		$criteria->compare('nombre',$this->nombre,true);

		
		if((isset($this->fechai) && trim($this->fechai) != "") && (isset($this->fechaf) && trim($this->fechaf) != "")){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime (  $this->fechaf )));

			$criteria->addBetweenCondition('fecha_rad', $this->fechai, $rangob);
		}else if((isset($this->fechai) && trim($this->fechai) != "") && (isset($this->fechaf) || trim($this->fechaf) == null)){
			$rangob = date ( 'Y/m/d' , strtotime ( '+1 day' , strtotime ( $this->fechai)));
			$criteria->addBetweenCondition('fecha_rad', $this->fechai, $rangob);
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
		$criteria->compare('id_agencia',$this->id_agencia);
		$criteria->compare('id_producto',$this->id_producto);

		$criteria->with=array('sarHistorialPend'); 
		$criteria->compare('"sarHistorialPend"."usuario_cod"',$this->usuarioPend);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'"sarHistorialPend"."fecha_inicio"'),
			'pagination'=>array('pagesize'=>30),
		));
	}

	public function nonit($attribute, $params){
		if($this->tipo_id == 'NI' && ($this->id_producto == 81 || $this->id_producto == 80))
		  $this->addError($attribute, 'Para los ramos de autos livianos y pesados no se puede seleccionar un NIT');
		return;
	}


	public function findcasodia($attribute, $params){
		if(!$this->identificacion)
			return;

		
		$result = Yii::app()
					->db
					->createCommand()
					->from('sar_radica')
					->where("identificacion=:identificacion and to_char(fecha_rad,'yyyymmdd') = to_char(now(),'yyyymmdd') and 
						status <> 'swRadica/anulado'", 
						array("identificacion"=>$this->identificacion))
					->queryRow();
		if($result)
			$this->addError($attribute, 'El caso con ID. "'.$this->$attribute.'" ya fue radicado el dia de hoy');
		
		
    }

	public function getUserEnEstudio(){	
	  if($this->id_producto == 81 || $this->id_producto == 80){
		$users = Yii::app()->db->createCommand()
		    ->select('distinct(usu.usuario_cod), count(id_historial) as cantidad')
		    ->from('adm_usuario usu')
		    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod and jerarquia_opcion=:jerarquia_opcion', array(':jerarquia_opcion'=>"4.4.7"))
		    ->leftJoin('sar_historial hi', 'hi.usuario_cod=usu.usuario_cod and fecha_termino is null and estado=:estado', array(':estado'=>"En estudio"))
		    ->where("not usuario_bloqueado ")   
		    ->order('cantidad asc')		    
		    ->group('usu.usuario_cod')		    
		    ->queryAll();
	  }else{
		$users = Yii::app()->db->createCommand()
		    ->select('distinct(usu.usuario_cod), count(id_historial) as cantidad')
		    ->from('adm_usuario usu')
		    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod and jerarquia_opcion=:jerarquia_opcion', array(':jerarquia_opcion'=>"4.4.2"))
		    ->leftJoin('sar_historial hi', 'hi.usuario_cod=usu.usuario_cod and fecha_termino is null and estado=:estado', array(':estado'=>"En estudio"))
		    ->where("not usuario_bloqueado ")   
		    ->order('cantidad asc')		    
		    ->group('usu.usuario_cod')		    
		    ->queryAll();	  	
	  }
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
			$interval = date_diff(date_create($this->fecha_rad),date_create($this->sarHistorialUlt->fecha_termino));
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

			$interval = date_diff(date_create($this->sarHistorialPend->fecha_inicio),date_create($elahora) );
			
			$ctatiempo = $interval->format('%a días %h horas %i minutos');
		}
		return $ctatiempo;
	}

	public function getUltimaFecha(){
		$ultimafecha = null;
		if ($this->status=='swRadica/no_vincular' || $this->status=='swRadica/cerrado' || $this->status=='swRadica/anulado')
			$ultimafecha = $this->sarHistorialUlt->fecha_termino;

		
		return $ultimafecha;
	}

	public function behaviors()
	{
	    return array(
        	'swBehavior'=>array(
	            'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
	            'workflowSourceComponent' => 'swSarlaft'
        	),
    	);
	}


	public function beforeValidate() {
		if($this->isNewRecord){
			$this->fecha_rad = new CDbExpression('NOW()');
			$this->id_agencia = Yii::app()->user->getIdAgencia();
		}


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

		if($this->modelHistorialPos->estado == 'En estudio')
			$this->modelHistorialPos->usuario_cod = $modelRadica->getUserEnEstudio();	

			
	}



}