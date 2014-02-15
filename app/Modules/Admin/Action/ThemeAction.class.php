<?php
/**
 * File Name: ThemeAction.class.php
 * Author: Blue
 * Created Time: 2013-11-21 13:49:38
*/
class ThemeAction extends WechatCommonAction{
	/**
	 * 首页方法
	 */
	public function themeList(){
		$themeObj = D('CmsTheme');
		$arrField = array('*');
		$arrMap = array();
		$arrOrder = array('mtime desc');
		$count = $themeObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$themeList = $themeObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('mtime_text', 'rendering_name');
		foreach($themeList as $k=>$v){
			$themeList[$k] = $themeObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'addUrl' => U('Admin/Theme/addTheme'),
			'useUrl' => U('Admin/Theme/doUse'),
			'editUrl' => U('Admin/Theme/editTheme'),
			'delUrl' => U('Admin/Theme/doDelTheme'),
			'pageHtml' => $pageHtml,
			'itemList' => $themeList,
            'left_current' => 'theme',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 使用主题
	 */
	public function doUse(){
		$siteObj = D('CmsSetting');
		$id = intval($this->_get('id'));
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
		if($siteObj->where($arrMap)->setField('theme_id', $id)){
			$url = U('Admin/Theme/themeList');
			$this->success('使用主题成功', $url);
		}else{
			$this->error('使用主题失败');
		}
	}

	/**
	 * 主题添加页面
	 */
	public function addTheme(){
		$tplData = array(
			'title' => '主题添加',
			'addUrl' => U('Admin/Theme/doAddTheme'),
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 主题添加动作
	 */
	public function doAddTheme(){
		$themeObj = D('Theme');
		$insert = $this->_post();
		if(!empty($_FILES['cover']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$insert['cover'] = $picList['cover']['savename'];
			}
		}
		$insert['ctime'] = $insert['mtime'] = time();
		if($themeObj->add($insert)){
			$url = U('Admin/Theme/themeList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	 * 主题编辑页面
	 */
	public function editTheme(){
		$themeObj = D('Theme');
		$id = intval($this->_get('id'));
		$themeInfo = $themeObj->getInfoById($id);
		$arrFormatField = array('cover_name');
		$themeInfo = array_merge($themeInfo, $themeObj->format($themeInfo, $arrFormatField));
		$tplData = array(
			'title' => '编辑主题',
			'editUrl' => U('Admin/Theme/doEditTheme'),
			'arrInfo' => $themeInfo,
		);
		$this->assign($tplData);
		$this->display();
	}
	
	/**
	 * 主题编辑操作
	 */
	public function doEditTheme(){
		$themeObj = D('Theme');
		$update = $this->_post();
		if(!empty($_FILES['cover']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$update['cover'] = $picList['cover']['savename'];
			}
		}
		$update['mtime'] = time();
		if($themeObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

	/**
	 * 删除
	 */
	public function doDelTheme(){
	}

}
 
