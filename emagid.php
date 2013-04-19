<?php 
namespace Emagid;



/**
* Base class for eMagid libraries 
*/
class Emagid{

	/**
	* @var an object that contains the connection string parameters (db_name, username, password, host)
	*/
	public $connection_string;



	/**
	* Defualt constructor 
	*/
	public function __construct(){
		$this->connection_string = new \stdClass;
		$this->loadLibraries();

		

		global $emagid ;


	}


	/**
	* Load the eMagid libraries 
	*/
	function loadLibraries(){
		$libraries = [
			'DB\db.php'
			//, 'Page\page.php'
		];

		foreach($libraries as $lib ){
			
			require_once($lib);
		}
	}

	/**
	*
	* Load all the files in a folder
	* 
	* @param $folder string path to the folder 
	*/
	function loadFolder($folder){
		

	}

}

?>