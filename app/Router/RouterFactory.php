<?php

namespace Natsu\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
     *
     * @var \DibiConnection
     */
    private $database;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter(\DibiConnection $database = null)
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
                //dump($id);exit;
                $fetchContent = $database->query("SELECT * FROM route WHERE contentId = ?", $id)->fetch();
                if($fetchContent)
                    return $fetchContent->url;
                else
                   return NULL;
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
                $router[] = new Route('sitemap.xml', 'Export:sitemap');
                $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
                //$router[] = new Route('index.php', 'Homepage:default');
                
		return $router;
	}

}
