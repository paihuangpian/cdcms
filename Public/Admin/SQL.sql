create table `admin`(
	`id` tinyint unsigned not null auto_increment comment 'id',
	`name` varchar(30) character set utf8 collate utf8_general_ci not null comment '登陆名',
	`password` varchar(32) character set utf8 collate utf8_general_ci not null comment '登陆密码',
	`portrait` varchar(100) character set utf8 collate utf8_general_ci not null comment '头像',
	`login_time` varchar(50) character set utf8 collate utf8_general_ci not null comment '登陆时间',
	`logout_time` varchar(50) character set utf8 collate utf8_general_ci not null comment '退出时间',
	`level` tinyint not null comment '权限',
	primary key (`id`)		
);