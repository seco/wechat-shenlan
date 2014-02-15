<?php
/**
 * 栏目-插件模型
 * @author blue
 * @version 2014-1-7
 */
class CmsCatPluginModel extends CommonModel{
    /**
     * 添加记录
     */
    public function editPlugin($data, $cat_id){
        $pluginObj = D('CmsCatPlugin');
        $data['cat_id'] = $cat_id;
        if(empty($data['id'])){
            //添加
            if(!empty($data['plugin_id'])){
                $pluginObj->add($data);
            }
        }else{
            //修改
            if($data['plugin_id'] == 0){
                //删除
                $pluginObj->where('id='.$data['id'])->delete();
            }else{
                //更新
                $pluginObj->save($data);
            }
        }
    }
}
