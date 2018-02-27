<?php
/**
 * User: kajweb
 * Date: 18/2/27
 * Time: 13:55
 * Ver 0.9.2
 */
include "curl.trait.php";   //1802271146

class wxUpload
{
    use curl;
    public $newticket;
    public $error;
    public $appid;


    public function perView( $fileMemory ){
        return self::_upload( $fileMemory );
    }

    public function perViewFile( $filePath ){
        $file = file_get_contents("wxapp.wx");
        return self::_upload( $filePath );
    }

    public function upload( $fileMemory, $userVersion, $userDesc ){
        return self::_upload( $fileMemory, true, $userVersion, $userDesc );
    }

    public function uploadFile( $filePath, $userVersion, $userDesc  ){
        $file = file_get_contents("wxapp.wx");
        return self::_upload( $filePath, true, $userVersion, $userDesc );
    }

    private function _upload( $file, $offical=false, $userVersion=false, $userDesc=false ){
        $perView = "https://servicewechat.com/wxa-dev/testsource";
        $upload = "https://servicewechat.com/wxa-dev/commitsource";
        $baseUrl = !$offical ? $perView : $upload;
        $map["_r"] = "0." . self::getRand(17);
        $map["appid"] = $this->appid;
        $map["platform"] = "1";
        $map["ext_appid"] = "";
        $map["os"] = "darwin";
        $map["clientversion"] = "1021802080";
        $map["gzip"] = "1";
        $offical && $map["user-version"] = $userVersion;
        $offical && $map["user-desc"] = $userDesc;
        // $map["path"] = "pages%2Findex%2Findex%3F";
        $map["newticket"] = $this->newticket;
        $url = self::makeUrl( $baseUrl, $map ) . "clientversion=1.02.1802080";
        $return = $this->post( $url, $file, false, 1 );
    echo $return;
        if( !$return ){
            $this->error = "上传出错，服务器返回空。可能是gZip配置问题";
            return false;
        }
        $returnArray = json_decode( $return, 1 );
        switch ( $returnArray['baseresponse']['errcode'] ) {
            case '0':
                break;
            case '40013':
            case '-80005':
            case '40001':
            case '42001':   //用户信息失效 -> access_token expired
                $this->error = $returnArray['baseresponse']['errmsg'];
                break;
            case '1':
                $this->error = "微信服务器返回错误代码1，可能是文件有错，无法正常打包";
                break;
            default:
                $this->error = "未知错误".$return;
                break;
        }
        $qrSrc = array_key_exists( "qrcode_img", $returnArray) ?
            "data:image/png;base64," . $returnArray['qrcode_img'] :
            false;
        return $qrSrc;
    }

    //no EXT dev
    public function __construct( $newticket, $appid ){
        $this->newticket = $newticket;
        $this->appid = $appid;
    }

    private function getRand( $len ) {
        $rand = '';
        for($i=0;$i<=$len;$i++){
            $rand .= rand( 0, 9 );
        }
        return $rand;
    }

    private function makeUrl( $baseUrl, $param ){
        $url = $baseUrl . "?";
        foreach ($param as $key => $value) {
            $url .= $key . "=" . $value . "&";
        }
        return $url;
    }

    //获取13位置的时间戳
    private function getMillisecond() { 
        ini_set('date.timezone','Asia/Shanghai');
        list($t1, $t2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000); 
    }

    //UeL
    private function sendStreamFile($url, $file)  {  
    if (empty($url) || empty($file))  
    {  
        return false;  
    }  
    $opts = array(  
            'http' => array(  
                    'method' => 'POST',  
                    'header' => 'content-type:application/x-www-form-urlencoded',  
                    'content' => $file  
            )  
    );  
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);  
    return $response;  
    }  
}