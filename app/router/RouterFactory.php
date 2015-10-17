<?php

namespace Natsu\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

        /**
     *
     * @var Nette\Database\Connection
     */
    private $database;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter(\Nette\Database\Connection $database = null)
	{
		$router = new RouteList;


                
                $pageRoute = new PageRoute('<id>',
                array(
                    'id' => array(
                        Route::PATTERN => ".*",
                        Route::FILTER_IN => function($id) use ($database) {
                           if(is_numeric($id)){
                                return $id;
                           }else{
                                
                                $page = $database->query("SELECT * FROM route WHERE url = ?", $id)->fetch();
                                if($page == NULL)
                                     return NULL;
                                return $page->contentId;
                           }
                        },
                        Route::FILTER_OUT =>function($id) use ($database){
                           if(!is_numeric($id)){
                return $id;
            }else{
                
                return $database->query("SELECT * FROM route WHERE contentId = ?", $id)->fetch()->url;
            }
                        }
                    ),
                    'presenter' => 'Content',
                    'action' => 'view'
                )

                );
               $pageRoute->database = $database;

                $router[] = $pageRoute;
               
                 

		$router[] = new Route('butaneko', 'Sign:in');
                $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
                //$router[] = new Route('index.php', 'Homepage:default');
                
		return $router;
	}

}