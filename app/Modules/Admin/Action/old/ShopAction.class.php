<?php
/**
 * File Name: ShopAction.class.php
 * Author: Blue
 * Created Time: 2013-11-28 11:56:03
*/
class ShopAction extends AdminCommonAction
{
	
	/**
	 * 创始人
	 */
	public function editAdmin()
	{
		$adminDao = M('shop_admin');
		$adminInfo = $adminDao->table('shop_admin')->where('id=1')->order('id desc')->find();
		//输出到模板
		$tplData = array(
			'adminInfo' => $adminInfo,	
		);
		$this->assign($tplData);
		$this->display();
	}
	
	public function doEditAdmin()
	{
		//模型
		$adminDao = M('shop_admin');

		//表单数据
		$username = trim($this->_post('username'));
		$email    = trim($this->_post('email'));
		$pass     = trim($this->_post('pass'));
		$repass   = trim($this->_post('repass'));

		//数据验证
		if (empty($username)) {
			$this->error('用户名不能为空');
		}
		if (!empty($pass) && ($pass != $repass)) {
			$this->error('两次密码输入不一致');
		}
		
		//更新数据
		$update = array(
			'username' => $username,
			'email'    => $email,
		);
		if (!empty($pass)) {
			$update['password'] = md5($pass);
		}
		$adminDao->table('shop_admin')->where('id=1')->save($update);
		$this->success('提交成功');
	}

}