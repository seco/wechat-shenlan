<?php
/**
 * 微网栏目分类表
 * @author blue
 * @version 2012-12-23
 */
class CmsCatModel extends CommonModel{
    /**
     * 获取news_ids
     */
    /*
    public function getNewsIds(){
        $arrField = array('id');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrOrder = array();
        $arrNewsId = D('CmsCat')->getList($arrField, $arrMap, $arrOrder);
        return $arrNewsId;
    }
     */
    
    /**
     * 根据ID获取详情
     * @return array $catInfo 栏目详情
     * @param int $id 栏目ID
     */
    public function getInfoById($id, $field = '*'){
        $map['id'] = array('eq', $id);
        $map['wechat_id'] = $_SESSION['wechat_id'];
        $catInfo = D('CmsCat')->getInfo($field, $map);
        $catInfo = D('CmsCat')->format($catInfo, array('ico_name', 'mtime_text'));
        $newsInfo = D('PushNews')->getNewsInfo($catInfo['news_id']);
        $info = array_merge($newsInfo, $catInfo);
        return $info;
    }

    /**
     * 获取列表
     * @return array $list 列表
     * @param array $arrField 字段
     * @param array $arrMap 条件
     * @param array $arrOrder 排序
     * @param int $offset 开始
     * @param int $length 长度
     */
    public function getList($field, $map, $order, $offset, $length)
    {
        $list = $tmpList = array();
        //filed
        $this->field($field);
        //map
        if (!empty($map)) {
            $this->where($map);
        }
        //order
        $this->order($order);
        //limit判断
        if ($offset > 0) {
            $this->limit($offset, $length);
        } else {
            $this->limit($length);
        }
        //列表
        $tmpList = $this->select();
        if (!empty($tmpList)) {
            foreach ($tmpList as $k=>$v) {
                $list[] = $v;
            }
        }
        foreach($list as $k=>$v){
            $v = D('CmsCat')->format($v, array('ico_name'));
            $newsList[$k] = D('PushNews')->getNewsInfo($v['news_id']);
            $list[$k] = array_merge($newsList[$k], $v);
        }
        return $list;
    }

    /**
     * 接口统一处理函数：获取推送内容
     * @return xml 推送信息
     * @param id 栏目ID
     */
    public function getPush($id){
        $catObj = D('CmsCatNews');
        $arrField = array('*');
        $arrMap['cat_id'] = array('eq', $id);
        $arrOrder = array();
        $catInfo = $catObj->getList($arrField, $arrMap, $arrOrder);
        $arrIds = array();
        foreach($catInfo as $k=>$v){
            $arrIds[] = $v['news_id'];
        }
        return D('PushNews')->getPush($arrIds);
    }

    /**
     * 获取微网前台栏目的输出信息
     * @return array 栏目信息
     * @param int 栏目ID
     * @param array $map 过滤条件
     */
    public function getCatInfo($id=0, $arrMap){
        //获取栏目信息
        $catInfo = D('CmsCat')->getInfoById($id);
        //获取自定义字段
        $catInfo = array_merge(D('CmsField')->getFieldList($catInfo['id'], 'cmsCat', 'other'), $catInfo);
        //获取显示模板
        if(!empty($catInfo['template_self'])){
            $template = $catInfo['template_self'];
        }elseif(!empty($catInfo['template_id'])){
            $template = D('CmsTemplate')->where('id='.$catInfo['template_id'])->getField('spell');
            $template = 'Default:'.$template;
        }else{
            $template = 'Default:detail';
        }
        //获取插件信息
        $plugin_id = D('CmsCatPlugin')->where('cat_id='.$catInfo['id'])->getField('plugin_id');
        if(!empty($plugin_id)){
            $catInfo['plugin'] = D('CmsPlugin')->where('id='.$plugin_id)->getField('spell');
        }
        if(empty($catInfo['id'])){
            $fid = '0';
        }else{
            $fid = $catInfo['id'];
        }
        //获取文章信息
        $arrField = array('*');
        $arrMap['fid']       = array('eq', $fid);
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['status']    = array('eq', '1');
        $arrOrder = array('display_order', 'mtime desc');
        //分页
        $count = D('CmsCat')->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        //获取下级栏目列表
        $catList = D('CmsCat')->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        //列表格式化
        foreach($catList as $k=>$v){
            $catList[$k] = array_merge(D('CmsField')->getFieldList($v['id'], 'cmsCat', 'other'), $v);
            $catList[$k]['url'] = U('Shenlan/Cat/cat', array('id'=>$v['id'], 'blue_key'=>$_SESSION['blue_key']));
        }
        //组装数据输出
        $catInfo = array(
            'catInfo' => $catInfo,
            'catList' => $catList,
            'pageHtml' => $pageHtml,
            'template' => $template,
        );
        return $catInfo;
    }

    /**
     * 输出格式化
     * @return array $info 格式化后的数组
     * @param  array $info 格式化前的数组
     * @param  array $arrFormatField 需要格式化的数组
     */
    public function format($info, $arrFormatField){
        //上级栏目名称
        if(in_array('fname', $arrFormatField)){
            $news_id = D('CmsCat')->where('id='.$info['fid'])->getField('news_id');
            $newsInfo = D('PushNews')->getNewsInfo($news_id);
            $info['fname'] = $newsInfo['title'];
        }
        //关键字
        if(in_array('keyword', $arrFormatField)){
            $news_id = D('PushNews')->where('id='.$info['news_id'])->getField('id');
            $routeInfo = D('PushRoute')->getRoute('pushNews', $news_id);
            $info['keyword'] = $routeInfo['keyword'];
        }
        //封面
        if(in_array('ico_name', $arrFormatField)){
            $info['ico_name'] = getPicPath($info['ico']);
        }
        //时间
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }

    /**
     * 后台添加栏目：获取栏目列表
     */
    public function getCatList(){
        $catObj = D('CmsCat');
        $arrField = array('id', 'fid', 'news_id');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrOrder = array('fid');
        $catList = $catObj->getList($arrField, $arrMap, $arrOrder);
        foreach($catList as $k=>$v){
            $catList[$k]['title'] = D('PushNews')->where('id='.$v['news_id'])->getField('title');
        }
        return($catList);
    }

    /**
     * 浏览量增加
     * @return
     * @param int $id 分类表资源ID
     */
    public function addView($id){
        $news_id = D('CmsCat')->where('id='.$id)->getField('news_id');
        $map['id'] = array('eq', $news_id);
        D('PushNews')->where($map)->setInc('views');
    }

    /**
     * 点赞量增加
     * @return json $up_count 点赞总数
     * @param int $id 分类表资源ID
     */
    public function addUp($id){
        $news_id = D('CmsCat')->where('id='.$id)->getField('news_id');
        $map['id'] = array('eq', $news_id);
        D('PushNews')->where($map)->setInc('ups');
    }

}
