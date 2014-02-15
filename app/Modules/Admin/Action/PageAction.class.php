<?php
/**
 * File Name: PageAction.class.php
 * Author: Blue
 * Created Time: 2013-10-11 16:27:15
*/
class PageAction extends AdminCommonAction{
    /**
     * 功能介绍
     */
    public function intro(){
        $this->assign('current', 'intro');
        $this->display('intro');
    }

    /**
     * 使用指南
     */
    public function guide(){
        $this->assign('current', 'guide');
        $this->display('guide');
    }

    /**
     * 帮助中心
     */
    public function help(){
        $this->assign('current', 'help');
        $this->display('help');
    }


    /**
     * 单页列表
     */
    public function pageList()
    {
        //模型
        $pageDao = D('Page');
        //条件
        $arrField = array('id', 'title', 'spell', 'display_order', 'status', 'mtime');
        $arrMap['site_id'] = array('in', $_SESSION['site_arrIds']);
		$keyword = $this->_post('keyword');
		if(!empty($keyword)){
			$arrMap['title'] = array('like', '%'.$keyword.'%');
		}
        $arrOrder = array(
            'display_order'  => 'asc',
        );
        //列表
        $pageList = $pageDao->getList($arrField, $arrMap, $arrOrder);
        if (!empty($pageList)) {
            $arrFormatField = array('status_name', 'mtime_text', 'url_admin_edit', 'url_admin_del');
            foreach ($pageList as $k=>$v) {
                $pageList[$k] = $pageDao->format($v, $arrFormatField);
            }
		}
        // 输出到模板
        $tplData = array(
            'pageList' => $pageList,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 添加单页
     */
    public function addPage()
    {
        $this->display();
    }

    /**
     * 处理:添加单页
     */
    public function doAddPage()
    {
        //模型
        $pageDao = D('Page');
        //表单数据
        $insert = $this->_post('page');
		$insert['ctime'] = $insert['mtime'] = $this->_G['timestamp'];
		$insert['content'] = htmlspecialchars_decode(stripslashes($insert['content']));
		$insert['site_id'] = $_SESSION['service_id'];
        //插入数据
        $id = $pageDao->add($insert);
        //页面跳转
        $url = U('Admin/Page/pageList');
        $this->success('添加成功', $url);
    }

    /**
     * 编辑单页
     */
    public function editPage()
    {
        //模型
        $pageDao = D('Page');
        //表单数据
        $id = $this->_get('id');
        $pageInfo = $pageDao->getInfoById($id);
        //输出模板
        $tplData = array(
            'pageInfo' => $pageInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 处理:编辑单页
     */
    public function doEditPage()
    {
        //模型
        $pageDao = D('Page');
        //表单数据
        $id = intval($this->_post('id'));
        $update = $this->_post('page');
		$update['mtime'] = $this->_G['timestamp'];
		$update['content'] = htmlspecialchars_decode(stripslashes($update['content']));
		$update['site_id'] = $_SESSION['service_id'];
        //更新数据
        $pageDao->where('id='.$id)->save($update);
        //页面跳转
        $url = U('Admin/Page/editPage', array('id'=>$id));
        $this->success('修改成功', $url);
    }

    /**
     * 删除单页
     */
    public function doDelPage()
    {
        //模型
        $pageDao = D('Page');
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
        $pageDao->where($arrMap)->delete();
        //页面跳转
        $this->success('删除成功');
    }
}
