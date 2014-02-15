<?php
/**
 * File Name: ReserveAction.class.php
 * Author: Blue
 * Created Time: 2013-12-5 10:40:57
*/
class ReserveAction extends HomeCommonAction{
	/**
	 * 在线预约
	 */
	public function reserve(){
	  $reserveObj = D('Reserve');
	  $itemObj = D('Item');
	  $arrMap['site_id'] = array('eq', $_SESSION['site_id']);
	  $arrMap['pid'] = array('neq', 0);
	  $itemList = $itemObj->where($arrMap)->select();
	  $tplData = array(
			   'title' => '在线预约',
			   'reserveUrl' => U('Shenlan/Reserve/doAddReserve'),
			   );
	  $this->assign($tplData);
	  $this->display('Reserve:reserve');
	}

	/**
	 * 在线预约操作
	 */
	public function doAddReserve(){
	  $reserveObj = D('CmsReserve');
	  $insert = $this->_post();
	  $insert['ctime'] = time();
      $insert['wechat_id'] = $_SESSION['wechat_id'];
      $insert['open_id'] = $_SESSION['guest_open_id'];
      if($reserveObj->add($insert)){
          $this->success('预约成功');
      }else{
          $this->error('预约失败');
      }
	}
}
 
