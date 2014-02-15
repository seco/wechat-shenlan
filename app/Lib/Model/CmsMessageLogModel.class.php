<?php
/**
 * File Name: MessageLogModel.class.php
 * Author: Blue
 * Created Time: 2013-11-14 9:47:36
*/
class MessageLogModel extends CommonModel{
	/**
	 * 输出格式化
	 */
	public function format($info, $arrFormatField){
		if(in_array('voice_name', $arrFormatField)){
			$ch = curl_init();
			$token = $this->getToken();
			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$token."&media_id=".$info['media_id'];
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close();
			print_r($result);
			exit;
		}
		//站点名称
		if(in_array('site_name', $arrFormatField)){
			$info['site_name'] = D('Website')->where('id='.$info['site_id'])->getField('site_name');
		}
		//接收时间
		if(in_array('recive_time_text', $arrFormatField)){
			$info['recive_time_text'] = date('Y-m-d H:i', $info['recive_time']);
		}
		//信息发送者备注
		if(in_array('guest_remarks', $arrFormatField)){
		  $info['guest_remarks'] = D('Remarks')->where("open_id='".$info['send_id']."'")->getField('name');
		}
		return $info;
	}


	/**
	 * 获取token
	 */
	public function getToken(){
		$grant_type = $_SESSION['info']['grant_type'];
		$appid = $_SESSION['info']['appid'];
		$appsecret = $_SESSION['info']['appsecret'];
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type='.$grant_type.'&appid='.$appid.'&secret='.$appsecret;
		//通过CURL获取access token信息
		$ch = curl_init();//初始化curl	
		curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		//将获取到的内容json解码为类
		$result = json_decode($data);
		if($result->expires_in !== 7200){
			$this->error('ERROR');
		}
		return $result->access_token;
	}

}
