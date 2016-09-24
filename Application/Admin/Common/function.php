<?php 
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
function msg( $msg, $state, $url=null ){
	return [ 'msg'=>$msg, 'state'=>$state, 'url'=>$url ];
}
function push(){
	$host = parse_url($_SERVER['HTTP_REFERER']);
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $k == $host['host'] ){
			return true;
		}
	}
	return false;
}

function ajaxData( $arr, $state, $url=null ){
	return [ 'arr'=>$arr, 'status'=>$state, 'url'=>$url ];
}


function Referer(){
	$host = parse_url($_SERVER['HTTP_REFERER']);
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $v == $host['host'] ){
			return true;
		}
	}
	return false;
}

function isServerName(){
	$name = $_SERVER["SERVER_NAME"];
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $v == $name ){
			return true;
		}
	}
	return false;
}

?>