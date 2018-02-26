<?php
trait curl
{
    //最后一次的curl的结果
     public $lastCurl;

//    $url:地址
//    $post_data：post参数
//    $return:返回结果 0只有头 1只有body 2全部
//    $cookies：设置cookies => xxx=xxx;
    function post( $url, $post_data=false, $cookies=false, $return=0, $headers=false ){
        $UserAgent = 'Mozilla/5.0 (Linux; Android 6.0.1; Redmi 4A Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/46.0.2490.76 Mobile Safari/537.36';
        $url = $this->dealUrl( $url );
        $curl = curl_init();
        //设置URL
        curl_setopt( $curl, CURLOPT_URL, $url );
        //设置cookies
        $cookies && curl_setopt( $curl, CURLOPT_COOKIE, $cookies );
        //设置post数据
        curl_setopt( $curl, CURLOPT_POST, 1);
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
        //不自动输出结果
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        //设置header
        curl_setopt( $curl, CURLOPT_HEADER,  ($return == 1)?0:1 );
        //设置body
        curl_setopt( $curl, CURLOPT_NOBODY,  ($return == 0)?1:0 );
        //执行
        // if($headers)
        //  curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        // else
        //  curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        //超时
        // curl_setopt($curl, CURLOPT_TIMEOUT,2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $result = curl_exec( $curl );
// echo curl_getinfo($curl, CURLINFO_HEADER_OUT);
        //如果是header和body都显示，则进行分割
        if( $return==2 )
        {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body = substr($result, $headerSize);
            $this->lastCurl = [ $header, $body ];
            return $this;
        }
        $this->lastCurl = $result;
        return $this;
    }

//    $url:地址
//    $return:返回结果 0只有头 1只有body 2全部
//    $cookies：设置cookies => xxx=xxx;
    function get( $url, $cookies=false, $return=1, $headers=false ){
        $UserAgent = 'Mozilla/5.0 (Linux; Android 6.0.1; Redmi 4A Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/46.0.2490.76 Mobile Safari/537.36';
        $url = $this->dealUrl( $url );
        $curl = curl_init();
        //设置URL
        curl_setopt( $curl, CURLOPT_URL, $url );
        //设置cookies
        $cookies && curl_setopt( $curl, CURLOPT_COOKIE, $cookies );
        //设置post数据
        //不自动输出结果
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        //设置header
        curl_setopt( $curl, CURLOPT_HEADER,  ($return == 1)?0:1 );
        // if($headers)
        //  curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        // else
        //  curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        //设置body
        curl_setopt( $curl, CURLOPT_NOBODY,  ($return == 0)?1:0 );
        //执行
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $result = curl_exec( $curl );
        // echo curl_getinfo($curl, CURLINFO_HEADER_OUT);
        //如果是header和body都显示，则进行分割
        if( $return==2 )
        {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($result, 0, $headerSize);
            $body = substr($result, $headerSize);
            $this->lastCurl = [ $header, $body ];
            return $this;
        }
        $this->lastCurl = $result;
        return $this;
    }

//    从header中提取cookie
//    $Header：表达式
//    $which：哪一个
//    **以后改写为支持多个**
    function getCookie( $cookie, $Header=false ){
        $Header || $Header = $this->lastCurl;
        $Header = is_array($Header)?$Header[0]:$Header;
        $DSESSIONID = explode($cookie . "=", $Header);
        if(count($DSESSIONID)>=2)
        {
            $DSESSIONID = explode(";", end($DSESSIONID));
            return $DSESSIONID[0];
        }else{
            return false;
        }
    }

    function getHeader( $header, $headers=false ){
        $NEW_LINE = "\r\n";
        $headers || $headers = $this->lastCurl;
        $headers = is_array($headers)?$headers[0]:$headers;
        $DSESSIONID = explode($header . ": ", $headers);
        if(count($DSESSIONID)>=2)
        {
            $DSESSIONID = explode( $NEW_LINE, end($DSESSIONID) );
            return $DSESSIONID[0];
        }else{
            return false;
        }
    }


    //简单处理url,添加http，不用正则
    function dealUrl( $url )
    {
         if( strpos( $url, "http" ) !== 0 )
         {
             return "http://" . $url;
         }
         else{
             return  $url;
         }
    }

    public function __toString()
    {
        if( is_array($this->lastCurl) ) {
            return $this->lastCurl[0];
        }
        return $this->lastCurl;
    }
}