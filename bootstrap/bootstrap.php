<?php

include __DIR__ .'/../config/credentials.php';
include __DIR__ .'/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();
$capsule->addConnection([
    'driver'   =>'mysql',
    'host'     =>$db_host,
    'database' =>$db_name,
    'username' =>$db_username,
    'password' =>$db_pass,
    'charset'  =>'utf8',
    'collation'=>'utf8_general_ci',
    'prefix'   =>''
]);

$capsule->bootEloquent();