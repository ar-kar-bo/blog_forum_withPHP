<?php
class DB
{
    private static $dbh=null;
    private static $res,$data,$count,$sql;

    public function __construct()
    {
        self::$dbh = new PDO('mysql:host=localhost;dbname=php_project','root','');
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    public function query($params=[])
    {
        // echo self::$sql.'<br>';//person essential 😁😁
        self::$res = self::$dbh->prepare(self::$sql);
        self::$res->execute($params);        
        return $this;
    }

    public function get()
    {
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        return self::$data;
    }

    public function getOne()
    {
        $this->query();
        self::$data = self::$res->fetch(PDO::FETCH_OBJ);
        return self::$data;
    }

    public function count()
    {
        $this->query();
        self::$count = self::$res->rowCount();
        return self::$count;
    }

    public static function raw($sql)
    {
        self::$sql = $sql;
        $db = new DB();
        return $db;
    }

    public static function table($table)
    {
        $sql = "select * from $table";
        self::$sql = $sql;
        $db = new DB();
        return $db;//same like $this
    }

    public static function create($table,$data)
    {
        $db = new DB();
        $str_col = implode(',',array_keys($data));
        $v ="?";
        for($i=1;$i<count($data);$i++){
            $v.=",?";
        }
        $sql = "insert into $table($str_col) values($v)";
        self::$sql = $sql;
        $values = array_values($data);
        $db->query($values);
        $id = self::$dbh->lastInsertId();
        return DB::table($table)->where('id',$id)->getOne();
    }

    public static function update($table,$data,$id)
    {
        $db = new DB();
        $str_col = implode('=?,',array_keys($data))."=?";
        $sql = "update $table set $str_col where id=$id";
        self::$sql = $sql;
        $values = array_values($data);
        $db->query($values);
        return DB::table($table)->where('id',$id)->getOne();
    }

    public static function delete($table,$id)
    {
        $db = new DB();
        $sql = "delete from $table where id=$id";
        self::$sql = $sql;
        $db->query();
        return 'deleted success.';
    }
    
    public function where($col,$operator,$value='')
    {
        if(func_num_args()==2){   
            self::$sql .= " where $col = '$operator'";
        }else{
            self::$sql .= " where $col $operator '$value'";
        }               
        return $this;
    }
    
    public function and($col,$operator,$value='')
    {
        if(func_num_args()==2){   
            self::$sql .= " and $col = '$operator'";
        }else{
            self::$sql .= " and $col $operator '$value'";
        }              
        return $this;
    }

    public function or($col,$operator,$value='')
    {
        if(func_num_args()==2){   
            self::$sql .= " or $col = '$operator'";
        }else{
            self::$sql .= " or $col $operator '$value'";
        }              
        return $this;
    }

    public function orderBy($col,$value)
    {
        self::$sql .= " order by $col $value";
        return $this;
    }

    public function paginate($records_per_page,$append = '')
    {
        if(isset($_GET['page'])){
            $page_no = $_GET['page'];
        }
        if(!isset($_GET['page']) or $_GET['page']<1){
            $page_no = $_GET['page'] = 1;
        }
        //get total count
        $count = $this->count();
        //select * from limit 0,5
        $index = ($page_no - 1) * $records_per_page;
        self::$sql.=" limit $index,$records_per_page";
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        
        $total_page_no =floor($count/$records_per_page)+1;
        if($page_no>1){
            $prev_page ="?page=".($page_no - 1);
        }else{
            $prev_page ="?page=1";
        }

        if($page_no<$total_page_no){
            $next_page ="?page=".($page_no + 1);
        }else{
            $next_page ="?page=".$total_page_no;
        }
        if($append != ''){
            $append = '&'.$append;
        }
        $data = [
            'data' => self::$data,
            'total'=> $count,
            'total_page_no'=> $total_page_no,
            'pre_url'=>$prev_page.$append,
            'next_url'=>$next_page.$append
        ];
        return $data;

    }
    
}



    
