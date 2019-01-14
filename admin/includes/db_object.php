<?php


class Db_object {
    protected static $db_table ="users";
    public $errors = array();
    public $upload_errors_array = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
);
     
    public function set_file($file) {
        
    if(empty($file) || !$file || !is_array($file)) {
        $this->errors[]="There was no file uploaded here.";
        return false;
 }  elseif($file['error'] !=0) {
        $this->errors[]=$this->upload_errors_array[$file['error']];
        return false;
   }else {
        
    $this->user_image = basename($file['name']);
    $this->tmp_path = $file['tmp_name'];
    $this->type = $file['type'];
    $this->size = $file['size'];
    }
    
    }
    
    
 
        
public static function find_all(){
return static::find_by_query("SELECT * FROM ".static::$db_table."");
        
    }
    
    public static function find_by_id($id){
    $the_result_array = static::find_by_query("SELECT * FROM " . static::$db_table . " WHERE id = $id LIMIT 1");
   
        
return !empty($the_result_array) ? array_shift($the_result_array): false; 
   
        
/*if(!empty($the_result_array)){
    $first_item=array_shift($the_result_array);
    return $first_item;
}else{
    return false;
}
  */  
    }
    


	public static function find_by_query($sql) {
		global $database;
		$result_set = $database->query($sql);
		$the_object_array = array();
		while($row = mysqli_fetch_array($result_set)) {

		$the_object_array[] = static::instantiation($row);

		}

		return $the_object_array;

		}
    
        public static function instantiation($the_record){ // for found_user=the record to show in a table.
         $calling_class= get_called_class();
        $the_object = new $calling_class;    //the_object=user
        
       /* $the_object->id         =$found_user['id'];
        $the_object->username   =$found_user['username'];
        $the_object->password   =$found_user['password'];
        $the_object->first_name =$found_user['first_name'];
        $the_object->last_name  =$found_user['last_name'];  */
        
        foreach($the_record as $the_attribute => $value){
            
            if($the_object->has_the_attribute($the_attribute)){
                $the_object->$the_attribute = $value;
            }
            
        }
        
        return $the_object;
        
    }
    
    private function has_the_attribute($the_attribute){
        
        $object_properties = get_object_vars($this);
        
        return array_key_exists($the_attribute,$object_properties);
        //return property_exists($this, $the_attribute);
        
    }
    
    
    protected function properties() {
        
       $properties=array();
                     
        foreach(static::$db_table_fields as $db_field){
            if(property_exists($this,$db_field)) {
                $properties[$db_field]=$this->$db_field;
                
            }
        }
        return $properties;
    }
    
    
    
    
    public function save() {
        global $database;
        return isset($this->id) ? $this->update() : $this->create();
    }
    
    protected function clean_properties(){
        global $database;
    
    $clean_properties =array();
    
    foreach($this->properties() as $key => $value){
      
        $clean_properties[$key]=$database->escape_string($value);
            
        }
    return $clean_properties;
    }
    
    public function create() {
        global $database;
        
        $properties=$this->clean_properties();
        
    $sql  ="INSERT INTO " .static::$db_table.  "(".implode(',',array_keys($properties)).")" ;
    $sql .="VALUES ('".implode("','", array_values($properties)) ."')";
    /*$sql .= $database->escape_string($this->username) ."', '";
    $sql .= $database->escape_string($this->password) . "', '";
    $sql .= $database->escape_string($this->first_name) ."', '";
    $sql .= $database->escape_string($this->last_name) ." ')";*/
    
    if($database->query($sql)) {
        $this->id = $database->the_insert_id();
        return true;
    } else {
        return false;
    }
    
    }//create method
    
    public function update(){
     global $database;
     $properties=$this->clean_properties();
        
        $properies_pairs=array();
        foreach($properties as $key => $value) {
            
            $properies_pairs[]="{$key} = '{$value}'";
        }
        
        
        
    $sql  ="UPDATE " .static::$db_table.  " SET ";
    $sql .=implode(",", $properies_pairs);
    $sql .= " WHERE id  = " . $database->escape_string($this->id);
    $database->query($sql);
        
 
    return (mysqli_affected_rows($database->connection) == 1) ? true : false;
    
    }//end of update
    
    public function delete() {
        global $database;
    $sql  ="DELETE FROM " .static::$db_table.  " ";
    $sql .= "WHERE id =" . $database->escape_string($this->id);
    $sql .= " LIMIT 1";
    $database->query($sql);
        
    return(mysqli_affected_rows($database->connection) ==1)? true : false;
    }
    
    public static function count_all() {
        
        global $database;
        
        $sql="SELECT COUNT(*) FROM " . static::$db_table;
        $result_set= $database->query($sql);
        $row= mysqli_fetch_array($result_set);
        
        return array_shift($row);
        
    }
    
    
}//en of class






?>