<?php
/**
 * File Name: WebsiteAction.class.php
 * Author: Blue
 * Created Time: 2013-11-18 17:21:11
*/
class WebsiteAction extends AdminCommonAction{
	/**
	 * 站点列表
	 */
	public function siteList(){
		$siteObj = D('Website');
		$arrField = array('*');
		$arrMap['id'] = array('in', $_SESSION['site_arrIds']);
		if($this->_post()){
			$arrMap = $this->getTitleSearch($this->_post());
		}
		$arrOrder = array();
		$count = $siteObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$siteList = $siteObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('cover_name', 'ctime_text');
		foreach($siteList as $k=>$v){
			$siteList[$k] = $siteObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '网站列表',
			'search' => '网站标题',
			'addUrl' => U('Admin/Website/addSite'),
			'editUrl' => U('Admin/Website/editSite'),
			'delUrl' => U('Admin/Website/doDelWebsite'),
			'pageHtml' => $pageHtml,
			'arrList' => $siteList,
		);
		$this->assign($tplData);
		$this->display();
	}
 
	/**
	 * 添加站点页面
	 */
	public function addSite(){
		$tplData = array(
			'title' => '添加站点',
			'addUrl' => U('Admin/Website/doAddSite'),
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 站点添加操作
	 */
	public function doAddSite(){
		$siteObj = D('Website');
		$insert = $this->_post();
		if(!empty($_FILES['cover']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$insert['cover'] = $picList['cover']['savename'];
			}
		}
		$insert['uid'] = $_SESSION['uid'];
		$insert['ctime'] = $insert['mtime'] = time();
		$rand = rand(1000, 9999);
		$insert['blue_key'] = $insert['ctime'].$rand;
		$insert['url'] = 'http://wechat.d-bluesoft.com/index.php?g=Shenlan&m=Item&a=index&blue_key='.$insert['blue_key'];
		if($siteObj->add($insert)){
			$url = U('Admin/Website/siteList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	 * 站点基本信息
	 */
	public function editSite(){
		$siteObj = D('Website');
		$id = intval($this->_get('id'));
		$arrMap['id'] = array('eq', $id);
		/*
		$update = $this->_post();
		if(!empty($update)){
			if(!empty($_FILES['cover']['name'])){
				$picList = uploadPic();
				if($picList['code'] != 'error'){
					$update['cover'] = $picList['cover']['savename'];
				}
			}
			if($siteObj->where($arrMap)->save($update)){
				$this->success('更新成功');
			}else{
				$this->error('更新失败');
			}
		}
		 */
		$siteInfo = $siteObj->where($arrMap)->find();
		$arrFormatField = array('theme_name', 'cover_name');
		$siteInfo = array_merge($siteInfo, $siteObj->format($siteInfo, $arrFormatField));
		$tplData = array(
			'title' => '站点信息',
			'editUrl' => U('Admin/Website/doEditSite'),
			'arrInfo' => $siteInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 更新站点信息操作
	 */
	public function doEditSite(){
		$siteObj = D('Website');
		$update = $this->_post();
		if(!empty($_FILES['cover']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$update['cover'] = $picList['cover']['savename'];
			}
		}
		if($siteObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}
}
