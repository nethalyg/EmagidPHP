<?php 

namespace Emagid\Mvc;


/**
* @todo : add routing table support
*/
class Mvc{



	/**
	* @var string site's root, used to determine where the controller starts 
	*/
	private static $debug = true; 


	/**
	* @var string site's root, used to determine where the controller starts 
	*/
	private static $root = '/'; 


	/**
	* @var string the default controller when none specified
	*/
	private static $default_controller = 'home'; 


	/**
	* @var string the default view when none specified
	*/
	private static $default_view = 'index'; 



	/**
	* @var array - routing table allows the user to  add new "translators " for routes
	*/
	private static $routes = []; 


	/**
	* Load the Mvc structure
	* 
	* @param array $arr 
	* 		'root' string - the absolute URI of the current site, should always end with '/' (e.g.: '/', '/mysite/')
	*/
	public static function load(array $arr = []){

		global $emagid; 

		if(isset($arr['template']))
			$emagid->template=$arr['template'];

		if(isset($arr['root']))
			self::$root=$arr['root'];

		if(isset($arr['default_controller']))
			self::$default_controller=$arr['default_controller'];

		if(isset($arr['default_view']))
			self::$default_view=$arr['default_view'];


		$uri = $_SERVER['REQUEST_URI'];


		if(self::startsWith($uri, self::$root)){
			$uri = substr($uri, strlen(self::$root));
		}

		if(self::startsWith($uri, '/')){
			$uri = substr($uri, 1);
		}

			



		$segments = $uri != '' && $uri != '/' ? explode('/', $uri) : array();




		$controller_name = count($segments) >0?$segments[0]:self::$default_controller;
		$view_name = count($segments)>1?$segments[1]:self::$default_view;



	
		// load the controller 
		require_once($emagid->base_path.'/controllers/'.$controller_name.'.php');


		$emagid->controller->name = $controller_name; 
		$emagid->controller->view = $view_name; 
		$emagid->controller->$view_name(); 

	}

	/** 
	* Checks whether a strings starts with a specific string.
	*
	* @todo Move this function to functions.inc.php
	*/
	static function startsWith($haystack,$needle,$case=true){
		if($case)
       		return strpos($haystack, $needle, 0) === 0;

   		return stripos($haystack, $needle, 0) === 0;
	}


	
}



?>