<?
include 'lib/wxPacker.class.php';
include 'lib/wxUpload.class.php';

$InputFolder = "wxapp";
$appid = "wxd264b75bd1c77051";

$wxPacker = new wxPacker( $InputFolder );
$pack = $wxPacker->getPack();

$newTicket = $_GET['newTicket'];
$wxUpload = new wxUpload( $newTicket, $appid  );
$src = $wxUpload->upload( $pack );
// $src = $wxUpload->uploadFile( "FormsCharles.wx.wx.zip" );
if($src)
{
	echo "上传成功<br>";
}else{
	echo "上传失败<br>";
	echo $wxUpload->error;
}
?>
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
<img src="<?=$src?>" /><br>
<a href="index.php">返回首页</a>
</body>
</html>