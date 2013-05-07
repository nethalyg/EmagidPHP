<?php 
namespace Emagid\Db;

/**
* base class for DB access and modeling .
*/
abstract class Db{

	/**
	* @var object - private db object, should be created once per instance of the encasing object.
	*/
	private $db ;

	/**
	* @var string id field for table
	*/	
	protected $fld_id = "id";

	/**
	* @var string table name 
	*/
	protected $table_name; 


	/** 
	* @var Array of fields . used for insert / update 
	*/ 
	protected $fields = array(); 


	/**
	* @var int id of the current record 
	*/
	public $id = 0 ;

	/**
	* @var Array relationships with other tables
	*/
	public $relationships = []  ;


	/**
	* Get or creates the db object
	* 
	* @return Object - the db object.
	*/
	protected function getConnection (){

		global $emagid ;

		if($this->db ==null ){

			require_once("ez_sql_core.php");
			require_once("ez_sql_mysql.php");

			$this->db = new \ezSQL_mysql(
				$emagid->connection_string->username, 
				$emagid->connection_string->password,  // pwd
				$emagid->connection_string->db_name,  // dbname 
				$emagid->connection_string->host);
		}

		return $this->db;
	}
        


	



/**
	* get list from a table
	* @param $params Array - conditions 
	*		 $params = array(
	*						"sql" => sql statement //If this param is set, all other params would be DISABLED
	*						"where" => array(field_name => handle, field_name => handle) //where condition for "=" and "AND"  only, NO "OR", "LIKE" or anyothers 
	*						"orderBy" => field_name 
	*						"sort" => ASC or DESC 
	*						"limit" => 10 //number
	*						"offset" => 10 //number
	*						);
	* @return Array array of objects from the db table.
	*/
	function getList($params = array()){

		$db = $this->getConnection(); 

		
<<<<<<< HEAD

		$sql = "SELECT * FROM $this->table_name";

		if(isset($params['where'])){ // apply where conditions
			$sql.=$this->buildWhere($params['where']);
		}

		$list =  $db->get_results($sql);

=======
	if(isset($params['sql'])){
			//if sql is set, just execute it without apply any other params
			$sql = $params['sql'];
	
	}else{
			$sql = "SELECT * FROM $this->table_name";
	
			// apply where conditions
			if(isset($params['where'])){ // apply where conditions
				$sql.=" WHERE ". $this->buildWhere($params['where']);
			}
			
			// apply order and sort
			isset($params['orderBy'])? $orderBy = $params['orderBy'] : $orderBy = "1";
			//isset($params['sort'])? $sort = $params['sort'] : $sort = "ASC";
			$sql.= " ORDER BY {$orderBy}";
			
			// apply pagination
			if(isset($params['limit'])){
				$sql.= " LIMIT ".$params['limit'];
			}
			
			if(isset($params['offset'])){
				$sql.= " OFFSET ".$params['offset'];
			}
	}//close construct sql
	
		 $dbList = $db->get_results($sql); 
>>>>>>> refs/remotes/Upstream/master

		foreach($list as $item){

			$this->loadRelationShips($item);
		}

		return $list;
	}


	private function loadRelationShips(&$object){
		$db = $this->getConnection(); 

		foreach($this->relationships as $relationship){
			$local_id = $object->{$relationship['local']};

			$sql = sprintf("SELECT * FROM %s WHERE %s=%s", 
					$relationship['table_name'] , 
					$relationship['remote'],
					$local_id

					);

			
			if(isset($relationship['relationship_type']) && $relationship['relationship_type']=='many' )
				$list = $db->get_results($sql); 
			else 
				$list = $db->get_row($sql); 
			$object->{$relationship['name']} = $list;
		}
	}


	function buildWhere($where){
		$arr = [] ; 

		foreach($where as $key=>$val ){
			array_push(sprintf("(%s='%s')", $key,$val));
		}

		return implode(" AND ", $arr);
	}



	/**
	* get list from a table
	* @param $id int get a single record by id.
	* @return Object  object representing a single result line from the DB .
	*/
	function getItem($id){

		$db = $this->getConnection(); 



		$sql = "SELECT * FROM $this->table_name WHERE $this->fld_id=".$id;

		$row = $db->get_row($sql);;


		if(count($row)>0){
			// assign the values to the current object. 
			// required for the update/insert functionality .
			$arr = object_to_array($row);
			
			foreach($arr as $key => $val){
				$this->{$key}=$val;
			}

			$this->loadRelationShips($this);
		}

		return $this;
	}

