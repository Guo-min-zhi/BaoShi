<?php
return array(
    'APP_DEBUG' => true,
    //开启日志
    'LOG_RECORD'=>true,
    //日志处理log类：lib/Think/Core/log.class.php中有处理级别，可以选择性的加入
    'LOG_RECORD_LEVEL'=>array('EMERG','ALERT', 'INFO'),

    'SHOW_PAGE_TRACE' =>true,
	//'配置项'=>'配置值'
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'baoshi', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'root', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀

	'LAYOUT_ON' => true,
	'LAYOUT_NAME' => 'layout',
);
?>