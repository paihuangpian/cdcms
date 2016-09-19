<?php 

namespace Admin\Controller;
use Think\Controller;
class AuthController extends Controller{
	public function _initialize(){

		if( !isServerName() || !Referer() && IS_AJAX || IS_POST ) 
			exit( $this->show( 'Could not locate remote server.' ) );

		if( !session('?user') ) exit( $this->redirect('Login/index') );

	}

}

?>