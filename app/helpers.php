<?php

/**
 * 获取客户端 ip
 * @return array|false|null|string
 */
function get_client_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }
    //判断服务器是否允许$_SERVER
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        //不允许就使用getenv获取
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }

    return $realip;
}

/**
 * 记录日志
 * @param $str
 */
function writelog($str)
{
    $file = fopen(public_path() . "/log.txt", "a");
    fwrite($file, date('Y-m-d H:i:s') . "   " . $str . "\r\n");
    fclose($file);
    //print_r($str.'<br/><br/>');
}

/**
 * 判断数组的键是否存在，并且佱不为空,如果为空，取给定的值作为默认值
 * @param $arr
 * @param $column
 * @return null
 */
function isset_and_not_empty($arr, $column, $defaultValue = '')
{
    if ((isset($arr[$column]) && $arr[$column])) {
        return $arr[$column];
    } else {
        return $defaultValue;
    }
}

/**
 * 字符串超出长度显示省略号
 *
 * @param [type] $string
 * @param [type] $length
 * @return void
 */
function string_limit($string, $length)
{
    return strlen($string) > 20 ? \Illuminate\Support\Str::limit($string, $length) . '…' : $string;
}

function array_filter_null($data)
{
    return array_filter($data, function ($temp) {
        return $temp !== null;
    });
}

function getBillNo()
{
    return date('YmdHis') . \Str::random(5);
}

// 英文(大寫小寫)數字組合13~16碼【OG】
function getNumberBillNo(){
    return date('ymdHis').str_pad(mt_rand(0,9999),4,0,STR_PAD_LEFT);
}

function randomFloat($min = 0, $max = 10)
{
    $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return sprintf("%.2f", $num);
}

function is_Mobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function getChar($num = 4)  // $num为生成汉字的数量
{
    $b = '';
    for ($i = 0; $i < $num; $i++) {
        // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
        $a = chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0));
        // 转码
        $b .= iconv('GB2312', 'UTF-8', $a);
    }
    return $b;
}

function systemconfig($name){
    return \App\Models\SystemConfig::getConfigValue($name);
}

function quicklink($name){
    return app(\App\Models\QuickUrl::class)->getLink($name);
}

/**
 * 判断当前时间是否在时间范围内
 * @param $start 起始时间 00:00:00
 * @param $end  结束时间 01:00:00
 * @return int 1表示在时间范围内，0表示不在时间范围内
 */
function checkIsBetweenTime($start,$end){
    $date= date('H:i');
    $curTime = strtotime($date);//当前时分
    $assignTime1 = strtotime($start);//获得指定分钟时间戳，00:00
    $assignTime2 = strtotime($end);//获得指定分钟时间戳，01:00
    $result = 0;
    if($assignTime1 > $assignTime2) $assignTime1-=60*60*24;
    if($curTime>$assignTime1&&$curTime<$assignTime2){
        $result = 1;
    }
    return $result;
}

function convertDateToArray($date,$field){
    $condition = [];
    list($start_at, $ends_at) = explode('~', $date);
    array_push($condition, [$field, '>', $start_at]);
    array_push($condition, [$field, '<', $ends_at]);
    return $condition;
}

function getRateMoney($rate,$money){
    return sprintf("%.2f",$rate * $money / 100);
}

function getUrl($url){
    return \Str::contains($url,'//') ? substr(strstr($url,'//'),2) : $url;
}

function curls($url, $params = false, $ispost = 1, $https = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($https || \Str::startsWith($url,'https:')) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        // 如果是多维数组需要进行处理
        curl_setopt($ch, CURLOPT_POSTFIELDS, count($params) == count($params,1)?$params:http_build_query($params));
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

function get_unique_array($arr){
    $array = array_flip($arr);
    return array_keys($array);
}

function float_number($number){
    $length = strlen(intval($number));  //数字长度
    if($length > 8){ //亿单位
        $str = substr_replace(floor($number * 0.0000001),'.',-1,0)."亿";
    }elseif($length >4){ //万单位
        //截取前俩为
        $str = floor($number * 0.001) * 0.1."万";
    }else{
        return $number;
    }
    return $str;
}

function money_unit($number){
    return $number ? $number.'元' : ' - ';
}


// 隐藏部分字符串
function func_substr_replace($str, $start = 2, $length = 3, $replacement = '*')
{
    $len = mb_strlen($str, 'utf-8');
    if ($len > intval($start + $length)) {
        $str1 = mb_substr($str, 0, $start, 'utf-8');
        $str2 = mb_substr($str, intval($start + $length), NULL, 'utf-8');
    } else {
        $str1 = mb_substr($str, 0, 1, 'utf-8');
        $str2 = mb_substr($str, $len - 1, 1, 'utf-8');
        $length = $len - 2;
    }
    $new_str = $str1;
    for ($i = 0; $i < $length; $i++) {
        $new_str .= $replacement;
    }
    $new_str .= $str2;
    return $new_str;
}