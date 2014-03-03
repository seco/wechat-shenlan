<?php
/**
 * 微网站统一控制器
 * @author chen
 * @version 2014-03-03
 */
class IndexAction extends MobileCommonAction
{
    /**
     * 预处理函数
     */
    public function _initialize()
    {
        //获取全站设置信息
        $siteInfo = D('CmsSetting')->where('wechat_id='.$_SESSION['wechat_id'])->find();
        $siteInfo = D('CmsSetting')->format($siteInfo, array('logo_name','theme_spell'));

        //获取菜单列表
        $catList = D('CmsCat')->where('fid=0 AND wechat_id='.$_SESSION['wechat_id'])->order('display_order')->select();
        foreach($catList as $k=>$v){
            $itemInfo = D('PushNews')->getInfoById($v['news_id']);
            $catList[$k] = array_merge($itemInfo, $v);
            $catList[$k]['url'] = U('Index/item', array('id'=>$v['id'], 'blue_key'=>$_SESSION['blue_key']));
        }

        //模版赋值
        $data = array(
            'site' => $siteInfo,
            'menuList'  => $catList,
            'home' => U('Index/index', array('blue_key'=>$_SESSION['blue_key'])),
        );
        $this->assign($data);
    }

    /**
     * 首页控制函数
     */
    public function index()
    {
        print_r($_SESSION);exit;
        $this->display('Default:index');
    }

    /**
     * 内页控制函数
     */
    public function item()
    {
        //获取栏目信息
        $id = intval($this->_get('id'));
        $itemInfo = D('CmsCat')->getInfoById($id);
        $itemInfo = D('CmsCat')->format($itemInfo, array('cover_name', 'mtime_text'));

        //获取子列表信息
        $itemList = D('CmsCat')->where('fid='.$id)->select();
        foreach($itemList as $k=>$v){
            $newsInfo = D('PushNews')->getInfoById($v['news_id']);
            $itemList[$k] = array_merge($newsInfo, $v);
            $itemList[$k]['url'] = U('Index/item', array('id'=>$v['id'], 'bule_key'=>$_SESSION['blue_key']));
            $itemList[$k]['cover_name'] = getPicPath($itemList[$k]['cover']);
        }
        $data = array(
            'info' => $itemInfo,
            'list' => $itemList,
        );
        $this->assign($data);
        $this->display($itemInfo['template_self']);
    }
}

