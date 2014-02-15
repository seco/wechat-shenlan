<?php
/**
 * File Name: PushModel.class.php
 * Author: Blue
 * Created Time: 2013-11-13 11:33:21
*/
class PushModel extends CommonModel{
    /**
     * 抽象方法
     */
    public function getPush($id, $keyword){
    }
    
	/**
	 * 格式化
	 */
	public function format($info, $arrFormatField){
		//站点名称
		if(in_array('site_name', $arrFormatField)){
			$info['site_name'] = D('Website')->where('id='.$info['site_id'])->getField('site_name');
		}
		//图片
		if(in_array('cover_name', $arrFormatField)){
			$info['cover_name'] = getPicPath($info['cover']);
		}
		//时间
		if(in_array('mtime_text', $arrFormatField)){
			$info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
		}
		return $info;
	}
}
