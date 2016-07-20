<?php

namespace Natsu\Presenters;

use Natsu\Control\ProgramHighlightBlockControl;
use Nette;
use Natsu\Model;


class HomepagePresenter extends BasePresenter
{
    /**
     * @inject
     * @var \Natsu\Model\EntityModel
     */
    public $entityModel;


	/**
     * @return \Natsu\Control\HomepageSliderControl
     */
    public function createComponentSlider(){
        $slider = new \Natsu\Control\HomepageSliderControl();
        return $slider;
    }

    public function createComponentNews(){
        $slider = new \Natsu\Control\NewsControl();
        return $slider;
    }

    public function createComponentParalax(){
          $paralax = new \Natsu\Control\ParalaxHomepage;
        $programHighlightBlock = $this->createComponentProgramHighlightBlock();
        $paralax->addComponent($programHighlightBlock, 'programHighlightBlock');

          return $paralax;

    }

    public function createComponentProgramHighlightBlock()
    {
        $sectionId = $this->context->getParameters()['sectionId'];
        $programModel = $this->entityModel->reflection('Program');

        $control = new ProgramHighlightBlockControl($programModel, $sectionId);
        return $control;
    }
}
