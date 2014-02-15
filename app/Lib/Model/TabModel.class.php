<?php
/**
 * File Name: PermissionModel.class.php
 * Author: Blue
 * Created Time: 2013-11-22 15:30:31
*/
class TabModel extends CommonModel{
    /**
     * 获取菜单列表
     */
    public function  getTabList(){
        $tabObj = D('Tab');
        $arrField = array('*');
        $arrMap['fid'] = array('eq', 0);
        $arrMap['status'] = array('eq', 1);
        $arrOrder = array('display_order');
        $tabList = $tabObj->getList($arrField, $arrMap, $arrOrder);
        foreach($tabList as $k=>$v){
            $chilField = array('*');
            $chilMap['fid'] = array('eq', $v['id']);
            $chilMap['status'] = array('eq', 1);
            $chilOrder = array('display_order');
            $tabList[$k]['children'] = $tabObj->getList($chilField, $chilMap, $chilOrder);
        }
        return $tabList;
    }

	/**
	 * 格式化
	 */
	public function format($info, $arrFormatField){
        if(in_array('real_url', $arrFormatField)){
            //$info['real_url'] = 
        }
		//name
		if(in_array('name_long', $arrFormatField)){
			if(empty($info['pid'])){
				$info['name_long'] = $info['name'];
				//有上层菜单
			}else{
				$pinfo = D('Permission')->getInfoById($info['pid']);
				$info['pname'] = '';
				//如果上层菜单还有上层菜单
				if(!empty($pinfo['pid'])){
					$info['pname'] .= D('Permission')->where('id='.$pinfo['pid'])->getField('name').'--';
				}
				$info['pname'] .= $pinfo['name'];
				$info['name_long'] = $info['pname'].'--'.$info['name'];
			}
		}
		return $info;
	}
}
