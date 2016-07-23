<?php
session_start();
require_once'classes/globals.php';
require_once'controllers/AppController.class.php';
require_once'vendor/Twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

spl_autoload_register(
    function ($class) {
	   if(is_file('controllers/'.$class.'.class.php'))require_once('controllers/'.$class.'.class.php');
	   elseif(is_file('classes/'.$class.'.class.php'))require_once('classes/'.$class.'.class.php');
    }
);

$page=AppController::getInstance();
echo $page->getContent();
?>