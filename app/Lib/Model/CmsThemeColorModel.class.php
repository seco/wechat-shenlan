<?php
/**
 * ��ɫ����
 * @author blue
 * @version 2014-1-02
 */
class CmsThemeColorModel extends CommonModel{
    /**
     * ��ȡ��ɫ�б�
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