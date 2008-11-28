<?php
/**
 * APF entry point
 *
 * This sets constant values and calls >> router >> controller >> model  
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */

//error_reporting(E_ALL);
global $config;
$config['web_root'] = "http://192.168.1.100/ruka/development/atom/apf/";

/**
 * Application root directory real path
 */ 
define('ROOTPATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
/**
 * System directory real path
 */ 
define('SYSTEMPATH', ROOTPATH . "system" . DIRECTORY_SEPARATOR);
/**
 * Index file path
 */ 
define('FCPATH', str_replace("\\","/",__FILE__));
/**
 * Controllers directory real path
 */ 
define('EXTENSIONPATH', ROOTPATH . "extensions" . DIRECTORY_SEPARATOR);


include_once(SYSTEMPATH . "router.php");
include_once(SYSTEMPATH . "controller.php");
include_once(SYSTEMPATH . "parser.php");
include_once(SYSTEMPATH . "atom.php");
include_once(SYSTEMPATH . "model.php");


$router = new AtomRouter();
$router->dispatch();

?>