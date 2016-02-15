<?php
namespace Natsu\Presenters;

use Mesour\DataGrid\NetteDbDataSource,
	Mesour\DataGrid\Grid;

/**
 * Description of ManagementPresenter
 *
 * @author campari
 */
class ManagementPresenter  extends BasePresenter {
    private $entityModel;

    public function inject(\Natsu\Model\EntityModel $entityModel){
        $this->entityModel = $entityModel;

    }
    
    public function createComponentProgram(){
        $ctl = new \Natsu\Control\ProgramControl;
        $ctl->setLocations(array(1,2,3));
        $ctl->setFestivalStart('2015-08-21 18:00:00');
        $ctl->setFestivalEnd('2015-08-23 17:00:00');
        $ctl->setSectionId(2015);
        $ctl->setEm($this->entityModel);
        return $ctl;
        
        
    }
    
    
     public function createComponentProgramListControl(){
        $ctl = new \Natsu\Control\ProgramListControl;
        $ctl->setSectionId(2015);
        $ctl->setTitle("Promítání");
        $ctl->setTypeId("3");
        $ctl->setEm($this->entityModel);
        return $ctl;
     }
        


    public function createComponentGrido(){
     

        $dataSource = $this->entityModel->reflection("datasource");
        $dibi_source = $dataSource->setTable("content")->table(NULL);
        
        $source = new \Mesour\DataGrid\DibiDataSource($dibi_source); //limitation for \DibiDataSource

        $grid = new \Mesour\DataGrid\Grid(NULL, "grid1");

	$grid->setDataSource($source);
	return $grid;
    }
}
?>
