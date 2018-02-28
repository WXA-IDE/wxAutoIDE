<?php
/**
 * User: kajweb
 * Date: 18/2/27
 * Time: 19:34
 * Ver 0.9.2
 */
include "curl.trait.php";   //1802271146
include 'phpqrcode/qrlib.php';

class wxUpload
{
    use curl;
    public $newticket;
    public $error;
    public $appid;


    public function preview( $fileMemory ){
        return self::_upload( $fileMemory );
    }

    public function previewFile( $filePath ){
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
        $preview = "https://servicewechat.com/wxa-dev/testsource";
        $upload = "https://servicewechat.com/wxa-dev/commitsource";
        $baseUrl = !$offical ? $preview : $upload;
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
// echo $return."<br>"; //如果运行不正常，启动这里调试
        if( !$return ){
            $this->error = "上传出错，服务器返回空。可能是gZip配置问题";
            return false;
        }
        $returnArray = json_decode( $return, 1 );
        // echo $return;
        // print_r($returnArray);
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
        $qrSrc = false;
        $returnArray['baseresponse']['errcode'] == 0 &&
        $qrSrc = "data:image/png;base64," . (
            array_key_exists( "qrcode_img", $returnArray) ?
                $returnArray['qrcode_img'] :
                self::getQRcode( $this->appid )
        );
        return $qrSrc;
    }

    private function getQRcode( $appid ){
        $size = 8;
        $margin = 1;
        $url = "https://open.weixin.qq.com/sns/getexpappinfo?appid=" . $appid;
        $temp = "qr_temp/";
        is_dir( $temp ) OR mkdir( $temp, 0777, true );
        $tempFile = $temp.$appid."_temp";
        QRcode::png( $url, $tempFile, QR_ECLEVEL_H, $size, $margin );
        return base64_encode( file_get_contents("$tempFile") );
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