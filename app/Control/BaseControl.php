<?php
/**
 * @author Jakub Stribrny <stribrny@1webit.cz>
 */

namespace Natsu\Control;


class BaseControl extends \Nette\Application\UI\Control {

    
    public function set($key, $value){
      // var_Dump("set".  ucfirst($key));
        if(method_exists($this, "set".  ucfirst($key))){
        //    var_Dump("ok");    
            $method = "set".  ucfirst($key);
          
            $this->$method($value);
        }
    }
    
} 