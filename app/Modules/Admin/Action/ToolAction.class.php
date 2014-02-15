<?php
/**
 * 微应用操作类
 * @author blue
 * @version 2013-12-27
 */
class ToolAction extends WechatCommonAction {
    /**
     * 获取应用列表
     */
    public function toolList(){
        $toolObj = D('PushTool');
        //设置条件
        $arrField = array('*');
        $arrMap['status'] = array('eq', 1);
        $arrOrder = array('display_order');
        //分页
        $count = $toolObj->getCount($arrMap);
        $page = page($count);
        $pageHtml = $page->show();
        //获取列表
        $toolList = $toolObj->getList($arrField, $arrMap, $arrOrder, $page->firstRow, $page->listRows);
        $arrFormatField = array('mtime_text', 'status');
        foreach($toolList as $k=>$v){
            $toolList[$k] = $toolObj->format($v, $arrFormatField);
        }
        //模板赋值
        $tplData = array(
            'itemList' => $toolList,
            'useUrl'   => U('Admin/Tool/useTool'),
            'left_current' => 'toolList',
        );
        $this->assign($tplData);
        $this->display();
    }

    /**
     * 使用微应用
     */
    public function useTool(){
        $toolObj = D('PushTool');
        $id = intval($this->_get('id'));
        $status = $this->_get('status');
        $tool_name = $toolObj->where('id='.$id)->getField('tool_name');
        if(!empty($status)){
            $result = D('PushRoute')->addRoute('pushTool', $id, $tool_name);
        }else{
            $result = D('PushRoute')->where("keyword='".$tool_name."'")->delete();
        }
        if(!empty($result)){
            $this->success('更新成功');
        }
    }
}
