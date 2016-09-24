<?php 

namespace Admin\Controller;
use Think\Controller;
class OrdersController extends CommonController{

	public function index(){
		$get = $this->Get;
		$m = D('Orders as a');
		$num = 2;
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->join('users as b on b.id = a.user_id')
				  ->join('carts as c on c.user_id = a.user_id')
				  ->field('b.name,a.*,c.*')
				  ->order('a.id desc')
				  ->limit( $page->firstRow.','.$page->listRow, $num )
				  ->select();
		$pay = array( 
			'status'	=>	array( '未付款', '已付款' ),
			'pay_type'	=>	array( '网上银行', '支付宝' ),
		);
		foreach ( $list as $key => $value ) {
			foreach ( $pay as $k => $v ) {
				foreach ( $v as $i => $l) {
					if( $i == $list[$key][$k] ){
						$list[$key][$k] = $l;
						break;
					}
				}
			}
		}
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch(T('Orders/index'));
		//if( $list ) return $this->ajaxReturn( ajaxData( $content, 1 ) );
		return $this->fnAjax( $content );
	}

	public function detail(){
		$get = $this->Get;
		$m = D('Orders as o');
		$field = array(
			'u.name as uname, u.email, u.mobile, u.level, u.status as ustatus, u.create_at',
			'o.id, o.number, o.price as oprice, o.num, o.status as ostatus, o.create_time, o.pay_type',
			'c.num as cNum',
			's.name as sname, s.price as sprice, s.price_vip as sprice_vip, s.thumbnail',

		);
		$row = $m->join('users as u on u.id = o.user_id','left')
				 ->join('carts as c on c.user_id = o.user_id','left')
				 ->join('classes as s on s.id = c.class_id','left')
				 ->field($field)
				 ->where( 'o.id='.$get['id'] )
				 ->find();
		$pay = array( 
			'ostatus'	=>	array( '未付款', '已付款' ),
			'pay_type'	=>	array( '网上银行', '支付宝' ),
		);
		foreach ( $pay as $key => $value ) {
			foreach ( $value as $k => $v ) {
				if( $k == $row[$key] ){
					$row[$key] = $v;
					break;
				}
			}
		}
		return $this->fnAjax( $row );
		//return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

	public function edit(){
		$post = $this->Post;
		$m = D('Orders');
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
		$m = D('Orders');
		$res = $m->add($post);
		if( $res ){
			return $this->fnAjax( '添加成功', 1, U('index') );
		}
		return $this->fnAjax( '添加失败！', 0 );
	}

	public function del(){
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], 'Orders' );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

}


?>