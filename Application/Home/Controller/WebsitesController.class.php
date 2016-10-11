<?php
namespace Home\Controller;
use Think\Controller;
class WebsitesController extends CommonController 
{
	// 我的网站首页
    public function index()
    {
    	$this->display('websites');
    }

    /**
     * 网站配置
     */
    public function config()
    {
    	if(IS_POST){
    		unset($_POST['x']);
    		unset($_POST['y']);
    		$config = M('Cms_config');
		 	$userid   = session('homeuser.id');
            //验证令牌
             if(!$config->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";
             }
    		 if($_FILES['config_logo']['error'] != 4 || $_FILES['config_pic']['error'] != 4 || $_FILES['config_ico']['error'] != 4){

    		 	//查询原有的图片  添加成功后 在删除
    		 	$config_logo_pic = $config->where("config_userid = {$userid}")->field('config_logo,config_pic,config_ico')->find();
    		 	if($config_logo_pic){
    		 		S('config_logo_pic',$config_logo_pic);
    		 	}
    			$username   = session('homeuser.name');//获取用户名 上传图片用到的路径
    			if(!file_exists('./Customer_Uploads/'.$username)){
    				mkdir('./Customer_Uploads/'.$username,0777);
                    chmod('./Customer_Uploads/'.$username,0777);
    			}
    			//上传缩略图
    			$upload = new \Think\Upload();// 实例化上传类    
                $upload->maxSize   =     3145728 ;// 设置附件上传大小    
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','ico');// 设置附件上传类型
                $upload->rootPath  =     './Customer_Uploads/';   
                $upload->savePath  =      "{$username}/"; // 设置附件上传目录    // 上传文件     
                $info   =   $upload->upload(); 
                if($info){
                	if($info['config_logo']){

                		$_POST['config_logo'] = '/Customer_Uploads/'.$info['config_logo']['savepath'].$info['config_logo']['savename'];
                	}
                	if($info['config_pic']){

                		$_POST['config_pic'] = '/Customer_Uploads/'.$info['config_pic']['savepath'].$info['config_pic']['savename'];
                	}
                	if($info['config_ico']){

                		$_POST['config_ico'] = '/Customer_Uploads/'.$info['config_ico']['savepath'].$info['config_ico']['savename'];
                	}
                }else{
                	$this->error($upload->getError()); 
                }
    		}

    		$_POST['config_time'] = time();
    		
    		$config_res = $config->where("config_userid = {$userid}")->find();//如果有数据就更新否则添加
    		if($config_res){
    			
    			$res = $config->where("config_userid = {$userid}")->save($_POST);
    			//删除图片
    			if($res){
    				if(S('config_logo_pic')){
    				if($_FILES['config_logo']['error'] != 4){@unlink('.'.S('config_logo_pic')['config_logo']);}
    				if($_FILES['config_pic']['error'] != 4){@unlink('.'.S('config_logo_pic')['config_pic']);}
    				if($_FILES['config_ico']['error'] != 4){@unlink('.'.S('config_logo_pic')['config_ico']);}
    				}
    			}
                S('config_logo_pic',null);
    		}else{
    			$_POST['config_userid'] = $userid;
    			$res = $config->add($_POST);
    		}
    		if($res){
    			echo "<script>alert('配置成功');</script>";
    			$this->redirect('Websites/config');

    		}else{

    			echo "<script>alert('配置失败！');</script>";
    			$this->redirect('Websites/config');
    		}

    	}else{
    		$userid = session('homeuser.id');
    		$config = M('Cms_config');
    		$configs = $config->where("config_userid = {$userid}")->find();
    		// dump($configs);
    		if(!$configs){
    			$configs = $config->where("config_userid = 1")->find();
    		}
    		$this->assign('configs',$configs);
	    	$this->display();
    	}
    }


        /**************************************栏目管理**************************************/

     /**
     * 添加栏目
     */
    public function column_add()
    {
        if(IS_POST){


            $pid = $_POST['column_pid'];
            $column = M('Cms_column');
            //验证令牌
             if(!$column->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";
             }
            //查询父级路径
            $res = $column->where("column_id = {$pid}")->field('column_path')->find();
            $_POST['column_path'] = $res['column_path'].$_POST['column_pid'].',';
            $_POST['column_userid'] = session('homeuser.id');
            $_POST['column_time'] = time();
            
            if($column->add($_POST)){

                $this->redirect('/Websites/column');

            }else{
                echo "<script>alert('添加失败！');window.location.href='';</script>";
            }
        }else{
            $userid = session('homeuser.id');
            $columns = M('Cms_column');
            $father = $columns->where("column_userid = {$userid} and column_pid = 0")->field('column_id,column_name')->select();
            $this->assign('father',$father);
            $this->assign('pid',I('pid'));
            $this->display();
            
        }


    }

