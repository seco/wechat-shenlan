<?php
/**
 * File Name: WechatAction.class.php
 * Author: Blue
 * Created Time: 2013-10-28 17:21:07
*/
class WechatapiAction extends WechatCommonAction{
    /**
     * 图片地址判断
     */
    public function is_pic(){
        $url = 'ww.baidu.com';
        $result = substr_count($url, 'http://');
        if(!empty($result)){
            echo 'not eq 0';
        }else{
            echo '0';
        }
    }


    /**
     * 获取百度新闻
     */
    public function baiduNews(){
        $url = 'http://news.baidu.com/n?cmd=7&loc=3996&name=%C9%BD%B6%AB&tn=rss';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $result, $values, $tags);
        xml_parser_free($parser);

        for($i=0; $i<5; $i++){
        foreach ($tags as $k=>$v){

            if($k == 'title'){
                $newsList[$i]['title'] = $values[$v[$i+2]]['value'];
            }elseif($k == 'description'){

                $description = $values[$v[$i+1]]['value'];
                //如果简介中有图片，就将其作为图文封面
                $pattern = '/<img(.*)src="(.*)"/Us';
                preg_match($pattern, $description, $content);
                $newsList[$i]['cover'] = $content['2'];

                $newsList[$i]['description'] = $values[$v[$i+1]]['value'];
            }elseif($k == 'link'){
                $newsList[$i]['url'] = $values[$v[$i+2]]['value'];
            }

        }
        }
        $content = D('PushNews')->setNews($newsList, 5);
        $content = D('PushText')->setText($content);
        //包装
        $texttpl = D('Tpl')->where('type="news"')->getField('texttpl');
	    $content = sprintf($texttpl, $content);
	    return ($content);

