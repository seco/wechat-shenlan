<?php
/**
 * 接收信息控制器
 * @author chen
 * version 2014-03-04
 */
class MessageAction extends MobileCommonAction
{
    /**
     * message
     */
    public function message()
    {
        $theme_name = $this->getThemeName();
        $info['title'] = '我要留言';
        $this->assign('info', $info);
        $this->display(ucfirst($theme_name).':leave');
    }

    /**
     * 添加评论操作
     */
    public function addMessage()
    {
        $data = $_POST;
        $data['ctime'] = time();
        $data['wechat_id'] = $this->wechat_id;
        $data['open_id'] = $this->guest_open_id;
        D('CmsLeave')->add($data);
        $this->success('提交成功', U('Index/index', array('key'=>$this->key)));
    }
}
