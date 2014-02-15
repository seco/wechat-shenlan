<?php
/**
 * File Name: TextModel.class.php
 * Author: Blue
 * Created Time: 2013-11-16 14:21:30
*/
class PushTextModel extends CommonModel{
    /**
     * 获取text数据
     * @return xml $content xml数据
     * @param int $id 文本资源ID
     */
	public function getPush($id){
	  $textInfo = D('PushText')->getInfoById($id);
	  return $this->setText($textInfo['content']);
	}

	/**
	 * 组装text
	 */
	public function setText($content){
	  $texttpl = D('Tpl')->where('type="text"')->getField('texttpl');
	  $content = sprintf($texttpl, $content);
	  return ($content);
	}

    
	/**
	 * 首页方法
	 */
	public function format($info, $arrFormatField){
		//站点名称
		if(in_array('site_name', $arrFormatField)){
			$info['site_name'] = D('Website')->where('id='.$info['site_id'])->getField('site_name');
		}
		//时间
		if(in_array('mtime_text', $arrFormatField)){
			$info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
		}
		return $info;
	}
} 
