<?
include 'lib/wxLogin.class.php';
$wxLogin = new wxLogin();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">  
<style>
html{
	font-size: 30px;
	text-align: center;
	padding: 50px 0;
}
img{
	width: 240px;
	height: 240px;
}
</style>
</head>
<body>
请扫描二维码登陆<br><br>
<?
if(!isset($_GET['uuid'])){
	$qrSrc = $wxLogin->getLoginQrCode();
}else{
	$qrSrc = "https://open.weixin.qq.com/connect/qrcode/{$_GET['uuid']}";
}
$qr = explode("/", $qrSrc );

?>
<img src="<?=$qrSrc?>" /><br><br>
<a href="main.php?uuid=<?=end($qr)?>">我已经确认登陆</a>
</body>
</html>