<?php
/**
 * 话题控制器
 * @author blue
 * @version 2013-12-24
 */
class TopicAction extends WechatCommonAction{
    /**
     * 话题列表
     */
    public function topicList(){
        $topicObj = D('PushTopic');
        $arrField = array('*');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrOrder = array('mtime desc');
        $count = $topicObj->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        $topicList = $topicObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('keyword', 'mtime_text');
        foreach($topicList as $k=>$v){
            $topicList[$k] = $topicObj->format($v, $arrFormatField);
        }
        $tplData = array(
            'addUrl' => U('Admin/Topic/addTopic'),
            'editUrl' => U('Admin/Topic/editTopic'),
            'delUrl' =>U('Admin/Topic/doDelTopic'),
            'itemList' => $topicList,
            'pageHtml' => $pageHtml,
            'left_current' => 'topic',
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 话题添加页面
     */
    public function addTopic(){
        $this->display();

    }
}
