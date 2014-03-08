<?php
/**
 * File Name: HomeCommonAction.class.php
 * Author: Blue
 * Created Time: 2013-11-15 10:59:09
*/
class MobileCommonAction extends CommonAction
{
    //类属性
    protected $key;
    protected $wechat_id;
    protected $guest_open_id;


	/**
	 * 判断key
	 */
    public function _initialize()
    {
		$key = trim($_GET['key']);
		if(empty($key)){
			echo '对不起，您所访问的站点不存在';
			exit;
		}
        $this->setStaticInfo($key);
        $data = array(
            'key'           => $key,
            'wechat_id'     => $wechat_id,
            'guest_open_id' => $guest_open_id,
            'site'          => $this->getSiteInfo(),
            'menuList'      => $this->getItemList(),
            'home'          => U('Index/index', array('key'=>$this->key)),
        );
        $this->assign($data);

	}
    /**
     * 设置全局变量
     * @param int $key KEY值
     */
    private function setStaticInfo($key)
    {
        //获取微信ID
        $wechat_id = D('UserWechat')->where('blue_key='.$key)->getField('id');
        //获取访问者信息
        $guest_open_id = trim($_GET['guest_open_id']);

        $this->key = $key;
        $this->wechat_id = $wechat_id;
        $this->guest_open_id = $guest_open_id;
    }

    /**
     * 获取网站设置信息
     * @return array $siteInfo 网站设置信息
     */
    protected function getSiteInfo()
    {
        $siteInfo = D('CmsSetting')->where('wechat_id='.$this->wechat_id)->find();
        $siteInfo = D('CmsSetting')->format($siteInfo, array('logo_name','theme_spell'));
        return $siteInfo;
    }

    /**
     * 获取栏目信息
     * @param int $id 
     * @return array $itemInfo
     */
    protected function getItemInfo($id)
    {
        $catInfo = D('CmsCat')->where('id='.$id)->find();
		$itemInfo = D('PushNews')->where('id='.$catInfo['news_id'])->find();
        $itemInfo = D('PushNews')->format($itemInfo, array('cover_name', 'mtime_text'));
		$itemInfo = array_merge($itemInfo, $catInfo);
        return $itemInfo;
    }

    /**
     * 获取栏目列表
     * @param int $fid 父级栏目ID
     * @return array $catList 栏目列表
     */
    protected function getItemList($fid=0)
    {
        $catList = D('CmsCat')->where('fid='.$fid.' AND wechat_id='.$this->wechat_id.' AND status=1')->order('display_order')->select();
        foreach($catList as $k=>$v){
            //获取栏目详细信息
            $itemInfo = D('PushNews')->getInfoById($v['news_id']);
            $catList[$k] = array_merge($itemInfo, $v);
            $catList[$k]['url'] = U('Index/item', array('id'=>$v['id'], 'key'=>$this->key));
            $catList[$k]['cover_name'] = getPicPath($catList[$k]['cover']);
        }
        return $catList;
    }

    /**
     * 获取主图名称
     * return string $themeName 主题名称
     */
    protected function getThemeName()
    {
        $siteInfo = $this->getSiteInfo();
        $themeName = D('CmsTheme')->where('id='.$siteInfo['theme_id'])->getField('spell');
        return $themeName;
    }

    /**
     * 设置浏览量
     * param int $id 栏目ID
     */
    protected function setViews($id)
    {
        $news_id = D('CmsCat')->where('id='.$id)->getField('news_id');
        D('PushNews')->where('id='.$news_id)->setInc('views');
    }
} 
