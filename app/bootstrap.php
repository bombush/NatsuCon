<?php

require __DIR__ . '/../vendor/autoload.php';

use RadekDostal\NetteComponents\DateTimePicker\DateTimePicker;
use RadekDostal\NetteComponents\DateTimePicker\DatePicker;

$configurator = new Nette\Configurator;

$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
//$configurator->setDebugMode(array());
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
        ->addDirectory(__DIR__ . '/../vendor/custom')
	->register();

DateTimePicker::register();
DatePicker::register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
