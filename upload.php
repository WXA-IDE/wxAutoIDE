<?
include 'lib/wxPacker.class.php';
include 'lib/wxUpload.class.php';

$InputFolder = "news";
$appid = "wx6fdfc32bedf1ecc8";//PLSC
// $appid = "wxbb981ccb50e3d42d";//京
$userVersion = "1.0.1";
$userDesc = "666";

$wxPacker = new wxPacker( $InputFolder );
$pack = $wxPacker->Gzip()->ES625()->getPack();

$newTicket = $_GET['newTicket'];
$wxUpload = new wxUpload( $newTicket, $appid  );
// $src = $wxUpload->preView( $pack );
$src = $wxUpload->upload( $pack, $userVersion, $userDesc );exit("可能上传成功，请去后台查看。如果不行，请先用preView测试");




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
<img src="<?=$src?>" /><br>
<a href="index.php">返回首页</a>
<h1>如果失败，请先   npm install --save</h1>
</body>
</html>