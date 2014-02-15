<?php
/**
 * 微信公众账号模型类
 * @author blue
 * @version 2013-12-23
 */
class UserWechatModel extends CommonModel{
    /**
     * 输出格式化
     * @return array 格式化后的数组
     * @param array $info 需要格式化的数组
     * @param array $arrFormatField 需要格式化的字符串
     */
    public function format($info, $arrFormatField){
        //公众账号LOGO
        if(in_array('logo_name', $arrFormatField)){
            $info['logo_name'] = getPicPath($info['logo']);
        }
        //时间
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }

    /**
     * 关系删除操作
     * @return boolen 删除结果
     * @param array $arrIds 需要删除的wechat唯一ID
     */
    public function delAll($arrMap){
        $arrTable = array('CmsCat', 'PushText', 'PushNews', 'PushTopic');
        foreach($arrTable as $k=>$v){
            D($v)->where($arrMap)->delete();
        }
    }
}
