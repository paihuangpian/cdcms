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
		if( $list ) return $this->fnAjax( $content );
		return $this->fnAjax( null, 0 );
	}

	public function detail(){
		$get = I('get.');
		$m = D('Users');
		$row = $m->where($get)->find();
		if( $row ) return $this->fnAjax( $row );
		return $this->fnAjax( null, 0 );
	}

	public function edit(){
		$post = $this->Post;
		$m = D('Users');
		$wh = [
			'id'=>$post['id'],
		];
		$row = $m->where($wh)->save($post);
		if( $row ) {
			return $this->fnAjax( '编辑成功！', 1, U('index') );
		}
		return $this->fnAjax( '编辑失败！', 0 );
	}

	public function add(){
		$post = $this->Post;
		$m = D('Users');
		$res = $m->add($post);
		if( $res ){
			return $this->fnAjax( '添加成功', 1, U('index') );
		}
		return $this->fnAjax( '添加失败！', 0 );
	}

	public function del(){
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], 'Users' );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

}

?>