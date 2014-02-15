<?php
/**
 * 后台-全局控制器
 * @version 2013-08-28
 */
class GlobalAction extends AdminCommonAction {

    /**
     * 初始化
     */
    public function _initialize()
    {
        $settings = D('Settings')->getCache();
        $this->assign($settings);
    }
    
    /**
     * 基本设置
     */
    public function basic()
    {

        $this->display();
    }

    /**
     * seo设置
     */
    public function seo()
    {
        $this->display();
    }

    /**
     * 处理：编辑配置文件
     */
    public function doEditSetting()
    {
        $settingDao = D('Settings');
        $data = $this->_post();
        $settingDao->replaceSetting($data);
        $this->success('更新成功');
    }

    public function doEditSeo()
    {
        $settingDao = D('Settings');
        $data = $this->_post();
        $settingDao->replaceSetting($data);
        $this->success('更新成功');
    }
 
}