    /**
     * 栏目列表
     */
    public function column()
    {   
        $userid = session('homeuser.id');
        $columns = M('Cms_column');
        $article = M('Cms_article');
        $res = $columns->where("column_userid = {$userid} and column_pid = 0")->field('column_id,column_name,column_status,column_type')->order('column_path')->select();

        foreach($res as $k=>$v){
            $res[ $k ][ $v['column_id'] ] = $article->where("article_column = {$v['column_id']}")->count();
        }
        $array = array('newss'=>'资讯','products'=>'产品','cover'=>'封面');
        foreach($res as &$vo){
            $vo['column_type'] = $array[ $vo['column_type'] ];
        }

        $this->assign('domain',session('domain'));
        $this->assign('columns',$res);

        $this->display();
    }

    /**
     * 异步加载下级栏目
     */
    public function column2()
    {
        if(IS_AJAX){
            $pid = I('pid');
            $userid = session('homeuser.id');
            $username = session('homeuser.name');
            $column2 = M('Cms_column');
            $article = M('Cms_article');
            $columns = $column2->where("column_userid = {$userid} and column_pid = {$pid}")->field('column_id,column_name,column_status,column_type')->order('column_path')->select();
            foreach($columns as $k=>$v){

                $columns[ $k ][ $v['column_id'] ] = $article->where("article_column = {$v['column_id']}")->count();

            }
            $array = array('newss'=>'资讯','products'=>'产品','cover'=>'封面');
            $status = array('正常','隐藏');
            foreach($columns as $vo){
                $str .= "
                      <style type='text/css'>.my_tables2 tr{height:5px;}</style>
                      <table border='0' class='my_tables2 zf_table' cellpadding='2' cellspacing='0' width='96%' id='table".$vo['column_id']."'>
                      <tbody><tr>
                      <td style='background-color:#FBFCE2;' class='bline' width='2%'>
                      <img style='cursor:pointer' id='img".$vo['column_id']."' onclick='LoadSunss(".$vo['column_id'].")' src='/Public/Home/column/dedeexplode.gif' height='11' width='11'>
                      </td>
                      <td style='background-color:#FBFCE2;'' class='bline'>
                      <table border='0' cellpadding='0' cellspacing='0' width='98%'><tbody><tr><td width='50%'>
                      <input class='np' name='tids[]' value='26' type='checkbox'>
                      <a href='".U('/Websites/article',array('cid'=>$vo['column_id']))."'>
                      <font color='red'></font>".$vo['column_name']."[ID:".$vo['column_id']."]</a>(文档：".$vo[ $vo['column_id'] ].") </td>
                      <td align='right'><a href='http://".session('homeuser.name').".".session('domain')."/indexs/".$vo['column_type']."?id=".$vo['column_id']."' target='_blank'>预览</a>|
                      <a href='".U('/Websites/article',array('cid'=>$vo['column_id']))."'>内容</a>|
                      <a >【". $vo['column_type'] = $array[ $vo['column_type'] ]."】</a>|
                      <a href='".U('/Websites/column_modify',array('id'=>$vo['column_id']))."''>更改</a>|
                      <a href='javascript:;' onclick='deletes(".$vo['column_id'].")' style='margin-right:50px;'>删除</a>&nbsp;
                        <button id='but".$vo['column_id']."' prompt='".$vo['column_status']."'  onclick='status(".$vo['column_id'].",".$vo['column_status'].");'>
                        ".$status[ $vo['column_status'] ]."</button>
                       </td></tr></tbody></table></td></tr>
                      <tr><td colspan='2' id='suns26'></td></tr>
                        <div style='margin-left:10px;display:none;' id='loads".$vo['column_id']."' judge='1'></div>
                    </tbody></table>";
            }
            echo $str;
        }else{
            $this->display('Public/404');
        }
    }

