<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Condroid;

/**
 * Description of CondroidImport
 *
 * @author yasuri
 */
class CondroidImport {
   /**
    *
    * @var \DibiConnection
    */
    private $db;
    private $file;
    /**
     *
     * @var DbSimple
     */
    private $dbSimple;
    public function setDb($db) {
        $this->db = $db;
    }

    public function setFile($file) {
        $this->file = $file;
        
    }
    
   
    private function loadFile(){
        $xml = simplexml_load_file($this->file, null, LIBXML_NOCDATA);
        return $xml;
    }

    
    
    public function run(){
        $this->dbSimple = new DbSimple();
        $this->dbSimple->db = $this->db;
        $file = $this->loadFile();
        $this->process($file);
    }
    
    private function process($xml){
        foreach($xml->programme as $p){
            $this->parse($p);
        }
    }
    
    
    private function parse($program){
        //echo $program->{'program-line'} . "\n";
        //exit;
        $p = new Programme;
        $p->id = (int)$program->pid;
        
       
        $content = $p->createContent($program);
        $contentId = $this->dbSimple->setTable("content")->setData($content)->insert();
        
        $route = $p->createRoute((string) $program->title, $contentId, $p->sectionId);
        $routeId = $this->dbSimple->setTable("route")->setData($route)->insert();
        
        
        $p->contentId = $contentId;
        
        
        $p->typeId = $p->toType((string) $program->type);
        $p->genreId = $this->dbSimple->getId("programgenre", (string) $program->{'program-line'});
        $p->roomId = $p->toLocation((string) $program->location);
        $p->timeFrom = date("Y-m-d G:i:s", strtotime($program->{'start-time'}));
        $p->timeTo = date("Y-m-d G:i:s", strtotime($program->{'end-time'}));
        $p->length = 0;
        $p->reprise = 0;
        
     //   print_R($p);
     //   exit;
        $newProgramme = (array) $p;
        $programId = $this->dbSimple->setTable("program")->setData($newProgramme)->insert();
         echo $programId." OK\n";
         //exit;
        
        
    }
    
    
    
    
    
    
}
