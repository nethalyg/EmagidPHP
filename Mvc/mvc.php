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

		if($arr['template'])
			$emagid->template=$arr['template'];

		if(isset($arr['root']))
			self::$root=$arr['root'];

		if(isset($arr['default_controller']))
			self::$default_controller=$arr['default_controller'];

		if(isset($arr['default_view']))
			self::$default_view=$arr['default_view'];



		$uri = $_SERVER['REQUEST_URI'];

		if(stristr($uri, "?")){
			$uri_parts = explode("?", $uri); 
			$uri = $uri_parts[0];

			//$_SERVER['QUERY_STRING'] =$uri_parts[1];
			self::rebuildQueryString($uri_parts[1]);

		}


		if(self::startsWith($uri, self::$root)){
			$uri = substr($uri, strlen(self::$root)+1);
		}

		if(self::startsWith($uri, '/')){
			$uri = substr($uri, 1);
		}

			



		$segments = $uri != '' && $uri != '/' ? explode('/', $uri) : array();



		$controller_name = self::getAndPop($segments) ; 


		if(!$controller_name ) {
				// if controller doesn't exist, view won't exist neigther .
				$controller_name = self::$default_controller ;
				$view_name = self::$default_view;
		}else {
			// controller exists, might have view definition, and parameters .
			$view_name = self::getAndPop($segments);

			if(!$view_name)
				$view_name = self::$default_view;
			




		}

		$view_name = count($segments)>1?$segments[1]:self::$default_view;


	
		// load the controller 
		require_once($emagid->base_path.'/controllers/'.$controller_name.'.php');


		$emagid->controller->name = $controller_name; 
		$emagid->controller->view = $view_name; 

		call_user_func_array(array(&$emagid->controller, $view_name),$segments);

		//$emagid->controller->$view_name(); 

	}


	/**
	* Get the first element from the array and remove it 
	*
	* @param array &$arr reference to the array 
	*/
	private static function getAndPop(&$arr ){
		if(count($arr)){
			$ret = $arr[0];

			array_shift($arr);

			return $ret ;

		}

		return null; 
	}



	/**
	* Rebuild the querystring 
	*
	* @param string $str - the text that comes after the "?" 
	*/
	private static function rebuildQueryString ($str){
		global $_GET; 

		$_GET = [] ;

		foreach (explode("&", $str) as $qs) {
			$key = explode("=", $qs)[0];
			$val = explode("=", $qs)[1];

			$_GET[$key] = $val;
		}



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