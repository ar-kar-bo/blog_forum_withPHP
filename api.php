<?php
require_once 'core/autoload.php';
$request = $_GET;

if(isset($request['like'])){
    
    $user_id = $request['user_id'];
    $article_id = $request['article_id'];
    $like_exist = DB::table('article_likes')->where('user_id',$user_id)->and('article_id',$article_id)->getOne();
    if($like_exist){
        echo  'already exist';
        // print_r($like_exist);
    }else{
        $user = DB::create('article_likes',[
            'user_id' => $user_id,
            'article_id' => $article_id
        ]);
        
        if($user){
            $count = DB::table('article_likes')->where('article_id',$article_id)->count();
            echo $count;
        }
    }
    
}
if(isset($_POST['comment'])){
    $user_id = User::auth()->id;
    $article_id = $_POST['article_id'];
    $comment = $_POST['comment'];

    $comment = DB::create('article_comments',[
        'user_id'   =>  $user_id,
        'article_id'=>  $article_id,
        'comment'   =>  $comment
    ]);
    if($comment){
        $cmt = DB::table('article_comments')->where('article_id',$article_id)->orderBy('id','desc')->get();
        $html = "";
        foreach($cmt as $c){
            $user = DB::table('users')->where('id',$c->user_id)->getOne();
            $html .= "<div class='card-dark mt-1'>
            <div class='card-body'>
                    <div class='row'>
                            <div class='col-md-1'>
                                    <img src='$user->image'
                                            style='width:50px;border-radius:50%'
                                            >
                            </div>
                            <div
                                    class='col-md-4 d-flex align-items-center'>
                                    $user->name
                            </div>
                    </div>
                    <hr>
                    <p>$c->comment</p>
            </div>
    </div>";
        }
        echo $html;
    }
    
}
