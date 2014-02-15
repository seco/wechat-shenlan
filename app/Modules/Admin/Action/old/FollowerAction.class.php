<?php
/**
 * File Name: FollowerAction.class.php
 * Author: Blue
 * Created Time: 2013-12-5 11:16:08
*/
class FollowerAction extends WechatCommonAction{
    /**
     * 添加备注
     */
    public function addRemark(){
        $open_id = $this->_get('open_id');
        $this->assign('open_id', $open_id);
        $this->assign('left_current', 'log');
        $this->display();
    }

    /**
     * 添加备注操作
     */
    public function doAddRemark(){
        $followerObj = D('Follower');
        $insert = $this->_post();
        $insert['wechat_id'] = $_SESSION['wechat_id'];
        if($followerObj->add($insert)){
            $url = U('Admin/Log/logList', array('type'=>'api'));
            $this->success('备注更改成功', $url);
        }else{
            $this->error('备注添加失败');
        }
    }

	/**
	 * 获取关注者列表
	 */
	public function followerList(){
	  $token = $this->getToken();
	  $ch = curl_init();
	  $next_id = '';
	  $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$token."&next_openid=".$next_id;
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  $result = json_decode($result, true);
	  $count = $result['total'];
	  $page = page($count);
	  $pageHtml = $page->show();
	  $arrList = $result['data']['openid'];
	  $tplData = array(
			   'title' => '关注者列表',
			   'search' => '关注者名称',
			   'arrList' => $arrList,
			   'pageHtml' => $pageHtml,
			   'editUrl' => U('Admin/Follower/editFollower'),
			   );
	  $this->assign($tplData);
	  $this->display();
	}

	/**
	 * 获取关注者信息
	 */
	public function editFollower(){
	  $open_id = $this->_get('open_id');
	  $token = $this->getToken();
	  $ch = curl_init();
	  $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$open_id;
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  print_r($result);
	}
}

