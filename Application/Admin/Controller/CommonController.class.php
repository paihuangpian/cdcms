<?php 

namespace Admin\Controller;
use Think\Controller;
class CommonController extends AuthController{

	protected $Post = null;

	public function _initialize(){
		if( !IS_AJAX ) exit( $this->show('Could not locate remote server.') );
		$this->Post = array_filter( I('post.') );
		$this->Get = array_filter( I('get.') );
	}

	public function _empty($name){
		exit('Could not locate remote server.');
	}

	public function _before_index(){

	}

}


?>