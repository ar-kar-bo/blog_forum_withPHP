<?php
require_once 'inc/header.php';
if(isset($_GET['slug'])){
    $article = Article::detail($_GET['slug']);
}else{
    Helper::redirect('404.php');   
}
?>


<div class="card card-dark">
    <div class="card-body">
        <div class="row">
                <div class="col-md-12">
                        <div class="card card-dark">
                                <div class="card-body">
                                        <div class="row">
                                                <!-- icons -->
                                                <div class="col-md-4">
                                                        <div class="row">
                                                                <div
                                                                        class="col-md-4 text-center">
                                                                        <?php
                                                                        $user_id = User::auth()? User::auth()->id:0;
                                                                        $article_id = $article->id;

                                                                        ?>
                                                                        <i      
                                                                                id="like"
                                                                                class="fas fa-heart text-warning" user_id="<?php echo $user_id;?>" article_id="<?php echo $article_id;?>">
                                                                        </i>
                                                                        <small id="like_count" class="text-muted"><?php echo $article->like_count;?></small>
                                                                </div>
                                                                <div
                                                                        class="col-md-4 text-center">
                                                                        <i
                                                                                class="far fa-comment text-dark"></i>
                                                                        <small
                                                                                class="text-muted"><?php  echo $article->comment_count;?></small>
                                                                </div>

                                                        </div>
                                                </div>
                                                <!-- Icons -->

                                                <!-- Category -->
                                                <div class="col-md-4">
                                                        <div class="row">
                                                                <div
                                                                        class="col-md-12">
                                                                        <a href=""
                                                                                class="badge badge-primary"><?php echo $article->category->name;?></a>

                                                                </div>
                                                        </div>
                                                </div>
                                                <!-- Category -->


                                                <!-- Category -->
                                                <div class="col-md-4">
                                                        <div class="row">
                                                                <div class="col-md-12">
                                                                        <?php
                                                                        foreach($article->languages as $language){                                                   ?>
                                                                        <a href="" class="badge badge-success">
                                                                        <?php echo $language->name;?>
                                                                        </a>
                                                                        <?php
                                                                        }
                                                                        ?>              
                                                                </div>
                                                        </div>
                                                </div>
                                                <!-- Category -->

                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <br>
        <div class="col-md-12">
                
                
                <h3><?php echo $article->title;?></h3>
                <p><?php echo $article->description;?>
                </p>
        </div>
                <!-- create comment -->
        <div class="card card-dark" id="cmtt">
                <div class="card-body">
                        <form method="POST" id="frmCmt">
                                <input id="comment" type="text" placeholder="Enter Comment" class="form-control">
                                <input id="btn" type="submit" value="Create" class="btn btn-outline-warning float-right">
                        </form>
                </div>
        </div>
        <!-- Comments -->
        <div class="card card-dark">
                <div class="card-header">
                        <h4>Comments</h4>
                </div>
                <div class="card-body">
                        <div id="comment_list">
                                <!-- Loop Comment -->
                        <?php
                        foreach($article->comments as $comment){
                        ?>
                        <div class="card-dark mt-1">
                                <div class="card-body">
                                        <div class="row">
                                                <div class="col-md-1">
                                                        <img src="<?php echo $comment->image?>"
                                                                style="width:50px;border-radius:50%"
                                                                alt="">
                                                </div>
                                                <div
                                                        class="col-md-4 d-flex align-items-center">
                                                        <?php echo $comment->name;?>
                                                </div>
                                        </div>
                                        <hr>
                                        <p><?php echo $comment->comment;?></p>
                                </div>
                        </div>
                        <?php
                        }
                        ?>
                        </div>
                        
                        
                </div>
        </div>
    </div>
</div>


<?php
require_once 'inc/footer.php';
?>
<script>
//comment


var frmCmt = document.getElementById("frmCmt");
frmCmt.addEventListener("submit",function(e){
        e.preventDefault();
        var data = new FormData();
        data.append("comment",document.getElementById("comment").value);
        data.append("article_id",<?php echo $article->id;?>);
        axios.post('api.php',data)
        .then(function(res){
                console.log(res.data);
                document.getElementById('comment_list').innerHTML = res.data;
        });
        

});
//like
var like = document.getElementById("like");
var like_count = document.getElementById("like_count");

like.addEventListener("click",function(){
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

        
});

if(like.getAttribute('user_id') == 0){
        document.getElementById("cmtt").style.display = "none" ;
}
</script>