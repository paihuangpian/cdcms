<?php 

namespace Admin\Controller;
use Think\Controller;
class AdminsController extends CommonController{

	public function index(){
		$get = I('get.');
		$m = D('Admins');
		$num = 2;
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->limit( $page->firstRow.','.$page->listRow, $num )->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch(T('Admins/index'));
		if( $list ) return $this->ajaxReturn( ajaxData( $content, 1 ) );
		return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

	public function detail(){
		$get = I('get.');
		$m = D('Admins');
		$row = $m->where($get)->find();
		if( $row ) return $this->ajaxReturn( ajaxData( $row, 1 ) );
		return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

	public function edit(){
		$post = I('post.');
		$post = array_filter($post);
		$m = D('Admins');
		$wh = [
			'id'=>$post['id'],
			'password'=>md5($post['password']),
		];
		$post['password'] = md5($post['passwords']);
		$row = $m->where($wh)->save($post);
		if( $row ) {
			return $this->ajaxReturn( ajaxData( '编辑成功！', 1 ) );
		}
		return $this->ajaxReturn( ajaxData( '密码认证错误！', 0 ) );
	}

	public function add(){
		$post = $this->Post;
		return $this->ajaxReturn( ajaxData( $post, 0 ) );
	}

}

?>