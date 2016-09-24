<?php
return array(
	//'配置项'=>'配置值'

	'__PUBLIC__' => __ROOT__ . '/Public',	//页面资源位置

	'SHOW_PAGE_TRACE'	=>	false,			//调试模式

	//设置模板定界符
	'TMPL_L_DELIM'	=>	'<{',				
	'TMPL_R_DELIM'	=>	'}>',				

	/*数据库配置*/
	'DB_TYPE'	=> 'mysql',					//数据库类型
	'DB_HOST'	=> 'localhost',				//数据库服务器地址
	'DB_NAME'	=> 'cdcms',					//数据库名
	'DB_USER'	=> 'root',					//用户名
	'DB_PWD'	=> 'root',					//密码
	'DB_PORT'	=> '3306',					//端口
	'DB_PREFIX'	=> '',						//数据库表前缀
	'DB_CHARSET'=> 'utf8',					//字符集
	'DB_DEBUG'	=> TRUE,					//数据库调试模式 开启后可以记录SQL日志

	/*令牌验证*/
	'TOKEN_ON' 		=> 	false,				//是否开启令牌验证
	'TOKEN_NAME' 	=> 	'token',			//令牌验证的表单隐藏字段名称
	'TOKEN_TYPE' 	=> 	'md5',				//令牌验证哈希规则
	'TOKEN_RESET'	=>	false, 				//令牌验证出错是否重置

	//引入自定义配置
	'LOAD_EXT_CONFIG'	=>	'const',

	//设置session过期时间
	'SESSION_OPTIONS'	=>	array( 
		'name'	=>	'user',
		'expire'=> 24 * 3600 ,
	),
);