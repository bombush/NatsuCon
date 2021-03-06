<?php
    namespace Natsu\Control;
/**
 * Description of MenuControl
 *
 * @author campari
 */
class ContentControl extends BaseControl {
    private $controls;
    private $em;

    public function setControls($controls){
        $this->controls = $controls;
    }
    
    public function setEm($em){
        $this->em = $em;
    }


    public function render(){
        $this->template->setFile(__DIR__."/templates/ContentControl.latte");
        $this->template->controls = $this->controls;
        $this->template->render();
    }
    

    public function createComponentMagic(){
        $controls = $this->controls;
        $em = $this->em;
        $control = new \Nette\Application\UI\Multiplier(
            function($id) use($controls,$em){
                    $ctrl = $controls[$id];
                    $className = ucfirst($ctrl->class)."Control";
                    $controlClass = "\Natsu\Control\\".$className;
                    $control = new $controlClass;
                    if(!empty($ctrl->params)){
                        parse_str($ctrl->params,$params);
                    //    print_R($params);
                        foreach($params as $key => $value){
                            $control->set($key, $value);
                        }
                        $control->set("em", $em);
                    }
                    return $control;

            }

        );


        return $control;
    }




}
?>