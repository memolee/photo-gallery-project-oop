<?php class User2{
    
    
        public id;
        public username;
        public password;
        public first_name;
        public last_name;
    
public static function find_all_query(){
    global $database;
return self::find_this_query("SELECT*FROM users");
}
    //we will call the method outside and loop trough it. // if you want to check your method go to admin_content. When you go there first extanciate it user class.
 // if you make it static you dont have to call class every time. 
    
    //instead of this; $variabe1= new Classname , $variable2= $user->method.
    //you can use this; $variable1= User::method.(it is too simple)
    
    public static function find_user_by_id($user_id){
       $result_set=self::find_this_query("SELECT * FROM users WHERE id=$user_id LIMIT 1");// result set meaning sonuç kümesi.
        $found_user=mysqli_fetch_array($result_set)
return $found_user;
    }
        
 public static function find_this_query($sql){
        global $database;
 $result_set=$database->query($sql);// result set meaning sonuç kümesi.
     $the_object_array = array();
     while($row=mysqli_fetch_array($result_set)){
         $the_object_array[]= self::instantiation($row);
         
     }
return $the_object_array;


}
    // we first stored the properties with $object_properties. then we check if our attribute in this properties. if it has, then we can loop in foreach loop
    public static function instantiation($the_record){
        $the_object = new self;
        foreach($the_object as $the_attribute =>$value){
            if($the_object->has_the_attribute($the_attribute)){
               $the_object->$the_attribute=$value;
            }
            
        }
        return the_object;
        
        private static function has_the_attribute($the_attribute){
            $object_properties= get_object_vars($this);
           return array_key_exists($the_attribute,$object_properties);
        }
    }

}

?>