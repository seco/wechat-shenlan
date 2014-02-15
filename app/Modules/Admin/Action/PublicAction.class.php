<?php
/**
 * 后台-首页
 * @author blue
 * @version 2013-12-19 
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
		$user = M('User');
        // 检查账号
        if (empty($_POST['username']) ) {
			$this->error('账号不能为空');
		}
        //检查密码
        if (empty($_POST['password']) ) {
            $this->error('密码不能为空');
        }

		$map['username'] = array('eq', $_POST['username']);
		$result = $user->where($map)->find();
		if(empty($result)){
			$this->error('账号不存在');
		}else{
			if($result['password'] === md5($_POST['password'])){
                $this->setSession($result['id']);
				$url = U('Admin/Wechat/wechatList');
				$this->success('登录成功', $url);
			}else{
				$this->error('登录失败');
			}
		}
    }

    /**
     * 首次登录后的SESSION处理工作
     */
    private function setSession($id){
        $_SESSION['uid'] = $id;
        $userInfo = D('User')->getInfoById($id);
        $userInfo = D('User')->format($userInfo, array('avatar_name'));
        $_SESSION['userInfo'] = $userInfo;
        $_SESSION['current_ip'] = get_client_ip();
        $_SESSION['current_time'] = time();
    }

    /**
     * 用户登出
     */
    public function logout() {
        $url = U('Admin/Public/login');
        //存储此次用户的登录信息
		$update = array(
			'last_time' => $_SESSION['current_time'],
			'last_ip' => $_SESSION['current_ip'],
		);
		D('User')->where('id='.$_SESSION['uid'])->save($update);
        unset($_SESSION);
		session_destroy();
        $this->success('登出成功！', $url);
    }

}
