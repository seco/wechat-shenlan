<?php
/**
 * File Name: LeaveAction.class.php
 * Author: Blue
 * Created Time: 2013-11-25 10:28:42
*/
class LeaveAction extends HomeCommonAction{
    /**
     * 获取评论列表
     */
    public function getLeaveList(){
		$leaveObj = D('CmsLeave');
        $data = $this->_post();
		$arrField = array('*');
		$arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
		$arrMap['fid'] = array('eq', 0);
        $arrMap['cat_id'] = $data['id'];
		$arrOrder = array('ctime desc');
        //获取cat信息
        $news_id = D('CmsCat')->where('id='.$data['id'])->getField('news_id');
        $itemInfo = D('PushNews')->where('id='.$news_id)->find();
        //获取评论列表
		$leaveList = $leaveObj->getList($arrField, $arrMap, $arrOrder, 0, $data['length']);
		$arrFormatField = array('ctime_text');
		foreach($leaveList as $k=>$v){
			$leaveList[$k] = $leaveObj->format($v, $arrFormatField);
			$leaveList[$k]['leave'] = $leaveObj->where('fid='.$v['id'])->select();
		}
        $length = $data['length'] + 4;
        $tplData = array(
            'commentList' => $leaveList,
            'cat_id'      => $data['id'],
            'length'      => $length,
            'itemInfo'    => $itemInfo,
        );
        $this->assign($tplData);
        $this->display('Plugin:comment');
    }
    
	/**
	 * 添加留言或评论
	 */
	public function doAddLeave(){
		$leaveObj = D('CmsLeave');
		$insert = $this->_post();
        $fieldInfo = $_POST['field'];
		$insert['ctime'] = time();
		$insert['wechat_id'] = $_SESSION['wechat_id'];
		$insert['open_id'] = $_SESSION['guest_open_id'];
        $id = $leaveObj->add($insert);
        //扩展字段处理
        D('CmsField')->updateFieldList($fieldInfo, $id, 'cmsLeave');
        $news_id = D('CmsCat')->where('id='.$insert['cat_id'])->getField('news_id');
        D('PushNews')->where('id='.$news_id)->setInc('comments');
	}
}