    /**
     * 修改栏目
     */
    public function column_modify()
    {
        if(IS_POST){
            $pid = $_POST['column_pid'];
            $column = M('Cms_column');
            //查询父级路径
            $res = $column->where("column_id = {$pid}")->field('column_path')->find();
            $_POST['column_path'] = $res['column_path'].$_POST['column_pid'].',';
            $_POST['column_time'] = time();
            if($column->save($_POST)){

                $this->redirect('/Websites/column');

            }else{

                echo "<script>alert('修改失败！');window.location.href='';</script>";
            }

        }else{

            $id = I('id');//本栏目id
            $columns = M('Cms_column');
            //查询所以栏目 在下拉框遍历
            $father = $columns->where('column_pid = 0 and column_userid = '.session('homeuser.id'))->field('column_id,column_name')->select();
            //查询当前修改的栏目
            $column = $columns->where("column_id = {$id} and column_userid = ".session('homeuser.id'))->field('column_id,column_name,column_pid,column_title,column_keywords,column_description,column_type,column_status,column_text')->find();
            $this->assign('father',$father);
            $this->assign('column',$column);
            $this->display();
        }
    }


    // /**
    //  * 更改栏目类型
    //  */
    // public function column_type()
    // {
    //     $id = I('id');
    //     $type = I('type');
    //     if(!$id || !is_numeric($id) || !$type){ echo "<script>alert('非法操作！');window.history.go(-1);</script>";}
    //     $column = M('Cms_column');
    //     $columns = $column->where('column_pid ='.$id.' and column_userid = '.session('homeuser.id'))->getField('column_id',true);
    //     $columns[] = $id;
    //     if($type == 'newss'){
    //         $data['column_type'] = 'products';
    //     }else{
    //         $data['column_type'] = 'newss';
    //     }
    //     foreach($columns as $v){
    //         $res = $column->where("column_id = {$v} and column_userid = ".session('homeuser.id'))->save($data);
    //     }
    //     $this->redirect('Websites/column');
    // }


    /**
     * 删除栏目
     */
    public function column_delete()
    {
        if(IS_AJAX){

            $id = $_POST['id'];
            $column = M('Cms_column');
            //查询是否有子级栏目
            $column_res = $column->where("column_pid = {$id}")->find();
            //查询是否有文章内容
            $article  = M('Cms_article')->where("article_column = {$id}")->find();
            if(!$column_res && !$article){
                if($column->delete($id)){
                    $this->ajaxReturn(1);//删除成功
                }else{

                    $this->ajaxReturn(2);//删除失败
                }
            }else{
                $this->ajaxReturn('有子级栏目或文章! 不能删除!');
            }
        }else{

            $this->display('Public/404');
        }
    }

    /**
     * 修改状态
     */
    public function column_status()
    {
        $id = I('id');//栏目id
        $status = I('status');//当前状态
        if($status == 1){
            $data['column_status'] = 0;
        }else{

            $data['column_status'] = 1;
        }
        $res = M('Cms_column')->where("column_id = {$id}")->save($data);
        if($res){
            $this->ajaxReturn($data['column_status']);
        }else{
            $this->ajaxReturn(null);
        }
    }

        /**************************************栏目管理结束**************************************/


        /**************************************文章管理**************************************/

    //文章列表
    public function article(){

        //查询栏目列表  移动文章时用

        C('DB_PREFIX','cms_');
        $column = M('Column');
        $columns = $column->where('column_userid ='.session('homeuser.id'))->field('column_id,column_name')->select();
        $this->assign('columns',$columns);

        $cid =  I('cid');//接收所属栏目的id
        $article = D('Article');
        if($cid){
            $count   = $article->where("article_column = {$cid} and article_userid =".session('homeuser.id'))->count();// 查询满足要求的总记录数
        }else{
            $count   = $article->where('article_userid ='.session('homeuser.id'))->count();// 查询满足要求的总记录数
        }
        $num     = 10;//每页显示的条数
        $number  =     ceil($count / $num);//页码数
        $page    = new \Think\Page($count,$num);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show    = $page->show();// 分页显示输出
        $articles = $article->limit($page->firstRow.','.$page->listRows)->article($cid);
        $username = session('homeuser.name').'.';
        $domainurl = 'http://'.$username.$_SESSION['domain'];//用户文章主域名
        $this->assign('domainurl',$domainurl);
        $this->assign('show',$show);//分页
        $this->assign('number',$number);//页码数
        $this->assign('count',$count);//总条数
        $this->assign('cid',$cid);//栏目id
        $this->assign('articles',$articles);
        $this->display('article');
    }


