<?php

namespace Emagid\Mvc; 

/**
* Base class for controllers
*/
abstract class Controller{

	/**
	* @var string 
	* name of active controller
	*/
	public $name = 'home' ;

	/**
	* @var string 
	* name of active view
	*/
	public $view = 'index';


	/**
	* load the view 
	*
	* @param string $view
	*         name of view to load. default is the class's view
	*/
	protected function load_view($view = null ){
		global $emagid ; 

		if(!$view)
			$view = $this->view; 

		$path= $emagid->base_path.'/views/'.$this->name.'/'.$view.'.php';



		include($path);



	}

}

?>