	/**
	* Delete the current record 
	*/
	function delete($id){

		$db = $this->getConnection();
		$sql = "DELETE FROM $this->table_name WHERE $this->fld_id=".$id;
		$db->query($sql);
	}


	function get_row($sql){
		$db = $this->getConnection();


		return $db->get_row($sql);
	}

	/**
	* Insert / Update the current record 
	*/
	function save(){
	  $db = $this->getConnection();
		
		
		$vals = object_to_array($this);
		
		// array used for update 
		$update = array(); 
		
		// arrays used for insert
		$insert_names = array(); 
		$insert_vals = array(); 
		
		
		
		// build both insert and update arrays
		foreach($this->fields as $fld){
			
			$val = $db->escape($vals[$fld]);
			$val = is_numeric($val)?$val:sprintf("'%s'", $val);
			
			
			array_push($update , sprintf("%s=%s", $fld, $val));
			array_push($insert_names, $fld);
			array_push($insert_vals, sprintf("%s", $val));	
		}
		
		
		// decide whether we need an INSERT or an UPDATE, and build the SQL query.
		if($this->id == 0){
			$vals1 = implode(',', $insert_names );
			$vals2 = implode(',', $insert_vals );
			
			$sql = "INSERT INTO $this->table_name ($vals1) VALUES($vals2)";
		}else {
			$vals = implode(',', $update );
			$sql = "UPDATE $this->table_name SET $vals";
			$sql .= " WHERE id={$this->id}";
		}
		
		

		if($db->query($sql)){
				return true;
			}else{
				die($sql . "<br/>" . mysql_error());
				return false;
			}
		
	}
        /**
         * get the number of elements in a table
         * @return int  number
         */
        function getCount(){
            $db = $this->getConnection(); 

            $sql = "SELECT COUNT(*) FROM $this->table_name";
    
            return $db->get_var($sql);
        }
        
     
	/**
	* load the form fields into the object after submit using POST.
	*/
	function loadFromPost(){
		foreach($_POST as $key=>$val){
			$this->{$key} = $val;
		}
	}
	

	/**
	* load the form fields into the object after submit using GET.
	*/
	function loadFromGet(){
		foreach($_GET as $key=>$val){
			$this->{$key} = $val;
		}
	}


<<<<<<< HEAD
=======
	public function __get($name){

		// check if data was already created 
		if(isset($this->data[$name]))
			return $this->data[$name];




		foreach($this->relationships as $relationship){


			if($relationship['name'] == $name){


				if(isset($relationship['class_name'])){ // creating a strong named object 

					$class = $relationship['class_name'];

					$obj = new $class;

					$local_val = $this->{$relationship['local']};


					if($relationship['relationship_type']=='many'){
						$key = $relationship['remote']; 
						
						$obj = $obj->getList([
								'where' => [
									 $key => $local_val
								] 
							]);
					}else{
						$obj->getItem($local_val);
					}
					
					$this->data[$name] = $obj;
					$this->{$name} = $obj;

					return $obj; 

				}else { // creating a generic type 

					$this->{$name} = $this->loadChildren($relationship);
					$this->data[$name] = $this->loadChildren($relationship);

				}
				
			}
		}

	}


	function loadChildren($params){
		$db = $this->getConnection();

		$table = $params['table_name'];
		$local = $params['local'];
		$local_val = $this->{$local}; 
		$remote = $params['remote'];
		$relationship_type = $params['relationship_type'];

		if($relationship_type=='many'){
			return $db->get_results("SELECT * FROM $table WHERE $remote='$local_val'");
		}else {
			return $db->get_row("SELECT * FROM $table WHERE $remote='$local_val'");
			
		}

	}
        
             
         /**
         * get the number of elements in a table
         * @return int  number
         */
        function getCount(){
            $db = $this->getConnection(); 

            $sql = "SELECT COUNT(*) FROM $this->table_name";
    
            return $db->get_var($sql);
        }
        
        
        function getValue($field, $id){
            $db = $this->getConnection(); 

            $sql = "SELECT $field FROM $this->table_name WHERE $this->fld_id=".$id;
    
            return $db->get_var($sql);
   
        }
        


>>>>>>> refs/remotes/Upstream/master
}

?>
