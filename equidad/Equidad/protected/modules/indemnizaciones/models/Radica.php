<?php

/**
 * This is the model class for table "idm_radica".
 *
 * The followings are the available columns in table 'idm_radica':
 * @property integer $id_radica
 * @property string $no_sinestro
 * @property string $fecha_rad
 * @property string $status
 * @property integer $id_producto
 * @property integer $id_agencia_expide
 * @property integer $id_agencia_tramita
 * @property string $respuesta
 *
 * The followings are the available model relations:
 * @property IdmHistorial[] $idmHistorials
 * @property SusProducto $idProducto
 * @property Tblradofi $idAgenciaExpide
 * @property Tblradofi $idAgenciaTramita
 * @property IdmRadicaDoc[] $idmRadicaDocs
 */
class Radica extends SWActiveRecord
{
	private $modelHistorialPre = null;
	private $modelHistorialPos = null;
	private $tramiteEstudio = null;
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
		return 'idm_radica';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_producto, id_agencia_expide, id_agencia_tramita', 'numerical', 'integerOnly'=>true),
			array('no_sinestro', 'length', 'max'=>8),
			//array('no_sinestro','unique'),   
			array('no_sinestro', 'length', 'min'=>8),
			array('status', 'length', 'max'=>50),
			array('status', 'SWValidator'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('no_sinestro, fecha_rad, status, id_producto, id_agencia_tramita, id_agencia_expide', 'required', 'on'=>'radicar'),
			array('status', 'required', 'on'=>'estudio'),
			array('id_radica, no_sinestro, fecha_rad, status, id_producto, searchHistorialPend, respuesta', 'safe'),
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
			'idProducto' => array(self::BELONGS_TO, 'Producto', 'id_producto'),
			'RadicaDocs' => array(self::HAS_MANY, 'RadicaDoc', 'id_radica'),
			'idmHistorials' => array(self::HAS_MANY, 'Historial', 'id_radica'),
			'idAgenciaExpide' => array(self::BELONGS_TO,	 'Agencia', 'id_agencia_expide'),
			'idAgenciaTramita' => array(self::BELONGS_TO,	 'Agencia', 'id_agencia_tramita'),
			'idmHistorialPend' => array(self::HAS_ONE, 'Historial', 'id_radica', 'condition'=>'id_historial=(select max(id_historial) from idm_historial where id_radica=t.id_radica)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_radica' => 'Tramite',
			'no_sinestro' => 'No. de Sinestro',
			'fecha_rad' => 'Fecha radicación',
			'status' => 'Proximo paso',
			'id_producto' => 'Producto',
			'id_agencia_expide' => 'Agencia expide',
			'id_agencia_tramita' => 'Agencia tramita',
			'respuesta' => 'Respuesta',
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
		$criteria->compare('id_radica',$this->id_radica);
		$criteria->compare('no_sinestro',$this->no_sinestro,true);
		$criteria->compare('fecha_rad',$this->fecha_rad,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('id_producto',$this->id_producto);
		$criteria->compare('id_agencia_tramita',$this->id_agencia_tramita);

		$criteria->with=array('idmHistorialPend'); 
		$criteria->compare('"idmHistorialPend"."usuario_cod"',$this->searchHistorialPend);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	

	public function behaviors()
	{
	    return array(
        	'swBehavior'=>array(
	            'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
	            'workflowSourceComponent' => 'swIndemnizaciones'
        	),
    	);
	}

	public function beforeValidate() {
		if($this->isNewRecord)
			$this->fecha_rad = new CDbExpression('NOW()');

		$this->no_sinestro = strtoupper($this->no_sinestro);
		return parent::beforeSave();
	}

	public function afterSave(){
		parent::afterSave();

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

		if(!isset($this->modelHistorialPre)){
			$this->modelHistorialPre=new Historial;
			$this->modelHistorialPre->id_radica = $this->id_radica;
			$this->modelHistorialPre->estado = $this->swGetStatus()->getLabel();
			$this->modelHistorialPre->fecha_inicio = new CDbExpression('NOW()');
		}	

		$this->modelHistorialPre->fecha_termino = new CDbExpression('NOW()');
		$this->modelHistorialPre->usuario_cod = Yii::app()->user->id;
		$this->modelHistorialPre->observacion = $_POST['Historial']['observacion'];
	}

	public function afterTransition($event)
	{		
		$user=Yii::app()->user->id;

		if($this->status === 'swRadica/en_estudio')
			$user = $this->getUserEnEstudio();

		$this->modelHistorialPos=new Historial;
		$this->modelHistorialPos->id_radica = $this->id_radica;
		$this->modelHistorialPos->fecha_inicio = new CDbExpression('NOW()');
		$this->modelHistorialPos->usuario_cod = $user;
		$this->modelHistorialPos->estado = $this->swGetStatus()->getLabel();
		$this->modelHistorialPos->observacion = '_';
	}

	public function afterFind(){
		$tramite = Yii::app()->db->createCommand()
		    ->select('min(rad.id_radica) as max_radica')
		    ->from('idm_radica rad')
		    ->leftJoin('idm_historial his', 'his.id_radica=rad.id_radica and his.id_historial = (select max(id_historial) from idm_historial where id_radica=rad.id_radica)')
		    ->where("his.usuario_cod=:usuario_cod", array(':usuario_cod'=>Yii::app()->user->id))   		      
		    ->andWhere("rad.status=:status", array(':status'=>'swRadica/en_estudio'))   		      
		    ->queryScalar();

		$this->tramiteEstudio=$tramite;
   		return parent::afterFind();
    }

	private function getUserEnEstudio(){		
		$users = Yii::app()->db->createCommand()
		    ->select('distinct(usu.usuario_cod), count(id_historial) as cantidad')
		    ->from('adm_usuario usu')
		    ->join('adm_usumenu um', 'um.usuario_cod=usu.usuario_cod and jerarquia_opcion=:jerarquia_opcion', array(':jerarquia_opcion'=>"4.3.2"))
		    ->leftJoin('idm_historial hi', 'hi.usuario_cod=usu.usuario_cod and fecha_termino is null and estado=:estado', array(':estado'=>"En estudio"))
		    ->where("not usuario_bloqueado")   
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
				array('data-toggle'=>'tooltip', 'data-placement'=>'top', 'data-original-title'=>'Realizar estudio')//html options
			);
	}
}