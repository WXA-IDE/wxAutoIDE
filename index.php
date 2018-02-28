<?
include 'lib/wxLogin.class.php';
$wxLogin = new wxLogin();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">  
<style>
html,body{
    font-size: 30px;
    text-align: center;
    height: 100%;
    margin: 0;
    padding: 0;

}
.bg{
    height: 100%;
    width: 100%;
    background: url(./resources/bg.jpg) no-repeat 50%;
    background-size: cover;
}
.login_box {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-left: -190px;
    margin-top: -270px;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    background-color: #fff;
    width: 380px;
    height: 540px;
    box-shadow: 0 2px 10px #999;
    -moz-box-shadow: #999 0 2px 10px;
    -webkit-box-shadow: #999 0 2px 10px;
}
img{
    width: 240px;
    height: 240px;
}
</style>
</head>
<body>
<div class="bg">
<div class="login_box">
    <br>
<?
if(!isset($_GET['uuid'])){
    $qrSrc = $wxLogin->getLoginQrCode();
}else{
    $qrSrc = "https://open.weixin.qq.com/connect/qrcode/{$_GET['uuid']}";
}
$qr = explode("/", $qrSrc );

?>
<img src="<?=$qrSrc?>" />
<br>请扫描二维码登陆<br><br>
<a href="main.php?uuid=<?=end($qr)?>">我已经确认登陆</a>
</div></div>
</body>
</html>