<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller 
{
    public function _initialize()
    {

    	if(!session('homeuser'))$this->redirect('/Login/login');

    	$h = date('G');
		if 		($h < 11) $date = '早上好';
		else if ($h < 13) $date = '中午好';
		else if ($h < 17) $date = '下午好';
		else 			  $date = '晚上好';
		$this->assign('date',$date);


		//查询用户是否已选择模板
    	$user_classes = M('User_tpl');
    	$userid = session('homeuser.id');
    	$res = $user_classes->where("user_id = {$userid}")->find();
        if($res['tpl_id'] != 1){
		$this->assign('user_classes',$res);
        
        }
    }
}
