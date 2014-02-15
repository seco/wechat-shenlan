<?php
/**
 * 微信公众账号管理
 * @author blue
 * @version 2013-12-19
 */
class WechatAction extends AdminCommonAction {
    /**
     *
     */
    public function _initialize(){
        parent::_initialize();
        $this->assign('left_current', 'wechatList');
    }
    /**
     * 列表信息
     */
    public function wechatList(){
        //实例化模型
        $wechatObj = D('UserWechat');
        //设置过滤条件
		$arrField = array('*');
		$arrMap['user_id'] = array('eq', $_SESSION['uid']);
		$arrOrder = array('id');
        //设置分页
		$count = $wechatObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
        //获取信息列表
		$wechatList = $wechatObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        //输出格式化
		$arrFormatField = array('logo_name', 'mtime_text');
		foreach($wechatList as $k=>$v){
			$wechatList[$k] = $wechatObj->format($v, $arrFormatField);
		}
        //获取可用账号数量
        $surplus = D('User')->where('id='.$_SESSION['uid'])->getField('surplus');
        //模板赋值
		$tplData = array(
			'addUrl' => U('Admin/Wechat/addWechat'),
			'editUrl' => U('Admin/Wechat/editWechat'),
			'delUrl' => U('Admin/Wechat/doDelWechat'),
            'accessUrl' => U('Admin/Home/index'),
			'pageHtml' => $pageHtml,
			'itemList' => $wechatList,
            'topMessage'  => '您的可用账号数量为 '.$surplus,
		);
		$this->assign($tplData);
		$this->display();
	}

    /**
     * 页面：添加公众账号
     */
    public function addWechat(){
        //判断用户是否还有可用的公众账号
        $surplus = D('User')->where('id='.$_SESSION['uid'])->getField('surplus');
        if(empty($surplus)){
            $url = U('Admin/Wechat/wechatList');
            $this->error('您的可用账号数量不足，不能继续添加', $url);
        }
        //模板赋值
        $tplData = array(
            'addUrl' => U('Admin/Wechat/doAddWechat'),
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 操作：添加公众账号
     */
    public function doAddWechat(){
		$wechatObj = D('UserWechat');
        //获取插入数据
		$insert = $this->_post();
		if(!empty($_FILES['logo']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$insert['logo'] = $picList['logo']['savename'];
			}
		}
		$insert['user_id'] = $_SESSION['uid'];
		$insert['ctime'] = $insert['mtime'] = time();
		$rand = rand(1000, 9999);
		$insert['blue_key'] = $insert['ctime'].$rand;
        $url = 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        $url .= U('Shenlan/Cat/index', array('blue_key'=>$blue_key));
        $insert['url'] = $url;
		if($wechatObj->add($insert)){
            //添加成功后将用户账号的可用数量减1
            D('User')->where('id='.$_SESSION['uid'])->setDec('surplus');
			$url = U('Admin/Wechat/wechatList');
			$this->success('添加成功', $url);
		}else{
			$this->error('添加失败');
		}
	}

    /**
     * 页面：编辑公众账号
     */
    public function editWechat(){
        $wechatObj = D('UserWechat');
        $id = intval($this->_get('id'));
        //获取账号信息数据
        $wechatInfo = $wechatObj->getInfoById($id);
        $wechatInfo = $wechatObj->format($wechatInfo, array('logo_name'));
        //模板赋值
        $tplData = array(
            'editUrl' => U('Admin/Wechat/doEditWechat'),
            'itemInfo' => $wechatInfo,
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 操作：编辑公众账号
     */
    public function doEditWechat(){
		$wechatObj = D('UserWechat');
		$update = $this->_post();
		if(!empty($_FILES['pic']['name'])){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
				$update['logo'] = $picList['pic']['savename'];
			}
		}
		if($wechatObj->save($update)){
			$this->success('更新成功');
		}else{
			$this->error('更新失败');
		}
	}

    /**
     * 操作：删除公众账号
     * 现在不允许用户自行删除公众账号，但可以任意编辑
     */
    public function doDelWechat(){
        $wechatObj = D('UserWechat');
		$delIds = array();
		$postIds = $this->_post('id');
		if(!empty($postIds)){
			$delIds = $postIds;
		}
		$getId = $this->_get('id');
		if(!empty($getId)){
			$delIds[] = $getId;
		}
		if(empty($delIds)){
			$this->error('请选择您要删除的数据');
		}
		$arrMap['id'] = $arrAllMap['wechat_id'] = array('in', $delIds);
        $result = $wechatObj->where($arrMap)->delete();
		if($result){
            $wechatObj->delAll($arrAllMap);
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}

