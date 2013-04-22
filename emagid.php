<?php 
namespace Emagid;



/**
* Base class for eMagid libraries 
*/
class Emagid{

	/**
	* @var Array list of folders to include 
	*/
	public $include_paths = [];

	/**
	* @var an object that contains the connection string parameters (db_name, username, password, host)
	*/
	public $connection_string;



	/**
	* Defualt constructor 
	*/
	public function __construct(){
		$this->connection_string = new \stdClass;
		$this->loadLibraries('/libs/Emagid', false);


		
		

		global $emagid ;


	}


	/**
	* Include libraries outside the emagid library 
	* 
	* @param $arr Array - array of include files, will override the default class config
	*/
	public function loadIncludes($arr = null ){
		if(isset($arr) && $arr != null){
			$this->include_paths = $arr;
		}

		foreach($this->include_paths as $folder ){


			$this->loadLibraries($folder);
			
		}

	}


	/**
	* Load the eMagid libraries 
	*/
	function loadLibraries($folder , $loadFiles = true){

		if ($handle = opendir($_SERVER["DOCUMENT_ROOT"]	.$folder)) {
		    /* Loop through directories  */
		    while (false !== ($entry = readdir($handle))) {
		    	if(!$this->startsWith($entry,'.')){ // skip git folders, up folder,etc... 
		    	
			    	if(stristr($entry,".php") ){
			    		if($loadFiles){ // load all files in the current directory
			    			require_once($folder."/".$entry);
			    		}
			    	} else { // it's a folder
			    			$this->loadLibraries($folder."/".$entry); // recursion 
			    	}
		        
				}
		    }

		    closedir($handle);
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



	function startsWith($haystack,$needle,$case=true){
		if($case)
       		return strpos($haystack, $needle, 0) === 0;

   		return stripos($haystack, $needle, 0) === 0;
	}

}

?>