    /**
     * 添加及修改文章
     */
    public function article_add()
    {
        if(IS_POST){
            $article = M('Cms_article');
            //验证令牌
             if(!$article->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";
             }
            $status =  $_FILES['article_pic']['error'];
            if($status != 4){
                //上传缩略图
                $username   = session('homeuser.name');//获取用户名 上传图片用到的路径
                if(!file_exists('./Customer_Uploads/'.$username)){
                    mkdir('./Customer_Uploads/'.$username,0777);
                    chmod('./Customer_Uploads/'.$username,0777);
                }
                //上传缩略图
                $upload = new \Think\Upload();// 实例化上传类    
                $upload->maxSize   =     3145728 ;// 设置附件上传大小    
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath  =     './Customer_Uploads/';   
                $upload->savePath  =      "{$username}/"; // 设置附件上传目录    // 上传文件     
                $info   =   $upload->upload(); 
                if(!$info) {// 上传错误提示错误信息        
                $this->error($upload->getError());    
                }else{// 上传成功        
                    $pic =  "./Customer_Uploads/".$info['article_pic']['savepath'].$info['article_pic']['savename'];
                    $image = new \Think\Image(); 
                    //图片剪切
                    $image->open($pic);
                    $image->thumb(500, 500)->save($pic);
                        // //添加水印
                        // $image->open($pic)->text('周飞','./simhei.ttf',100,'#000000',\Think\Image::IMAGE_WATER_SOUTHEAST)->save($pic); 
                   $_POST['article_pic'] = substr($pic,1);//得到上传的图片
                }
            }
             //上传文章
            if($_POST['id']){
                $id = $_POST['id'];
                unset($_POST['id']);
                $res = $article->where("article_id = {$id}")->save($_POST);
                $prompt = '更新成功';
            }else{
                $_POST['article_userid'] = session('homeuser.id');
                $_POST['article_time'] = time();
                $res = $article->add($_POST);
                $prompt = '添加成功';
            }
            if($res){

                $this->redirect('Websites/article_middle',array('prompt'=>$prompt,'cid'=>$_POST['article_column'],'aid'=>$res));
            }else{

                 $this->redirect('Websites/article_add',array('cid'=>$_POST['article_column']));
            }
        }else{

            $cid = I('cid');//栏目id
            $column = M('Cms_column');
            //查询所以栏目 在下拉框遍历
            $columns = $column->where('column_userid ='.session('homeuser.id'))->field('column_id,column_name')->select();
            $this->assign('columns',$columns);
            $this->assign('cid',$cid);

            //修改时用
            if(I('id')){
                $id = I('id');
                $article = M('Cms_article');
                $res = $article->where("article_id = {$id} and article_userid =".session('homeuser.id'))->find();
                $this->assign('article',$res);
            }
            $this->display();
        }
    }


    /**
     * 添加文章成功后的中间页
     */
    public function article_middle()
    {
        $this->display();
    }
    /**
     * 添加文章时查询是否已有同名文章 ajax
     */
    public function article_title()
    {
        $title = $_POST['title'];
        $article = M('Cms_article');
        $res = $article->where("article_title = '{$title}' and article_userid =".session('homeuser.id'))->field('article_title')->find();
        if($res){
            $this->ajaxReturn('已有同名文章，请重新填写！');
        }else{
            $this->ajaxReturn(false);

        }
    }

    /**
     * 删除文章
     */
    public function article_delete()
    {
        $id = $_POST['id'];
        $article = M('Cms_article');

        //缓存要删除文章的所有图片
        if($id){
            $res = $article->where('article_id ='.$id)->field('article_pic,article_text')->find();
            if($res){
                S(session('homeuser.name').'article_pics',$res['article_pic']);//缓存缩略图
                $pic_url = sp_getcontent_imgs($res['article_text']);//获取article_text 里的所有图片src
                //遍历取出图片src
                if($pic_url){
                    foreach($pic_url as $v){
                        $pic_src[] = $v['src'];
                    }
                    S(session('homeuser.name').'article_text',$pic_src);//缓存article_text的所有图片路径
                }
            }
        }
        $article_res = $article->where("article_id = {$id}")->delete();
        //如果删除文章成功 删除图片
        if($article_res){
            foreach(S(session('homeuser.name').'article_text') as $v){
                @unlink('.'.$v);
            }
            @unlink('.'.S(session('homeuser.name').'article_pics'));
        }
          S(session('homeuser.name').'carousel_pics',null);
          S(session('homeuser.name').'article_text',null);

        if($article_res){
                $this->ajaxReturn(1);//删除成功
        }else{
            $this->ajaxReturn('删除失败！');
        }
    }


