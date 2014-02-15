<?php
/**
 * File Name: ShopModel.class.php
 * Author: Blue
 * Created Time: 2013-11-28 12:03:21
*/
class ShopModel extends CommonModel{
  /**
   * 格式化
   */
  public function format($info, $arrFormatField){
    //封面
    if(in_array('cover_name', $arrFormatField)){
      $info['cover_name'] = getPicPath($info['cover']);
    }
    return $info;
  }
}
 
