<?php
/**
 * �˵�ģ��
 */
class MenuModel extends CommonModel{
    /**
     * ��ʽ��
     */
    public function format($info, $arrFormatField){
        //����
        if(in_array('type_name', $arrFormatField)){
            $info['type_name'] = ($info['type'] == 'view') ? '����' : '�ؼ���';
        }
        return $info;
    }
}