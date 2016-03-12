<?php

require_once  'vendor/autoload.php';

$driver = new \Smalot\Docker\Machine\Driver\OracleVirtualbox();
$driver->loadConfig();

$manager = new \Smalot\Docker\Machine\Manager('docker-machine');
var_dump($manager->getList());
