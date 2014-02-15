<?php
/**
 * File Name: ItemAction.class.php
 * Author: Blue
 * Created Time: 2013-11-21 17:32:15
*/
class CatAction extends HomeCommonAction{
    //主题变量
    public $theme;

	/**
	 * 获取首页banner列表
	 */
	public function getBanner(){
		$bannerObj = D('CmsBanner');
		$arrField = array('*');
		$arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
		$arrOrder = array('display_order');
		$bannerList = $bannerObj->getList($arrField, $arrMap, $arrOrder);
		foreach($bannerList as $k=>$v){
			$bannerList[$k] = $bannerObj->format($v, array('cover_name'));
		}
		return $bannerList;
	}

	/**
	 * 首页
	 */
	public function index(){
        $catInfo = D('CmsCat')->getCatInfo('0');
        //模板赋值
        $siteInfo = D('CmsSetting')->where('wechat_id='.$_SESSION['wechat_id'])->find();
        $siteInfo = D('CmsSetting')->format($siteInfo, array('logo_name'));
		$tplData = array(
			'title' => $siteInfo['site_name'],
            'itemInfo' => $catInfo,
            'siteInfo' => $siteInfo,
        );
        $theme_spell = D('CmsSetting')->getThemeSpell($_SESSION['wechat_id']);
        //首页留言记录
		$this->assign($tplData);
		$this->display('Index:'.$theme_spell);
	}

    /**
     * 微网内容显示
     */
    public function cat(){
        $id = intval($this->_get('id'));
        $catInfo = D('CmsCat')->getCatInfo($id);
        //浏览量增加
        D('CmsCat')->addView($id);
        //留言记录
        D('Log')->setVisitLog($id);
        //模板赋值
        $this->assign('itemInfo', $catInfo);
        $this->assign('title', $catInfo['catInfo']['title']);
        $this->display($catInfo['template']);
    }

    /**
     * 无栏目时的内容展示
     */
    public function news(){
        $id = intval($this->_get('id'));
        $newsInfo = D('PushNews')->getNewsInfo($id);
        $catInfo['catInfo'] = $newsInfo;
        $this->assign('itemInfo', $catInfo);
        $this->assign('title', $newsInfo['title']);
        $this->display('Default:detail');
    }

    /**
     * 点赞
     */
    public function addUp(){
        $id = intval($this->_get('id'));
        D('CmsCat')->addUp($id);
        $this->success('投票成功');
    }
}
