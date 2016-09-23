<?php


namespace Home\Model;
use Think\Model;

class UsersModel extends Model
{
    /**
     * 用户注册创建数据
     */
    public function data_s($datas)
    {
    	 	 $s = '0123456789abcdefghijklmnopqrstuvwxyz'; //62个字符
        	 $strs = str_shuffle($s);//随机打乱
        	 $str = substr($strs,mt_rand(6,30),10);//随机字符串
    	 	 $data['name'] = htmlspecialchars($datas['name']);//用户名
             $data['password_rand'] = md5($str);//密码验证字符
             $data['password'] = md5($datas['password'].$data['password_rand']);//密码
             $data['email'] = htmlspecialchars($datas['email']);//邮箱
             // $data['mobile'] = htmlspecialchars($datas['tel']);//手机
             $data['create_at'] = date('Y-m-d H:i:s',time());//注册时间
             $data['update_at'] = date('Y-m-d H:i:s',time());//更新时间

             return $data;
    }


    /**
     * 会员中心数据转换
     */
    public function user_vip($userid)
    {
            $level = array('普通会员','VIP会员');//会员等级
            $status = array('正常','已过期');//会员状态
            $user = $this->where("id = {$userid}")->find();
            $user['level'] = $level[ $user['level'] ];
            $user['status'] = $status[ $user['status'] ];
            return $user;
    }
		



	
}
 
