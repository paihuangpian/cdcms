<?php


namespace Home\Model;
use Think\Model;

class LoginModel extends Model
{
 	//自动验证
    // protected $_validate = array(
    //     array('name', 'require', '用户名不能为空'),
    //     array('email', 'email', '邮箱格式不正确'),
    //     array('repwd','pwd','确认密码不正确',0,'confirm'),
    //     array('name','','帐号名称已经存在！',0,'unique',3),
    // );


    protected function data_s($datas)
    {
    	 	 $data['name'] = $datas['contact'];
             $data['password'] = $datas['password'];
             $data['email'] = $datas['email'];
             $data['mobile'] = $datas['tel'];
             $data['create_at'] = date('Y-m-d H:i:s',time());
             $data['update_at'] = date('Y-m-d H:i:s',time());

             return $data;
    }
		



	
}
 
