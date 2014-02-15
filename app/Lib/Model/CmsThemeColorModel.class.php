<?php
/**
 * 配色方案
 * @author blue
 * @version 2014-1-02
 */
class CmsThemeColorModel extends CommonModel{
    /**
     * 获取配色列表
     * @return array $colorList
     */
    public function getColorList(){
        $colorObj = D('CmsThemeColor');
        $arrField = array('*');
        $arrMap = array();
        $arrOrder = array('mtime desc');
        $colorList = $colorObj->getList($arrField, $arrMap, $arrOrder);
        return $colorList;
    }
}