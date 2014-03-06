<?php
/**
 * 世纪情缘
 * @author blue
 * @version 2014-1-2
 */
class ShijiAction extends HomeCommonAction{
    /**
     * 搜索
     */
    public function search(){
        $search = $this->_post();
        //获取Field_id
        $sex_id = D('CmsField')->where("value='sex'")->getField('id');
        $age_id = D('CmsField')->where("value='age'")->getField('id');
        $marry_id = D('CmsField')->where("value='marry'")->getField('id');
        //获取获取性别
        $sexMap['field_id'] = array('eq', $sex_id);
        $sexMap['content'] = array('eq', $search['sex']);
        $sex_list = D('CmsFieldContent')->where($sexMap)->getField('cat_id', true);
        //根据id获取cat_id
        //$ageMap['field_id'] = array('eq', $age_id);
        $ageMap = '(`field_id`='.$age_id.')and(`content`'.$search['age'].')';
        $age_list = D('CmsFieldContent')->where($ageMap)->getField('cat_id', true);
        //获取婚姻状况
        $marryMap['field_id'] = array('eq', $marry_id);
        $marryMap['content'] = array('eq', $search['marry']);
        $marry_list = D('CmsFieldContent')->where($marryMap)->getField('cat_id', true);
        //取交集
        $cat_id_list = array_intersect($sex_list, $age_list, $marry_list);
        //获取catList
        $id = intval($search['id']);
        $arrMap['id'] = array('in', $cat_id_list);
        $arrMap['status'] = array('eq', '1');
        $catInfo = D('CmsCat')->getCatInfo($id, $arrMap);
        //留言量加1
        D('CmsCat')->addView($id);
        switch($search['age']){
        case 'between '.(date('Y')-30).date('md').' and '.(date('Y')-20).date('md'):
            $age_name = '20-30';
            break;
        case 'between '.(date('Y')-40).date('md').' and '.(date('Y')-31).date('md'):
            $age_name = '31-40';
            break;
        case 'between '.(date('Y')-50).date('md').' and '.(date('Y')-41).date('md'):
            $age_name = '41-50';
            break;
        case '< '.(date('Y')-50).date('md'):
            $age_name = '50以上';
            break;
        }
        $tplData = array(
            'sex' => $search['sex'],
            'age' => $search['age'],
            'age_name' => $age_name,
            'marry' => $search['marry'],
            'itemInfo' => $catInfo,
            'title' => $catInfo['catInfo']['title'],
        );
        $this->assign($tplData);
        $this->display($catInfo['template']);
    }


    /**
     * 留言
     */
    public function leave(){
        $id = intval($this->_get('id'));
        $this->assign('title', '给他/她留言');
        $this->assign('itemInfo', array('catInfo'=>array('id'=>$id)));
        $this->display('Shiji:leave');
    }

    /**
     * 我要征婚
     */
    public function order(){
        $fieldList = D('CmsField')->getFieldList('0', 'cmsLeave');
        $this->assign('title', '我要征婚');
        $this->assign('fieldList', $fieldList);
        $this->display('Shiji:order');
    }
}

        
