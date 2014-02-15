<?php
/**
 * 微网栏目-文章对应表
 * @author blue
 * @version 2012-12-23
 */
class CmsCatNewsModel extends CommonModel{
    /**
     * 根据cat_id获取news_ids
     * @return array 文章ID
     * @param int 微网栏目ID
     */
    public function getNewsIds($catId){
        $linkObj = D('CmsCatNews');
        $arrMap['cat_id'] = array('eq', $id);
        $linkList = $linkObj->where($arrMap)->select();
        if(empty($linkLink)){
            exit;
        }
        $arrIds = array();
        foreach($linkList as $k=>$v){
            $arrIds[] = $v['news_id'];
        }
        return $arrIds;
    }
}
