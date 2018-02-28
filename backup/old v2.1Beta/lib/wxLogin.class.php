<?php
/**
 * Created by Sublime.
 * User: qingting4
 * Date: 18/2/26
 * Time: 上午09:53
 */
include "curl.trait.php";
include "simple_html_dom.php";

class wxLogin
{
    use curl;
    public $error;

    const STATE_REFRESH = "402";       //超时，需要重新获取数据
    const STATE_CANCEL = "403";        //用户点击取消
    const STATE_WAIT_CONFIRM = "404";  //用户扫描成功，等待用户确认
    const STATE_CONFIRM = "405";       //用户点击确认登陆
    const STATE_WAIT = "408";          //继续等待用户扫码

    //获取二维码
    function getLoginQrCode(){
        define( "BASE_URL", "https://open.weixin.qq.com" );
    	define( "APPID", "wxde40e023744664cb" );
    	define( "REDIRECT_URI", "https%3a%2f%2fmp.weixin.qq.com%2fdebug%2fcgi-bin%2fwebdebugger%2fqrcode&scope=snsapi_login&state=login" );
    	$html = new simple_html_dom();
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . APPID . "&redirect_uri=" . REDIRECT_URI;
        $html->load_file( $url );
        // if( !$page ){
        //     $this->error = "page Error";
        //     return "false";
        // }
        $qrCode = $html->find( ".qrcode", 0 );
        if( !$qrCode ){
            $this->error = "qrCode Error";
            return "false";
        }
        $qrCodeSrc = BASE_URL . $qrCode->src;
        $html->clear();
    	return $qrCodeSrc;
    }

    //此函数会阻塞 -> $data = file_get_contents( $url );
    public function getLoginState( $uuid ){
        set_time_limit(60);
        $url = "https://long.open.weixin.qq.com/connect/l/qrconnect?uuid={$uuid}&_=" . self::getMillisecond();
        $data = @file_get_contents( $url );
        $dataArray = self::string2array($data);
        if( !array_key_exists( "window.wx_errcode", $dataArray) ){
            $dataArray['window.wx_errcode'] = self::STATE_REFRESH;   
        }
        return $dataArray;
        //另起请求处理
        switch ( $dataArray['window.wx_errcode'] ) {
            case self::STATE_CANCEL:

                break;
            case self::STATE_WAIT_CONFIRM:

                break;
            case self::STATE_CONFIRM:

                break;
            case self::STATE_WAIT:

                break;
            case self::STATE_REFRESH:
            default:

                break;
        }
        // if( substr($data, start) )
    }

    public function getUserInfo( $wx_code ){
        $url = "https://mp.weixin.qq.com/debug/cgi-bin/webdebugger/qrcode?code={$wx_code}&state=darwin&os=darwin&clientversion=1.02.1802080";
        $data = $this->get( $url, false, 2 )->lastCurl;
        $header = $data[0];
        $body = json_decode( $data[1], 1 );
        $return = false;
        if( $body['baseresponse'] && $body['baseresponse']['errcode'] == 0 ){
            $userInfo['Debugger-Ticket'] = $this->getHeader("Debugger-Ticket");
            $userInfo['Debugger-NewTicket'] = $this->getHeader("Debugger-NewTicket");
            $userInfo['Debugger-Signature'] = $this->getHeader("Debugger-Signature");
            $body['userInfo'] = $userInfo;
            $return = $body;
        } else {
            // echo $header."\n";
            $this->error = "wx_code Error";
        }
        return $return;
    }
    
    //把x1=xx;x2=xx变为以x1、x2命名的数组
    private function string2array( $string ){
        $dataArray = explode( ";", $string );
        $return = [];
        foreach ( $dataArray as $itme ) {
            $_ = explode( "=", $itme );
            if( empty($_[0]) )
                break;
            $return[$_[0]] = $_[1];
        }
        return $return;
    }

    //获取13位置的时间戳
    private function getMillisecond() { 
        ini_set('date.timezone','Asia/Shanghai');
        list($t1, $t2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000); 
    } 
}