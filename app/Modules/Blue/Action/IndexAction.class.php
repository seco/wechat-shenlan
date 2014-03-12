<?php
/**
 * 首页
 * @version 2013-09-10
 */
class IndexAction extends AdminCommonAction
{

	//框架页
	public function index()
	{
		$this->assign('channel', $this->_getChannel());
		$this->assign('menu',    $this->_getMenu());
		C('SHOW_PAGE_TRACE', false);
		$this->display();
	}

	/**
	 * 首页
	 */
	public function main()
	{
		$this->display();
	}

	/**
	 * 顶部频道
	 */
	protected function _getChannel()
	{
		return array(
			'index'   => '首页',
			'content'   => '内容管理',
		);
	}

	/**
	 * 左侧菜单
	 */
	protected function _getMenu()
	{
		//初始化
		$menu = array();

		// 后台管理首页
		$menu['index'] = array(
			'首页' => array(
				'首页' => U('admin/index/main'),
			),
		);

		//全局
		$menu['global'] = array(
			'全局配置' => array(
				'基本信息' => U('admin/Global/basic'),
				'SEO设置'  => U('admin/global/seo'),
				'单页管理' => U('Admin/global/page'),
			),
		);
		//内容
		$menu['content'] = array(
            '用户管理' => array(
                '会员列表' => U('Blue/User/ls'),
            ),
            '主题管理' => array(
                '主题列表' => U('Blue/Theme/ls'),
            ),
            '栏目管理' => array(
                '栏目列表' => U('Blue/Tab/ls'),
            ),
		);
		return $menu;
	}
}
