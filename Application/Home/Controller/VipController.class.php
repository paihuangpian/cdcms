<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 会员模块
 */
class VipController extends CommonController 
{

    public function index(){

    	$this->redirect('vip');
    }

    /**
     * 会员基本信息
     */
    public function vip()
    {
    	$user = D('Users');
    	$userid = session('homeuser.id');
    	$userdata = $user->field('id,name,email,level,status,create_at,update_at')->user_vip($userid);
    	$this->assign('user',$userdata);
    	$this->display();
    }

    /**
     * 修改密码
     */
    public function Modify_pwd() 
    {
    	 $pwd_j = I('post.pwd_j');//旧密码
    	 $pwd   = I('post.pwd_q');//新密码
    	 $id    = session('homeuser.id');
		 $user = M('Users');
		 // 验证令牌
	     if($user->autoCheckToken($_POST)){
	        $this->ajaxReturn('令牌错误！');
	     }

	     $userres = $user->field('password,password_rand')->where("id = {$id}")->find();
	     if(md5($pwd_j.$userres['password_rand']) != $userres['password']){
	        $this->ajaxReturn('原密码错误！');
	     }

	     $s = '0123456789abcdefghijklmnopqrstuvwxyz'; //62个字符
    	 $strs = str_shuffle($s);//随机打乱
    	 $str = substr($strs,mt_rand(6,30),10);//随机字符串
    	 $data['password_rand'] = md5($str);//密码验证字符串
	     $data['password'] = md5($pwd.$data['password_rand']);
	     $res = $user->where("id = {$id}")->save($data);
	     if($res){
	     		$this->ajaxReturn(1);
	     }else{

	     		$this->ajaxReturn('修改失败！');
	     }
    }

    /**
     * 会员特权信息
     */
    public function user_level()
    {
    	
    }


    public function _empty()
    {
    	$this->display('Public/404');
    }





}