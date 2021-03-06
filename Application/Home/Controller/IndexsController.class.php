<?php
namespace Home\Controller;
use Think\Controller;
class IndexsController extends CommonController 
{
    public function index()
    {
        $templates = M('Templates');
        $templatess = $templates->select();
        $this->assign('templates',$templatess);
        // dump($templatess);exit;
    	$this->display(); 
    }


     /**
     * 模板选择
     */
    public function tpl_choose()
    {
	$user_tpl = M('User_tpl');
        $tpl = $user_tpl->where("user_id = ".session('homeuser.id'))->field('tpl_id')->find();
        if($tpl['tpl_id'] != 1){
            
        $preg = "/demo_cms_/";
        if(preg_filter($preg,'',session('homeuser.name'))){echo "<script>alert('你是演示用户禁止修改模板！');window.history.go(-1);</script>";exit;}
        }
        $tplid['tpl_id'] = I('id');//接受模板id 创建数据
        $userid = session('homeuser.id');//用户id
        $templates = M('Templates');
        $res = $templates->where("id = {$tplid['tpl_id']}")->field('id')->find();//查询模板是否存在
        if($res){
            $user_tpl = M('User_tpl');
            //查询当前用户 已拥有的模板是否是当前模板
            if($user_tpl->where("user_id = {$userid} and tpl_id = {$tplid['tpl_id']}")->find()){
                echo "<script>alert('此模板您已经安装！');window.history.go(-1);</script>";
                exit;
            }
            //更新模板
            $tplres = $user_tpl->where("user_id = {$userid}")->save($tplid);
            if($tplres){
               // echo "<script>alert('模板安装成功，你可以访问".session('homeuser.name').".cms.com 进行查看。');window.history.go(-1);</script>"; 
                echo "<script>alert('安装成功！');</script>";
                $this->redirect('Websites/config');
            }else{

                echo "<script>alert('抱歉！模板安装失败！请重新安装！');window.history.go(-1);</script>";
            }
        }else{

            echo "<script>alert('抱歉！该模板已下线！请选择其他模板！');window.history.go(-1);</script>";
        }

      
    }
}
