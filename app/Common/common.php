<?php
/**
 * 扩展函数库
 * @version 2013-02-17
 */

/**
 * 分页函数
 */

function page($count, $listRows = '10', $theme = '')
{
    import('ORG.Util.Page');
    //初始化
    $page = new Page($count, $listRows);

    //显示文字
    $page->setConfig('first','<<');
    $page->setConfig('last','>>');
    $page->setConfig('prev', '<');
    $page->setConfig('next', '>');
    //样式
    //prePage=上5页，upPage=上一页，downPage=下一页 nextPage=下5页
    //$page->setConfig('theme', "%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%");
    if ($theme = 'simple') {
        $theme = "%upPage% %linkPage% %downPage%";
    } else {
        $theme = "%first% %prePage% %upPage% %linkPage% %downPage% %nextPage% %end% 共%totalPage%页";
    }
    $page->setConfig('theme', $theme);

    return $page;
}




/**
 * 转换为安全的纯文本
 *
 * @param string  $text
 * @param boolean $parse_br    是否转换换行符
 * @param int     $quote_style ENT_NOQUOTES:(默认)不过滤单引号和双引号 ENT_QUOTES:过滤单引号和双引号 ENT_COMPAT:过滤双引号,而不过滤单引号
 * @return string|null string:被转换的字符串 null:参数错误
 */
/**function t($text, $parse_br = false, $quote_style = ENT_NOQUOTES)
{
    if (is_numeric($text))
        $text = (string)$text;

    if (!is_string($text))
        return null;

    if (!$parse_br) {
        $text = str_replace(array("\r","\n","\t"), ' ', $text);
    } else {
        $text = nl2br($text);
    }

    //$text = stripslashes($text);
    $text = htmlspecialchars($text, $quote_style, 'UTF-8');

    return $text;
}*/

/**
 * 发送邮件
 * @param   string  $toEmail    收件人的email
 * @param   string  $toName     收件人的称呼
 * @param   string  $subject    邮件主题
 * @param   string  $body       邮件内容
 * @param   string  $attachs    附件数组
 * @return  boolean/string
 */
 function sendMail($toEmail, $toName, $subject = '', $body = '', $attachs = array())
 {
    $setting = $GLOBALS['_G']['setting'];
    $setting = array(
        'smtp_host' => $setting['email_host'],
        'smtp_port' => $setting['email_port'],
        'smtp_user' => $setting['smtp_user'],
        'smtp_pass' => $setting['smtp_pass'],
        'smtp_from_email'   => $setting['smtp_user'],
        'smtp_from_name'    => $setting['smtp_from_name'],
    );
    //导入类库
    vendor('PHPMailer.class#phpmailer');
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    //$mail->SMTPSecure = 'ssl';
    //调试模式：0=不显示，1=错误和信息， 2=只显示信息
    $mail->SMTPDdebug = 0;

    //设置验证信息
    $mail->SMTPAuth = true;
    $mail->Host = $setting['smtp_host'];
    $mail->Port = $setting['smtp_port'];
    $mail->Username = $setting['smtp_user'];
    $mail->Password = $setting['smtp_pass'];
    
    //设置发件人信息和回复信息
    $mail->SetFrom($setting['smtp_from_email'], $setting['smtp_from_name']);
    $mail->AddReplyTo($setting['smtp_from_email'], $setting['smtp_from_name']);

    //邮件内容
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($toEmail, $toName);
    
    //设置附件
    if (is_array($attachs)) {
        foreach ($attachs as $attach) {
            is_file($attach) && $mail->AddAttachment($attach);
        }
    }
    if ($mail->Send()) {
        return true;
    } else {
        return  $mail->ErrorInfo;
    }
 }



////////////////////加密解密////////////////////
/**
 * 加密
 * @todo    算法待选择
 * @param   string  $plainText  明文
 * @return  string  $cipherText 密文
 */
function encrypt($plainText)
{
    //import('ORG.Crypt.Base64');
    //$crypt = new Base64();
    $cipherText = '';
    $cipherText = base64_encode($plainText);
    return $cipherText;
}

/**
 * 解密
 * @todo    算法待选择
 * @param   string  $ciphertext 密文
 * @return  string  $plainText  明文
 */
function decrypt($cipherText)
{
    $plainText = '';
    $plainText = base64_decode($cipherText);
    return $plainText;
}


////////////////////图片附件////////////////////
/**
 * 上传图片
 */
function upload()
{
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();// 实例化上传类
    $upload->maxSize  = 3145728 ;// 设置附件上传大小
    $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg', 'mpeg', 'mp3','mp4', 'flv');// 设置附件上传类型
    $upload->savePath =  './data/attach/';// 设置附件上传目录
    //子目录配置
    $upload->autoSub = true;
    $upload->subType = 'custom';
    $upload->subDir = date('Ym').'/'.date('d').'/';
    //缩略图
    $upload->thumb = true;
    $upload->thumbPrefix = '';
    $upload->thumbSuffix = '_b,_m,_s';
    $upload->thumbMaxWidth = '800, 300, 150';
    $upload->thumbMaxHeight = '800, 300, 150';
    $result = $upload->upload();
    //插入到图片表中
    $tmpList = $upload->getUploadFileInfo();
    foreach ($tmpList as $k=>$v) {
        $fileList[$v['key']] = $v;
    }
    return $fileList;
}

/**
 * 上传图片
 */
