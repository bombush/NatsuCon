<?php

namespace Natsu\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{

	public function createComponentSlider(){
            $slider = new \Natsu\Control\HomepageSliderControl();
            return $slider;
        }

        public function createComponentNews(){
            $slider = new \Natsu\Control\NewsControl();
            return $slider;
        }



}
