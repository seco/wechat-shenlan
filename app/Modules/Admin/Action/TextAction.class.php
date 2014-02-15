<?php
/**
 * File Name: TextAction.class.php
 * Author: Blue
 * Created Time: 2013-11-16 14:10:41
*/
class TextAction extends WechatCommonAction{
	/**
	 * 文字素材列表
	 */
	public function textList(){
		$textObj = D('PushText');
		$arrField = array('*');
		$arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrOrder = array('mtime desc');
		$count = $textObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$textList = $textObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('mtime_text');
		foreach($textList as $k=>$v){
			$textList[$k] = $textObj->format($v, $arrFormatField);
		}
		$textTpl = array(
			'addUrl' => U('Admin/Text/addText'),
			'editUrl' => U('Admin/Text/editText'),
			'delUrl' => U('Admin/Text/doDelText'),
			'pageHtml' => $pageHtml,
			'itemList' => $textList,
            'left_current' => 'text',
		);
		$this->assign($textTpl);
		$this->display();
	}

	/**
	 * 文字素材添加页面
	 */
	public function addText(){
		$tplData = array(
			'addUrl' => U('Admin/Text/doAddText'),
            'left_current' => 'text',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 文字素材的添加操作
	 */
	public function doAddText(){
		$textObj = D('PushText');
		$insert = $this->_post();
        $insert['wechat_id'] = $_SESSION['wechat_id'];

        //判断关键字是否可用
        if(!D('PushRoute')->checkKeyword($insert['keyword'])){
            $this->error('关键字不可用');
        }

		$insert['ctime'] = time();
        $id = $textObj->add($insert);
		if($id){
            D('PushRoute')->addRoute('pushText', $id, $insert['keyword']); 
			$url = U('Admin/Text/textList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	 * 文字素材的编辑页面
	 */
	public function editText(){
		$textObj = D('PushText');
		$id = $this->_get('id');
		$textInfo = $textObj->getInfoById($id);
        $routeInfo = D('PushRoute')->getRoute('pushText', $id);
		$tplData = array(
			'editUrl' => U('Admin/Text/doEditText'),
			'itemInfo' => $textInfo,
            'routeInfo' => $routeInfo,
            'left_current' => 'text',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 文字素材的编辑操作
	 */
	public function doEditText(){
		$textObj = D('PushText');
		$update = $this->_post();
        $routeInfo = $this->_post('route');

        //判断关键字是否可用
        if(!D('PushRoute')->checkKeyword($routeInfo['keyword'], $routeInfo['id'])){
            $this->error('关键字不可用');
        }

		$update['mtime'] = time();
		if($textObj->save($update)){
            D('PushRoute')->editRoute($routeInfo);
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

	/**
	 * 文字素材的删除操作
	 */
	public function doDelText(){
		$textObj = D('PushText');
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
		$arrMap['id'] = $arrRouteMap['obj_id'] = array('in', $delIds);
		if($textObj->where($arrMap)->delete()){
            D('PushRoute')->delRoute('pushText', $arrRouteMap);
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

}

