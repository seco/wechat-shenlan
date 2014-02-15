<?php
/**
 * File Name: NewsModel.class.php
 * Author: Blue
 * Created Time: 2013-10-12 9:40:04
*/
class PushNewsModel extends CommonModel{

    /**
     * 获取图文的详细信息
     * @return array $newsInfo 格式化的图文信息
     * @param int $id 图文资源的ID
     */
    public function getNewsInfo($id){
        $newsObj = D('PushNews');
        $newsInfo = $newsObj->getInfoById($id);
        $arrFormatField = array('cover_name', 'mtime_text');
        $newsInfo = $newsObj->format($newsInfo, $arrFormatField);
        return $newsInfo;
    }
    
    /**
     * 接口统一函数：组装news列表
     * @return xml @content 处理后的数据
     * @param max $id 图文资源的ID或IDs
     */
    public function getPush($id){
        //如果是数组IDs
        if(is_array($id)){
            foreach($id as $k=>$v){
                $newsList[$k] = D('PushNews')->getNewsInfo($v['id']);
            }
            $count = $this->count($id);
        }else{
            $newsInfo = D('PushNews')->getNewsInfo($id);
            $newsList = array($newsInfo);
            $count = '1';
        }
        return $this->setNews($newsList, $count);
    }

    /**
     * 组装xml
     * @return xml $content 组装为xml后的数据
     * @param array $newsList 需要输出的图文数组
     * @param int $count 数组的数量
     */
    public function setNews($newsList, $count){
        $texttpl = D('Tpl')->where('type="news"')->getField('texttpl');
        $content = "<MsgType><![CDATA[news]]></MsgType>";
        $content .= "<ArticleCount>".$count."</ArticleCount>";
        $content .= "<Articles>";
        foreach($newsList as $k=>$v){
            //判断url是否需要处理
            $result = substr_count($v['cover'], 'http://');
            if(empty($result)){
                $v['cover'] = str_replace('./', 'http://'.$_SERVER['HTTP_HOST'].'/', getPicPath($v['cover']));
            }
            if(empty($v['url'])){
                //判断是否有cat
                $cat_id = D('CmsCat')->where('news_id='.$v['id'])->getField('id');
                if(empty($cat_id)){
                    $action = 'news';
                    $id = $v['id'];
                }else{
                    $action = 'cat';
                    $id = $cat_id;
                }
                $v['url'] = 'http://'.$_SERVER['HTTP_HOST'].U('Shenlan/Cat/'.$action, array(
                    'id'            => $id,
                    'blue_key'      => $_SESSION['blue_key'],
                    'guest_open_id' => $_SESSION['guest_open_id'],
                ));
            }
            $content .= sprintf($texttpl, $v['title'], $v['description'], $v['cover'], $v['url']);
        }
        $content .= "</Articles>";
        return $content;
	}

    /**
     * 格式化
     * @return array $info 格式化后的数组
     * @param  array $info 格式化前的数组
     * @param  array $arrFormatField 需要格式化的数组
     */
    public function format($info, $arrFormatField){
        //封面
        if(in_array('cover_name', $arrFormatField)){
            $info['cover_name'] = getPicPath($info['cover']);
        }
        //关键字
        if(in_array('keyword', $arrFormatField)){
            $routeInfo = D('PushRoute')->getRoute('pushNews', $info['id']);
            $info['keyword'] = $routeInfo['keyword'];
        }
        //时间
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        return $info;
    }
}
