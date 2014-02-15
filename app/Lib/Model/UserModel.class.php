<?php
/**
 * File Name: UserModel.class.php
 * Author: Blue
 * Created Time: 2013-11-15 9:02:37
*/
class UserModel extends CommonModel{
	/**
	 * 输出格式化
	 */
	public function format($info, $arrFormatField){
		//分组
		if(in_array('group_name', $arrFormatField)){
			$info['group_name'] = ($info['group_id'] == 1) ? '管理员' : '普通会员';
		}
		//时间
		if(in_array('last_time_text', $arrFormatField)){
			$info['last_time_text'] = date('Y-m-d H:i', $info['last_time']);
		}
		//头像
		if(in_array('avatar_name', $arrFormatField)){
			$info['avatar_name'] = getPicPath($info['avatar']);
		}
		return $info;
	}
}
