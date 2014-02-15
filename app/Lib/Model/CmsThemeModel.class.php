<?php
/**
 * File Name: ThemeModel.class.php
 * Author: Blue
 * Created Time: 2013-11-21 13:56:30
*/
class CmsThemeModel extends CommonModel{
	/**
	 * 格式化
	 */
	public function format($info, $arrFormatField){
		//时间
		if(in_array('mtime_text', $arrFormatField)){
			$info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
		}
		//效果图
		if(in_array('rendering_name', $arrFormatField)){
			$info['rendering_name'] = getPicPath($info['rendering']);
		}
		//类型
		if(in_array('type_name', $arrFormatField)){
			$info['type_name'] = '深蓝';
		}
		return $info;
	}
} 
