<?php
namespace Natsu\Presenters;

class ExportPresenter extends BasePresenter{
    private $entityModel;

    public function inject(\Natsu\Model\EntityModel $entityModel){
        $this->entityModel = $entityModel;

    }

    public function actionRss(){
        $em = $this->entityModel->reflection("export");
        $rss = $em->getRss();
        $this->add("rss", $rss);
    }

     public function renderRss(){
         $this->prepare();
     }


    public function actionSitemap(){
        $em = $this->entityModel->reflection("export");
        $map = $em->getSitemap();
        $this->add("map", $map);
    }

    public function renderSitemap(){
         $this->prepare();
     }
}
?>
