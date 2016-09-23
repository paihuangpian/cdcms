<?php


namespace Home\Model;
use Think\Model;

class VipModel extends Model
{
 	

    public function user_vip($userid)
    {
            $user = $this->where("id = {$userid}")->find();

    	 	

             return $user;
    }
		



	
}
 
