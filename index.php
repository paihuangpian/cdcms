<?php
session_start();
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');

//隐藏Home
$arr    =    explode('/',$_SERVER['PHP_SELF']);
 if(count($arr) > 2 && $arr[2]!=='Admin' && $arr[2] !== 'admin' && $arr[2] !== 'Weixin' && $arr[2] !== 'weixin'){
 	
    define('BIND_MODULE','Home');
 }

// 获取域名前缀
$domain = explode('.',$_SERVER['HTTP_HOST']);
$_SESSION['domain']  =  $domain['1'].'.'.$domain['2'];



// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';