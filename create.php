<?php
require_once 'inc/header.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // $post = Article::create($_POST);
    if(Helper::validation($_POST) == 'success'){
        $post = Article::create($_POST);
    }else{
        $error = Helper::validation($_POST);
    }
}
?>

<div class="card card-dark">
        <div class="card-header">
                <h3>Create New Article</h3>
        </div>
        <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                        <?php
                        if(isset($post) and $post == 'success'){
                        ?>
                        <div class="alert alert-success">Article Created Success.</div>
                        <?php
                        }
                        ?>
                        <?php
                        if(isset($error['title'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['title'];?></div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                                <label for="title" class="text-white">Enter Title</label>
                                <input type="text" id="title" name="title" class="form-control"
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
                                    ?>
                                        <option value="<?php echo $c->id;?>"><?php echo $c->name;?></option>
                                    <?php
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
                            foreach($lang as $l){
                            ?>
                            <span class="mr-2">
                                <input class="form-check-input" type="checkbox"
                                        name="language_id[]" value="<?php echo $l->id;?>" id="<?php echo $l->id;?>">
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
                        </div>
                        <?php
                        if(isset($error['description'])){
                        ?>
                        <div class="alert alert-warning"><?php echo $error['description'];?></div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                                <label for="" class="text-white">Enter Description</label>
                                <textarea name="description" class="form-control" id=""
                                        cols="30" rows="10"></textarea>
                        </div>
                        <input type="submit" value="Create"
                                class="btn  btn-outline-warning">
                </form>
        </div>
</div>
<?php
require_once 'inc/footer.php';
?>