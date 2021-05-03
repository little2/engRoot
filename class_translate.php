<?php
// +----------------------------------------------------------------------
// | PHP MVC FrameWork v1.0 在线翻译类 使用iciba接口 无需申请Api Key
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2099 http://blog.wxsshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zlx3323 <605476114@qq.com> 2019年6月1日 下午15:22:15
// +----------------------------------------------------------------------
/**
 * 在线翻译类
 * @author zlx3323 <605476114@qq.com>
 */
class Translate {
    /**
     * 支持的语种
     * @var ArrayAccess
     */
    static $Lang = Array (
            'auto' => '自动检测',

            'de' => '德语',
            'ru' => '俄语',
            'fr' => '法语',
            'ko' => '韩语',

            'ja' => '日语',


            'es' => '西班牙语',

            'en' => '英语',

            'zh' => '中文'
    );
    /**
     * 获取支持的语种
     * @return array 返回支持的语种
     */
    function getLang() {
        return self::$Lang;
    }

    function getTrans($text,$rand=0) 
    {
     
      $r=(time()+$rand)%4;

      switch($r)
      {
        case 0:
          return $this->getBaiduTranslate($text);
        break;

        case 1:          
          return $this->getGoogleTranslate($text);

        case 2:          
          return $this->getYoudaoTranslate($text);  

        case 3: 
          if(strlen($text)>14)
          {
            return $this->exec($text);  
          }  
          return null;       
            
         
      }
    }




    function getBaiduTranslate($text)
    {      
      $appid='20191029000347333';
      $key='7atm_BBIB5YMQy3gM7zF';
      $salt='little2';
      $q=$text;
      $sign=md5($appid.$q.$salt.$key);
      $url="http://api.fanyi.baidu.com/api/trans/vip/translate";

      $transData = array (
        'appid' => $appid,
        'salt' => $salt,
        'sign' => $sign,
        'from' => 'auto',
        'to' => 'zh',
        'q' => $text
      );

      $result_array = $this->getPostReturn($transData , $url);
      print_r($result_array);
      die;
      $result['content']['out'] = $result_array['trans_result'][0]['dst'];
      $result['way'] = 'baidu';
      return  $result; 
    }

    function getGoogleTranslate($text)
    {
      $url="http://translate.google.cn/translate_a/single";

      $transData = array (
        'client' => 'gtx',
        'dt' => 't',
        'dj' => '1',
        'ie' => 'UTF-8',
        'sl' => 'auto',
        'tl' => 'zh_CN',
        'q' => $text
      );

      
      $result_array = $this->getPostReturn($transData , $url);

      $outputStrArr['content']['out']=$result_array['sentences'][0]['trans'];
      $outputStrArr['way'] = 'google';
      return $outputStrArr;
    }

    function getYoudaoTranslate($text)
    {
      $url = "http://fanyi.youdao.com/translate?&doctype=json&type=AUTO&i=".$text;
      $result = file_get_contents($url);
      $result_array=json_decode($result,true);

      $outputStrArr['content']['out']=$result_array['translateResult'][0][0]['tgt'];
      $outputStrArr['way'] = 'youdao';
      return $outputStrArr;    
    }

    function getTianTranslate($text)
    {
      //https://www.tianapi.com/apiview/49
      $url = "http://api.tianapi.com/txapi/enwords/index?key=abc18d82b0d5ef72e0614cd4e26dc474&word=".$text;
      $result = file_get_contents($url);
      
      $result_array=json_decode($result,true);
     
      $outputStrArr['content']['out']=$result_array['newslist'][0]['content'];
      $outputStrArr['way'] = 'tian';
      return $outputStrArr;    
    }   

    /**
     * 执行文本翻译
     * @param string $text 要翻译的文本
     * @param string $from 原语言语种 默认:中文
     * @param string $to 目标语种 默认:英文
     * @return boolean string 翻译失败:false 翻译成功:翻译结果
     */
    function exec($text, $from = 'en', $to = 'zh') {
      if(!$text) return false;
        // http://fy.iciba.com/ajax.php?a=fy&f=auto&t=auto&w=%E6%88%91%E7%88%B1%E4%BD%A0
        $url = "http://fy.iciba.com/ajax.php?a=fy";
        $transData = array (
                'f' => $from,
                't' => $to,
                'w' => $text
        );
        $result = $this->getPostReturn($transData , $url);

        $result['way'] = 'iciba';
        return $result;

//         $data = http_build_query ( $data );

//         $ch = curl_init ();

//         curl_setopt ( $ch, CURLOPT_URL, $url );
//         curl_setopt ( $ch, CURLOPT_REFERER, "http://fy.iciba.com" );
//         curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.76 Mobile Safari/537.36" );
// //        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0' );
//         curl_setopt ( $ch, CURLOPT_HEADER, 0 );
//         curl_setopt ( $ch, CURLOPT_POST, 1 );
//         curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
//         curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
//         curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
//         $result = curl_exec ( $ch );
//         curl_close ($ch);
//         $result_array=json_decode($result,true);
//         if(!isset($result_array['content'])){
//           return false;
//         }

//         return $result_array;
    }

    function getPostReturn($transData , $url){
        $postData = http_build_query ( $transData , $url);

        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_REFERER, "http://fy.iciba.com" );
        curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.76 Mobile Safari/537.36" );
//        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0' );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
        $result = curl_exec ( $ch );
        curl_close ($ch);
        $result_array=json_decode($result,true);
        // if(!isset($result_array['content'])){
        //   return false;
        // }
        return $result_array;
    }

    /*
     * Create and execute the HTTP CURL request.
    *
    * @param string $url        HTTP Url.
    * @param string $authHeader Authorization Header string.
    *
    * @return string.
    *
    */
    function curlRequest($url, $authHeader=null){
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt ($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
      //  curl_setopt ($ch, CURLOPT_HTTPHEADER, array($authHeader));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
        //Execute the  cURL session.
        $curlResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        return $curlResponse;
    }

    function downloadMP3($keyword,$mp3url){

      $vocabulary_path='vocabulary';
      $file=$vocabulary_path.'/'.$keyword.'.mp3';
      
      
      if(!is_readable($vocabulary_path))
      {
          is_file($vocabulary_path) or mkdir($vocabulary_path,0700);
      }
     
      $_is_Download=file_exists($file);

      if(!$_is_Download)
      {       
        $strResponse=self::curlRequest($mp3url);
        file_put_contents($file, $strResponse);
      }
    }

}
?>
