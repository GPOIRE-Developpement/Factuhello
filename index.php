<?php

require_once __DIR__.'/vendor/autoload.php';

session_start();

use guillaumepaquin\factuhello\dispatch\Dispatcher;
use guillaumepaquin\factuhello\model\Repository;

Repository::setConfig('db.config.ini');
Repository::getInstance();

$d = new Dispatcher();
$d->run();