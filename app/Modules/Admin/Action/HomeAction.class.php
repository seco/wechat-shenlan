<?php
/**
 * 微信公众账号管理系统首页
 * @author blue
 * @version 2013-12-23
 */
class HomeAction extends AdminCommonAction {
   /**
    * Index
    */
    public function index(){
        $id = intval($this->_get('id'));
        $_SESSION['wechat_id'] = $id;
        $this->redirect('Admin/Log/logList', array('type'=>'visit'));
    } 
}
