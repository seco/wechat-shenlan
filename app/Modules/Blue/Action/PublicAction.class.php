<?php
/**
 * 后台-首页
 * @version 2013-09-10
 */
class PublicAction extends CommonAction
{
	/**
	 * 页面：后台登陆
	 */
	public function login()
	{
		
		$this->display();
	}


	/**
	 * 处理：后台登陆
	 */
	public function doLogin()
	{

		// 检查账号
		if (empty($_POST['name']) ) {
			$this->error('账号不能为空');
		}

		//检查密码
		if (empty($_POST['password']) ) {
			$this->error('密码不能为空');
		}

        /*
		//登陆验证
		$isLogin = true;
		//跳转
		if (!$isLogin) {
			$this->error('登陆失败');
		}
         */
		
        $map['username'] = array('eq', $_POST['name']);
        $map['group_id'] = array('eq', 1);
        $userInfo = D('User')->where($map)->find();
        if($userInfo['password'] == md5($_POST['password'])){
            $_SESSION['aid'] = $userInfo['id'];
            $_SESSION['user_info'] = $userInfo;
            $this->success('登陆成功', U('Blue/Index/index'));
        }else{
            $this->error('登录失败');
        }
	}






	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->error('没有登录',__GROUP__.'/Public/login');
		}
	}

	// 顶部页面
	public function top() {
		C('SHOW_RUN_TIME', false);			// 运行时间显示
		C('SHOW_PAGE_TRACE', false);
		$this->display();
	}

	public function drag(){
		C('SHOW_PAGE_TRACE',false);
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		$this->display();
	}

	// 尾部页面
	public function footer() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}

	// 菜单页面
	public function menu() {
		$this->checkUser();
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}



	public function index() {
		//如果通过认证跳转到首页
		redirect(__GROUP__);
	}

	// 用户登出
	public function logout() {
        if(isset($_SESSION['aid'])){
            unset($_SESSION['aid']);
            unset($_SESSION['user_info']);
        /*
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
         */
			session_destroy();
			$this->success('登出成功！', U('Blue/Public/login/'));
		}else {
			$this->error('已经登出！');
		}
	}

	// 登录检测
	public function checkLogin() {
		if(empty($_POST['account'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须！');
		}elseif (empty($_POST['verify'])){
			$this->error('验证码必须！');
		}
		//生成认证条件
		$map			=   array();
		// 支持使用绑定帐号登录
		$map['account']	= $_POST['account'];
		$map["status"]	=	array('gt',0);
		if(session('verify') != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}
		$authInfo = M('User')->where($map)->find();
		//使用用户名、密码和状态的方式进行认证
		if(false === $authInfo) {
			$this->error('帐号不存在或已禁用！');
		}else {
			if($authInfo['password'] != md5($_POST['password'])) {
				$this->error('密码错误！');
			}
			$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
			$_SESSION['email']	=	$authInfo['email'];
			$_SESSION['loginUserName']		=	$authInfo['nickname'];
			$_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
			if($authInfo['account']=='admin') {
				$_SESSION['administrator']		=	true;
			}
			//保存登录信息
			$User	=	M('User');
			$ip		=	get_client_ip();
			$time	=	time();
			$data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	$ip;
			$User->save($data);
			$this->success('登录成功！',__GROUP__.'/Index/index');

		}
	}
	// 更换密码
	public function changePwd() {
		$this->checkUser();
		//对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
		$map['password']= md5($_POST['oldpassword']);
		if(isset($_POST['account'])) {
			$map['account']	 =	 $_POST['account'];
		}elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
		}
		//检查用户
		$User	=   M("User");
		if(!$User->where($map)->field('id')->find()) {
			$this->error('旧密码不符或者用户名错误！');
		}else {
			$User->password	=	md5($_POST['password']);
			$User->save();
			$this->success('密码修改成功！');
		 }
	}

	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->display();
	}

	public function verify() {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
		import("@.ORG.Util.Image");
		Image::buildImageVerify(4,1,$type);
	}

	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
}
