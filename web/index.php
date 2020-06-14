<?php

require '../vendor/autoload.php';
require '../system/Autoloader.php';

$loader = new system\Autoloader();

$loader->addNamespace('system', '../system');
$loader->addNamespace(system\Application::DEFAULT_CONTROLLER_NAMESPACE, '../controllers');
$loader->addNamespace(system\Application::DEFAULT_HELPERS_NAMESPACE, '../helpers');
$loader->addNamespace(system\Application::DEFAULT_MODEL_NAMESPACE, '../models');

(new system\Application())->run();
