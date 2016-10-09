<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller 
{
	
    public function index(){

    	$this->redirect('login');
    }

    /**
     * 登录
     */
    public function login()
    {
        if(IS_POST){

            $users = M('Users');
            // 验证令牌
             if(!$users->autoCheckToken($_POST)){
                $this->redirect('/Login/registered',array('error'=>'令牌错误！'));
             }
            //验证用户
            $name = htmlspecialchars(I('user_name')) ;
            //查询用户
            $user = $users->where("name = '{$name}' or  email = '{$name}'")->field('id,name,email,status,password,password_rand,temporary')->find();
            if($user['status'] != 0)$this->redirect('/Login/login',array('error'=>'你的账户已被禁用！'));
            $pass = md5(I('user_password').$user['password_rand']);//post过来的密码拼接注册时存储的随机字符串
            if($user['password'] == $pass){
                session('homeuser',$user);
                $data['update_at'] = $user['temporary'];
                $data['temporary'] = date('Y-m-d H:i:s',time());
                $users->where("id = {$user['id']}")->save($data);//更新最后上次登录时间 //写入当前登录时间 

                //查询用户是否已选择模板  如果没有选择继续跳转到选择模板的界面
                $user_classes = M('User_tpl');
                $userid = session('homeuser.id');
                $res = $user_classes->where("user_id = {$userid}")->find();
                if($res['tpl_id'] != 1){
                    $this->redirect('Websites/index');
                }else{
                    $this->redirect('/Index/index');
                }

            }else{

                $this->redirect('/Login/login',array('error'=>'用户名或密码错误'));
            }
        }else{

        	$this->display();
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('homeuser',null);
        $this->redirect('/Login/login');
    }

    /**
     * 注册
     */
    public function registered()
    {
    	if(IS_POST){
    		 $user = D('Users');
             // 验证令牌
             $email = I('post.email');
             $name  = I('post.name');
             $name_ = $user->where("name = '{$name}'")->find();
             if($name_){
                $this->redirect('/Login/registered',array('error'=>'此用户名已经被注册！'));
             }
             $email_ = $user->where("email = '{$email}'")->find();
             if($email_){
                $this->redirect('/Login/registered',array('error'=>'此邮箱已经被注册！'));
             }
             if(!$user->autoCheckToken($_POST)){
                $this->redirect('/Login/registered',array('error'=>'令牌错误！'));
             }
             // 验证验证码
             $verify = new \Think\Verify(); 
             $code = $verify->check($_POST['code']);
             if(!$code){
                 $this->redirect('/Login/registered',array('error'=>'验证码错误！'));
             }

             $data = $user->data_s($_POST);//创建数据
             $res = $user->add($data);
             if($res){
                $users = $user->where("id = {$res}")->field('id,name,email,status,password,password_rand,temporary')->find();
                session('homeuser',$users);

                $user_tpl = M('User_tpl');
                $data_tpl['user_id'] = $res;
                $user_tpl->add($data_tpl);
                $this->redirect('/Index/index');

             }else{

                $this->redirect('/Login/registered',array('error'=>'注册失败！'));
             }
    	}else{

    		   $this->display('register');
    	}
    }


    /**
     * 用户名唯一验证
     */
    public function judge_name()
    {
        $name = I('post.name');
        $user = M('Users');
        $username = $user->where("name = '{$name}'")->find();
        if($username){

            $this->ajaxReturn('该邮箱已经被注册！');
        }else{

            $this->ajaxReturn(1);
        }
    }

    /**
     * 邮箱唯一验证
     */
    public function judge_email()
    {
        $email = I('post.email');
        $user = M('Users');
        $useremail = $user->where("email = '{$email}'")->find();
        if($useremail){

            $this->ajaxReturn('该邮箱已被注册!');
        }else{

            $this->ajaxReturn(1);
        }
    }



    /**
     * 找回密码
     */
    public function retrieve_pwd()
    {
       if(IS_POST){
             $user = M('Users');
             // 验证令牌
             if(!$user->autoCheckToken($_POST)){
                $this->redirect('/Login/retrieve_pwd',array('error'=>'令牌错误！'));
             }
             // 验证验证码
             $verify = new \Think\Verify(); 
             $code = $verify->check($_POST['code']);
             if(!$code){
                 $this->redirect('/Login/retrieve_pwd',array('error'=>'验证码错误！'));
             }
             $name = I('post.name');
             $email = I('post.email');
             $user_res = $user->where("name = '{$name}' and email = '{$email}'")->find();
             if($user_res){
                $str = createluan(30);//随机字符串
                $time = date('Y/m/d H:i:s',time());
                S("retrieve_pwd.{$str}",$name,1800);//缓存数据30分钟
                $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']."/Login/reset_pwd?key={$str}";
                $body = "您好，{$name}:<br><br>
                         请点击下面的链接来重置您的密码。<br><br>
                         <a href='{$url}'>$url</a><br><br>
                         如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中。<br><br>
                         该验证邮件有效期为30分钟，超时请重新发送邮件。<br><br>
                         发件时间：{$time} <br><br>
                         此邮件为系统自动发出的，请勿直接回复。";
                //发送邮件
                $email_res = sendMail($email,'云狄建站找回密码邮件',$body);
                if($email_res){

                    echo "<script>alert('邮件也发送至 {$email} 请在邮箱中点击链接重置密码，如果没有收到邮件，请查看垃圾箱。');window.location.href='".U('Login/retrieve_pwd')."'</script>";
                }else{

                    $this->redirect('/Login/retrieve_pwd',array('error'=>'邮件发送失败！请重新提交。'));
                }

             }else{

                $this->redirect('/Login/retrieve_pwd',array('error'=>'用户名或邮箱不正确！'));
             }

       }else{
            
            $this->display();
       } 
    }


    /**
     * 重置密码
     */
    public function reset_pwd()
    {   
        if(IS_POST){
             $key = I('post.key');
             $user = M('Users');
             // 验证令牌
             if(!$user->autoCheckToken($_POST)){

                $this->redirect('/Login/reset_pwd',array('key'=>$key,'error'=>'令牌错误！'));
             }
             // 验证验证码
             $verify = new \Think\Verify(); 
             $code = $verify->check($_POST['code']);
             if(!$code){

                 $this->redirect('/Login/reset_pwd',array('key'=>$key,'error'=>'验证码错误！'));
             }
             $name = htmlspecialchars(I('post.username'));
             $pwd = I('post.password');
             $str = md5(createluan(30));//生成随机字符串

             $data['password_rand'] = $str;
             $data['password'] = md5($pwd.$str);
             $res = $user->where("name = '{$name}'")->save($data);
             if($res){
                $users = $user->where("name = '{$name}'")->field('id,name,email,status,password,password_rand')->find();
                session('homeuser',$users);
                S("retrieve_pwd.{$key}",null);
                $this->redirect('/Index/index');

             }else{

                $this->redirect('/Login/reset_pwd',array('key'=>$key,'error'=>'重置密码失败！'));
             }
            
        }else{
                $key = I('get.key');
                $name = S("retrieve_pwd.$key");
                if($name){
                    $this->assign('name',$name);
                    $this->assign('key',$key);


                }else{
                    if(!$_GET['error'] || !$name || !$key){
                        
                    echo "<script>alert('链接也过期！');window.location.href='".U('Login/login')."';</script>";
                    }
                }
             $this->display(); 

        }
    }




    /**
     * 验证码
     */
    public function code()
    {
        $config = array('fontSize' => 30,'useCurve' => false,);
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }


    public function _empty()
    {
    	$this->display('Public/404');
    }
}