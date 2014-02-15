<?php
/**
 * File Name: MenuAction.class.php
 * Author: Blue
 * Created Time: 2013-11-9 16:43:33
*/
class MenuAction extends WechatCommonAction{

	/**
	 * 菜单列表
	 */
	public function menuList(){
        $menuObj = D('Menu');
        $arrField = array('*');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['fid'] = array('eq', 0);
        $arrOrder = array('display_order');
        $menuList = $menuObj->getList($arrField, $arrMap, $arrOrder);
        foreach($menuList as $k=>$v){
            $map['fid'] = array('eq', $v['id']);
            $menuList[$k]['sub_button'] = $menuObj->where($map)->select();
        }
        $tplData = array(
            'addMenu' => U('Admin/Menu/addMenu'),
            'delMenu' => U('Admin/Menu/doDelMenu'),
            'updateMenu' => U('Admin/Menu/createMenu'),
            'itemList' => $menuList,
            'left_current' => 'menu',
        );
		$this->assign($tplData);
		$this->display();
	}

	/**
	 * 页面：添加菜单
	 */
	public function addMenu(){
        $menuObj = D('Menu');
        $insert = $this->_post();
        $insert['wechat_id'] = $_SESSION['wechat_id'];
        $insert['mtime'] = time();
        $id = $menuObj->add($insert);
        if($id){
            $data = array(
                'id' => $id,
                'name' => $insert['name'],
                'type' => $insert['type'],
                'value' => $insert['value'],
            );
            $data = json_encode($data);
            echo $data;
        }
	}

    /**
     * 下载菜单
     */
    public function downMenu(){
        $token = $this->getToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$token;
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		//将获取到的内容json解码为数组
		$menuList = json_decode($data, true);
        foreach($menuList['menu']['button'] as $k=>$v){
            $insert = array();
            $insert['fid'] = '0';
            $insert['wechat_id'] = $_SESSION['wechat_id'];
            $insert['name'] = $v['name'];
            $insert['type'] = ($v['type']) ? $v['type'] : '0';
            $insert['value'] = ($v['url'].$v['key']) ? ($v['url'].$v['key']) : '0';
            $insert['mtime'] = time();
            $id = D('Menu')->add($insert);
            if(isset($v['sub_button'])){
                foreach($v['sub_button'] as $k2=>$v2){
                    $insert = array();
                    $insert['fid'] = $id;
                    $insert['wechat_id'] = $_SESSION['wechat_id'];
                    $insert['name'] = $v2['name'];
                    $insert['type'] = $v2['type'];
                    $insert['value'] = ($v2['url']) ? $v2['url'] : $v2['key'];
                    $insert['mtime'] = time();
                    D('Menu')->add($insert);
                }
            }
            $url = U('Admin/Menu/menuList');
            $this->success('更新菜单成功', $url);
        }
    }


	/**
	 * 操作：添加菜单
	 */
	public function createMenu(){
		$token = $this->getToken();
        if(empty($token)){
            $this->error('获取TOKEN失败');
        }
		//以post方式发送菜单内容给微信服务器
		//$json = $this->array_utf8_encode_recursive($_SESSION['menuInfo']);
        $menuObj = D('Menu');
        $arrField = array('id, fid, name, type, value');
        $arrMap['wechat_id'] = array('eq', $_SESSION['wechat_id']);
        $arrMap['fid'] = array('eq', 0);
        //判断是否需要下载最新菜单
        $count = $menuObj->getCount($arrMap);
        if(empty($count)){
            $this->redirect('Menu/downMenu');
        }
        $arrOrder = array('display_order');
        $menuList = $menuObj->getList($arrField, $arrMap, $arrOrder);
        foreach($menuList as $k=>$v){
            $menuList[$k]['name'] = urlencode($v['name']);
            if($v['type'] == 'view'){
                $menuList[$k]['url'] = $v['value'];
            }else{
                $menuList[$k]['key'] = urlencode($v['value']);
            }
            //获取子菜单
            $map['fid'] = array('eq', $v['id']);
            $subMenuList = $menuObj->where($map)->select();
            $menuList[$k]['sub_button'] = ($subMenuList) ? $subMenuList : array();
            foreach($menuList[$k]['sub_button'] as $k2=>$v2){
                $menuList[$k]['sub_button'][$k2]['name'] = urlencode($v2['name']);
                $menuList[$k]['sub_button'][$k2]['value'] = urlencode($v2['value']);
                if($v2['type'] == 'view'){
                    $menuList[$k]['sub_button'][$k2]['url'] = $v2['value'];
                }else{
                    $menuList[$k]['sub_button'][$k2]['key'] = urlencode($v2['value']);
                }
            }
        }
        $menuList = array('button'=>$menuList);
        $json = json_encode($menuList);
		$json = urldecode($json);
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, 1);//发送一个post请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);//post提交的数据包
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置限制时间
		curl_setopt($ch, CURLOPT_HEADER, 0);//显示返回的header区域内容
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//以文件流的形式获取
		$result = curl_exec($ch);
		curl_close($ch);
        $result = json_decode($result, true);
		if($result['errcode'] === 0){
		    $this->success('菜单更新成功');
        }else{
            $this->error('菜单更新失败');
        }
 	}

	/**
	 * Indents a flat JSON string to make it more human-readable.
	 * @param string $json The original JSON string to process.
	 * @return string Indented version of the original JSON string.
	 */
	function indent ($json) {

		$result = '';
		$pos = 0;
		$strLen = strlen($json);
		$indentStr = '	';
		$newLine = "\n";
		$prevChar = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);
			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}
			// Add the character to the result string.
			$result .= $char;
			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			$prevChar = $char;
		}

		return $result;

	}

    /**
     * 编辑菜单
     */
    public function editMenu(){
        $id = intval($this->_post('id'));
        $update = $this->_post();
        D('Menu')->where('id='.$id)->save($update);
    }

	/**
	 * 操作：删除菜单
	 */
	public function doDelMenu(){
        $id = intval($this->_get('id'));
        $menuList = D('Menu')->where('fid='.$id)->select();
        if(!empty($menuList)){
        foreach($menuList as $k=>$v){
            D('Menu')->where('id='.$v['id'])->delete();
        }
        }
        D('Menu')->where('id='.$id)->delete();
        /*
		$token = $this->getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_Setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);
		if($result['errcode'] !== 0){
			$url = U('Admin/Menu/menuList');
			$this->success('删除成功', $url);
		}else{
			$this->success($result['errmsg']);
		}
         */
	}

}



