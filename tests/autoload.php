<?php
include_once __DIR__.'/../vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("Paynow\\", dirname(__DIR__) . '/src/', true);
$classLoader->add('', dirname(__DIR__) . '/src/helper.php');
$classLoader->register();