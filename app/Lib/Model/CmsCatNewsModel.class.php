<?php
/**
 * ΢����Ŀ-���¶�Ӧ��
 * @author blue
 * @version 2012-12-23
 */
class CmsCatNewsModel extends CommonModel{
    /**
     * ����cat_id��ȡnews_ids
     * @return array ����ID
     * @param int ΢����ĿID
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
