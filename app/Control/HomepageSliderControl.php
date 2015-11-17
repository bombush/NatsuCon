<?php


namespace Natsu\Control;

use Nette\Application\UI;
use Nette\Utils\ArrayHash;

class HomepageSliderControl extends UI\Control{
     public function render(){
        $this->template->slides = $this->getSliderImages();
        $this->template->setFile(__DIR__."/templates/HomepageSliderControl.latte");
        $this->template->render();
    }

    private function getSliderImages(){
        $images = [];

        $image1 = new ArrayHash();
        $image1->title = "Obr1";
        $image1->url = "url1";
        $image1->src = "xxx";
        $images[] = $image1;

        $image2 = new ArrayHash();
        $image2->title = "Obr1";
        $image2->url = "url1";
        $image2->src = "xxx";
        $images[] = $image2;

        $image3 = new ArrayHash();
        $image3->title = "Obr1";
        $image3->url = "url1";
        $image3->src = "xxx";
        $images[] = $image3;

        $image4 = new ArrayHash();
        $image4->title = "Obr1";
        $image4->url = "url1";
        $image4->src = "xxx";
        $images[] = $image4;
        return $images;

    }


}

interface IHomepageSliderControlFactory{

	/**
	 * @return HomepageSliderControl
	 */
	public function create();
}

?>
