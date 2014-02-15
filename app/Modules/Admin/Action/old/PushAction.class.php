<?php
/**
 * File Name: PushAction.class.php
 * Author: Blue
 * Created Time: 2013-11-9 14:23:18
*/
class PushAction extends AdminCommonAction{
	/**
	 * 回复图文消息素材列表
	 */
	public function pushList(){
		$pushObj = D('Push');
		$arrField = array('*');
		$arrMap['site_id'] = array('in', $_SESSION['site_arrIds']);
        //获取搜索条件
        $searchList = $this->_post();
        $arrMap = array_merge($arrMap, $this->getTitleSearch($searchList));
		$arrOrder = array();
		$count = $pushObj->getCount($arrMap);
		$page = page($count, 10);
		$pageHtml = $page->show();
		$pushList = $pushObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('cover_name', 'site_name', 'mtime_text');
		foreach ($pushList as $k=>$v){
			$pushList[$k] = $pushObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '图文素材',
			'search' => '图文搜索',
			'addUrl' => U('Admin/Push/addPush'),
			'editUrl' => U('Admin/Push/editPush'),
			'delUrl' => U('Admin/Push/doDelPush'),
			'arrList' => $pushList,
			'pageHtml' => $pageHtml,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 页面：添加图文素材
	 */
	public function addPush(){
		$siteObj = D('Website');
		//不需要分页的时候就简单一点
		$arrMap['id'] = array('in', $_SESSION['site_arrIds']);
		$siteList = $siteObj->where($arrMap)->select();
		$tplData = array(
			'title' => '新建图文',
			'addUrl' => U('Admin/Push/doAddPush'),
			'arrList' => $siteList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 操作：添加图文素材
	 */
	public function doAddPush(){
		$pushObj = D('Push');
		$insert = $this->_post();
		if(!empty($_FILES['pic']['name'])){
			$picList = uploadPic();
			$insert['cover'] = $picList['pic']['savename'];
		}
		$insert['content'] = htmlspecialchars_decode(stripslashes($insert['content']));
		$insert['ctime'] = $insert['mtime'] = time();
		$id = $pushObj->add($insert);
		if($id){
		  if(empty($insert['url'])){
		    $site_id = $pushObj->where('id='.$id)->getField('site_id');
		    $blue_key = D('Website')->where('id='.$site_id)->getField('blue_key');
		    $pushUrl = U('Shenlan/Item/itemInfo', array('id'=>$id,'blue_key'=>$blue_key));
		    $pushUrl = 'http://'.$_SERVER['HTTP_HOST'].$pushUrl;
		    $pushObj->where('id='.$id)->setField('url', $pushUrl);
		    $url = U('Admin/Push/pushList');
		    $this->success('添加成功', $url);
		  }
		}else{
		      $this->error('添加失败');
		}
	}

	/**
	 * 页面：编辑图文
	 */
	public function editPush(){
		$pushObj = D('Push');
		$id = intval($this->_get('id'));
		$pushInfo = $pushObj->getInfoById($id);
		$arrFormatField = array('cover_name');
		$pushInfo = array_merge($pushInfo, $pushObj->format($pushInfo, $arrFormatField));
		$tplData = array(
			'title' => '编辑图文',
			'editUrl' => U('Admin/Push/doEditPush'),
			'arrInfo' => $pushInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 操作：编辑图文素材
	 */
	public function doEditPush(){
		$pushObj = D('Push');
		$update = $this->_post();
		if(!empty($_FILES['pic']['name'])){
			$picList = uploadPic();
			$update['cover'] = $picList['pic']['savename'];
		}
		if(empty($update['url'])){
		  $site_id = $pushObj->where('id='.$update['id'])->getField('site_id');
		  $blue_key = D('Website')->where('id='.$site_id)->getField('blue_key');
		  $url = U('Shenlan/Item/itemInfo', array('id'=>$update['id'],'blue_key'=>$blue_key));
		  $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
		  $update['url'] = $url;
		}
		$update['content'] = htmlspecialchars_decode(stripslashes($update['content']));
		$update['mtime'] = time();
		if($pushObj->save($update)){
			$this->success('编辑成功');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	 * 图文删除
	 */
	public function doDelPush(){
		$pushObj = D('Push');
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
		if($pushObj->where($arrMap)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	///////////////////////文字素材管理/////////////////////
	/**
	 * 文字素材列表
	 */
	public function textList(){
		$textObj = D('Text');
		$arrField = array('*');
		$arrMap['site_id'] = array('in', $_SESSION['site_arrIds']);
        $searchList = $this->_post();
        $arrMap = array_merge($arrMap, $this->getTitleSearch($searchList));
		$count = $textObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$textList = $textObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('site_name', 'mtime_text');
		foreach($textList as $k=>$v){
			$textList[$k] = $textObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '文字素材',
			'search' => '文字标题',
			'addUrl' => U('Admin/Push/addText'),
			'editUrl' => U('Admin/Push/editText'),
			'delUrl' => U('Admin/Push/doDelText'),
			'pageHtml' => $pageHtml,
			'arrList' => $textList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 文字素材添加页面
	 */
	public function addText(){
		$siteObj = D('Website');
		$arrMap['id'] = array('in', $_SESSION['site_arrIds']);
		$siteList = $siteObj->where($arrMap)->select();
		$tplData = array(
			'title' => '添加文字',
			'addUrl' => U('Admin/Push/doAddText'),
			'arrList' => $siteList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 文字素材的添加操作
	 */
	public function doAddText(){
		$textObj = D('Text');
		$insert = $this->_post();
		$insert['ctime'] = time();
		if($textObj->add($insert)){
			$url = U('Admin/Push/textList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}
		
	/**
	 * 文字素材的编辑页面
	 */
	public function editText(){
		$textObj = D('Text');
		$id = $this->_get('id');
		$textInfo = $textObj->getInfoById($id);
		$tplData = array(
			'title' => '文字编辑',
			'editUrl' => U('Admin/Push/doEditText'),
			'arrInfo' => $textInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 文字素材的编辑操作
	 */
	public function doEditText(){
		$textObj = D('Text');
		$update = $this->_post();
		$update['mtime'] = time();
		if($textObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

	/**
	 * 文字素材的删除操作
	 */
	public function doDelText(){
		$textObj = D('Text');
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
		if($textObj->where($arrMap)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}

	///////////////////////规则管理/////////////////////////
	
	/**
	 * 响应规则列表
	 */
	public function typeList(){
		$typeObj = D('Type');
		$arrField = array('*');
		$arrMap['site_id'] = array('in', $_SESSION['site_arrIds']);
        $searchList = $this->_post();
		$arrMap = array_merge($arrMap, $this->getTitleSearch($searchList));
		$arrOrder = array('ctime desc');
		$count = $typeObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$typeList = $typeObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('site_name', 'mtime_text');
		foreach($typeList as $k=>$v){
			$typeList[$k] = $typeObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '类型列表',
			'search' => '类型搜索',
			'addUrl' => U('Admin/Push/addType'),
			'editUrl' => U('Admin/Push/editType'),
			'delUrl' => U('Admin/Push/doDelType'),
			'pageHtml' => $pageHtml,
			'arrList' => $typeList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 页面：添加规则
	 */
	public function addType(){
		$siteObj = D('Website');
		$arrMap['id'] = array('in', $_SESSION['site_arrIds']);
		$siteList = $siteObj->where($arrMap)->select();
		$tplData = array(
			'title' => '添加规则',
			'addUrl' => U('Admin/Push/doAddType'),
			'arrList' => $siteList,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 操作：添加规则
	 */
	public function doAddType(){
		$typeObj = D('Type');
		$insert = $this->_post();
		$insert['ctime'] = $insert['mtime'] = time();
		//获取blue_key
		$site_id = intval($this->_post('site_id'));
		$insert['site_id'] = $site_id;
		if($typeObj->add($insert)){
			$url = U('Admin/Push/typeList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}


	/**
	 * 编辑规则页面
	 */
	public function editType(){
		$typeObj = D('Type');
		$id = intval($this->_get('id'));
		$typeInfo = $typeObj->getInfoById($id);
		$arrFormatField = array('function_name_text');
		$typeInfo = array_merge($typeInfo, $typeObj->format($typeInfo, $arrFormatField));
		$tplData = array(
			'title' => '编辑规则',
			'editUrl' => U('Admin/Push/doEditType'),
			'typeInfo' => $typeInfo,
			'arrView' => $arrView,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 编辑规则操作
	 */
	public function doEditType(){
		$typeObj = D('Type');
		$update = $this->_post();
		if($typeObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

	//获取消息类型分类
	public function getEvent(){
		$pid = $_POST['pid'];
		echo json_encode(D('Category')->getMsgtypeList($pid));
		//print_r(json_encode(D('Category')->getMsgtypeList($pid)));exit;
	}

	/**
	 * 删除规则
	 */
	public function doDelType(){
		$typeObj = D('Type');
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
		if($typeObj->where($arrMap)->delete()){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}
