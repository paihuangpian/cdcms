<?php 

namespace Admin\Controller;
use Think\Controller;
class UsersController extends CommonController{

	public function index(){
		$get = I('get.');
		$m = D('Users');
		$num = 2;
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->limit( $page->firstRow.','.$page->listRow, $num )->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch(T('Users/index'));
		if( $list ) return $this->ajaxReturn( ajaxData( $content, 1 ) );
		return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

	public function detail(){
		$get = I('get.');
		$m = D('Users');
		$row = $m->where($get)->find();
		if( $row ) return $this->ajaxReturn( ajaxData( $row, 1 ) );
		return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

}

?>