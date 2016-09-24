<?php 

namespace Admin\Controller;
use Think\Controller;
class AdminsController extends CommonController{

	public function index(){
		$get = $this->Get;
		$content = $this->fnPage( 'Admins', 'Admins/index', 5 );
		return $this->fnAjax( $content[0], $content[1] );
	}

	public function detail(){
		$get = $this->Get;
		$res = $this->fnDetail( $get['id'], 'Admins', 'password', true );
		return $this->fnAjax( $res[0], $res[1] );
	}

	public function edit(){
		$post = $this->Post;
		$res = $this->fnEdit( 'Admins', $post );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function add(){
		$post = $this->Post;
		$res = $this->fnAdd( 'Admins', $post );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function del(){
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], 'Admins' );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

}

?>