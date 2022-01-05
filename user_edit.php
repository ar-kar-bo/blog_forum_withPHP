<?php 
require_once 'inc/header.php';
if(isset($_GET['user'])){
    $slug = $_GET['user'];
    $user = DB::table('users')->where('slug',$slug)->getOne();
    if(!$user){
    Helper::redirect('404.php');

    }
    if($_SERVER['REQUEST_METHOD']=='POST'){
        // print_r($_POST);
        $res = User::update($_POST);
        if(isset($res) and $res == 'success'){
        Helper::redirect('user_edit.php?user='.$slug.'&success');
        }
    }
}else{
    Helper::redirect('404.php');
}

?> 
<div class="card card-dark">
        <div class="card-header bg-warning">
                <h3>Edit User</h3>
        </div>
        <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                        <?php   
                        if(isset($_GET['success'])){
                        ?>
                        <div class="alert alert-success">User Updated Success.</div>
                        <?php
                        }
                        ?>
                        <input type="hidden" name="slug" value="<?php echo $user->slug;?>">
                        <div class="form-group">
                                <label for="" class="text-white">Enter Username</label>
                                <input type="name" name="name" value="<?php echo $user->name;?>" class="form-control" placeholder="enter username">
                        </div>
                        <div class="form-group">
                                <label for="" class="text-white">Enter Email</label>
                                <input type="name" name="email" value="<?php echo $user->email;?>" class="form-control" placeholder="enter email">
                        </div>
                        <div class="form-group">
                                <label for="" class="text-white">Enter Password</label>
                                <input type="password" name="password" class="form-control" placeholder="enter username">
                        </div>
                        <div class="form-group">
                                <label for="" class="text-white">Choose Image File</label>
                                <input type="file" name="image" class="form-control">
                                <img src="<?php echo $user->image;?>" style="width: 200px;border-radius:20px;" alt="">
                        </div>
                        <input type="submit" value="Update" class="btn  btn-outline-warning">
                        <a href="<?php echo "user_edit.php?user=".$slug;?>">
                                <input type="button" value="Clear" class="btn  btn-outline-warning">
                        </a>
                        
                        <!-- <button class="btn btn-danger" onclick="clearForm()">Clear</button> -->
                </form>
        </div>
</div>
<?php

        require_once 'inc/footer.php';
        
?>