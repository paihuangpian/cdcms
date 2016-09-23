<?php


namespace Home\Model;
use Think\Model;

class FriendshipModel extends Model
{
	public function friendship($cid=null)
	{
		

		$friendship = $this->where('friendship_userid ='.session('homeuser.id'))->select();

		$arr = array('开放浏览','关闭浏览');
		foreach($friendship as &$v){
			$v['friendship_addtime'] = date('Y-m-d',$v['friendship_addtime']); 
			$v['friendship_status'] = $arr[ $v['friendship_status'] ];
		}
		return $friendship;
		
	}


	
}
 
