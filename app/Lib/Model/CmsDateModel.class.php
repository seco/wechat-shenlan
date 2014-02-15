<?php
/**
 * File Name: ReserveModel.class.php
 * Author: Blue
 * Created Time: 2013-12-4 16:30:19
*/
class CmsDateModel extends CommonModel{
	/**
	 * 格式化
	 */
  public function format($info, $arrFormatField){
    //时间
    if(in_array('reserve_time_text', $arrFormatField)){
      $info['reserve_time_text'] = date('Y-m-d H:i', $info['reserve_time']);
    }
    return $info;
  }
}
 
