<?php
/**
 * File Name: ReserveAction.class.php
 * Author: Blue
 * Created Time: 2013-12-4 16:14:49
*/
class ReserveAction extends WechatCommonAction{
	/**
	 * 预约信息列表
	 */
	public function reserveList(){
        //实例化模型
	    $reserveObj = D('CmsReserve');
        //设置条件
	    $arrField = array('*');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
	    $arrOrder = array('ctime desc');
        //分页
	    $count = $reserveObj->getCount($arrMap);
	    $page = page($count);
	    $pageHtml = $page->show();
        //获取预约列表
	    $reserveList = $reserveObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        //格式化
	    $arrFormatField = array('ctime_text');
	    foreach($reserveList as $k=>$v){
	        $reserveList[$k] = $reserveObj->format($v, $arrFormatField);
	    }
        //模板赋值
	    $tplData = array(
            'pageHtml' => $pageHtml,
			'delUrl' => U('Admin/Reserve/doDelReserve'),
			'itemList' => $reserveList,
            'left_current' => 'reserveList',
        );
	    $this->assign($tplData);
	    $this->display();
	}

	/**
	 * 查看详情
	 */
	public function editReserve(){
        //实例化模型
	    $reserveObj = D('Reserve');
	    $id = intval($this->_get('id'));
	    $reserveInfo = $reserveObj->getInfoById($id);
        $this->assign('itemInfo', $reserveInfo);
	    $this->display();
    }

	/**
	 * 留言删除
	 */
	public function doDelReserve(){
		$reserveObj = D('CmsReserve');
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
		if($reserveObj->where($arrMap)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

}
 
