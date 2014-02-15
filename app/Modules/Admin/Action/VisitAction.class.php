<?php
/**
 * File Name: VisitAction.class.php
 * Author: Blue
 * Created Time: 2013-11-26 11:21:07
*/
class VisitAction extends AdminCommonAction{
	/**
	 * 访问列表
	 */
	public function visitList(){
		$visitObj = D('VisitLog');
		$arrField = array('*');
		$arrMap = array();
		$arrMap['site_id'] = array('eq', $_SESSION['service_id']);
		if($this->_post()){
			$arrMap = $this->getTitleSearch($this->_post());
		}
		if($this->_get()){
			$arrMap = $this->getBodySearch($this->_get());
		}
		$arrOrder = array('visit_time desc');
		$count = $visitObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$visitList = $visitObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('visit_time_text', 'guest_remarks');
		foreach($visitList as $k=>$v){
			$visitList[$k] = $visitObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'arrList' => $visitList,
			'title' => '访问列表',
			'search' => '访问者名称',
			'editUrl' => U('Admin/Remarks/addRemarks'),
			'pageHtml' => $pageHtml,
		);
		$this->assign($tplData);
		$this->display();
	}
}
 
