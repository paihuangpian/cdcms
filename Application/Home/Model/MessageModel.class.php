<?php


namespace Home\Model;
use Think\Model;

class MessageModel extends Model
{
 	
    public function message_data($datas)
    {
    	$userid = $this->table('users')->where("name = '{$datas['name']}'")->field('id')->find();
    	$data['message_userid'] 	= $userid['id'];//用户id
    	$data['message_username'] 	= $datas['username'];//留言者姓名
    	$data['message_tel'] 		= $datas['tel'];//留言者电话
    	$data['message_url'] 		= $datas['url'];//留言页面
    	$data['message_email']		= $datas['email'];//留言者邮箱
    	$data['message_qq'] 		= $datas['qq'];//留言者qq
    	$data['message_text'] 		= $datas['text'];//留言内容
    	$data['message_addtime'] 	= time();
    	return $data;
    }


    public function message_conversion()
    {
        $messages = $this->where('message_userid ='.session('homeuser.id'))->select();
        foreach($messages as &$v){
            $v['message_addtime'] = date('Y-m-d H:i:s',$v['message_addtime']);
        }
        return $messages;
    }
}

 
