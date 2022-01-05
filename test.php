<?php
require_once 'core/autoload.php';
print_r($_SESSION);
echo $_SESSION['user_id'];
$user = DB::table('users')->where('id',1)->getOne();
print_r($user);
