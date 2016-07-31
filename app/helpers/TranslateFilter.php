<?php


namespace Natsu\Helpers;
use Nette\Object;

class TranslateFilter extends Object {
    /**
     * @param text string 
     * @return string
     */
    public function __invoke($text)
    {
        switch($text){
             case "Fri": return "Pá"; break;
             case "Sat": return "So"; break;
             case "Sun": return "Ne"; break;
             case "Friday": return "Pátek"; break;
             case "Saturday": return "Sobota"; break;
             case "Sunday": return "Neděle"; break;
        }
        return $text;
    }

}
