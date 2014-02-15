<?php
/**
 * File Name: ShunhopAction.class.php
 * Author: Blue
 * Created Time: 2013-11-25 15:10:25
*/
class ChunhopAction extends HomeCommonAction{
	/**
	 * 首页方法
	 */
	public function index(){
		$this->display('Index:index');
	}

	/**
	 * 作品展示
	 */
	public function caseList(){
		$this->display('Showcase:showcaseList');
	}

	/**
	 * 作品展示详情
	 */
	public function caseInfo(){
		$id = intval($this->_get('id'));
		switch ($id){
		case 1:
			$this->display('Showcase:caseInfo1');
			break;
		case 2:
			$this->display('Showcase:caseInfo2');
			break;
		case 3:
			$this->display('Showcase:caseInfo3');
			break;
		case 4:
			$this->display('Showcase:caseInfo4');
			break;
		}
	}

	/**
	 * 婚礼视频
	 */
	public function videoList(){
		$this->display('Video:videoList');
	}

	/**
	 * 精品套系
	 */
	public function setList(){
		$this->display('Set:setList');
	}

	/**
	 * 精品套系详情
	 */
	public function setInfo(){
		$id = intval($this->_get('id'));
		switch($id){
		case 1:
			$this->display('Set:setInfo1');
			break;
		case 2:
			$this->display('Set:setInfo2');
			break;
		case 3:
			$this->display('Set:setInfo3');
			break;
		case 4:
			$this->display('Set:setInfo4');
			break;
		}
	}

	/**
	 * 免费咨询
	 */
	public function consulting(){
		$this->display('Consulting:consulting');
	}

	/**
	 * 关于珍合
	 */
	public function about(){
		$this->display('About:about');
	}
}
 
