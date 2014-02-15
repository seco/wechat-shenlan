<?php
/**
 * File Name: LoginAction.class.php
 * Author: Blue
 * Created Time: 2013-12-3 14:41:58
*/
class LoginAction extends CommonAction{
	/**
	 * 网页授权接口
	 */
	public function index(){
	  $appid = 'wx8549a801747b4a64';
	  $redirect_uri = 'http://www.kindherz.com?g=Wechat&m=Login&a=login';
      $redirect_uri = urlencode($redirect_uri);
	  $response_type = 'code';
	  $scope = 'snsapi_userinfo';
	  $state = 'ok';
	  $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type='.$response_type.'&scope='.$scope.'&state='.$state.'#wechat_redirect';
	  print_r($url);exit;
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_HEADER, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  print_r($result);
	}

	/**
	 * 根据code换取token
	 */
	public function getUserInfo(){
        $code = $this->_get('code');
        if(empty($code)){
            $this->redirect('Index/index');
            exit;
        }
        $state = $this->_get('state');
        $siteInfo = D('Website')->where('blue_key='.$state)->find();
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$siteInfo['appid'].'&secret='.$siteInfo['appsecrect'].'&code='.$code.'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
	}
}
 
