<?php
/**
 * 自定义字段模型类
 * @author blue
 * @version 2014-1-2
 */
class CmsFieldModel extends CommonModel{
    /**
     * 获取CmsField表中的cat_id
     */
    private function getCatId($cat_id, $obj_type){
        switch($obj_type){
        case 'cmsCat':
            $cat_fid = D('CmsCat')->where('id='.$cat_id)->getField('fid');
            break;
        case 'cmsLeave':
            $cat_fid = D('cmsLeave')->where('id='.$cat_id)->getField('cat_id');
            break;
        }
        return $cat_fid;
    }

    /**
     * 获取CmsFieldContent字段内容
     */
    public function getFieldContent($cat_id, $obj_type, $field_id){
        $contentObj = D('CmsFieldContent');
        $arrMap = array(
            'cat_id'   => $cat_id,
            'obj_type' => $obj_type,
            'field_id' => $field_id,
        );
        $contentInfo = $contentObj->where($arrMap)->find();
        return $contentInfo;
    }
           
    /**
     * 获取字段名称+内容列表
     * 
     */
    public function getFieldList($cat_id, $obj_type='cmsCat', $return='index', $cat_fid){
        $fieldObj = D('CmsField');
        $arrField = array('*');
        if(!empty($cat_fid)){
            $cat_fid = $cat_fid;
        }elseif(empty($cat_id)){
            $cat_fid = '0';
        }else{
            $cat_fid = $this->getCatId($cat_id, $obj_type);
        }
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['obj_type']  = array('eq', $obj_type);
        $arrMap['cat_id']    = array('eq', $cat_fid);
        $arrMap['status']    = array('eq', '1');
        $arrOrder = array('display_order');
        //获取自定义字段名称列表
        $fieldList = $fieldObj->getList($arrField, $arrMap, $arrOrder);
        //关联数组
        $assList = array();
        foreach($fieldList as $k=>$v){
            $contentInfo = $this->getFieldContent($cat_id, $obj_type, $v['id']);
            $fieldList[$k]['content_id'] = $contentInfo['id'];
            $fieldList[$k]['content']    = $contentInfo['content'];
            $assList[$v['value']]       = $contentInfo['content'];
        }
        if($return == 'index'){
            return $fieldList;
        }elseif($return == 'other'){
            return $assList;
        }else{
            return array();
        }
    }

    /**
     * 更新字段数据
     */
    public function updateFieldList($fieldList, $cat_id, $obj_type='cmsCat'){
        $fieldInfo = array();
        foreach($fieldList as $k=>$v){
            $fieldInfo['id'] = $v['id'];
            $fieldInfo['obj_type'] = $obj_type;
            $fieldInfo['cat_id'] = $cat_id;
            $fieldInfo['field_id'] = $k;
            $fieldInfo['content'] = $v['content'];
            $fieldInfo['mtime'] = time();
            if(empty($fieldInfo['id'])){
                $fieldInfo['ctime'] = time();
                D('CmsFieldContent')->add($fieldInfo);
            }else{
                D('CmsFieldContent')->save($fieldInfo);
            }
        }
    }

    /**
     * 删除字段数据
     */
    public function delFieldList($cat_ids, $obj_type){
        $fieldObj = D('CmsFieldContent');
        $arrMap['obj_type'] = array('eq', $obj_type);
        $arrMap['cat_id'] = array('in', $cat_ids);
        $fieldObj->where($arrMap)->delete();
    }

}
