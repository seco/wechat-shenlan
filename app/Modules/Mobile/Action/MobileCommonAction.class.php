<?php
/**
 * File Name: HomeCommonAction.class.php
 * Author: Blue
 * Created Time: 2013-11-15 10:59:09
*/
class MobileCommonAction extends CommonAction{
    //主题
    public $theme = 'Default';

	/**
	 * 获取站点信息
	 */
	public function _initialize(){
		$blue_key = $this->_get('blue_key');
		if(empty($blue_key)){
			echo '对不起，您所访问的站点不存在';
			exit;
		}
		if($blue_key !== $_SESSION['blue_key']){
            //设置SESSION信息
            $this->setSession($blue_key);
        }
        //模板赋值
        $color_spell = D('CmsSetting')->getColorSpell($_SESSION['wechat_id']);
        $this->assign('color_spell', $color_spell);
        $this->assign('indexUrl', U('Shenlan/Cat/index', array('blue_key'=>$blue_key)));
	}

	/**
	 * 获取格式化后的站点信息
     * @return null
     * @param string $blue_key 网站唯一key值
	 */
	private function setSession($blue_key){
        $_SESSION['blue_key'] = $blue_key;
        //获取微信ID
        $_SESSION['wechat_id'] = D('UserWechat')->where('blue_key='.$blue_key)->getField('id');
        //获取访问者信息
        $_SESSION['guest_open_id'] = $this->_get('guest_open_id');
        //获取网站设置信息
        $_SESSION['setting'] = D('CmsSetting')->where('wechat_id='.$_SESSION['wechat_id'])->find();
	}

} 
