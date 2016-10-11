<?php


namespace Home\Model;
use Think\Model;

class PostersModel extends Model
{
 	
    public function posters_data($datas)
    {
    	$data['posters_userid'] 	= session('homeuser.id');//用户id
    	$data['posters_name'] 	    = $datas['name'];
        $data['posters_url']        = $datas['url'];
        $data['posters_type']       = $datas['type'];
        $data['posters_type']       = $datas['type'];
    	$data['posters_status'] 	= $datas['status'];
    	$data['posters_addtime'] 	= time();
    	return $data;
    }


    
}

 
