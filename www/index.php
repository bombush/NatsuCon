<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require __DIR__ . '/.maintenance.php';

/*
echo '<pre>';
print_r($_SERVER);
print_r(__DIR__);
echo '</pre>';
exit;
 * 
 */

if(strstr($_SERVER['REQUEST_URI'],".php")){
   include "./postAcceptor.php";
   exit;


}





$container = require '../app/bootstrap.php';

$container->getByType('Nette\Application\Application')->run();
