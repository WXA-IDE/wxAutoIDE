<?php
/**
 * Created by Sublime.
 * User: kajweb
 * Date: 18/2/26
 * Time: 上午15:58
 * Ver 1.0
 */
include "curl.trait.php";
// include "simple_html_dom.php";

class wxUpload
{
    use curl;
    public $newticket;
    public $error;

    public function upload( $fileMemory ){
        return self::_upload( $fileMemory );
    }

    public function uploadFile( $filePath ){
        $file = file_get_contents("wxapp.wx");
        return self::_upload( $filePath );
    }

    private function _upload( $file ){
        $baseUrl = "https://servicewechat.com/wxa-dev/testsource";
        $map["_r"] = "0." . self::getRand(17);
        $map["appid"] = "wxd264b75bd1c77051";
        $map["platform"] = "1";
        $map["ext_appid"] = "";
        $map["os"] = "darwin";
        $map["clientversion"] = "1021802080";
        $map["gzip"] = "1";
        $map["path"] = "pages%2Findex%2Findex%3F";
        $map["newticket"] = $this->newticket;
        $url = self::makeUrl( $baseUrl, $map ) . "clientversion=1.02.1802080";
        $return = $this->post( $url, $file, false, 1 );
        if( !$return ){
            $this->error = "网络错误，请联系客服";    //返回空错误，原因未明
            return false;
        }
        $returnArray = json_decode( $return, 1 );
        // if( !$returnArray ){
        //     $this->error = "网络错误，请联系客服";    //返回空错误，原因未明
        //     return false;
        // }
        if( $returnArray['baseresponse']['errcode'] != 0 ){
            // print_r($returnArray) -80002未有授权应用
            $this->error = "上传失败错误";
            return false;
        }
        $qrSrc = "data:image/png;base64," . $returnArray['qrcode_img'];
        return $qrSrc;
    }

    //no EXT dev
    public function __construct( $newticket ){
        $this->newticket = $newticket;
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

    //UL
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