<?php 

namespace Admin\Controller;
use Think\Controller;
class CommonController extends AuthController{

	protected $Post = null;
	protected $Get = null;

	public function _initialize(){
		if( !IS_AJAX ) exit( $this->show('Could not locate remote server.') );
		$post = array_filter( I('post.') );
		$get = array_filter( I('get.') );
		@$post['password'] = md5(@$post['password']);
		$this->Post = $post;
		$this->Get = $get;
	}

	public function _empty($name){
		exit('Could not locate remote server.');
	}

	public function _before_index(){

	}

}


?>