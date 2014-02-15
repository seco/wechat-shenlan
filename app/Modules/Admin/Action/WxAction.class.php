<?php
/**
 * 微信接口处理类
 * @author: blue
 * @version: 2013-12-20
 */


class WxAction extends CommonAction
{
    //类属性
    private $wechat_id;
    private $token;

    /**
     * api接口处理函数
     */
	public function wxapi(){
        $_SESSION['blue_key'] = $_GET['blue_key'];
        $wechatInfo = D('UserWechat')->where("blue_key='".$_SESSION['blue_key']."'")->find();
        $this->wechat_id = $wechatInfo['id'];
        $this->token = $wechatInfo['token'];
		$this->valid();
		$this->responseMsg();
	}

    /**
     * 验证函数
     */
	private function valid()
    {
        $echoStr = $_GET["echostr"];
        //判断是否为验证数据
        if(!empty($echoStr)){
            if($this->checkSignature()){
            	echo $echoStr;
            	exit;
            }
        }
    }

    /**
     * 验证过程
     */
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$tmpArr = array($this->token, $timestamp, $nonce);
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
     * 处理数据
     */
    private function responseMsg()
    {
        //获取post数据
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //如果post数据为空，则退出
        if(empty($postStr)){
            exit;
        }
        //将post数据解码为数组
        $arrPost = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将访问信息存入数据库
        //如果用户发送的是关注或取消关注事件
        //或者直接存入用户发送的text信息
        D('Log')->setApiLog($arrPost, $this->wechat_id);
        //将发送者ID存入SESSION
        //如何用户是要访问微网站，session还有用，否则无用
        $_SESSION['guest_open_id'] = $arrPost['FromUserName'];
        //将用户发送的信息转换为关键字形式
        $keyword = $this->getKeyword($arrPost);
        //根据post数组获取返回数据内容
        $content = $this->getContent($keyword);
        //组装xml头部
        $content = $this->setHeader($arrPost, $content);
        echo $content;
    }

    /**
     * 把用户发送的原始信息转换为关键字形式
     * @return string $keyword 关键字
     * @param array $arrPost 用户POST提交的数据
     */
    private function getKeyword($arrPost){
        //若用户发送的信息为text格式，则直接返回text内容
        if($arrPost['MsgType'] == 'text'){
            return $arrPost['Content'];
        //若用户发送的是关注事件，则返回“关注”
        }elseif($arrPost['Event'] == 'subscribe'){
            return '关注';
        //若用户返回的是取消关注事件，则返回“取消关注"
        }elseif($arrPost['Event'] == 'unsubscribe'){
            return '取消关注';
        //若用户发送的是菜单点击事件，则返回点击值
        }elseif($arrPost['EventKey']){
            return $arrPost['EventKey'];
        //否则，返回“无匹配”
        }else{
            return '默认';
        }
    }

    /**
     * 根据获取到的关键字搜索路由表进行匹配
     * @return xml $content 处理后的数据
     * @param string $keyword 关键字
     */
    private function getContent($keyword){
        $routeObj = D('PushRoute');
        $arrMap = array(
            'keyword' => $keyword,
            'wechat_id' => $this->wechat_id,
        );
        //使用topic模式
        $routeInfo = $routeObj->where($arrMap)->find();
        //如果无匹配，则直接退出
        if(empty($routeInfo)){
            $noneMap = array(
                'keyword' => '默认',
                'wechat_id' => $this->wechat_id,
            );
            $routeInfo = $routeObj->where($noneMap)->find();
        }
        if(empty($routeInfo)){
            exit;
        }
        return D(ucfirst($routeInfo['obj_type']))->getPush($routeInfo['obj_id'], $keyword);
        /*
        $arrField = array();
        $arrMap['keyword'] = array('eq', $keyword);
        $arrOrder = array('mtime desc');
        //获取匹配数据
        $result = $routeObj->getList($arrField, $arrMap, $arrOrder);
        if(empty($result)){
            $result = $routeObj->getInfoByKeyword('无匹配');
            if(empty($result)){
                exit;
            }
        }
        $arrIds = array();
        foreach($result as $k=>$v){
            //匹配优先级：默认工具类最高，其次是文本，最后是图文
            if($v['obj_type'] == 'Tool'){
                return $this->getContentByTool($k, $keyword);
                exit;
            }elseif($v['obj_type'] == 'Text'){
                return $this->getContentByText($k);
                exit;
            }elseif($v['obj_type'] == 'News'){
                $arrIds[$k] = $v['obj_id'];
            }
        }
        return $this->getContentByNews($obj_type, $arrIds);
        */
    }

    /**
     * 设置头部
     * @return xml $content 最后输出的信息
     * @param array $arrPost 用户POST提交的数据
     * @param xml $content 输出信息的BODY
     */
    private function setHeader($arrPost, $content){
        $fromUsername = $arrPost['FromUserName'];
        $toUsername = $arrPost['ToUserName'];
        $time = time();
        $texttpl = M('Tpl')->where('type="header"')->getField('texttpl');
        $resultStr = sprintf($texttpl, $fromUsername, $toUsername, $time, $content);
        return $resultStr;
    }

}
