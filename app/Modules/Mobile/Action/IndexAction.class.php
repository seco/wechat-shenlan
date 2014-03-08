<?php
/**
 * 微网站统一控制器
 * @author chen
 * @version 2014-03-03
 */
class IndexAction extends MobileCommonAction
{
    /**
     * 首页控制函数
     */
    public function index()
    {
        $theme_name = $this->getThemeName();
        $this->display(ucfirst($theme_name).':index');
    }

    /**
     * 内页控制函数
     */
    public function item()
    {
        //获取栏目信息
        $id = intval($this->_get('id'));
        $info = $this->getItemInfo($id);
        $data = array(
            'info' => $info,
            'list' => $this->getItemList($id),
        );
        //浏览记录
        $this->setViews($id);
        $this->assign($data);
        $this->display($info['template_self']);
    }
}

