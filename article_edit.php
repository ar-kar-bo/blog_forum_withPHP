<?php
require_once 'inc/header.php';
if(isset($_GET['slug'])){
    $slug = $_GET['slug'];
    $article = DB::table('articles')->where('slug',$slug)->getOne();
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(Helper::validation($_POST) == 'success'){
        print_r($_POST);
        // die();
        $post = Article::update($_POST);
        if(isset($post) and $post == 'success'){
            Helper::redirect('article_edit.php?slug='.$slug.'&success');
            }
    }else{
        $error = Helper::validation($_POST);
    }
}

?>

<div class="card card-dark">
        <div class="card-header">
                <h3>Edit Article</h3>
        </div>
        <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                        <?php
                        if(isset($_GET['success'])){
                        ?>
                        <div class="alert alert-success">Article Created Success.</div>
                        <?php
                        }
                        ?>
                        <input type="hidden" name="slug" value="<?php echo $slug;?>">
                        <?php
                        if(isset($error['title'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['title'];?></div>
                        <?php
                        }
                        ?>

                        <div class="form-group">
                                <label for="title" class="text-white">Enter Title</label>
                                <input type="text" id="title" name="title" value="<?php echo $article->title; ?>" class="form-control"
                                        placeholder="Enter Title">
                        </div>
                        <?php
                        if(isset($error['category'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['category'];?></div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                                <label for="category" class="text-white">Choose Category</label>
                                <select name="category_id" id="category" class="form-control">
                                    <?php
                                    $cat = DB::table('category')->get();
                                    
                                    foreach($cat as $c){
                                        if($c->id == $article->category_id){
                                    ?>
                                        
                                        <option selected value="<?php echo $c->id;?>"><?php echo $c->name;?></option>
                                        <?php
                                        }else{
                                        ?>
                                        <option value="<?php echo $c->id;?>"><?php echo $c->name;?></option>
                                        
                                    <?php
                                    }
                                    }
                                    ?>
                                    
                                </select>
                        </div>
                        <?php
                        if(isset($error['language'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['language'];?></div>
                        <?php
                        }
                        ?>
                        <div class="form-check form-check-inline">
                            <?php
                            $lang = DB::table('languages')->get();
                            $art_lang = DB::table('article_languages')->where('article_id',$article->id)->get();
                            foreach($lang as $l){
                            ?>
                            <span class="mr-2">
                            <?php
                                if(Helper::check($l->id,$art_lang)){
                            ?>
                                <input class="form-check-input" checked type="checkbox" name="language_id[]" value="<?php echo $l->id;?>" id="<?php echo $l->id;?>">
                            <?php                                
                                }else{                                
                            ?>
                                <input class="form-check-input" type="checkbox"                                name="language_id[]" value="<?php echo $l->id;?>" id="<?php echo $l->id;?>">
                            <?php
                                }
                            ?>
                            <label class="form-check-label"
                                    for="<?php echo $l->id;?>"><?php echo $l->name;?></label>
                            </span>
                            <?php
                            }
                            ?>
                            
                        </div>
                        <br><?php
                        if(isset($error['image'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['image'];?></div>
                        <?php
                        }
                        ?><br>
                        
                        <div class="form-group">
                                <label for="file">Choose Image</label>
                                <input type="file" id="file" class="form-control" name="image">
                                <img src="<?php echo $article->image;?>" style="width: 200px;border-radius:20px;" alt="">
                        </div>
                        <?php
                        if(isset($error['description'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['description'];?></div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                                <label for="description" class="text-white">Enter Description</label>
                                <textarea autofocus name="description" class="form-control" id="description"
                                        cols="30" rows="10"><?php echo $article->description;?></textarea>
                        </div>
                        <input type="submit" value="Update" class="btn  btn-outline-warning">
                        <a href="<?php echo "article_edit.php?slug=".$slug;?>">
                                <input type="button" value="Clear" class="btn  btn-outline-warning">
                        </a>
                    
                </form>
        </div>
</div>
<?php
require_once 'inc/footer.php';
?>