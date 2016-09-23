<?php
return array(


	'DB_TYPE'               =>     'mysql',    	 	              // 数据库类型
    'DB_HOST'               =>      '192.168.1.20', 	          // 服务器地址
    'DB_NAME'               =>      'cdcms',          	          // 数据库名
    'DB_USER'               =>      'root',      		          // 用户名
    'DB_PWD'                =>      'root',                       // 密码
    'DB_PREFIX'             =>      '',    		                  // 数据库表前缀
    'URL_MODEL'             =>      2,          		          //重写模式
    'SHOW_PAGE_TRACE'       =>      true,       		          //开启页面trace
    'URL_HTML_SUFFIX'       =>      'html',      		          //设置伪静态
    'TMPL_L_DELIM'    		=>      '{{',
    'TMPL_R_DELIM'    		=>      '}}',


    'TOKEN_ON'              =>      true,                       // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'            =>      '__hash__',                 // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'            =>      'md5',                      //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'           =>      true,                       //令牌验证出错后是否重置令牌 默认为true




    /*配置邮件发送服务器*/
    'MAIL_HOST'             =>      'smtp.163.com',                 /*smtp服务器的名称、smtp.163.com*/
    'MAIL_SMTPAUTH'         =>      TRUE,                           /*启用smtp认证*/
    'MAIL_DEBUG'            =>      FLASE,                          /*是否开启调试模式*/
    'MAIL_USERNAME'         =>      'yundijz@163.com',              /*邮箱名称*/
    'MAIL_FROM'             =>      'yundijz@163.com',              /*发件人邮箱*/
    'MAIL_FROMNAME'         =>      '广州云狄网络科技有限公司',     /*发件人昵称*/
    'MAIL_PASSWORD'         =>      'ydjz888',                      /*发件人邮箱的密码*/
    'MAIL_CHARSET'          =>      'utf-8',                        /*字符集*/
    'MAIL_ISHTML'           =>      TRUE,                           /*是否HTML格式邮件*/

);