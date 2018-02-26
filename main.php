<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">  
<style>
html{
	text-align: center;
	padding: 50px 0;
}
img{
	width: 120px;
	height: 120px;
}
</style>
</head>
<body>
<?php
include 'lib/wxLogin.class.php';
$wxLogin = new wxLogin();
$wlN = $wxLogin->getLoginState($_GET['uuid']);
if($wlN["window.wx_errcode"]==$wxLogin::STATE_CONFIRM){
	$wxCode = $wlN["window.wx_code"];
	$wxCode = trim( $wxCode, "'" );
	$userInfo = $wxLogin->getUserInfo($wxCode);
	$show = "你好 ".$userInfo['nickname']."<br>";
	$show .= "你的openid是".$userInfo['openid']."<br>";
	$show .=	"<a href=\"upload.php?newTicket={$userInfo['userInfo']['Debugger-NewTicket']}\">点击上传wxapp目录</a>";
	if( !$userInfo ){
		$show = "重复授权，请返回再次重试<br>";
		$show .= "<a href=\"index.php\">返回</a>";
	}
}elseif($wlN["window.wx_errcode"]==$wxLogin::STATE_WAIT_CONFIRM){
	$show = "你扫描了二维码，但是未点击确认<br>";
	$show .= "<a href=\"index.php?uuid={$_GET['uuid']}\">返回</a>";
}
elseif($wlN["window.wx_errcode"]==$wxLogin::STATE_CANCEL){
	$show = "你取消了登陆<br>";
	$show .= "<a href=\"index.php?uuid={$_GET['uuid']}\">返回</a>";
}
else{
	$show = "你尚未扫码登陆<br>";
	$show .= "<a href=\"index.php?uuid={$_GET['uuid']}\">返回</a>";
}
echo $show;
?>
</body>
</html>