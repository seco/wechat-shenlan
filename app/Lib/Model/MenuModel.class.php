<?php
/**
 * 菜单模型
 */
class MenuModel extends CommonModel{
    /**
     * 格式化
     */
    public function format($info, $arrFormatField){
        //类型
        if(in_array('type_name', $arrFormatField)){
            $info['type_name'] = ($info['type'] == 'view') ? '链接' : '关键字';
        }
        return $info;
    }
}