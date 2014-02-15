<?php
/**
 * 微网主题模板模型
 * @author blue
 * @version 2013-12-25
 */
class CmsTemplateModel extends CommonModel{
    /**
     * 获取模板列表
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