<?php
class Helper{
    public static function redirect($page)
    {
        header("Location:$page");
    
    }
    public static function filter($str)
    {
        $str = trim($str);
        $str = stripslashes($str);
        $str = htmlspecialchars($str);
        return $str;

    }
    public static function slug($str)
    {
        $str = strtolower($str);
        $str = str_replace(' ','-',$str);
        $str .= '-'.time();
        return $str;
    }

    public static function check($id,$lang){
        foreach($lang as $l){
            if($l->language_id == $id){        
                return true;
            }
        }
        return false;
    }

    public static function validation($request){
        $error = [];
        if(isset($request)){
            if(empty($request['title'])){
                $error['title'] = "Write A Title!";
            }
            if(empty($request['category_id'])){
                $error['category'] = "Choose A Category!";
            }
            if(empty($request['language_id'])){
                $error['language'] = "Select Language!";
            }
            if(empty($_FILES['image'])){
                $error['image'] = "Put Cover Image!";
            }
            if(empty($request['description'])){
                $error['description'] = "Write A Description!";
            }
            if(count($error)){
                
                return $error;
            }else{
                return 'success';
            }
        }
    }
   
}
