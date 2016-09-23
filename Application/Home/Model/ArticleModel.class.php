<?php


namespace Home\Model;
use Think\Model;

class ArticleModel extends Model
{
	public function article($cid=null,$conditions=null)
	{
		//判断查询一个栏目的文章  还是查询所有
		if($cid){

			$article = $this->where("article_column = {$cid} and article_userid =".session('homeuser.id'))->select();
		}else{
			if($conditions){
				$article = $this->where("{$conditions} like '%".session('keywords')."%' and article_userid =".session('homeuser.id'))->select();
			}else{
			$article = $this->where('article_userid ='.session('homeuser.id'))->select();
				
			}

		}

		$arr = array('开放浏览','关闭浏览');
		foreach($article as &$v){
			$res = $this->table('Cms_column')->where("{$v['article_column']} = column_id")->field('column_name')->find();
			$v['article_column'] = $res['column_name'];
			$v['article_time'] = date('Y-m-d',$v['article_time']); 
			$v['article_status'] = $arr[ $v['article_status'] ];
		}
		return $article;
		
	}


	//文章s搜索
	public function search($conditions,$keywords,$orderby)
	{
		
	}
}
 
