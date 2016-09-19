<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends AuthController {

    public function index(){
    	return $this->display('Index/index');
    }

   //  public function login(){

   //  	if( session('?user') ){
   //  		return redirect( U('Index') );
   //  	}

   //  	if( IS_POST ){
			// $post = I('post.');
			// //$this->ajaxReturn( [ 'post.verify'=>$post['verify'], 'verify'=>$this->check_verify($post['verify']) ] );

			// $m = D('Admin');

			// if( $m->create($post,4) ){
			// 	exit( $this->ajaxReturn( [ 'msg'=>$m->getError(), 'state'=>1 ] ) );
			// }else{
			// 	exit( $this->ajaxReturn( [ 'msg'=>'登陆成功！', 'state'=>1 ] ) );
			// }
			// exit();
   //  		if( !$this->check_verify($post['verify']) ){
   //  			return $this->ajaxReturn( [ 'msg'=>'验证码错误', 'state'=>0 ] );
   //  		}

   //  		$m = M('Admin');

   //  		if( !$m->autoCheckToken($post) ){
			// 	return $this->ajaxReturn(['msg'=>'错误的请求！']);
			// }

   //  		$sql = 'name="'.$post['userName'].'" AND password="'.$post['password'].'"';

   //  		$row = $m->where($sql)->select();
			// //$this->ajaxReturn(['sql'=>$sql,'row'=>empty($row)]);
   //  		if( empty($row) ){ 
   //  			return $this->ajaxReturn( [ 'msg'=>'用户名或密码不正确！', 'state'=>0 ] ); 
   //  		}
    		
   //  		session( 'user', $post );
   //  		return $this->ajaxReturn( [ 'msg'=>'登陆成功！', 'state'=>1 ] );

   //  	}

   //  	$this->display('login/index');
   //  }

    // public function logout(){
    // 	session('user',null);
    // 	return redirect('Index/login');
    // }

 //    private function check_verify($code, $id = ''){
	//     $verify = new \Think\Verify();
	//     return $verify->check($code, $id);
	// }

    // public function verify( $config = null ){
    // 	$config ? $config : $config = [
    // 		'useImgBg'	=> false,	//是否使用背景图片
    // 		'fontSize' 	=> 30,		//验证码字体大小
    // 		'length'	=> 4,		//验证码位数
    // 		'useNoise'	=> false, 	//关闭验证码杂点
    // 		'useCurve'	=> false,	//是否使用混淆曲线
    // 		'imageH'	=> 0,		//验证码高度
    // 		'bg'		=> [238,238,238],
    // 	];
    // 	$Verify = new \Think\Verify($config);
    // 	return $Verify->entry();
    // }
}