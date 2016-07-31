<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Condroid;

/**
 * Description of Programme
 *
 * @author yasuri
 */
class Programme {
   public $id = 0;
   public $sectionId = 2015;
   public $statusId = 14;
   public $contentId = 0;
   public $typeId = 0;
   public $genreId = 0;
   public $roomId = 0;
   public $timeFrom = NULL;
   public $timeTo = NULL;
   public $length = 0;
   public $reprise = 0;

    /**
     * Programme types map.
     * Left-side is condroid code,
     * right side is application-specific
     *
     * @var array
     */
    protected $types = [
        'F' => 3,
        'C' => 6,
        'G' => 7,
        'P' => 1,
        'Q' => 2,
        'W' => 4
    ];

    /**
     * Locations map.
     * Left-side is Condroid string,
     * right-side is application-specific
     *
     * @var array
     */
    protected $locations = [
        'Edo'       => 1,
        'Nara'      => 2,
        'Meidži'    => 3,
        'Workshopy' => 4,
        'Ostatní'   => 5
    ];

    public function createContent($xml){
       $content = array();
       $content['userId'] = 1;
       $content['sectionId'] = 2015;
       $content['author'] = (string)$xml->author;
       $content['title'] = $content['pageTitle'] = (string) $xml->title;
       $content['text'] = (string) $xml->annotation;
       $content['activeFrom'] = date("Y-m-d G:i:s", time());
       $content['activeUntil'] = date("Y-m-d G:i:s", time()+(3600*24*30*12*10));
       return $content;
       
   }
   
   public function createRoute($title, $contentId, $sectionId){
       $route = array();
       $route['contentId'] = $contentId;
       $webalize = \Nette\Utils\Strings::webalize($title);
       $webalize = \Nette\Utils\Strings::substring($webalize, 0, 50);
       
       $route['url'] = "program/".$sectionId."/".$webalize;
       $route['creationTime'] = date("Y-m-d G:i:s");
       return $route;
       
       
   }
   
   public function toType($type){
       $type = trim($type);
       $types = $this->types;

       return $types[$type];
       
   }

    public function fromType($type) {
        $types = array_flip($this->types);
        return $types[ $type ];
    }

    public function toLocation($location){
       $location = trim($location);
       $locations = $this->locations;
       
       return isset($locations[$location]) ? $locations[$location] : 5;
   }

    /**
     * @param $location
     *
     * @return mixed
     */
   public function fromLocation($location) {
       $locations = array_flip($this->locations);

       return $locations[$location];
   }
}
