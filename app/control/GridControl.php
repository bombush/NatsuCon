<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;
use App\Controls\Grido\Grid;
use Nette\Utils\Html;

/**
 * Description of GridControl
 *
 * @author yasuri
 */
class GridControl extends BaseControl {
   private $grid;
   private $name;
   private $dibiSource;
   private $columns;
   private $buttons;
   private $pk;
   private $callBack;
   private $presenter;
   
   public function setColumns($columns) {
       $this->columns = $columns;
   }
   
public function setPresenter($presenter){
    $this->presenter = $presenter;
}

   public function setButtons($buttons) {
       $this->buttons = $buttons;
   }

   public function setPk($pk) {
       $this->pk = $pk;
   }

   public function setCallBack($callBack) {
       $this->callBack = $callBack;
   }

      
   public function getGrid() {
       return $this->grid;
   }

   public function getName() {
       return $this->name;
   }

   public function getDibiSource() {
       return $this->dibiSource;
   }

   public function setGrid($grid) {
       $this->grid = $grid;
   }

   public function setName($name) {
       $this->name = $name;
   }

   public function setDibiSource($dibiSource) {
       $this->dibiSource = $dibiSource;
   }
   
   public function run(){
         $grid = new Grid($this->getParent(), $this->name);
         $grid->model = $this->dibiSource;
         $grid->setPrimaryKey($this->pk);
         foreach($this->columns as $name => $title){
           $gridColumn= $grid->addColumnText($name, $title)
            ->setFilterText()
            ->setSuggestion();
           if(isset($this->callBack[$name])){
               $gridColumn->setColumn($this->callBack[$name]);
           }
         
             
         }
         
         foreach($this->buttons as $action => $title){
                $grid->addActionHref($action, $title, $this->presenter.":".$action);
         }
         
         
         
        
        $grid->setExport();
        $this->setGrid($grid);
       
   }


    
    
    
    
    
}
