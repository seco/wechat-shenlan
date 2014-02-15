<?php
/**
 * 微信公众账号管理系统基础控制器类
 * @author blue
 * @version 2013-12-23
 */
class WechatCommonAction extends AdminCommonAction {
    /**
     * 判断是否选择了公众账号
     */
    public function _initialize(){
        parent::_initialize();
        if(!isset($_SESSION['wechat_id'])){
            $this->redirect('Admin/Index/index');
        }
        $this->assign('current', 'wechat');
        $this->assign('tabList', D('Tab')->getTabList());
    }

    /**
     * 获取token
     */
    public function getToken(){
        $siteInfo = D('UserWechat')->where('id='.$_SESSION['wechat_id'])->find();
        $grant_type = 'client_credential';
        $appid = $siteInfo['appid'];
        $appsecret = $siteInfo['appsecrect'];
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type='.$grant_type.'&appid='.$appid.'&secret='.$appsecret;
        //通过CURL获取access token信息
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        //将获取到的内容json解码为类
        $result = json_decode($result);
        if($result->expires_in !== 7200){
        }
        return $result->access_token;
    }

}
