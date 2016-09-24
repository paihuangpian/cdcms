<?php 

namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{

	public function index(){

		if( session('?user') ) return $this->redirect('Index/index');
		
		if( !IS_AJAX ) return $this->display('Login/index');

		if( !Referer() ) return $this->ajaxReturn( ajaxData( 'Could not locate remote server.', 0 ) );

		$post = I('post.');

		if( !check_verify($post['verify']) ) return $this->ajaxReturn( ajaxData( '验证码错误！', 0 ) );

		$m = D('admins');
		$res = $m->fn_login( $m, $post );

		if( $res ){
			$data = [ 'ip'=>get_client_ip() ];
			$m->where( 'id='.$res['id'] )->save($data);
			session( 'user', $res );
			session( 'user.last', date('Y-m-d H:s:i',time()) );
			return $this->ajaxReturn( ajaxData( '登录成功！', 1, U('Index/index') ) );
		}

		return $this->ajaxReturn( ajaxData( $m->msg, 0 )  );
	}

	public function verify( $config = null ){
		$config ? $config : $config = [
			'useImgBg'	=> false,	//是否使用背景图片
			'fontSize' 	=> 30,		//验证码字体大小
			'length'	=> 4,		//验证码位数
			'useNoise'	=> true, 	//关闭验证码杂点
			'useCurve'	=> true,	//是否使用混淆曲线
			'imageH'	=> 0,		//验证码高度
			'bg'		=> [238,238,238],
		];
		$Verify = new \Think\Verify($config);
		return $Verify->entry();
	}

	public function logout(){
		$data = [ 'last'=>session('user.last') ];
		D('admins')->where( 'id='.session('user.id') )->save($data);
		session( '[destroy]' );
		return $this->redirect('Login/index');
	}
}

?>