<?php
/**
 * File Name: ItemAction.class.php
 * Author: Blue
 * Created Time: 2013-11-21 17:07:42
*/
class CatAction extends WechatCommonAction{
    /**
     * 微网设置管理
     */
    public function setting(){
        $setObj = D('CmsSetting');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $setInfo = $setObj->where($arrMap)->find();
        $arrFormatField = array('logo_name', 'theme_name', 'color_name');
        $setInfo = $setObj->format($setInfo, $arrFormatField);
        $tplData = array(
            'itemInfo' => $setInfo,
            'editUrl'  => U('Admin/Cat/doEditSet'),
            'colorList' => D('CmsThemeColor')->getColorList(),
            'left_current' => 'setting',
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 微网设置操作
     */
    public function doEditSet(){
        $setObj = D('CmsSetting');
        $id = intval($this->_post('id'));
        $update = $this->_post();
        if(!empty($_FILES['pic']['name'])){
            $picList = uploadPic();
            if($picList['code'] != 'error'){
                $update['logo'] = $picList['pic']['savename'];
            }
        }
        $update['wechat_id'] = $_SESSION['wechat_id'];
        if(!empty($id)){
            if($setObj->save($update)){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }else{
            $blue_key = D('UserWechat')->where('id='.$_SESSION['wechat_id'])->getField('blue_key');

            $url = 'http://';
            $url .= $_SERVER['HTTP_HOST'];
            $url .= U('Shenlan/Cat/index', array('blue_key'=>$blue_key));
            $update['url'] = $url;
            if($setObj->add($update)){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }
    }

	/**
	 * 微网栏目列表
	 */
	public function catList(){
        //获取上级栏目信息
        $fid = intval($this->_get('id'));
		$catObj = D('CmsCat');
        $catInfo = $catObj->getInfoById($fid);
        //获取头部分级标题列表
        $titleList = array($catInfo);
        $i = 1;
        while(!empty($titleList[$i-1]['fid'])){
            $titleList[$i] = $catObj->getInfoById($titleList[$i-1]['fid']);
            $i++;
        }
        //设置条件
		$arrField = array('*');
		$arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['fid'] = array('eq', $fid);
		$arrOrder = array('mtime desc');
        //设置分页
		$count = $catObj->getCount($arrMap);
		$page = page($count);
		$pageHtml = $page->show();
        //获取栏目列表
		$catList = $catObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
		$arrFormatField = array('mtime_text', 'keyword', 'fname');
		foreach($catList as $k=>$v){
			$catList[$k] = $catObj->format($v, $arrFormatField);
		}
        //输出模板赋值
		$tplData = array(
            'titleList' => $titleList,
            'catInfo' => $catInfo,
            'catList' => U('Admin/Cat/catList'),
			'addUrl' => U('Admin/Cat/addCat'),
			'editUrl' => U('Admin/Cat/editCat'),
			'delUrl' => U('Admin/Cat/doDelCat'),
			'pageHtml' => $pageHtml,
			'itemList' => $catList,
            'left_current' => 'cat',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 内容添加页面
	 */
	public function addCat(){
        $fid = intval($this->_get('id'));
		$catObj = D('CmsCat');
        $catInfo = $catObj->getInfoById($fid);
        $fieldList = D('CmsField')->getFieldList(0, 'cmsCat', 0, $fid);
		$tplData = array(
            'template_default' => $this->getTemplateDefault($fid),
            'catInfo'  => $catInfo,
			'addUrl'   => U('Admin/Cat/doAddCat'),
            'typeList' => D('CmsCat')->getCatList(),
            'temList'  => D('CmsTemplate')->getTemList(),
            'fieldList' => $fieldList,
            'left_current' => 'cat',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 内容添加动作
	 */
	public function doAddCat(){
		$catObj = D('CmsCat');
		$catInfo = $this->_post('cat');
        $newsInfo = $this->_post('news');
        $routeInfo = $this->_post('route');
        $pluginInfo = $this->_post('plugin');
        $fieldInfo = $_POST['field'];

        //判断关键字是否可用
        if(!D('PushRoute')->checkKeyword($routeInfo['keyword'])){
            $this->error('关键字不可用');
        }

		if(!empty($_FILES)){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
                if(!empty($picList['pic']['savename'])){
                    $newsInfo['cover'] = $picList['pic']['savename'];
                }
                if(!empty($picList['ico']['savename'])){
                    $catInfo['ico'] = $picList['ico']['savename'];
                }
            }
		}
        $catInfo['wechat_id'] = $newsInfo['wechat_id'] = $_SESSION['wechat_id'];
 		$catInfo['ctime'] = $catInfo['mtime'] = $newsInfo['ctime'] = $newsInfo['mtime'] = time();
		$newsInfo['content'] = htmlspecialchars_decode(stripslashes($newsInfo['content']));
        
        //添加内容信息，并获取newsid
        $news_id = D('PushNews')->add($newsInfo);
        $catInfo['news_id'] = $news_id;
        //添加路由信息
        D('PushRoute')->addRoute('pushNews', $news_id, $routeInfo['keyword']);
        //添加类别信息
        $id = $catObj->add($catInfo);
        //添加评论插件
        D('CmsCatPlugin')->editPlugin($pluginInfo, $id);
        //添加自定义字段信息
        D('CmsField')->updateFieldList($fieldInfo, $id);

        //返回
        $url = U('Admin/Cat/catList');
        $this->success('添加成功', $url);
	}

	/**
	 * 内容编辑页面
	 */
	public function editCat(){
        //获取cat数据
		$catObj = D('CmsCat');
		$id = intval($this->_get('id'));
		$catInfo = $catObj->getInfoById($id);
		$arrFormatField = array('keyword', 'fname', 'ico_name');
		$catInfo = $catObj->format($catInfo, $arrFormatField);
        //获取news数据
        $newsInfo = D('PushNews')->getNewsInfo($catInfo['news_id']);
        $routeInfo = D('PushRoute')->getRoute('pushNews', $newsInfo['id']);
        //获取扩展字段数据
        $fieldList = D('CmsField')->getFieldList($id, 'cmsCat');
        //获取插件数据
        $pluginInfo = D('CmsCatPlugin')->where('cat_id='.$id)->find();
        //模板赋值
		$tplData = array(
			'editUrl'   => U('Admin/Cat/doEditCat'),
            'routeInfo' => $routeInfo,
            'newsInfo'  => $newsInfo,
			'catInfo'  => $catInfo,
            'typeList'  => $catObj->getCatList(),
            'fieldList' => $fieldList,
            'pluginInfo' => $pluginInfo,
            'temList'  => D('CmsTemplate')->getTemList(),
            'left_current' => 'cat',
		);
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 内容编辑操作
	 */
	public function doEditCat(){
		$catObj = D('CmsCat');
		$update = $this->_post('cat');
        $newsInfo = $this->_post('news');
        $routeInfo = $this->_post('route');
        $fieldInfo = $_POST['field'];
        $pluginInfo = $this->_post('plugin');

        //判断关键字是否可用
        if(!D('PushRoute')->checkKeyword($routeInfo['keyword'], $routeInfo['id'])){
            $this->error('关键字不可用');
        }

		if(!empty($_FILES)){
			$picList = uploadPic();
			if($picList['code'] != 'error'){
                if(!empty($picList['pic']['savename'])){
                    $newsInfo['cover'] = $picList['pic']['savename'];
                }
                if(!empty($picList['ico']['savename'])){
                    $update['ico'] = $picList['ico']['savename'];
                }
            }
		}
		$update['mtime'] = $newsInfo['mtime'] = time();
		$newsInfo['content'] = htmlspecialchars_decode(stripslashes($newsInfo['content']));
        $catObj->save($update);
        D('PushNews')->save($newsInfo);
        D('PushRoute')->editRoute($routeInfo);
        D('CmsField')->updateFieldList($fieldInfo, $update['id']);
        D('CmsCatPlugin')->editPlugin($pluginInfo, $update['id']);
        $this->success('更新成功');
    }

	/**
	 * 删除
	 */
	public function doDelCat(){
		$catObj = D('CmsCat');
		$delIds = array();
		$postIds = $this->_post('id');
		if(!empty($postIds)){
			$delIds = $postIds;
		}
		$getId = $this->_get('id');
		if(!empty($getId)){
			$delIds[] = $getId;
            $delId = $getId;
		}
		if(empty($delIds)){
			$this->error('请选择您要删除的内容');
		}
		$map['id'] = $linkMap['cat_id'] = array('in', $delIds);
        
        //获取图文ID
        $news_id = D('CmsCat')->where('id='.$delId)->getField('news_id');

        //Cat删除
		$catObj->where($map)->delete();
        //自定义字段删除
        D('CmsFieldContent')->where($linkMap)->delete();
        //留言评论删除
        D('CmsLeave')->where($linkMap)->delete();
        //图文删除,只有单条删除的情况下
        if(!empty($news_id)){
            D('PushNews')->where('id='.$news_id)->delete();
            $routeMap['obj_type'] = array('eq', 'pushNews');
            $routeMap['obj_id'] = array('eq', $news_id);
            D('PushRoute')->where($routeMap)->delete();
        } 
		$this->success('删除成功');
	}

    /**
     * 根据上级cat的子类模板估算出此次新增加模板值
     */
    private function getTemplateDefault($fid){
        $template_default = D('CmsCat')->where('fid='.$fid)->getField('template_self');
        return $template_default;
    }



}
