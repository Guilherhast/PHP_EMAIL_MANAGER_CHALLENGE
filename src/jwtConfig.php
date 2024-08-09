<?php

define('ROOT', __DIR__ );
require_once(ROOT . '/vendor/autoload.php');

use Dotenv\Dotenv as Dotenv;

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();
