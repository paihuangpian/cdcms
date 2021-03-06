<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 会员模块
 */
class MessageController extends Controller 
{
    //留言列表
    public function message()
    {
        if(!session('homeuser.id')){$this->redirect('Login/login');}
        C('DB_PREFIX','cms_');
        $message = D('Message');
        $messages = $message->message_conversion();
        $this->assign('messages',$messages);
        $this->display();
    }

    public function message_add()
    {
        if(IS_POST){
            $verify = new \Think\Verify(); 
             $code = $verify->check($_POST['code']);
             if(!$code){
                echo "<script>alert('验证码错误！');window.history.go(-1);</script>";
                exit;
             }
            if(!$_POST['name']){ echo "<script>alert('未知错误！');window.history.go(-1);</script>";exit;}
            $user = M('Users');
            C('DB_PREFIX','cms_');
            $message = D('Message');
            $data = $message->message_data($_POST);
            $res = $message->add($data);
            if($res){
                $useremail = $user->where("id = {$data['message_userid']}")->field('email')->find();
                //向用户发送邮件
                 $time = date('Y-m-d H:i:s',$data['message_addtime']);
                 $body = "留言者姓名:{$data['message_username']}<br>
                          留言者电话:{$data['message_tel']}<br>
                          留言者邮箱:{$data['message_email']}<br>
                          留言者Q  Q:{$data['message_qq']}<br>
                          留言内容:{$data['message_text']}<br>
                          留言页面:{$data['message_url']}<br>
                          留言时间:{$time}<br> ";
                $email_res = sendMail($useremail['email'],'云狄建站提醒您，您有新的留言。',$body);
                echo "<script>alert('留言成功，我们会尽快与您联系。');window.history.go(-1);</script>";
            }else{
                 echo "<script>alert('提交失败！');window.history.go(-1);</script>";
            }
        }else{
            $this->redirect('Login/login');
        }
    }


    /**
     * 删除留言
     */
    public function message_delete()
    {
        if(IS_AJAX){
            $id = I('id');
            $message = M('Cms_message');
            $res = $message->where("message_id = {$id} and message_userid =".session('homeuser.id'))->delete();
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
            
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