<?
include 'lib/wxPacker.class.php';
include 'lib/wxUpload.class.php';

$InputFolder = "dist";
$appid = "wxbb981ccb50e3d42d";
$userVersion = "1.0.1";
$userDesc = "666";

$wxPacker = new wxPacker( $InputFolder );
$pack = $wxPacker->getPack();

$newTicket = $_GET['newTicket'];
$wxUpload = new wxUpload( $newTicket, $appid  );
$src = $wxUpload->upload( $pack, $userVersion, $userDesc );
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
	<!-- 预览才返回二维码，上传不返回二维码 -->
<!-- <img src="<?=$src?>" /><br> -->
<a href="index.php">返回首页</a>
</body>
</html>