<?php


namespace Home\Model;
use Think\Model;

class CarouselModel extends Model
{
	public function carousel($cid=null)
	{
		$carousel = $this->where('carousel_userid ='.session('homeuser.id'))->select();
		$arr = array('开放浏览','关闭浏览');
		foreach($carousel as &$v){
			$v['carousel_addtime'] = date('Y-m-d',$v['carousel_addtime']); 
			$v['carousel_status'] = $arr[ $v['carousel_status'] ];
		}
		return $carousel;
	}
}
 
