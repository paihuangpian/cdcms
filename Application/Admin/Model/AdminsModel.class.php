<?php  

namespace Admin\Model;
use Think\Model;
/**
* 
*/
class AdminsModel extends Model
{
	/**
	*array( 验证字段, 验证规则, 错误提示, 验证条件, 附加规则, 验证时间 )
	*验证条件[ 字段存在就验证(0) or 必须验证(1) or 值不为空验证(2) ]
	*验证时段[ 更新时验证(1) or 编辑时验证(2) or 全部情况下验证(3) or 登录时验证(4) ]
	*/
	protected $tableName = 'Admins';
	public $msg = null;

	public function _auto( $db, $data=null ){
		$action = 'fn_'.ACTION_NAME;
		$post = $data ? $data : I('post.');
		return $this->$action( $db, $post );
	}

	public function fn_add( $db, $data ){
		$preg = preg_match( '/^[a-z0-9_x80-xff]+[^_]$/', $data['name'] );
		if( !$preg ){
			$this->msg = '用户名不能少于3位且不能带有特殊字符和数字';
			return false;
		}
		$name = array( 'name'=>$data['name'] );
		$res = $db->where( $name )->find();
		if( $res ) {
			$this->msg = '用户名已存在！';
			return false;
		}
		$preg = preg_match( '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/', $data['password'] );
		if( !$preg ) {
			$this->msg = '密码不能少于6位并且字母数字混合';
			return false;
		}
		if( $data['password'] != $data['passwords'] ){
			$this->msg = '两次密码不匹配！';
			return false;
		}
		$data['password'] = md5( $data['password'] );
		return $data;
	}

	public function fn_edit( $db, $data ){
		$preg = preg_match( '/^[a-z0-9_x80-xff]+[^_]$/', $data['name'] );
		if( !$preg ){
			$this->msg = '用户名不能少于3位且不能带有特殊字符和数字';
			return false;
		}
		$name = array( 'name'=>$data['name'] );
		$password = array( 'id'=>$data['id'], 'password'=>md5( $data['password'] ) );
		$res = $db->where( $name )->find();
		if( $res ) {
			$this->msg = '用户名已存在！';
			return false;
		}
		$preg = preg_match( '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/', $data['passwords'] );
		if( !$preg ) {
			$this->msg = '密码不能少于6位并且字母数字混合';
			return false;
		}
		$res = $db->where( $password )->find();
		if( !$res ) {
			$this->msg = '原密码输入有误！';
			return false;
		}
		$data['password'] = md5( $data['passwords'] );
		return $data;
	}

	public function fn_login( $db, $data ){
		$post = $data ? $data : I('post.');
		$post['password'] = md5( $post['password'] );
		$res = $db->where( $post )->find();
		if( !$res ) {
			$this->msg = '用户名或密码错误！';
			return false;
		}
		return $res;
	}

}

?>