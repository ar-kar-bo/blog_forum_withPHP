<?php

use Illuminate\Validation\Concerns\FilterEmailValidation;
use PHPUnit\TextUI\Help;

class User{

    public static function auth()
    {
        if(isset($_SESSION['user_id'])){
            $user_id = $_SESSION['user_id'];
            return DB::table('users')->where('id',$user_id)->getOne();
        }
        return false;
        
    }
/*stdClass Object(
    [id] => 
    [name] =>
    [email] => 
    [password] => $2y$10$q6stng8/IoEgz1DJ
    [image] => 
) */
    public function login($request)
    {
        $error = [];
        $email = Helper::filter($request['email']);
        $password = Helper::filter($request['password']);

        if(empty($request['email'])){
            $error[] = "Email Field is required!";
        }
        if(!filter_var($request['email'],FILTER_VALIDATE_EMAIL)){
            $error[] = "Invalid Email Format!";
        }
        if(empty($request['password'])){
            $error[] = "Password Field is required!";
        }
        if(count($error)){
            return $error;
        }

         //check email
         $user = DB::table('users')->where('email',$email)->getOne();
         //password verify
         if($user){
                 
                 $db_password = $user->password;//hash
                 if(password_verify($password,$db_password)){
                         $_SESSION['user_id'] =$user->id;
                         return 'success';
                 }else{
                         //password wrong
                         $error[] = 'Wrong Password';
                 }
         }else{
                 //email not found
                 $error[] = 'Wrong Email';
         }
         return $error;
    }
    public function register($request)
    {
        $error = [];
        if(isset($request)){
            if(empty($request['name'])){
                $error[] = "Name Field is required!";
            }
            if(empty($request['email'])){
                $error[] = "Email Field is required!";
            }
            if(!filter_var($request['email'],FILTER_VALIDATE_EMAIL)){
                $error[] = "Invalid Email Format!";
            }
            if(empty($request['password'])){
                $error[] = "Password Field is required!";
            }
            //check email already exist
            $user = DB::table('users')->where('email',$request['email'])->getOne();
            if($user){
                $error[] = "Email already Exist";
            }




            if(count($error)){
                
                return $error;
            }else{
                //insert data
                $user = DB::create('users',[
                    'name'      =>  Helper::filter($request['name']),
                    'slug'      =>  Helper::slug($request['name']),
                    'email'     =>  Helper::filter($request['email']),
                    'password'  =>  password_hash($request['password'],PASSWORD_BCRYPT)
                ]);
                //session user_id
                $_SESSION['user_id']=$user->id;
                
                return 'success';
            }
        }
        
    }
    public static function update($request)
    {
        $user = DB::table('users')->where('slug',$request['slug'])->getOne();
        if($request['password']){
            //new password 
            $password = password_hash($request['password'],PASSWORD_BCRYPT);

        }else{
            //old password
            $password = $user->password;
        }

        if($request['email']){
            $email = Helper::filter($request['email']);
        }else{
            $email = $user->email;
        }

        if($request['name']){
            $name =  Helper::filter($request['name']);
        }else{
            $name = $user->name;
        }
        $image = $_FILES['image'];
        $image_name = $image['name'];
        $path = "assets/images/".$image_name;
        $tmp_name = $image['tmp_name'];
        if(!move_uploaded_file($tmp_name,$path)){
            $path = $user->image;        
        }

        DB::update("users",[
            'name'=>$name,
            'image'=>$path,
            'email'=>$email,
            'password'=>$password
        ],$user->id);
        return 'success';
    }
}