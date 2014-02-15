<?php
/**
 * 全局资源路由表
 * @author blue
 * @version 2013-12-24
 */
class PushRouteModel extends CommonModel{
    /**
     * 判断关键字是否被占用
     * @return boolen 如何关键字已经被占用，则返回false，否则返回true
     * @keyword string $keyword 关键字
     */
    public function checkKeyword($keyword, $id=0){
        //$keyword = mb_convert_encoding($keyword, 'utf8', 'gbk');
        if(empty($keyword)){
            return true;
            exit;
        }
        if(($keyword == '关注') OR ($keyword == '默认')){
            return false;
            exit;
        }
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['keyword'] = array('eq', $keyword);
        $resultId = D('PushRoute')->where($arrMap)->getField('id');
        if(empty($resultId)){
            return true;
        }elseif($resultId == $id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 添加路由信息
     * @return boolen
     * @param string $obj_type 资源类型
     * @param int $obj_id 资源ID
     * @param string $keyword 关键字
     */
    public function addRoute($obj_type, $obj_id, $keyword){
        $routeObj = D('PushRoute');
        $insert = array(
            'obj_type'  => $obj_type,
            'obj_id'    => $obj_id,
            'keyword'   => $keyword,
            'wechat_id' => $_SESSION['wechat_id'],
            'ctime'     => time(),
            'mtime'     => time(),
        );
        $result = $routeObj->add($insert);
        return $result;
    }

    /**
     * 获取关键字
     * @return string $keyword 关键字
     * @param string $obj_type 资源类型
     * @param int $obj_id 资源ID
     */
    public function getRoute($obj_type, $obj_id){
        $routeObj = D('PushRoute');
        $arrMap = array(
            'obj_type' => $obj_type,
            'obj_id'   => $obj_id,
        );
        $routeInfo = $routeObj->where($arrMap)->find();
        return $routeInfo;
    }

    /**
     * 路由表更新
     * @return boolen
     * @param string $obj_type 资源类型
     * @param int $obj_id 资源ID
     * @param string $keyword 关键字
     */
    public function editRoute($update){
        $routeObj = D('PushRoute');
        $update = array(
            'wechat_id' => $_SESSION['wechat_id'],
            'id'        => $update['id'],
            'keyword'   => $update['keyword'],
            'mtime'     => time(),
        );
        $routeObj->save($update);
    }

    /**
     * 删除路由表记录
     * @return boolen
     * @param string $obj_type 资源类型
     * @param array $map 资源ID数组
     */
    public function delRoute($obj_type, $map){
        $routeObj = D('PushRoute');
        $routeObj->where($map)->delete();
    }
}
