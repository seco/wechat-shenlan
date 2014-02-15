<?php
/**
 * File Name: UserAction.class.php
 * Author: Blue
 * Created Time: 2013-11-11 17:45:17
*/
class UserAction extends AdminCommonAction{
	/**
	 * 会员列表
	 */
	public function userList(){
		$userObj = D('Users');
		$arrField = array('*');
		$arrMap = array();
		$keyword = $this->_post('keyword');
		if(!empty($keyword)){
			$arrMap['username'] = array('like', '%'.$keyword.'%');
		}
		$arrOrder = array('last_time desc');
		$count = $userObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
		$userList = $userObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('group_name', 'last_time_text');
		foreach($userList as $k => $v){
			$userList[$k] = $userObj->format($v, $arrFormatField);
		}
		$arrView = array(
			'title' => '会员列表',
			'search' => '会员姓名',
			'addUrl' => U('Admin/User/addUser'),
			'editUrl' => U('Admin/User/editPermission'),
			'edit' => U('Admin/User/editUser'),
		);
		$tplData = array(
			'arrView' => $arrView,
			'arrList' => $userList,
			'pageHtml' => $pageHtml,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 账户生成页面
	 */
	public function addUser(){
		$arrView = array(
			'title' => '账户生成',
			'addUrl' => U('Admin/User/doAddUser'),
		);
		$tplData = array(
			'arrView' => $arrView,
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 用户生成操作
	 */
	public function doAddUser(){
		$userObj = D('Users');
		$insert = $this->_post('arr');
		$insert['lasttime'] = time();
		$password = mt_rand();
		$insert['password'] = md5($password);
		$insert['group_id'] = '2';
		$insert['grant_type'] = 'client_credential';
		$insert['permission'] = '2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,41,42';
		if($userObj->add($insert)){
			echo ('用户名:'.$insert['username']);
			echo ('密码:'.$password);
			exit;
		}else{
			$this->error('添加失败');
		}
	}

	/**
	 * 用户权限管理界面
	 */
	public function editPermission(){
	  $permissionObj = D('Permission');
	  $userObj = D('Users');
	  $id = intval($this->_get('id'));
	  $userInfo = $userObj->getInfoById($id);
	  $arrFormatField = array('last_time_text', 'cover_name');
	  $userInfo = array_merge($userInfo, $userObj->format($userInfo, $arrFormatField));
	  //模块列表
	  $permissionList = $permissionObj->where('pid=0')->select();
	  foreach($permissionList as $k=>$v){
	    $permissionList[$k]['list'] = $permissionObj->where('pid='.$v['id'])->select();
	  }
	  $tplData = array(
			   'title' => '会员管理',
			   'editUrl' => U('Admin/User/doEditUser'),
			   'arrInfo' => $userInfo,
			   'permissionList' => $permissionList,
			   );
	  $this->assign($tplData);
	  $this->display();
	}
	

	//////////////////////////会员自管理////////////////////////////////////		
	/**
	 * 会员登录后获取到的自身信息
	 */
	public function basic(){
		$id = $_SESSION['uid'];
		$userObj = D('User');
		$userInfo = $userObj->getInfoById($id);
		$userInfo = $userObj->format($userInfo, array('avatar_name'));
		$tplData = array(
			'editUrl' => U('Admin/User/doEditUser'),
			'itemInfo' => $userInfo,
            'left_current' => 'basic',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 用户信息更新操作
	 */
	public function doEditUser(){
		$userObj = D('User');
		$update = $this->_post();
		if(!empty($_FILES['pic']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$update['avatar'] = $picList['pic']['savename'];
			}
		}
		$update['mtime'] = time();
		if($userObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

    /**
     * 更新密码界面
     */
    public function editPwd(){
        $this->assign('left_current', 'editPwd');
        $this->display();
    }

    /**
     * 更换密码
     */
	public function updatePwd() {
		$user = M("User");
		$map['id'] = $_SESSION['uid'];
		$map['password'] = md5($_POST['oldpassword']);
		if(empty($_POST['newpassword']) OR empty($_POST['repassword'])){
			$this->error('密码不能为空');
		}
        if(!$user->where($map)->find()) {
            $this->error('旧密码不符');
		}elseif($_POST['newpassword'] !== $_POST['repassword']) {
			$this->error('两次输入的密码不一致');
		}else{
            $user->password = md5($_POST['newpassword']);
            $user->save();
            $this->success('密码修改成功！');
         }
    }

	//////////////////////////////站点信息管理///////////////////////////
	/**
	 * 站点基本信息
	 */
	public function siteInfo(){
		$siteObj = D('Website');
		$arrMap['uid'] = array('eq', $_SESSION['uid']);
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
		$siteInfo = $siteObj->where($arrMap)->find();
		$arrFormatField = array('theme_name', 'cover_name');
		$siteInfo = array_merge($siteInfo, $siteObj->format($siteInfo, $arrFormatField));
		$tplData = array(
			'title' => '站点信息',
			'editUrl' => U('Admin/User/siteInfo'),
			'arrInfo' => $siteInfo,
		);
		$this->assign($tplData);
		$this->display();
	}

	//////////////////////////////系统功能模块管理/////////////////////////////////////
	/**
	 * 网站权限增加
	 */
	public function addPermission(){
		$permissionObj = D('Permission');
		$arrField = array('id', 'name', 'pid');
		$arrMap = array();
		$arrOrder = array('pid');
		$permissionList = $permissionObj->getList($arrField, $arrMap, $arrOrder);
		$arrFormatField = array('name_long');
		foreach($permissionList as $k=>$v){
			$permissionList[$k] = $permissionObj->format($v, $arrFormatField);
		}
		$tplData = array(
			'title' => '模板添加',
			'addUrl' => U('Admin/User/doAddPermission'),
			'arrList' => $permissionList,
		);
		$this->assign($tplData);
		$this->display();
	}
	/**
	 * 功能列表添加操作
	 */
	public function doAddPermission(){
		$permissionObj = D('Permission');
		$insert = $this->_post();
		$insert['ctime'] = $insert['mtime'] = time();
		if($permissionObj->add($insert)){
			$url = U('Admin/User/addPermission');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}
} 
