<?php
namespace Home\Controller;
use Think\Controller;
class IndexsController extends CommonController 
{
    public function index()
    {

    	
    	$this->display(); 
    }



     /**
     * 模板选择
     */
    public function tpl_choose()
    {
        $tplid['tpl_id'] = I('id');//接受模板id 创建数据
        $userid = session('homeuser.id');//用户id
        $templates = M('Templates');
        $res = $templates->where("id = {$tplid['tpl_id']}")->field('id')->find();//查询模板是否存在
        if($res){
            $user_tpl = M('User_tpl');
            //查询当前用户 已拥有的模板是否是当前模板
            if($user_tpl->where("user_id = {$userid} and tpl_id = {$tplid['tpl_id']}")->find()){
                echo "<script>alert('次模板您已经安装！');window.history.go(-1);</script>";
            }
            //更新模板
            $tplres = $user_tpl->where("user_id = {$userid}")->save($tplid);
            if($tplres){
               echo "<script>alert('模板安装成功，你可以访问".session('homeuser.name').".cms.com 进行查看。');window.history.go(-1);</script>"; 
            }else{

                echo "<script>alert('抱歉！模板安装失败！请重新安装！');window.history.go(-1);</script>";
            }
        }else{

            echo "<script>alert('抱歉！该模板已下线！请选择其他模板！');window.history.go(-1);</script>";
        }

      
    }
}