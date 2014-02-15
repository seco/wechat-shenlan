<?php
/**
 * 通用工具模型
 * @author blue
 * @version 2013-12-21
 */
class PushToolModel extends CommonModel {
    /**
     * 接口统一函数：获取工具信息
     */
    public function getPush($id, $keyword){
        $toolObj = D('PushTool');
        $toolInfo = $toolObj->getInfoById($id);
        return $this->$toolInfo['function'](substr($keyword, 6, strlen($keyword)-6));
    }

    /**
     * 格式化
     * @return array $info 格式化后的数组
     * @param array $info 格式化前的数组
     * @param array $arrFormatField 需要格式化的数组
     */
    public function format($info, $arrFormatField){
        //时间
        if(in_array('mtime_text', $arrFormatField)){
            $info['mtime_text'] = date('Y-m-d H:i', $info['mtime']);
        }
        //使用状态
        if(in_array('status', $arrFormatField)){
            $info['status'] = D('PushRoute')->checkKeyword($info['tool_name']);
            $info['status_name'] = ($info['status']) ? '未使用'
                : '已使用';
        }
        return $info;
    }

    ///////////////////////功能类函数///////////////////////////////////
    /**
     * 百度API：根据经纬度获取城市名称
     */
    public function getAddress(){
        $url = 'http://api.map.baidu.com/geocoder?location='.$_SESSION['latitude'].','.$_SESSION['longitude'].'&output=json&key=5c1da412cb98cbde54f87d45d8feda56';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        $city = $result['result']['addressComponent']['city'];
        $_SESSION['current_city'] = $city;
    }

    /**
     * 天气搜索
     */
    private function weather($city){
        //$city = '济南';
        $map['city'] = $city;
        $cityCode = M('Weather')->where("`city`='".$city."'")->getField('citycode');
        $url = 'http://m.weather.com.cn/data/'.$cityCode.'.html';
        $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        //json解码为对象
        $result = json_decode($result, true);
        $content = $result['weatherinfo']['city'].' '.$result['weatherinfo']['week'].' '.$result['weatherinfo']['temp3'];
        return D('PushText')->setText($content);
    }

    /**
     * 百度翻译
     */
    private function trans($keyword){
        $url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id=9peNkh97N6B9GGj9zBke9tGQ&q=".$keyword."&from=auto&to=auto";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        //json解码为对象
        $result = json_decode($result, true);
        $content = $result['trans_result']['0']['dst'];
        return D('PushText')->setText($content);
    }

    /**
     * 聊天机器人
     */
    private function chat($content){
        $key = '92773352-3798-4737-859d-bbb5dbe77b26';
        $url = "http://sandbox.api.simsimi.com/request.p?key=".$key."&lc=ch&text=".$content;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        return D('PushText')->setText($result['response']);
    }

    /**
     * 虫洞api
     */
    private function chong($content){
        //$url = "http://wap.unidust.cn/api/searchout.do?type=wap&ch=1001&info=".$content."&appid=71";
        $url = "http://xyapi.sinaapp.com/Api/?type=joke";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);exit;
    }

    /**
     * 获取地址位置
     */
    public function getLocation(){
        $latitude = $this->_post('latitude');
        $longitude = $this->_post('longitude');
        if(!empty($latitude)){
            $_SESSION['latitude'] = $latitude;
            $_SESSION['longitude'] = $longitude;
            $this->getAddress();
            $this->redirect('Hotel/result');
        }

    }

    /**
     * 获取百度新闻
     */
    public function baiduNews(){
        $url = 'http://cn.engadget.com/rss.xml';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $result, $values, $tags);
        xml_parser_free($parser);

        for($i=0; $i<5; $i++){
        foreach ($tags as $k=>$v){

            if($k == 'title'){
                $newsList[$i]['title'] = $values[$v[$i+2]]['value'];
            }elseif($k == 'description'){

                $description = $values[$v[$i+1]]['value'];
                //如果简介中有图片，就将其作为图文封面
                $pattern = '/<img(.*)src="(.*)"/Us';
                preg_match($pattern, $description, $content);
                $newsList[$i]['cover'] = $content['2'];

                $newsList[$i]['description'] = $values[$v[$i+1]]['value'];
            }elseif($k == 'link'){
                $newsList[$i]['url'] = $values[$v[$i+2]]['value'];
            }

        }
        }
        $content = D('PushNews')->setNews($newsList, 5);
	    return ($content);

    }

    /**
     * 获取新浪RSS新闻数据
     */
    public function sinaNews(){
        $url = "http://rss.sina.com.cn/news/marquee/ddt.xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        //$arrResult = (array)simplexml_load_string($result);
        //$i = 0;
        /*
          foreach($arrResult['channel']->item as $k=>$v){
          $arrNews[$i]['title'] = $v->title;
          $arrNews[$i]['description'] = $v->description;
          $i++;
          }
        */

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $result, $values, $tags);
        xml_parser_free($parser);


        foreach($tags as $k2=>$v2){
            foreach($values as $k1=>$v1){
                for($i=2; $i<=6; $i++){
                    if($k2 == 'title'){
                        $arrNews[$i]['title'] = $values[$v2[$i]]['value'];
                    }elseif($k2 == 'description'){
                        $arrNews[$i]['description'] = $values[$v2[$i-1]]['value'];
                    }
                }
            }
        }
        $content = $arrNews[2]['title'].$arrNews[2]['description'];
        return D('PushText')->setText($content);
    }

    /**
     * 笑话
     */
    private function joke(){
        $rand = rand(1, 1400);
        $url = 'http://m.haha365.com/zz_joke/index_'.$rand.'.htm';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $pattern = '/<div class="haha">(.*)<\/h3>(.*)<\/div>/Us';
        preg_match($pattern, $result, $content);
        $content = mb_convert_encoding($content[2], 'UTF-8', 'gbk');
        $content = strip_tags($content);
        return D('PushText')->setText($content);
    }

    /**
     * 每日英语
     */
    private function english(){
        $rand = rand('100', '727');
        $url = 'http://wap.iciba.com/dailysentence/content/'.$rand.'#anchor';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $pattern = '/<div class="dayC" id="(.*)">(.*)<a(.*)<h2 class="cn">(.*)<\/h2>/Us';
        preg_match($pattern, $result, $content);
        $content[2] = strip_tags($content[2]);
        return D('PushText')->setText($content[2].$content[4]);
        //print_r($content);exit;
    }

    /**
     * 可乐
     */
    private function kele(){
        $rand = rand('01', '290');
        $url = 'http://www.kelepuzi.com/text/page/'.$rand.'.html';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $pattern = '/<div class="post-content">(.*)<\/p>/Us';
        preg_match($pattern, $result, $content);
        $content = strip_tags($content[1]);
        return D('PushText')->setText($content);
        //print_r($content);exit;
    }

    /**
     * engadget
     */
    public function engadget(){
        $url = 'http://cn.engadget.com/rss.xml';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
    }
}
