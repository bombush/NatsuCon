<?php
namespace Natsu\Model;


use Nette;

class ExportModel extends EntityModel {
     public $tableName = "content";
     public $tableNameRoute = "route";

     public function getRss(){
         $stm = $this->database->query("SELECT content.*, route.url
                FROM content INNER JOIN route ON  route.contentId = content.id
                WHERE content.isNews = 1 AND content.isDraft = 0");
         return $stm->fetchAll();
     }

     public function getSitemap(){
         $stm = $this->database->query("SELECT content.*, route.url
                FROM content INNER JOIN route ON  route.contentId = content.id
                WHERE content.isDraft = 0");
         return $stm->fetchAll();
     }
}