        print_r();
    }

  private $site_id;
  private $access_token;
	/**
	 * 开放API
	 */
	public function wxapi(){
		//每一个微信公众账号都要有一个唯一标识的字符串
		//然后字符串中包含有用户的个人信息，和token
		//$string = "1111111111";
		//define("TOKEN", $_GET['token']);
		//define("TOKEN", "bluesoft");
		//$this->token = $_GET['token'];
	    $blue_key = $_GET['blue_key'];
		$map['blue_key'] = array('eq', $blue_key);
		$siteInfo = D('Website')->where($map)->find();
		$this->site_id = $siteInfo['id'];
		$this->access_token = $siteInfo['access_token'];
        if($_GET['echostr']){
            $this->valid();
        }
		$this->responseMsg();
	}

	private function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
		exit;
        }
	}

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
		$tmpArr = array($this->access_token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 回复
	 */
	private function responseMsg(){
	  $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	  if(empty($postStr)){
	    echo 'post is null';
	    exit;
	  }
	  //将xml转化为数组
	  $arrPost = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	  //将微信消息记录存入数据库
	  $this->doAddMessage($arrPost);
	  //将发送者ID存入SESSION
	  $_SESSION['guest_open_id'] = $arrPost['FromUserName'];
	  //根据post数组获取相应内容
	  $content = $this->getContent($arrPost);
	  if(empty($content)){
	    echo '获取数据错误';
	    exit;
	  }
	  $fromUsername = $arrPost['FromUserName'];
	  $toUsername = $arrPost['ToUserName'];
	  $time = time();
	  $texttpl = M('Tpl')->where('type="header"')->getField('texttpl');
	  $resultStr = sprintf($texttpl, $fromUsername, $toUsername, $time, $content);
	  echo $resultStr;
	}
	////////////////////////信息记录//////////////////////////////////
	/**
	 * 消息记录
	 */
	private function doAddMessage($arrPost){
	  //消息记录
	  if($arrPost['MsgType'] !== 'event'){
	    $open_id = $arrPost['ToUserName'];
	    $insert = array(
			    'site_id' => $this->site_id,
			    'send_id' => $arrPost['FromUserName'],
			    'msgtype' => $arrPost['MsgType'],
			    'content' => $arrPost['Content'],
			    'media_id' => $arrPost['MediaId'],
			    'recive_time' => time(),
			    );
	    D('MessageLog')->add($insert);
	  }
	}
	//////////////////////////处理函数///////////////////////////////////
	/**
	 * 根据获取到的表信息分配不同的处理函数
	 */
	private function getContent($arrPost){
	  $tableInfo = $this->getTableInfo($arrPost);
	    //如果table_name为空，则说明是功能函数
	  if(empty($tableInfo['table_name'])){
 	    $content = $this->$tableInfo['function_name'](substr($arrPost['Content'], 6, strlen($arrPost['Content'])-6));
	    //如果存在排序规则，则调用getContentByOrder函数
	  }elseif(!empty($tableInfo['order_by'])){
	    $content = $this->$tableInfo['function_name']($tableInfo['table_name'], $tableInfo['order_by']);
	    //如果存在数据ID，则调用getContentById或getTextContent函数
	  }elseif(!empty($tableInfo['push_id'])){
	    $content = $this->$tableInfo['function_name']($tableInfo['table_name'], $tableInfo['push_id']);
	    //如果table_name不为空，且不存在order和push_id，则默认调用shop函数获取LBS信息
	  }else{
	    $content = $this->$tableInfo['function_name']($tableInfo['table_name'], $arrPost);
	  }
	  return $content;
	}

	/**
	 * 获取商铺信息
	 */
	private function getShopContent($tableName, $arrPost){
	  $shopObj = D(ucfirst($tableName));
	  $arrField = array('*');
	  $arrMap = array();
	  $arrOrder = array('sort_number');
	  $shopList = $shopObj->getList($arrField, $arrMap, $arrOrder);
	  foreach($shopList as $k=>$v){
	    $x = $shopList[$k]['x'] - $arrPost['Location_X'];
	    $y = $shopList[$k]['y'] - $arrPost['Location_Y'];
	    $x = $x*2*3.141592653*6371.004/360;
	    $y = $y*2*3.141592653*6371.004/360;
	    if(($x*$x+$y*$y) <= 100){
	      $newList[] = $v;
	    }
	  }
	  if(count($newList) >= 5){
	    $count = 5;
	  }else{
	    $count = count($newList);
	  }
	  return $this->setNews($newList, $count);
	}

	/**
	 * 根据排序获取content信息
	 */
	private function getContentByOrder($tableName, $order){
	  $pushObj = D(ucfirst($tableName));
	  $pushList = $pushObj->order($order)->limit(5)->select();
	  $count = count($pushList);
	  return $this->setNews($pushList, $count);
	}

	/**
	 * 根据Push_id获取content信息
	 */
	private function getContentByIds($tableName, $ids){
	  $pushObj = D(ucfirst($tableName));
	  $arrIds = explode(',', $ids);
	  $map['id'] = array('in', $arrIds);
	  $pushList = $pushObj->where($map)->select();
	  $count = count($arrIds);
	  return $this->setNews($pushList, $count);
	}


	/**
	 * 根据Push_id获取文本content信息
	 */
	private function getTextContent($tableName, $id){
	  $pushObj = D(ucfirst($tableName));
	  $pushInfo = $pushObj->where('id='.$id)->getField('content');
	  return $this->setText($pushInfo);
	}

	/////////////////////////通用函数//////////////////////////////

	/**
	 * 通用函数:获取匹配到的信息
	 */
	private function getTableInfo($arrPost){
	  //查询条件
	  $map = array();
	  $map['msgtype'] = array('eq', $arrPost['MsgType']);
	  $map['event'] = array('eq', ($arrPost['Event']) ? $arrPost['Event'] : '');
	  $map['eventkey'] = array('eq', ($arrPost['EventKey']) ? $arrPost['EventKey'] : '');
	  $map['content'] = array('eq', substr($arrPost['Content'], 0, 6));
	  $map['media_id'] = array('eq', ($arrPost['MediaId']) ? $arrPost['MediaId'] : '');
	  $map['site_id'] = array('in', array($this->site_id, 0));
	  //获取规则信息
	  $tableInfo = D('Type')->where($map)->find();
	  if(empty($tableInfo)){
	    echo  '暂无匹配信息';
	    exit;
	  } 
	  return $tableInfo;
	}

	/**
	 * 组装News
	 */
	private function setNews($pushList, $count){
	  $texttpl = D('Tpl')->where('type="news"')->getField('texttpl');
	  $content = "<MsgType><![CDATA[news]]></MsgType>";
	  $content .= "<ArticleCount>".$count."</ArticleCount>";
	  $content .= "<Articles>";
	  foreach($pushList as $k=>$v){
		$v['cover'] = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', getPicPath($v['cover']));
		$v['url'] = $v['url'].'&guest_open_id='.$_SESSION['guest_open_id'];
		$content .= sprintf($texttpl, $v['title'], $v['description'], $v['cover'], $v['url']);
	  }
	  $content .= "</Articles>";
	  //print_r ($content);
	  return $content;
	}

	/**
	 * 组装text
	 */
	private function setText($content){
	  $texttpl = D('Tpl')->where('type="text"')->getField('texttpl');
	  $content = sprintf($texttpl, $content);
	  return ($content);
	}

	/**
	 * 获取二维码ticket
	 */
	public function getTicket(){
		$ch = curl_init();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
		$json = array(
			'expire_seconds' => 1800,
			'action_name' => 'OR_SCENE',
			'action_info' => array(
				'scene' => array(
					'scene' => 123
				),
			),
		);
		$json = json_encode($json);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		//$result = json_decode($result);
		echo $result;
	}

	public function orange(){
		$json = '';
		$menu = json_decode($json, true);
		$stype = array('city', 'citycode');
		foreach($menu['城市代码'] as $k=>$v){
			foreach($v['市'] as $k2=>$v2){
				$result = array_combine($stype, $v2);
				D('Weather')->add($result);
			}
		}
	}


	///////////////////////功能类函数///////////////////////////////////
	/**
	 * 天气搜索
	 */
	private function weather($city){
		//$city = '济南';
		$map['city'] = $city;
		$cityCode = M('Weather')->where("`city`='".$city."'")->getField('citycode');
		$url = 'http://m.weather.com.cn/data/'.$cityCode.'.html';
		$header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		//json解码为对象
		$result = json_decode($result, true);
		$content = $result['weatherinfo']['city'].' '.$result['weatherinfo']['week'].' '.$result['weatherinfo']['temp3'];
		return $this->setText($content);
	}

	/**
	 * 百度翻译
	 */
	private function trans($keyword){
		$url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=9peNkh97N6B9GGj9zBke9tGQ&q=".$keyword."&from=auto&to=auto";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		//json解码为对象
		$result = json_decode($result, true);
		$content = $result['trans_result']['0']['dst'];
		return $this->setText($content);
	}

	/**
	 * 聊天机器人
	 */
	private function chat($content){
		$key = '92773352-3798-4737-859d-bbb5dbe77b26';
		$url = "http://sandbox.api.simsimi.com/request.p?key=".$key."&lc=ch&text=".$content;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);
		return $this->setText($result['response']);
	}

	/**
	 * 虫洞api
	 */
	private function chong($content){
		//$url = "http://wap.unidust.cn/api/searchout.do?type=wap&ch=1001&info=".$content."&appid=71";
		$url = "http://xyapi.sinaapp.com/Api/?type=joke";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		print_r($result);exit;
	}

	/**
	 * 获取新浪RSS新闻数据
	 */
	public function sinaNews(){
	  $url = "http://rss.sina.com.cn/news/marquee/ddt.xml";
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  //$arrResult = (array)simplexml_load_string($result);
	  //$i = 0;
	  /*
	  foreach($arrResult['channel']->item as $k=>$v){
	    $arrNews[$i]['title'] = $v->title;
	    $arrNews[$i]['description'] = $v->description;
	    $i++;
	  }
	  */
	  
	      $parser = xml_parser_create();
	      xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	      xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	      xml_parse_into_struct($parser, $result, $values, $tags);
	      xml_parser_free($parser);

	      
		foreach($tags as $k2=>$v2){
		  foreach($values as $k1=>$v1){
		    for($i=2; $i<=6; $i++){
		      if($k2 == 'title'){
			$arrNews[$i]['title'] = $values[$v2[$i]]['value'];
		      }elseif($k2 == 'description'){
			$arrNews[$i]['description'] = $values[$v2[$i-1]]['value'];
		      }
		    }
		}
	      }
		$content = $arrNews[2]['title'].$arrNews[2]['description'];
		return $this->setText($content);
	}

	/**
	 * 笑话
	 */
	private function joke(){
	  $rand = rand(1, 1400);
	  $url = 'http://m.haha365.com/zz_joke/index_'.$rand.'.htm';
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  $pattern = '/<div class="haha">(.*)<\/h3>(.*)<\/div>/Us';
	  preg_match($pattern, $result, $content);
	  $content = mb_convert_encoding($content[2], 'UTF-8', 'gbk');
	  $content = strip_tags($content);
	  return $this->setText($content);
	}

	/**
	 * 每日英语
	 */
	private function english(){
	  $rand = rand('100', '727');
	  $url = 'http://wap.iciba.com/dailysentence/content/'.$rand.'#anchor';
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  $pattern = '/<div class="dayC" id="(.*)">(.*)<a(.*)<h2 class="cn">(.*)<\/h2>/Us';
	  preg_match($pattern, $result, $content);
	  $content[2] = strip_tags($content[2]);
	  return $this->setText($content[2].$content[4]);
	  //print_r($content);exit;
	}
	  
	/**
	 * 可乐
	 */
	private function kele(){
	  $rand = rand('01', '290');
	  $url = 'http://www.kelepuzi.com/text/page/'.$rand.'.html';
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  $pattern = '/<div class="post-content">(.*)<\/p>/Us';
	  preg_match($pattern, $result, $content);
	  $content = strip_tags($content[1]);
	  return $this->setText($content);
	  //print_r($content);exit;
	}
	
	/**
	 * engadget
	 */
	public function engadget(){
	  $url = 'http://cn.engadget.com/rss.xml';
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $result = curl_exec($ch);
	  curl_close($ch);
	  print_r($result);
	}
}
