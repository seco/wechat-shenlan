<?php
/**
 * File Name: VisitLogModel.class.php
 * Author: Blue
 * Created Time: 2013-11-26 13:59:28
*/
class VisitLogModel extends CommonModel{
	/**
	 * 格式化
	 */
	public function format($info, $arrFormatField){
		//时间
		if(in_array('visit_time_text', $arrFormatField)){
			$info['visit_time_text'] = date('Y-m-d H:i', $info['visit_time']);
		}
		//访问者备注
		if(in_array('guest_remarks', $arrFormatField)){
		  $info['guest_remarks'] = D('Remarks')->where("open_id='".$info['guest_open_id']."'")->getField('name');
		}
		return $info;
	}
}
