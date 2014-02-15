<?php
/**
 * ����ģ��
 * @author blue
 * @version 2013-12-21
 */
class PushTopicModel extends CommonModel{
    /**
     * �ӿ�ͳһ����������ȡpush����
     * @return xml $content �����������Ϣ
     * @param int $id ��ԴID
     */
    public function getPush($id){
        //ʵ����topic-news��
        $topicObj = D('PushTopicNews');
        //����ƥ������
        $arrField = array('*');
        $arrMap['push_id'] = array('eq', $id);
        $arrOrder = array();
        //��ȡtopic�������
        $topicList = $topicObj->getList($arrField, $arrMap, $arrOrder);
        //��ȡnews���ID����
        $arrIds = array();
        foreach($topicList as $k=>$v){
            $arrIds[] = $v['news_id'];
        }
        return D('PushNews')->getPush($arrIds);
    }

    /**
     * ��ʽ��
     * @return array $info ��ʽ���������
     * @param  array $info ��ʽ��ǰ������
     * @param  array $arrFormatField ��Ҫ��ʽ��������
     */
    public function format($info, $arrFormatField){
        //�ؼ���
        if(in_array('keyword', $arrFormatField)){
            $routeInfo = D('PushRoute')->getRoute('pushTopic', $info['id']);
            $info['keyword'] = $routeInfo['keyword'];
        }
        //ʱ��
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }
}
