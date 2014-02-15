<?php
/**
 * File Name: LeaveAction.class.php
 * Author: Blue
 * Created Time: 2013-11-12 10:38:45
*/
class LeaveAction extends WechatCommonAction{
	/**
	 * 首页方法
	 */
	public function leaveList(){
        //实例化模型
		$leaveObj = D('CmsLeave');
        //设置条件
		$arrField = array('*');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
		$arrOrder = array('ctime desc');
        //分页
		$count = $leaveObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
        //获取留言列表
		$leaveList = $leaveObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        //格式化
		$arrFormatField = array('ctime_text');
		foreach($leaveList as $k=>$v){
			$leaveList[$k] = $leaveObj->format($v, $arrFormatField);
		}
        //模板赋值
		$tplData = array(
            'editUrl' => U('Admin/Leave/editLeave'),
			'delUrl' => U('Admin/Leave/doDelLeave'),
			'itemList' => $leaveList,
			'pageHtml' => $pageHtml,
            'left_current' => 'leave',
		);
		$this->assign($tplData);
		$this->display();
    }

    /**
     * 查看详情
     */
    public function editLeave(){
        $id = intval($this->_get('id'));
        $leaveInfo = D('CmsLeave')->getInfoById($id);
        $leaveInfo = D('CmsLeave')->format($leaveInfo, array('cat_title'));
        $fieldList = D('CmsField')->getFieldList($id, 'cmsLeave');
        $tplData = array(
            'itemInfo' => $leaveInfo,
            'fieldList' => $fieldList,
            'left_current' => 'leave',
        );
        $this->assign($tplData);
        $this->display();
    }

	/**
	 * 留言删除
	 */
	public function doDelLeave(){
		$leaveObj = D('CmsLeave');
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
		if($leaveObj->where($arrMap)->delete()){
            D('CmsField')->delFieldList($delIds, 'cmsLeave');
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

}
 
