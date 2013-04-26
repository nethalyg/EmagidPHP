<?php
/** 
* Global functions 
*/ 


/** 
	* convert an array to an object 
	*
	* @param Array
	* @return strClass 
	*/
	function array_to_object($array) {
	  $obj = new stdClass;

	  foreach($array as $k => $v) {
	     if(is_array($v)) {
	        $obj->{$k} = array_to_object($v); //RECURSION
	     } else {
	        $obj->{$k} = $v;
	     }
	  }

	  return $obj;
	} 

	/** 
	* convert object to an array
	*
	* @param $data Object 
	* @return Array named array 
	*/
	function object_to_array($data)
	{
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = object_to_array($value);
	        }
	        return $result;
	    }
	    return $data;
	}



	function clone_into($source, &$target){

		  foreach($source as $k => $v) {
		     if(is_array($v)) {
		        $target->{$k} = array_to_object($v); //RECURSION
		     } else {
		        $target->{$k} = $v;
		     }
		  }

	}



	
?>