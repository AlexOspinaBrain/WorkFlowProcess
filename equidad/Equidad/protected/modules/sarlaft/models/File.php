<?php

/**
 * This is the model class for table "sar_file".
 *
 * The followings are the available columns in table 'sar_file':
 * @property integer $id_file
 * @property string $name_file
 * @property string $path_file
 * @property string $fecha
 * @property integer $id_historial
 * @property integer $id_radica
 *
 * The followings are the available model relations:
 * @property SarHistorial $idHistorial
 * @property SarRadica $idRadica
 */
class File extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
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
		return 'sar_file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fecha', 'default', 'value' =>new CDbExpression('NOW()')),
			array('id_historial', 'default', 'value' =>0),
			array('name_file, path_file, fecha, id_historial, id_radica', 'required'),
			array('id_historial, id_radica', 'numerical', 'integerOnly'=>true),
			array('name_file', 'length', 'max'=>100),
			array('path_file', 'length', 'max'=>200),
			array('fecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_file, name_file, path_file, fecha, id_historial, id_radica', 'safe', 'on'=>'search'),
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
			'idHistorial' => array(self::BELONGS_TO, 'Historial', 'id_historial'),
			'idRadica' => array(self::BELONGS_TO, 'Radica', 'id_radica'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_file' => 'Id File',
			'name_file' => 'Adjunto',
			'path_file' => 'Path File',
			'fecha' => 'Fecha',
			'id_historial' => 'Id Historial',
			'id_radica' => 'Id Radica',
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

		$criteria->compare('id_file',$this->id_file);
		$criteria->compare('name_file',$this->name_file,true);
		$criteria->compare('path_file',$this->path_file,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('id_historial',$this->id_historial);
		$criteria->compare('id_radica',$this->id_radica);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

		protected function afterFind ()
    {
        // convert to display format
        $this->fecha = date ('Y/m/d h:i a', strtotime ($this->fecha));          
        parent::afterFind ();
    }

   // protected function beforeValidate(){			
		//$this->name_file = pathinfo($this->path_file);
		//$this->name_file = $this->name_file["basename"];
		//$this->id_historial = $this->idTramite->tramiteHistorialUlt->id_historial;
	//	return parent::beforeValidate();
	//}

    protected function beforeSave(){	    
		if($this->isNewRecord){
			//if(Yii::app()->params['entorno']=== 'Produccion'){
				$path = "vol1/".date('Ymd')."/sarlaft/{$this->id_radica}/{$this->id_historial}";
				$pathpx = date('Ymd')."/sarlaft/{$this->id_radica}/{$this->id_historial}";
			//}
			//else				
			//	$path = "tmp/".date('Ymd')."/{$this->idTramite->idProducto->tipo}/{$this->idTramite->no_tramite}";

			@mkdir("/".$path, 0777, true);
			copy ($this->path_file,"/".$path."/".$this->name_file);
			$this->path_file = "http://imagine.laequidadseguros.coop/vol1/{$pathpx}/{$this->name_file}";


		}			
		return parent::beforeSave();
	}



	protected function ftp_mksubdirs($ftpcon,$ftpbasedir,$ftpath){
   		@ftp_chdir($ftpcon, $ftpbasedir); 
   		$parts = explode('/',$ftpath); 
   		foreach($parts as $part){
      		if(!@ftp_chdir($ftpcon, $part)){
	      		ftp_mkdir($ftpcon, $part);
	      		ftp_chmod($ftpcon, 0775, $part);
	      		ftp_chdir($ftpcon, $part);     
    	  	}
   		}
	}
}