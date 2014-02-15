<?php
/**
 * File Name: RemarksAction.class.php
 * Author: Blue
 * Created Time: 2013-12-2 12:00:49
*/
class RemarksAction extends AdminCommonAction{
	/**
	 * 为微信号原始ID添加备注
	 */
	public function addRemarks(){
	  $remarksObj = D('Remarks');
	  $open_id = $this->_get('open_id');
	  $remarksInfo = $remarksObj->where("open_id='".$open_id."'")->find();
	  if(empty($remarksInfo)){
	    $arrInfo = array('open_id'=>$open_id);
	  }else{
	    $arrInfo = $remarksInfo;
	  }
	  $tplData = array(
			   'title' => '添加备注',
			   'addUrl' => U('Admin/Remarks/doAddRemarks'),
			   'arrInfo' => $arrInfo,
			   );
	  $this->assign($tplData);
	  $this->display();
	}

	/**
	 * 添加备注操作
	 */
	public function doAddRemarks(){
	  $remarksObj = D('Remarks');
	  $insert = $this->_post();
	  if($remarksObj->add($insert)){
	    $this->success('添加成功');
	  }else{
	    $this->error('添加失败');
	  }
	}
}
