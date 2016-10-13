<?php
session_start();
    header("Content-Type:text/html;charset=utf-8");
    error_reporting( E_ERROR | E_WARNING );
    date_default_timezone_set("Asia/chongqing");
    include "Uploader.class.php";

    $base64 = isset($_POST["base64"]) ? true:false;

    //上传配置/Customer_Uploads/upload/image/
    $config = array(
        "savePath" => "../../Customer_Uploads/".$_SESSION['homeuser']['name']."/" ,             //存储文件夹
        "maxSize" => 10000 ,                   //允许的文件最大尺寸，单位KB
        "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
    );
    //上传文件目录
    $Path = "../../Customer_Uploads/".$_SESSION['homeuser']['name']."/";

    //背景保存在临时目录中
    $config[ "savePath" ] = $Path;
    $up = new Uploader("upfile", $config, $base64);
    $callback=$_GET['callback'];

    $info = $up->getFileInfo();
    /**
     * 返回数据
     */
    if($callback) {
        echo '<script>'.$callback.'('.json_encode($info).')</script>';
    } else {
        echo json_encode($info);
    }
?>