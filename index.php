<?php
declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/vendor/autoload.php';

use nrv\nancy\dispatch\Dispatcher;
use nrv\nancy\repository\NrvRepository;

NrvRepository::setConfig("config/db_config.ini");
$dispatcher = new Dispatcher();
$dispatcher->run();
