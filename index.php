<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use nrv\nancy\dispatch\Dispatcher;
use nrv\nancy\repository\NrvRepository;
$pdo = NrvRepository::setConfig("config/db_config.ini");
$dispatcher = new Dispatcher();
$dispatcher->run();