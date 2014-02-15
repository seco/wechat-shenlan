<?php
/**
 * 话题模型
 * @author blue
 * @version 2013-12-21
 */
class PushTopicModel extends CommonModel{
    /**
     * 接口统一处理函数：获取push数据
     * @return xml $content 处理后的输出信息
     * @param int $id 资源ID
     */
    public function getPush($id){
        //实例化topic-news类
        $topicObj = D('PushTopicNews');
        //设置匹配条件
        $arrField = array('*');
        $arrMap['push_id'] = array('eq', $id);
        $arrOrder = array();
        //获取topic表的数据
        $topicList = $topicObj->getList($arrField, $arrMap, $arrOrder);
        //获取news表的ID数组
        $arrIds = array();
        foreach($topicList as $k=>$v){
            $arrIds[] = $v['news_id'];
        }
        return D('PushNews')->getPush($arrIds);
    }

    /**
     * 格式化
     * @return array $info 格式化后的数组
     * @param  array $info 格式化前的数组
     * @param  array $arrFormatField 需要格式化的数组
     */
    public function format($info, $arrFormatField){
        //关键字
        if(in_array('keyword', $arrFormatField)){
            $routeInfo = D('PushRoute')->getRoute('pushTopic', $info['id']);
            $info['keyword'] = $routeInfo['keyword'];
        }
        //时间
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }
}
