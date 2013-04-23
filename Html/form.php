<?php
namespace Emagid\Html ;


class Form {

	/**
	* @var $model the Db object
	*/
	private $model ; 

	/**
	* Constructor
	*
	* @param Object  $model - object extended from Db
	*/
	function __construct($model){
		$this->model = $model; 
	}


	function textBoxFor($field_name, $htmlObjects = []){
		if(!isset($htmlObjects['type']))
		{
			$htmlObjects['type'] = 'text';	
		}

		$html = sprintf("<input name=\"%s\"", $field_name);

		foreach($htmlObjects as $key=>$val){
			$html.=sprintf(" %s=\"%s\"", $key,$val);
		}

		if(isset($this->model->{$field_name})){
			$html.= sprintf(" value=\"%s\"", $this->model->{$field_name});
		}

		$html.=" />";

		return $html;


	}


	function textAreaFor($field_name, $htmlObjects = []){
		

		$html = sprintf("<textarea name=\"%s\"", $field_name);

		foreach($htmlObjects as $key=>$val){
			$html.=sprintf(" %s=\"%s\"", $key,$val);
		}


		$html.=">";

		if(isset($this->model->{$field_name})){
			$html.= $this->model->{$field_name};
		}
		$html.="</textarea>";


		return $html;


	}



	function dropDownListFor($field_name, $options = [], $label , $htmlObjects = []){
		

		$html = sprintf("<select name=\"%s\"", $field_name);

		foreach($htmlObjects as $key=>$val){
			$html.=sprintf(" %s=\"%s\"", $key,$val);
		}



		$html.=">";

		$val = isset($this->model->{$field_name})?$this->model->{$field_name}:""; 

		if($label){
			$html.= sprintf("<option value=\"\">%s</option>",$label);
		}

		if(array_keys($options) !== range(0, count($options) - 1)){ // the array is associative 
			foreach($options as $key=>$value){
				echo("<h1>$key=$value</h1>");
				$selected = $key == $val?"selected=\"selected\"":"";

				$html.= sprintf("<option value=\"%s\" %s>%s</option>",$key,$selected, $value);
			}
		}else{
			foreach($options as $value){
				$selected = $value == $val?"selected=\"selected\"":"";

				$html.= sprintf("<option value=\"%s\" %s>%s</option>",$value,$selected, $value);
			}
		}

		
		

		$html.="</select>";


		return $html;


	}

}
?>