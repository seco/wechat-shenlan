<?php
/**
 * 配置文件
 * @version 2013-10-05
 */
return array(
	'URL_MODEL' => 0,
 	'DB_TYPE'=>'mysql',
	'DB_HOST'=>'localhost',
	'DB_NAME'=>'wechat_db',
	'DB_USER'=>'wechat',
	'DB_PWD'=>'wechat',

	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'wx_',
    
	'TMPL_L_DELIM' => '{',
	'TMPL_R_DELIM' => '}',
	
	'APP_AUTOLOAD_PATH'=>'@.TagLib',
	'APP_GROUP_MODE'=>1,
	'APP_GROUP_LIST'=>'Shenlan, Admin, Mobile, Blue',
	'DEFAULT_GROUP'=>'Admin',
	'SHOW_PAGE_TRACE'=>false,

);
