<?php
/**
 * ΢������ģ��ģ��
 * @author blue
 * @version 2013-12-25
 */
class CmsTemplateModel extends CommonModel{
    /**
     * ��ȡģ���б�
     */
    public function getTemList(){
        $temObj = D('CmsTemplate');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array('display_order');
        $temList = $temObj->getList($arrField, $arrMap, $arrOrder);
        return $temList;
    }
}