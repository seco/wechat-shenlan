<?php
/**
 * File Name: LeaveModel.class.php
 * Author: Blue
 * Created Time: 2013-11-25 10:23:59
*/
class CmsLeaveModel extends CommonModel{
	/**
	 * 输出格式化
	 */
	public function format($info, $arrFormatField){
		//时间
		if(in_array('ctime_text', $arrFormatField)){
			$info['ctime_text'] = date('Y-m-d H:i', $info['ctime']);
		}
        //文章标题
        if(in_array('cat_title', $arrFormatField)){
            $news_id = D('CmsCat')->where('id='.$info['cat_id'])->getField('news_id');
            $info['cat_title'] = D('PushNews')->where('id='.$news_id)->getField('title');
        }
		return $info;
	}
}
 
