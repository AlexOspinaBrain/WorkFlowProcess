<?php
/**
 * AuthWebUser class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Web user that allows for passing access checks when enlisted as an administrator.
 *
 * @property boolean $isAdmin whether the user is an administrator.
 */
class AuthWebUser extends CWebUser
{
    /**
     * @var string[] a list of names for the users that should be treated as administrators.
     */
    public $admins = array('admin');
    public $itemsMenu = array();

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();
        $this->setIsAdmin(in_array($this->name, $this->admins));
        $this->itemsMenu=Yii::app()->db->createCommand()
                ->select('men.*')
                ->from('adm_menu men')
                ->join('adm_usumenu usu', 'men.jerarquia_opcion=usu.jerarquia_opcion')
                ->where('usuario_cod=:usuario_cod', array(':usuario_cod'=>$this->id))
                ->queryAll();
    }

    /**
     * Returns whether the logged in user is an administrator.
     * @return boolean the result.
     */
    public function getIsAdmin()
    {
        return $this->getState('__isAdmin', false);
    }

    /**
     * Sets the logged in user as an administrator.
     * @param boolean $value whether the user is an administrator.
     */
    public function setIsAdmin($value)
    {
        $this->setState('__isAdmin', $value);
    }

    /**
     * Performs access check for this user.
     * @param string $operation the name of the operation that need access check.
     * @param array $params name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return basename(path)oolean whether the operations can be performed by this user.
     */
    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        if ($this->getIsAdmin()) {
            return true;
        }else{
            $assigned = Yii::app()->db->createCommand()
            ->select()
            ->from('adm_usumenu asi')
            ->where(
                array('AND', 'usuario_cod = :usuario_cod', 'jerarquia_opcion = :operation'), 
                array(':usuario_cod' => $this->id, ':operation' => $operation)
            )
            ->queryRow();  
            
            return (bool) $assigned;
        }
    }

    public function getAuthItem($name)
    {
      $item = array();
      
      foreach ($this->itemsMenu as $key => $value) {
        if($value['jerarquia_opcion'] === $name){
          $item=$this->itemsMenu[$key];
          break;
        }
      }
      
      return $item;    
    }

    public function getMenu($parents = array(), $omite = array())
    {   
        $menu = array();

        foreach ($parents as $item) { 
            if ( $task = $this->getAuthItem($item)) {             
           
                $task['label'] = $task['opcion'];
               
                if(empty($task['url'])) 
                  $task['url'] = "#";
                
                if(!in_array ( $task['jerarquia_opcion'], $omite))
                  $task['items'] = $this->getChildsMenu( $task['jerarquia_opcion'], $omite );

                $menu[]= $task;
            }
        }       
       
        return $menu;
    }

    public function getChildsMenu($item, $omite)
    {
        $itemsChild = array();
        $cant = substr_count($item, '.')+1;

        foreach ($this->itemsMenu as $key => $value) {
          if (preg_match('/^'.$item.'/',$value['jerarquia_opcion']) && substr_count($value['jerarquia_opcion'], '.') == $cant){
            $indice = substr($value['jerarquia_opcion'], strrpos($value['jerarquia_opcion'], ".")+1);
            $itemsChild[$indice] = $this->itemsMenu[$key];
            $itemsChild[$indice]['label'] = $itemsChild[$indice]['jerarquia_opcion'].' '.$itemsChild[$indice]['opcion'];
            if(empty($itemsChild[$indice]['url_opcion'])) 
              $itemsChild[$indice]['url'] = "#";
            elseif(!$itemsChild[$indice]['new'])
              $itemsChild[$indice]['url'] = '../principal.php?p='.str_replace(".", "-",$itemsChild[$indice]['jerarquia_opcion']);
            else
              $itemsChild[$indice]['url'] = array('/suscripcion');

            if(!in_array ( $itemsChild[$indice]['jerarquia_opcion'], $omite))
             $itemsChild[$indice]['items'] = $this->getChildsMenu( $itemsChild[$indice]['jerarquia_opcion'], $omite );
          }          
        }

        ksort($itemsChild);
       
        return $itemsChild;
    }
}
