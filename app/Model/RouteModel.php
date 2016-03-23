<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Natsu\Model;


use Nette;

/**
 * Description of RouteModel
 *
 * @author campari
 */
class RouteModel extends EntityModel {
    //put your code here
    public $tableName = "route";

    public function createRoute($contentId, $pageTitle){
        $url = \Nette\Utils\Strings::webalize(substr($pageTitle, 0, 50));
        $route = $this->getRoute($contentId);
        if(!$route){
            $this->setTable($this->tableName);
            $url = $this->getUnique($contentId, $url);
            $this->insert($this->newRoute($contentId, $url));
        }
    }
    
    public function updateRoute($contentId, $routeUrl){
        
        $route = $this->getRoute($contentId);
        $route->url = $this->getUnique($contentId, $routeUrl);
                  $this->setTable("route");
        $this->update($route);
    }

    private function newRoute($id, $url){
        $route = new \Nette\Utils\ArrayHash();
        $route->contentId = $id;
        $route->url = $url;
        $route->creationTime = date('Y-m-d G:i:s', time());
        return $route;

    }

    private function getUnique($contentId, $url){
        $stm = $this->database->query("SELECT * FROM route WHERE id != ? AND url = ?", $contentId, $url);
        $route = $stm->fetch();
        if(!$route){
            return $url;
        }else{
            return $url."-".$contentId;
        }
    }

    private function getRoute($id){
        $statement = $this->database->query("SELECT * FROM route WHERE contentId = ?", $id);
        if($statement){
            return $statement->fetch();
        }else{
            return false;
        }
    }



}
?>
