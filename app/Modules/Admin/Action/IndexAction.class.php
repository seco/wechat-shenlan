<?php
/**
 * 后台首页
 * @author blue
 * @version 2013-12-19
 */
class IndexAction extends AdminCommonAction {
    
    //框架页
    public function index() {
        C('SHOW_PAGE_TRACE', false);
        //$this->assign('channel', $this->_getChannel());
        //$this->assign('menu',    $this->_getMenu());
        $this->redirect('Wechat/wechatList');
    }

    /**
     * 首页
     */
    public function main() {
        echo '<h2>深蓝微信平台</h2>';
        $this->display();
    }

    /**
     * 头部菜单
     */
    protected function _getChannel() {
		$arrList = array();
		$tabList = $this->getMenuList();
		foreach($tabList as $k=>$v){
			$arrList[$v['spell']] = $v['name'];
		}
		return $arrList;
	}

    /**
     * 左侧菜单
     */
    protected function _getMenu() {
        $menu = array();
		$first_list = $this->getMenuList();
		foreach($first_list as $k=>$v){
			$second_list = $this->getMenuList($v['id']);	
			foreach($second_list as $k2=>$v2){
				$third_list = $this->getMenuList($v2['id']);
				foreach($third_list as $k3=>$v3){
					$third_real_list[$v3['name']] = U($v3['url']);
					$second_real_list[$v2['name']] = $third_real_list;
				}
				$third_real_list = array();
			}
			$menu[$v['spell']] = $second_real_list;
			$second_real_list = array();
		}
        return $menu;
    }

	/**
	 * 获取菜单列表
	 */
	private function getMenuList($id=0){
		$tabObj = D('Tab');
		$arrField = array('*');
		$arrMap['fid'] = array('eq', $id);
        //tab信息存储位置
		$arrMap['id'] = array('in', $_SESSION['info']['tab']);
		$arrOrder = array();
		$tabList = $tabObj->getList($arrField, $arrMap, $arrOrder);
		return $tabList;
	}
}