function uploadPic()
{
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();// 实例化上传类
    $upload->maxSize  = 3145728 ;// 设置附件上传大小
    $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->savePath =  './data/attach/';// 设置附件上传目录
    //子目录配置
    $upload->autoSub = true;
    $upload->subType = 'custom';
    $upload->subDir = date('Ym').'/'.date('d').'/';
    //缩略图
    $upload->thumb = true;
    $upload->thumbPrefix = '';
    $upload->thumbSuffix = '_b,_m,_s';
    $upload->thumbMaxWidth = '800, 300, 150';
    $upload->thumbMaxHeight = '800, 300, 150';
    $result = $upload->upload();
    //插入到图片表中
    $tmpList = $upload->getUploadFileInfo();
    foreach ($tmpList as $k=>$v) {
        $picList[$v['key']] = $v;
    }
    return $picList;
}


/**
 * 上传附件
 */
function uploadAttach()
{
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();
    //附件大小
    $upload->maxSize  = 31457280 ;
    //附件类型
    $upload->allowExts  = array('pdf', 'mp3', 'avi', 'flash', 'mp4', 'mpeg', 'flv');
    //附件路径
    $upload->savePath =  './Public/Uploads/';// 设置附件上传目录
    $upload->autoSub = true;
    $upload->subType = 'custom';
    $upload->subDir = date('Ym').'/'.date('d').'/';
    //上传
    $result = $upload->upload();
    //上传结果
    $tmpList = $upload->getUploadFileInfo();
    foreach ($tmpList as $k=>$v) {
        $attachList[$v['key']] = $v;
    }
    return $attachList;
}


/**
 * 获取图片完整路径
 */
function getPicPath($pic, $size)
{
    $path = '';
    if (!empty($pic)) {
        //$path = 'images/cover_middle.png';
        $picName =  substr($pic, 0, strrpos($pic, '.'));
        $picExtension = substr($pic, strrpos($pic, '.'));
        if ($size == 'b') {
            $path = $picName.'_b'.$picExtension;
        } else if ($size == 'm') {
            $path = $picName.'_m'.$picExtension;
        } else if ($size == 's') {
            $path = $picName.'_s'.$picExtension;
        } else {
            $path = $picName.$picExtension;
        }
        $path = "./data/attach/".$path; 
    }
    return $path;
}

/**
 * 获取附件路径
 */
function getAttachPath($attach)
{
    $path = '';
    if ($attach) {
        $path = "./Public/Uploads/".$attach;
    }
    return $path;
}





////////////////////环境////////////////////
/**
 * 获取来路地址
 */
function getReferer()
{
    $referer = '';
    if (!empty($_GET['referer'])) {
        $referer = trim($_GET['referer']);
    } else {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    //之前是登陆、注册页，直接跳转到个人中心
    if (strpos($referer, 'login') || strpos($referer, 'register') || $referer == '') {
        $referer = U('User/Index/index');
    }
    return $referer;
}


/**
 * 获取客户端ip地址
 */
function getClientIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
        $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "unknown";
    }
    return($ip);
}

////////////////////验证函数////////////////////

/**
 * email格式是否正确
 */
function isEmailVaild($email)
{
    return true;
}

/**
 * url格式是否正确
 */
function isUrlValid($url)
{
    return true;
}

/**
 * 手机号码格式
 */
function isMobileValid($mobile)
{
    return true;
}

/**
 * 身份证号码格式
 */
function isIdCardValid($idCard)
{
    return true;
}

/**
 * 邮编格式验证
 */
function isZipcodeValid($zipcode)
{
    return true;
}

/**
 * 昵称是否合法
 */
function isUnameVaild($uname)
{
    return true;
}

function getShort($str, $length = 40, $ext = '') {
    $str    =   htmlspecialchars($str);
    $str    =   strip_tags($str);
    $str    =   htmlspecialchars_decode($str);
    $strlenth   =   0;
    $out        =   '';
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
    foreach($match[0] as $v){
        preg_match("/[\xe0-\xef][\x80-\xbf]{2}/",$v, $matchs);
        if(!empty($matchs[0])){
            $strlenth   +=  1;
        }elseif(is_numeric($v)){
            //$strlenth +=  0.545;  // 字符像素宽度比例 汉字为1
            $strlenth   +=  0.5;    // 字符字节长度比例 汉字为1
        }else{
            //$strlenth +=  0.475;  // 字符像素宽度比例 汉字为1
            $strlenth   +=  0.5;    // 字符字节长度比例 汉字为1
        }

        if ($strlenth > $length) {
            $output .= $ext;
            break;
        }

        $output .=  $v;
    }
    return $output;
}



function tranSeo($i,$m,$data)
{      
     $result['seo_title'] = $data[$i.'_'.$m.'_'.'title'];
     $result['seo_keyword'] = $data[$i.'_'.$m.'_'.'keyword'];
     $result['seo_description'] = $data[$i.'_'.$m.'_'.'description'];
    return $result;
    
}


/*
*接口
*获取个人基本信息
*/

function getBaseInfo($params,$funcName){

        $curlPost = http_build_query($params);
        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,'http://42.51.8.149/WS_Student.asmx/'.$funcName);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        echo  curl_error($ch);
        $data = curl_exec($ch);//运行curl
        //echo $data;exit;
        curl_close($ch);
        $doc = new DOMDocument();
        $doc->loadXML($data);

        $studentList = array();
        $studentElemList = $doc->getElementsByTagName('ds');

        foreach ($studentElemList as $studentElem) {
            $student = array();
            foreach ($studentElem->childNodes as $fieldElem) {
                $key = $fieldElem->tagName;
                $value = $fieldElem->nodeValue;
                if ($key && $value) {
                    $student[$key] = $value;
                }
            }
            $studentList[] = $student;
        }

       return $studentList;
}

/**
 * 输出用于测试的数据
 */
function cbug($content){
	if(is_array($content)){
		$content = serialize($content);
	}
	$insert = array();
	$insert['content'] = $content;
	$insert['ctime'] = time();
	D('DebugLog')->add($insert);
}


     
?>
