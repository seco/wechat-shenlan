<?php
/**
 * File Name: MessageAction.class.php
 * Author: Blue
 * Created Time: 2013-11-11 17:26:39
*/
class MessageAction extends AdminCommonAction{
	/**
	 * 首页方法
	 */
	public function messageList(){
		$messageObj = D('MessageLog');
		$arrField = array('*');
		$arrMap['site_id'] = array('in', $_SESSION['site_arrIds']);
        //获取搜索条件
        $searchList = $this->_request();
		$arrMap = array_merge($arrMap, $this->getTitleSearch($searchList));
 		//$arrMap = $this->getBodySearch($this->_get());
		$arrOrder = array('recive_time desc');
		$count = $messageObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$messageList = $messageObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('site_name', 'recive_time_text', 'guest_remarks');
		foreach($messageList as $k=>$v){
			$messageList[$k] = $messageObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '消息记录',
			'search' => '信息记录',
			'editUrl' => U('Admin/Remarks/addRemarks'),
			'delUrl' => U('Admin/Message/doDelMessage'),
			'pageHtml' => $pageHtml,
			'arrList' => $messageList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 查看页面
	 */
	public function viewMessage(){
		$messageObj = D('MessageLog');
		$id = intval($this->_get('id'));
		$messageInfo = $messageObj->getInfoById($id);
		//$arrFormatField = array('voice_name');
		//$messageInfo = array_merge($messageInfo, $messageObj->format($messageInfo, $arrFormatField));
		$arrView = array(
			'title' => '信息详情',
		);
		$tplData = array(
			'arrInfo' => $messageInfo,
			'arrView' => $arrView,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 删除
	 */
	public function doDelMessage(){
		$messageObj = D('MessageLog');
		//数据
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
		  $delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
		  $delIds[] = $getId;
		}
		//删除数据
		if (empty($delIds)) {
		  $this->error('请选择您要删除的数据');
		}
		$arrMap['id'] = array('in', $delIds);
		if($messageObj->where($arrMap)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

} 
