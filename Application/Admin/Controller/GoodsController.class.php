<?php 

namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController{

	public function index(){
		$post = $this->Post;
		$m = D('classes');
		$num = 2;
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->order('id')
				  ->limit( $page->firstRow.','.$page->listRow, $num )
				  ->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch( T('Goods/index') );
		return $this->fnAjax( $content );

	}

	public function detail(){
		$get = $this->Get;
		$m = D('classes');
		$row = $m->where($get)->find();
		if( $row ) return $this->fnAjax( $row );
		return $this->fnAjax( null, 0 );
	}

	public function edit(){
		$post = $this->Post;
		$m = D('classes');
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
		$m = D('classes');
		$res = $m->add($post);
		if( $res ){
			return $this->fnAjax( '添加成功', 1, U('index') );
		}
		return $this->fnAjax( '添加失败！', 0 );
	}

	public function del(){
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], 'classes' );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function upload(){
		$upload = new \Think\Upload();
		$upload->maxSize = 3145728;
		$upload->exts = [ 'jpg', 'gif', 'png', 'jpeg' ];
		$upload->rootPath = './Uploads/Admin/';
		$upload->savePath = '';
		$upload->subName = date('Y-m-d',time()).'/';
		$info = $upload->upload();
		if( !$info ){
			return $this->fnAjax( ['msg'=>$upload->getError()], 0 );
		}
		foreach($info as $file){
            $FilePath = $file['savename'];
        }
		$msg = [ 'msg'=>'上传成功', 'url'=>'/cdcms/Uploads/Admin/'.$upload->subName.$FilePath, ];
		return $this->fnAjax( $msg );
	}

}

?>