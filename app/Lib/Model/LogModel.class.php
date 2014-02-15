<?php
/**
 * 记录模型
 */
class LogModel extends CommonModel {
    /**
     * 添加记录信息
     * @return null
     * @param array $arrPost POST提交的数据
     * @param int $wechat_id 微信ID
     */
    public function setApiLog($arrPost, $wechat_id){
        $logObj = D('Log');
        //是否需要把这个字段存储为int?
        switch ($arrPost['MsgType']){
        case 'event':
            $msgtype = $arrPost['Event'];
            $content = '事件';
            break;
        case 'text':
            $msgtype = 'text';
            $content = $arrPost['Content'];
            break;
        default :
            $msgtype = 'other';
            $content = '未知类型';
            break;
        }
        $insert = array(
            'wechat_id'  => $wechat_id,
            'guest_name' => $arrPost['FromUserName'],
            'msgType'    => $msgtype,
            'content'    => $content,
            'ctime'      => time(),
        );
        $logObj->add($insert);
    }

    /**
     * 微网访问记录
     * @return
     * @param string $content 浏览内容
     */
    public function setVisitLog($cat_id){
        $logObj = D('Log');
        $insert = array(
            'wechat_id'  => $_SESSION['wechat_id'],
            'guest_name' => ($_SESSION['guest_open_id']) ? $_SESSION['guest_open_id'] : '0',
            'msgType'    => 'visit',
            'content'    => $cat_id,
            'ctime'      => time(),
        );
        $logObj->add($insert);
    }

    /**
     * 格式化
     * @return array $info 格式化后的数据
     * @param  array $info 格式化前的数据
     * @param  array $arrFormatField 需要格式化的数据
     */
    public function format($info, $arrFormatField){
        //时间
        if(in_array('ctime_text', $arrFormatField)){
            $info['ctime_text'] = date('Y-m-d H:i', $info['ctime']);
        }
        //访问内容
        if(in_array('content', $arrFormatField)){
            if($info['msgType'] == 'visit'){
                $news_id = D('CmsCat')->where('id='.$info['content'])->getField('news_id');
                $info['content'] = D('PushNews')->where('id='.$news_id)->getField('title');
            }
        }
        //访问者姓名
        if(in_array('guest_name', $arrFormatField)){
            $remark = D('Follower')->where("open_id='".$info['guest_name']."'")->getField('remark');
            if(!empty($remark)){
                $info['guest_name'] = $remark;
            }
        }
        return $info;
    }

}
