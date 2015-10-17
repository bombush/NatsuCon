<?php
namespace Natsu\Router;

class PageRoute extends \Nette\Application\Routers\Route {
   /**
    *
    * @var Nette\Database\Connection
    */
    public $database;

    public function match(\Nette\Http\IRequest $request){
        /**
         * @var $appRequest \Nette\Application\Request
         */
        $appRequest = parent::match($request);

        if(!isset($appRequest->parameters['id']))
                return NULL;

        if(!is_numeric($appRequest->parameters['id'])){
            $page = $this->database->query("SELECT contentId FROM route WHERE url = ?", $appRequest->parameters['id'])->fetch();
            if($page == NULL){
                return NULL;
            }
            $appRequest->parameters['id'] = $page->contentId;
        }

       return $appRequest;
    }
}