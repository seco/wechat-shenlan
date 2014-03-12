<?php
/**
 * 主题管理类
 * @author  chen
 * @version 2014-03-12
 */
class ThemeAction extends AdminCommonAction
{
    /**
     * 主题列表
     */
    public function ls()
    {
        $themeObj = D('CmsTheme');
        $arrField = array();
        $arrMap = array();
        $arrOrder = array('id');
        $search = $this->_post();
        if(!empty($search)){
            $arrMap['theme_name'] = array('like', '%'.$search['theme_name'].'%');
        }
        $count = $themeObj->getCount($arrMap);
        $page = page($count, 6);
        $pageHtml = $page->show();
        $themeList = $themeObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('rendering_name', 'ctime_text');
        foreach($themeList as $k=>$v){
            $themeList[$k] = $themeObj->format($v, $arrFormatField);
        }
        $data = array(
            'themeList' => $themeList,
            'pageHtml' => $pageHtml,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 添加主题
     */
    public function add()
    {
        $data = $this->_post();
        if(empty($data)){
            $this->display();
            exit;
        }
        $themeObj = D('CmsTheme');
        if(!empty($_FILES)){
            $picList = uploadPic();
            if($picList['code'] != 'error'){
                if(!empty($picList['img']['savename'])){
                    $data['rendering'] = $picList['img']['savename'];
                }
            }
        }
        $data['ctime'] = $data['mtime'] = time();
        $themeObj->add($data);
        $this->success('添加成功', U('Blue/Theme/ls'));
    }

    /**
     * 更新
     */
    public function edit()
    {
        $data = $this->_post();
        $themeObj = D('CmsTheme');
        if(empty($data)){
            $id = $this->_get('id', 'intval');
            $themeInfo = $themeObj->getInfoById($id);
            $themeInfo = $themeObj->format($themeInfo, array('rendering_name'));
            $this->assign('themeInfo', $themeInfo);
            $this->display();
            exit;
        }
        if(!empty($_FILES)){
            $picList = uploadPic();
            if($picList['code'] != 'error'){
                if(!empty($picList['img']['savename'])){
                    $data['rendering'] = $picList['img']['savename'];
                }
            }
        }
        $data['mtime'] = time();
        if($themeObj->save($data)){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }

    /**
     * 删除
     */
    public function del()
    {
		$delIds = array();
		$postIds = $this->_post('id');
		if (!empty($postIds)) {
			$delIds = $postIds;
		}
		$getId = intval($this->_get('id'));
		if (!empty($getId)) {
			$delIds[] = $getId;
		}
		if (empty($delIds)) {
			$this->error('请选择您要删除的数据');
		}
		$map['id'] = array('in', $delIds);
		D('CmsTheme')->where($map)->delete();
		$this->success('删除成功');
    }

}