    /**
     * 批量删除文章
     */
    public function article_delarc()
    {
        $article_arrid = $_POST['arr'];
        $article = M('Cms_article');

        //缓存图片 删除文章成功后删除
        if($article_arrid){
            foreach($article_arrid as $v){
               $articleres[] = $article->where('article_id ='.$v)->field('article_pic,article_text')->find(); 
            }
           foreach($articleres as $vo){
            $article_pics[] =  $vo['article_pic'];
            $pic_url[] = sp_getcontent_imgs($vo['article_text']);//获取article_text 里的所有图片src
           }
             S(session('homeuser.name').'article_pics',$article_pics);//缓存缩略图
             S(session('homeuser.name').'article_text',$pic_url);//缓存缩略图
        }
        foreach($article_arrid as $id){
            $strid .= ','.$id;//把id拼接成用逗号隔开的字符串
        }
        $res = $article->delete(substr($strid,1));//截取前面的逗号并执行删除
        //删除图片
        if($res){
            foreach(S(session('homeuser.name').'article_text') as $value){
                foreach($value as $src){
                     @unlink('.'.$src['src']);
                }
            }
            foreach(S(session('homeuser.name').'article_pics') as $pic){
                @unlink('.'.$pic);
            }
        }
         S(session('homeuser.name').'article_text',null);
         S(session('homeuser.name').'article_pics',null);
        if($res){
            $this->ajaxReturn(1);
        }else{
            
            $this->ajaxReturn('批量删除失败！');
        }
    }

