<?php

use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\Help;

require_once 'inc/header.php';
$user_id = User::auth()? User::auth()->id:0;
if(isset($_GET['category'])){
    $slug = $_GET['category'];
    $article = Article::articleByCategory($slug);
}elseif(isset($_GET['language'])){
    $slug = $_GET['language'];
    $article = Article::articleByLanguage($slug);
}elseif(isset($_GET['search'])){
    $search = $_GET['search'];
    // echo $search;
    // print_r($_GET);
    $article = Article::search($search);
    // die();
}elseif(isset($_GET['your_article'])){
    $article = Article::your_article($user_id);
    $alert = "No Article";
}elseif(isset($_GET['delete'])){
    $alert = Article::delete($_GET['delete']);
    $article = Article::all();
}else{
    $article = Article::all();
}


?>
<div class="card card-dark">
    <div class="card-body">
        <a href="<?php echo $article['pre_url'];?>" class="btn btn-danger">Prev Posts</a>
        <a href="<?php echo $article['next_url'];?>" class="btn btn-danger float-right">Next Posts</a>
    </div>
</div>
<div class="card card-dark">
    <div class="card-body">
        <div class="row">
            <!-- Loop this -->
            <?php
            
            foreach($article['data'] as $d){
                    ?>
                <div class="col-md-4 mt-2">
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top"
                            src="<?php echo $d->image;?>"
                            alt="Card image cap">
                    <div class="card-body">
                            <h5 class="text-dark"><?php echo $d->title;?></h5>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div
                                    class="col-md-3 text-center">
                                    <i class="fas fa-heart text-warning" id="like<?php echo $d->id;?>"
                                        onclick="like(<?php echo $d->id;?>)"
                                         user_id="<?php echo $user_id;?>" article_id="<?php echo $d->id;?>">
                                </i>
                                        
                                    <small id="like_count<?php echo $d->id;?>"
                                            class="text-muted"><?php echo $d->like_count;?></small>
                            </div>
                            <div
                                    class="col-md-3 text-center">
                                    <i
                                            class="far fa-comment text-dark"></i>
                                    <small
                                            class="text-muted"><?php echo $d->comment_count;?></small>
                            </div>
                            <div
                                    class="col-md-3 text-center">
                                    <a href="<?php echo "detail.php?slug=$d->slug";?>"
                                            class="badge badge-warning p-1">View</a>
                            </div>
                            <?php
                                    if(User::auth()){
                                    ?> 
                            <div
                                    class="col-md-3 text-center">
                                    <a href="#"
                                            class="badge badge-warning p-1 dropdown-toggle" id="option<?php echo $d->id;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">option</a>
                                    <div class="dropdown-menu" aria-labelledby="option<?php echo $d->id;?>">
                                    <?php
                                    if($d->user_id == User::auth()->id){
                                    ?> 
                                    <a class="dropdown-item" href="<?php echo "index.php?delete=$d->slug";?>">Delete</a>
                                    <a class="dropdown-item" href="<?php echo "article_edit.php?slug=$d->slug";?>">Edit</a>
                                    <?php
                                    }
                                    ?> 
                                    <a class="dropdown-item" href="api.php" id="save_exist<?php echo $d->id;?>"
                                        onclick="save(<?php echo $d->id;?>)"
                                        user_id="<?php echo $user_id;?>" article_id="<?php echo $d->id;?>">Save</a>
                                                                     
                                </div>
                            </div>
                            <?php
                            }
                            ?> 
                        </div>

                    </div>
                </div>
            </div>
                    <?php
            }
            ?>
            
        </div>
    </div>
</div>
<?php 
require_once 'inc/footer.php';
?>
<script>
    //like
function like(id){
var like = document.getElementById(`like${id}`);
var like_count = document.getElementById(`like_count${id}`);

        var user_id = like.getAttribute('user_id');
        var article_id = like.getAttribute("article_id");
        // alert(user_id+article_id);  
        if(user_id == 0){
                location.href ='login.php';
        }else{
        axios.get(`api.php?like&user_id=${user_id}&article_id=${article_id}`)
        .then(function(res){
                if(res.data == 'already exist'){
                        toastr.warning('Already Like');
                }
                if(Number.isInteger(res.data)){
                        like_count.innerHTML = res.data;
                        toastr.success('Liked Success.');
                }
                
        })
        }
}
function save(id){
var like = document.getElementById(`save${id}`);
var save_exist = document.getElementById(`save_exist${id}`);

        var user_id = like.getAttribute('user_id');
        var article_id = like.getAttribute("article_id");
        // alert(user_id+article_id);  
        axios.get(`api.php?save&user_id=${user_id}&article_id=${article_id}`)
        .then(function(res){
                if(res.data == 'Unsave'){
                        save_exist.innerHTML = 'Save';
                        toastr.warning('Unsave');
                }
                if(res.data == 'Saved'){
                        save_exist.innerHTML = 'Unsave';
                        toastr.success('Saved');
                }
                
        })
        }
</script>