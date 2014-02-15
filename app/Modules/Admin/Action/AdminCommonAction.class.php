<?php
/**
 * 后台-公共控制器
 * @author blue
 * @version 2013-12-19
 */
class AdminCommonAction extends CommonAction {
	/**
	 * 判断是否登录
	 */
	public function _initialize(){
		if(!isset($_SESSION['uid'])){
			$this->redirect('Admin/Public/login');
		}
        $this->assign('current', 'user');
	}

	/**
	 * 获取列表头部搜索条件
	 */
	public function getTitleSearch($searchList){
        $arrMap = array();
        if(!empty($searchList['title'])){
            $arrMap['title'] = array('like', '%'.$searchList['title'].'%');
        }
        if(!empty($searchList['site_type'])){
            $map['id'] = array('in', $_SESSION['site_arrIds']);
            $map['site_type'] = array('eq', $searchList['site_type']);
            $site_id = D('Website')->where($map)->getField('id');
            $arrMap['site_id'] = array('eq', $site_id);
			}
        if(!empty($searchList['open_id'])){
            $arrMap['open_id'] = array('eq', $searchList['open_id']);
        }
        if(!empty($searchList['site_id'])){
            $arrMap['site_id'] = array('eq', $searchList['site_id']);
        }
        if(!empty($searchList['blue_key'])){
            $arrMap['blue_key'] = array('eq', $searchList['blue_key']);
        }
        //微网站内容管理模块
        if(!empty($searchList['pid'])){
            $arrMap['pid'] = array('eq', $searchList['pic']);
        }
        if(!empty($searchList['name'])){
            $arrMap['name'] = array('like', '%'.$serachList['name'].'%');
        }
        if(!empty($searchList['open_id'])){
            $arrMap['open_id'] = array('eq', $searchList['open_id']);
        }
        return $arrMap;
	}

	/**
	 * 获取列表内部搜索条件
	 */
	public function getBodySearch($searchList){
        foreach($searchList as $k=>$v){
            $arrMap[$k] = array('eq', $v);
        }
        return $arrMap;
	}
}