    /**
     * 移动文章
     */
    public function article_moveArc()
    {
        $article_arrid = $_POST['arr'];//所有文章id
        $column_id     = $_POST['column_id'];//要移动到该栏目的id
        $article = M('Cms_article');
        foreach($article_arrid as $id){
            $res = $article->where("article_id = {$id}")->setField('article_column',"$column_id");
        }
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn('移动失败！');
        }

    } 

    //文章搜索
    public function article_search()
    {
        C('DB_PREFIX','cms_');
        if(!empty($_POST['conditions'])){
            session('conditions',$_POST['conditions']);//查找条件
        }
        if(!empty($_POST['keywords'])){

            session('keywords',$_POST['keywords']);//关键词
        }
        if(!empty($_POST['orderby'])){

            session('orderby',$_POST['orderby']);//排序条件
        }
        //搜索条件
        switch (session('conditions')) {
            case 1:
                $conditions = 'article_title';
                break;
            case 2:
                $conditions = 'article_keywords';
                break;
            case 3:
                $conditions = 'article_description';
                break;

            default:
                $conditions = '';
                break;
        }

        //排序条件
        if(session('orderby') == 'pubdate'){
            $orderby = 'article_time';
        }else{
            $orderby = 'article_id';
        }
        //查询栏目列表  移动文章时用
        $column = M('Column');
        $columns = $column->where('column_userid = '.session('homeuser.id'))->field('column_id,column_name')->select();
        $this->assign('columns',$columns);
        $article = D('Article');
        $count = $article->where("{$conditions} like '%".session('keywords')."%' and article_userid =".session('homeuser.id'))->count();
        $num     = 15;//每页显示的条数
        $number  =  ceil($count / $num);//页码数
        $page    = new \Think\Page($count,$num);
        $articles = $article->where("{$conditions} like '%".session('keywords')."%' and article_userid =".session('homeuser.id'))->limit($page->firstRow.','.$page->listRows)->order("$orderby")->article(null,$conditions);
        $show    = $page->show();// 分页显示输出
        $this->assign('articles',$articles);
        $this->assign('show',$show);//分页
        $this->assign('number',$number);//页码数
        $this->assign('count',$count);//总条数
        $this->display('article');

    }

        /**************************************文章管理结束**************************************/
        /**************************************轮播图管理**************************************/

    /**
    * 轮播图列表
    */
    public function carousel()
    {
         C('DB_PREFIX','cms_');
        $carousel   = D('Carousel');
        $count      = $carousel->where('carousel_userid ='.session('homeuser.id'))->count();//总条数
        $num        = 4;//每页显示条数
        $number     = ceil($count / $num);//页数
        $page       = new \Think\Page($count,$num);// 
        $show       = $page->show();// 分页显示输出
        $carousels  = $carousel->limit($page->firstRow.','.$page->listRows)->carousel($cid);
        $this->assign('show',$show);//分页
        $this->assign('number',$number);//页码数
        $this->assign('count',$count);//总条数
        $this->assign('carousels',$carousels);
        $this->display();
    }

    /**
     * 添加轮播图  修改轮播图
     */
    public function carousel_add()
    {
         C('DB_PREFIX','cms_');
        if(IS_POST){
            //验证令牌
             $carousel = M('Carousel');
             if(!$carousel->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";
             }
            if($_FILES['carousel_pic']['error'] != 4){
                 $username   = session('homeuser.name');//获取用户名 上传图片用到的路径
               if(!file_exists('./Customer_Uploads/'.$username)){
                    mkdir('./Customer_Uploads/'.$username,0777);
                    chmod('./Customer_Uploads/'.$username,0777);
                }
                //如果是修改 查询原有图片 缓存起来 添加成功后删除
                if($_GET['id']){
                    $carousel_res = $carousel->where('carousel_id ='.$_GET['id'])->field('carousel_pic')->find(); 
                    S(session('homeuser.name').'carousel_pic',$carousel_res['carousel_pic']);
                }
                //上传缩略图
                $upload = new \Think\Upload();// 实例化上传类    
                $upload->maxSize   =     3145728 ;// 设置附件上传大小    
                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型   
                $upload->rootPath  =     './Customer_Uploads/';   
                $upload->savePath  =      "{$username}/"; // 设置附件上传目录    // 上传文件     
                $info   =   $upload->upload(); 
                if($info){
                    if($info['carousel_pic']){
                        $_POST['carousel_pic'] = '/Customer_Uploads/'.$info['carousel_pic']['savepath'].$info['carousel_pic']['savename'];
                    }
                }else{
                    $this->error($upload->getError()); 
                }
            }

            //如果get下的id为真就是修改 否则是添加
            $id = $_GET['id'];
            if($id){
                $res = $carousel->where("carousel_id = {$id}")->save($_POST);
                if($res){
                    if(S(session('homeuser.name').'carousel_pic')){@unlink('.'.S(session('homeuser.name').'carousel_pic'));}
                }
            }else{
                $_POST['carousel_userid'] = session('homeuser.id'); 
                $_POST['carousel_addtime'] = time();
                $res = $carousel->add($_POST);
            }
             S(session('homeuser.name').'carousel_pic',null);
            if($res){
                    
                    $this->redirect('Websites/carousel');
            }else{
                    if($id){
                            $this->redirect('Websites/carousel_add',array('error'=>'修改失败！','id'=>$id));
                    }else{
                            $this->redirect('Websites/carousel_add',array('error'=>"添加失败！"));
                    }
            }

        }else{

            $id = I('id');
            if($id){
                $carousel = M('Carousel');
                $res = $carousel->where("carousel_id = {$id}")->find();
                $this->assign('carousel',$res);
            }
            $this->display();
        }
    }

    /**
     * 删除轮播图
     */
    public function carousel_delete()
    {
        $id = $_POST['id'];
        $carousel = M('Cms_carousel');
        //缓存图片 删除用
        if($id){
            $carousel_res = $carousel->where('carousel_id ='.$id)->field('carousel_pic')->find(); 
            S(session('homeuser.name').'carousel_pic',$carousel_res['carousel_pic']);
        }
        $carousel_res = $carousel->where("carousel_id = {$id}")->delete();
        if($carousel_res){
                if(S(session('homeuser.name').'carousel_pic')){@unlink('.'.S(session('homeuser.name').'carousel_pic'));}
                S(session('homeuser.name').'carousel_pic',null);
                $this->ajaxReturn(1);//删除成功
        }else{
            $this->ajaxReturn('删除失败！');
        }
    }

    /**
     * 批量删除轮播图
     */
    public function carousel_delarc()
    {
        $carousel_arrid = $_POST['arr'];
        $carousel = M('Cms_carousel');
        //缓存要删除的图片
        if($carousel_arrid){
            foreach($carousel_arrid as $v){
                $carousel_res[] = $carousel->where('carousel_id ='.$v)->field('carousel_pic')->find(); 
            }
                S(session('homeuser.name').'carousel_pic',$carousel_res);
        }
       
        foreach($carousel_arrid as $id){
            $strid .= ','.$id;//把id拼接成用逗号隔开的字符串
        }
        $res = $carousel->delete(substr($strid,1));//截取前面的逗号并执行删除
        //删除图片
        if($res){
            foreach(S(session('homeuser.name').'carousel_pic') as $v){
                @unlink('.'.$v['carousel_pic']);
            }
        }
                S(session('homeuser.name').'carousel_pic',null);

        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn('批量删除失败！');
        }
    }
        /**************************************轮播图管理结束**************************************/
        /**************************************友情链接管理**************************************/
    /**
     * 友情链接列表
     */
    public function friendship(){
        C('DB_PREFIX','cms_');
        $friendship     = D('Friendship');
        $count      = $friendship->where('friendship_userid ='.session('homeuser.id'))->count();//总条数
        $num        = 4;//每页显示条数
        $number     = ceil($count / $num);//页数
        $page       = new \Think\Page($count,$num);// 
        $show       = $page->show();// 分页显示输出
        $friendships    = $friendship->where('friendship_userid ='.session('homeuser.id'))->limit($page->firstRow.','.$page->listRows)->friendship($cid);
        $this->assign('show',$show);//分页
        $this->assign('number',$number);//页码数
        $this->assign('count',$count);//总条数
        $this->assign('friendships',$friendships);
        $this->display();
    }


    /**
     * 添加友链  修改友链
     */
    public function friendship_add()
    {
        if(IS_POST){
             //如果get下的id为真就是修改 否则是添加
             $id = $_GET['id'];
             $friendship = M('Cms_friendship');
             //验证令牌
             if(!$friendship->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";
             }
            if($_FILES['friendship_logo']['error'] != 4){
                if($id){
                    $pic = $friendship->where('friendship_id ='.$id.' and friendship_userid ='.session('homeuser.id'))->field('friendship_logo')->find();
                    S(session('homeuser.name').'friendship_logo',$pic['friendship_logo']);
                }
                $username = session('homeuser.name');
                if(!file_exists('./Customer_Uploads/'.$username)){
                    mkdir('./Customer_Uploads/'.$username,0777);
                    chmod('./Customer_Uploads/'.$username,0777);
                }
                //上传缩略图
                $upload = new \Think\Upload();// 实例化上传类    
                $upload->maxSize   =     3145728 ;// 设置附件上传大小    
                $upload->rootPath  =     './Customer_Uploads/';   
                $upload->savePath  =      "{$username}/"; // 设置附件上传目录    // 上传文件     
                $info   =   $upload->upload(); 
                if($info){
                    if($info['friendship_logo']){

                        $_POST['friendship_logo'] = '/Customer_Uploads/'.$info['friendship_logo']['savepath'].$info['friendship_logo']['savename'];
                    }
                }else{
                    $this->error($upload->getError()); 
                }
            }
            if($id){
                $res = $friendship->where("friendship_id = {$id} and friendship_userid =".session('homeuser.id'))->save($_POST);
                if($res){
                    @unlink('.'.S(session('homeuser.name').'friendship_logo'));
                }
            }else{
                $_POST['friendship_addtime'] = time();
                $_POST['friendship_userid'] = session('homeuser.id');
                $res = $friendship->add($_POST);
            }
            S(session('homeuser.name').'friendship_logo',null);
            if($res){
                    $this->redirect('Websites/friendship');
            }else{
                    if($id){
                            $this->redirect('Websites/friendship_add',array('error'=>'修改失败！','id'=>$id));
                    }else{
                            $this->redirect('Websites/friendship_add',array('error'=>"添加失败！"));
                    }
            }
        }else{
            $id = I('id');
            if($id){
                $friendship = M('Cms_friendship');
                $res = $friendship->where("friendship_id = {$id} and friendship_userid = ".session('homeuser.id'))->find();
                $this->assign('friendship',$res);
            }
            $this->display();
        }
    }




    /**
     * 删除
     */
    public function friendship_delete()
    {
        $id = $_POST['id'];
        $friendship = M('Cms_friendship');
        if($id){
                    $pic = $friendship->where('friendship_id ='.$id.' and friendship_userid ='.session('homeuser.id'))->field('friendship_logo')->find();
                    S(session('homeuser.name').'friendship_logo',$pic['friendship_logo']);
                }

        $friendship_res = $friendship->where("friendship_id = {$id} and friendship_userid =".session('homeuser.id'))->delete();
         if($friendship_res){
                    @unlink('.'.S(session('homeuser.name').'friendship_logo'));
                    S(session('homeuser.name').'friendship_logo',null);
        }else{
                    S(session('homeuser.name').'friendship_logo',null);
        }
        if($friendship_res){
                $this->ajaxReturn(1);//删除成功
        }else{
            $this->ajaxReturn('删除失败！');
        }
    }

    /**
     * 批量删除
     */
    public function friendship_delarc()
    {
        $friendship_arrid = $_POST['arr'];
        $friendship = M('Cms_friendship');
          //缓存要删除的图片
        if($friendship){
            foreach($friendship_arrid as $v){
                $friendship_res[] = $friendship->where('friendship_id ='.$v)->field('friendship_logo')->find(); 
            }
                S(session('homeuser.name').'friendship_logo',$friendship_res);
        }
        foreach($friendship_arrid as $id){
            $strid .= ','.$id;//把id拼接成用逗号隔开的字符串
        }
        $res = $friendship->delete(substr($strid,1));//截取前面的逗号并执行删除
         //删除图片
        if($res){
            foreach(S(session('homeuser.name').'friendship_logo') as $v){
                @unlink('.'.$v['friendship_logo']);
            }
        }
        S(session('homeuser.name').'friendship_logo',null);
        if($res){

            $this->ajaxReturn(1);
        }else{

            $this->ajaxReturn('批量删除失败！');
        }

        
    }
        /**************************************友情链接管理结束**************************************/
    /**
     * 图片管理
     */
    public function posters()
    {
        $posters = M('Cms_posters');
        $res = $posters->where('posters_userid = '.session('homeuser.id'))->select();
        $this->assign('posters',$res);
        $this->display();
    }


    public function add_posters()
    {
        C('DB_PREFIX','cms_');
        if(IS_POST){
            $posters = D('posters');
            $id = I('get.id');
            //验证令牌
             if(!$posters->autoCheckToken($_POST)){
                echo "<script>alert('令牌错误！');window.history.go(-1);</script>";exit;
             }
             if(!$_POST['type']){echo "<script>alert('请选择页面！');window.history.go(-1);</script>";exit;}
             if(!$id){
                if($_FILES['pic']['error'] == 4){
                    echo "<script>alert('请选择图片！');window.history.go(-1);</script>";exit;
                }
             }
            $data = $posters->posters_data($_POST);

            if($_FILES['pic']['error'] != 4){
                if($id){
                    $pic = $posters->where('posters_id ='.$id.' and posters_userid ='.session('homeuser.id'))->field('posters_pic')->find();
                    S(session('homeuser.name').'posters_pic',$pic['posters_pic']);
                }
                $username = session('homeuser.name');
                if(!file_exists('./Customer_Uploads/'.$username)){
                    mkdir('./Customer_Uploads/'.$username,0777);
                    chmod('./Customer_Uploads/'.$username,0777);
                }
                //上传缩略图
                $upload = new \Think\Upload();// 实例化上传类    
                $upload->maxSize   =     3145728 ;// 设置附件上传大小    
                $upload->rootPath  =     './Customer_Uploads/';   
                $upload->savePath  =      "{$username}/"; // 设置附件上传目录    // 上传文件     
                $info   =   $upload->upload(); 
                if($info){
                    if($info['pic']){

                        $data['posters_pic'] = '/Customer_Uploads/'.$info['pic']['savepath'].$info['pic']['savename'];
                    }
                }else{
                    $this->error($upload->getError()); 
                }
            }
            if($id){
                $res = $posters->where("posters_id = {$id} and posters_userid = ".session('homeuser.id'))->save($data);
                $x = '修改';
            }else{
                $res = $posters->add($data);
                $x = '添加';
            }
             if(S(session('homeuser.name').'posters_pic') && $data['posters_pic']){
                @unlink('.'.S(session('homeuser.name').'posters_pic'));
            }
            S(session('homeuser.name').'posters_pic',null);  

            if($res){
                echo "<script>alert('".$x."成功');window.location.href='".U('posters')."'</script>";
            }else{
              if($id){
                 $this->redirect('add_posters',array('id'=>$id));

              }else{

                 $this->redirect('add_posters');
              }
            }

        }else{

            $posters = M('posters');
            $id = I('get.id');
            if($id){
            $res = $posters->where("posters_id = {$id} and posters_userid = ".session('homeuser.id'))->find();
            $this->assign('posters',$res);
                
            }

            $this->display();
        }
    }

    public function posters_delete()
    {
        if(IS_AJAX){
            $id = I('id');
            $posters = M('Cms_posters');
           if($id){
                    $pic = $posters->where('posters_id ='.$id.' and posters_userid ='.session('homeuser.id'))->field('posters_pic')->find();
                    S(session('homeuser.name').'posters_pic',$pic['posters_pic']);
                    $res = $posters->where("posters_id = {$id} and posters_userid = ".session('homeuser.id'))->delete();

                }
            if(S(session('homeuser.name').'posters_pic') && $res){
                @unlink('.'.S(session('homeuser.name').'posters_pic'));
            }
            S(session('homeuser.name').'posters_pic',null);  
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn('删除失败！');
            }

            
        }
    }


    public function posters_only()
    {
        if(IS_AJAX){
            $type = I('type');
            if($type){
                $posters = M('Cms_posters');
                $res = $posters->where("posters_type = '{$type}' and posters_userid = ".session('homeuser.id'))->find();
                if($res){
                    $this->ajaxReturn('每个页面只能显示一张baauer图！添加多张将会被覆盖！');
                }
            }
        }
    }



    public function _empty()
    {
    	$this->display('Public/404');
    }


   
}