<?php 

namespace Admin\Controller;
use Think\Controller;
class EmptyController extends Controller{

	public function index(){
		$cityName = CONTROLLER_NAME;
		exit('Could not locate remote server.');
	}

}


?>