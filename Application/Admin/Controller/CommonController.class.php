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
		//@$post['password'] = md5(@$post['password']);
		$this->Post = $post;
		$this->Get = $get;
	}

	public function _empty($name){
		exit('Could not locate remote server.');
	}

	public function _before_index(){

	}

	public function fnAjax( $arr, $status=1, $url=null ){
		return $this->ajaxReturn( array( 
			'arr' 		=> $arr,
			'status' 	=> $status,
			'url' 		=> $url,
		) );
	}

	public function fnPage( $val, $tep, $num=2, $field=null, $the=false ){
		$m = D($val);
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->field( $field, $the )->limit( $page->firstRow.','.$page->listRow, $num )->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		return array( $this->fetch(T($tep)), 1 );
	}

	public function fnDetail( $id, $val, $field=null, $the=false ){
		$m = D($val);
		$row = $m->where( 'id='.$id )->field( $field, $the )->find();
		if( !$row ) return array( $m->getError(), 0 );
		return array( $row, 1 );
	}

	public function fnAdd( $val, $data, $field=null, $the=false ){
		$m = D($val);
		$oD = $m->_auto( $m, $data );
		if( !$oD ) return array( $m->msg, 0 );
		$res = $m->add($oD);
		return array( '添加成功！', 1 );
	}

	public function fnEdit( $val, $data, $field=null, $the=false ){
		$m = D($val);
		$oD = $m->_auto( $m, $data );
		if( !$oD ) return array( $m->msg, 0 );
		$row = $m->where( 'id='.$data['id'] )->field( $field, $the )->save($oD);
		if( $row ) return array( '编辑成功！', 1 );
		return array( $m->getError(), 0 );
	}

	public function fnDel( $id, $val ){
		$m = D($val);
		$res = $m->where( 'id='.$id )->delete();
		if( $res ) return array( '删除成功！', 1 );
		return array( $m->getError(), 0 );
	}

}


?>