<?php
class Article{
    public static function all(){
        $data = DB::table('articles')->orderBy("id",'DESC')->paginate(4);
        foreach($data['data'] as $k=>$d){
            $data['data'][$k]->comment_count = DB::table('article_comments')->where('article_id',$d->id)->count();
            $data['data'][$k]->like_count = DB::table('article_likes')->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function search($search){
        $data = DB::table('articles')->where('title','like',"%$search%")->orderBy("id",'DESC')->paginate(2,"search=$search");
        foreach($data['data'] as $k=>$d){
            $data['data'][$k]->comment_count = DB::table('article_comments')->where('article_id',$d->id)->count();
            $data['data'][$k]->like_count = DB::table('article_likes')->where('article_id',$d->id)->count();
        }
        return $data;
    }
    public static function your_article($user_id){
        $data = DB::table('articles')->where('user_id',$user_id)->orderBy("id",'DESC')->paginate(2,"your_article");
        foreach($data['data'] as $k=>$d){
            $data['data'][$k]->comment_count = DB::table('article_comments')->where('article_id',$d->id)->count();
            $data['data'][$k]->like_count = DB::table('article_likes')->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function articleByCategory($slug){
        $category_id = DB::table('category')->where('slug',$slug)->getOne()->id;
        $data = DB::table('articles')->where('category_id',$category_id)->orderBy("id",'DESC')->paginate(2,"category=$slug");
        foreach($data['data'] as $k=>$d){
            $data['data'][$k]->comment_count = DB::table('article_comments')->where('article_id',$d->id)->count();
            $data['data'][$k]->like_count = DB::table('article_likes')->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function articleByLanguage($slug){
        $language_id = DB::table('languages')->where('slug',$slug)->getOne()->id;
        $data = DB::raw("SELECT * FROM article_languages INNER JOIN articles ON articles.id = article_languages.article_id WHERE article_languages.language_id = $language_id")->orderBy("articles.id",'DESC')->paginate(2,"language=$slug");
        foreach($data['data'] as $k=>$d){
            $data['data'][$k]->comment_count = DB::table('article_comments')->where('article_id',$d->id)->count();
            $data['data'][$k]->like_count = DB::table('article_likes')->where('article_id',$d->id)->count();
        }
        return $data;
    }

    public static function detail($slug){
        $data = DB::table('articles')->where('slug',$slug)->getOne();

        //try to get like_count
        $data->like_count = DB::table('article_likes')->where('article_id',$data->id)->count();
        //try to get comment_count
        $data->comment_count = DB::table('article_comments')->where('article_id',$data->id)->count();
        //try to get category
        $data->category = DB::table('category')->where('id',$data->category_id)->getOne();
        //try to get language
        $data->languages = DB::raw("SELECT languages.id,languages.slug,languages.name FROM `article_languages` LEFT JOIN languages ON languages.id = article_languages.language_id WHERE article_id = $data->id")->get();
        //try to get comments
        // $data->comments = DB::table('article_comments')->where('article_id',$data->id)->get();
        $data->comments = DB::raw("SELECT article_comments.comment,users.name,users.image FROM `article_comments` LEFT JOIN users ON article_comments.user_id = users.id WHERE article_id=$data->id")->get();
        return $data;

    }
    public static function create($request){
        //image upload
        $image = $_FILES['image'];
        $image_name = $image['name'];
        $path = "assets/article/".$image_name;
        $tmp_name = $image['tmp_name'];    
        if(move_uploaded_file($tmp_name,$path)){
            $article = DB::create('articles',[
                'user_id'=>User::auth()->id,
                'category_id'=>$request['category_id'],
                'slug'=> Helper::slug($request['title']),
                'title'=> $request['title'],
                'image'=>$path,
                'description'=>$request['description']
            ]);
            if($article){
                foreach($request['language_id'] as $id){
                    DB::create('article_languages',[
                        'article_id'=>$article->id,
                        'language_id'=>$id
                    ]);
                }
                return 'success';
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
    public static function update($request){
        $article = DB::table('articles')->where('slug',$request['slug'])->getOne();

        $image = $_FILES['image'];
        $image_name = $image['name'];
        $path = "assets/article/".$image_name;
        $tmp_name = $image['tmp_name'];
        if(!move_uploaded_file($tmp_name,$path)){
            $path = $article->image;        
        }    

        $update_article = DB::update('articles',[
            'category_id'=>$request['category_id'],
            'title'=> $request['title'],
            'image'=>$path,
            'description'=>$request['description']
        ],$article->id);

        if($update_article){
            
            // foreach($request['language_id'] as $id){
            //     DB::create('article_languages',[
            //         'article_id'=>$article->id,
            //         'language_id'=>$id
            //     ]);
            // }
            return 'success';
        }
        return false;
            
    }
    public static function delete($slug){
        $id = DB::table('articles')->where('slug',$slug)->getOne()->id;
        $data = DB::delete('articles',$id);
        return $data;
        
    }
    public static function save($slug){
        if(isset($request['saved'])){
    
            $user_id = $request['user_id'];
            $article_id = $request['article_id'];
            $save_exist = DB::table('article_saved')->where('user_id',$user_id)->and('article_id',$article_id)->getOne();
            if($save_exist){
                $user = DB::delete('article_saved',$save_exist->id);      
            }else{
                $user = DB::create('article_saved',[
                    'user_id' => $user_id,
                    'article_id' => $article_id
                ]);               
            }
            
        }
    }
    
}

/*
data
Array
(
    [data] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 2
                    [article_id] => 2
                    [language_id] => 3
                    [user_id] => 2
                    [category_id] => 1
                    [slug] => slug2
                    [title] => title2
                    [image] => https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlNe90xHTFPXP67MEk0gnul2xGJsx_M8SDXuFybm0OU_ShyFO0RxQeuChooGNXjAAyUOw&usqp=CAU
                    [description] => description 2
                    [comment_count] => 0
                    [like_count] => 2
                )

        )

    [total] => 1
    [total_page_no] => 1
    [pre_url] => ?page=1&language=laravel
    [next_url] => ?page=1&language=laravel
)
*/
/*stdClass Object
(
    [id] => 3
    [user_id] => 3
    [category_id] => 2
    [slug] => slug3
    [title] => title3
    [image] => image3
    [description] => description 3
    [like_count] => 1
    [comment_count] => 1
    [category] => stdClass Object
        (
            [id] => 2
            [slug] => web-dev
            [name] => Web Development 
        )

    [languages] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 1
                    [slug] => javascript
                    [name] => JavaScript
                )

            [1] => stdClass Object
                (
                    [id] => 2
                    [slug] => php
                    [name] => PHP
                )

        )

    [comments] => Array
        (
            [0] => stdClass Object
                (
                    [comment] => this is good
                    [name] => aung aung
                    [image]=> profile.jpg
                )

        )

) */