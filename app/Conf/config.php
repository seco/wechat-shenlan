<?php
/**
 * 配置文件
 * @version 2013-10-05
 */
return array(
	'URL_MODEL' => 0,
 	'DB_TYPE'=>'mysql',
	'DB_HOST'=>'192.168.1.188',
	'DB_NAME'=>'wechat',
	'DB_USER'=>'root',
	'DB_PWD'=>'',

	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'wx_',
    
	'TMPL_L_DELIM' => '{',
	'TMPL_R_DELIM' => '}',
	
	'APP_AUTOLOAD_PATH'=>'@.TagLib',
	'APP_GROUP_MODE'=>1,
	'APP_GROUP_LIST'=>'Shenlan, Admin',
	'DEFAULT_GROUP'=>'Admin',
	'SHOW_PAGE_TRACE'=>true,

);
