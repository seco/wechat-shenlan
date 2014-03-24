<?php
/**
 * 用户管理类
 * @author chen
 * @version 2014-03-12
 */
class UserAction extends AdminCommonAction
{
    /**
     * 用户列表
     */
    public function ls()
    {
        $userObj = D('User');
        $arrField = array();
        $arrMap = array();
        $arrOrder = array('id');
        $search = $this->_post();
        if(!empty($search)){
            $arrMap['username'] = array('like', '%'.$search['username'].'%');
        }
        $count = $userObj->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        $userList = $userObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('avatar_name', 'last_time_text');
        foreach($userList as $k=>$v){
            $userList[$k] = $userObj->format($v, $arrFormatField);
        }
        $data = array(
            'userList' => $userList,
        );
        $this->assign($data);
        $this->display();
    }

    /**
     * 添加用户
     */
    public function add()
    {
        $data = $this->_post();
        if(empty($data)){
            $this->display();
            exit;
        }
        $password = substr(time(), -5).mt_rand(2, 20);
        $data['password'] = md5($password);
        $data['reg_time'] = time();
        $data['surplus'] = '1';
        $data['avatar'] = '';
        D('User')->add($data);
        echo '用户名：'.$data['username'];
        echo '<br/>';
        echo '密码：'.$password;
    }

    /**
     * 删除用户
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
		D('User')->where($map)->delete();
		$this->success('删除成功');
    }

    /********************站点管理***********************/
    /**
     * 站点列表
     */
    public function siteList()
    {
        $id = $this->_get('id', 'intval');
        if(empty($id)){
            $this->error('ID is empty');
        }
        $siteObj = D('UserWechat');
        $arrField = array();
        $arrMap['user_id'] = array('eq', $id);
        $arrOrder = array('id');
        $siteList = $siteObj->getList($arrField, $arrMap, $arrOrder);
        $arrFormatField = array('logo_name', 'ctime_text');
        foreach($siteList as $k=>$v){
            $siteList[$k] = $siteObj->format($v, $arrFormatField);
        }
        $data = array(
            'siteList' => $siteList,
        );
        $this->assign($data);
        $this->display();
    }

}
