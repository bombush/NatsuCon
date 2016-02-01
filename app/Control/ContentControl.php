<?php
    namespace Natsu\Control;
/**
 * Description of MenuControl
 *
 * @author campari
 */
class ContentControl extends BaseControl {
    private $controls;

    public function setControls($controls){
        $this->controls = $controls;
    }


    public function render(){
        $this->template->setFile(__DIR__."/templates/ContentControl.latte");
        $this->template->controls = $this->controls;
        $this->template->render();
    }

   


    public function createComponentMagic(){
        $controls = $this->controls;
        $control = new \Nette\Application\UI\Multiplier(
            function($id) use($controls){
                    $ctrl = $controls[$id];
                    $className = ucfirst($ctrl->class)."Control";
                    $controlClass = "\Natsu\Control\\".$className;
                    $control = new $controlClass;
                    return $control;

            }

        );


        return $control;
    }




}
?>