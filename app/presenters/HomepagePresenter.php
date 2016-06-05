<?php

namespace Natsu\Presenters;

use Nette;
use Natsu\Model;


class HomepagePresenter extends BasePresenter
{

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
          return $paralax;
          
      }  


}
