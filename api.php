<?php
include 'lib/wxLogin.class.php';

$act = $_GET['act'];
switch ( $act ) {

	case 'getUUID':
		$wxLogin = new wxLogin();
		$uuidJ = ['uuid'=>$wxLogin->getLoginQrCode()];
		echo  json_encode($uuidJ);
		break;

	case 'check':
		$uuid = $_GET['uuid'];
		$wxLogin = new wxLogin();
		$wlN = $wxLogin->getLoginState($_GET['uuid']);
		echo json_encode($wlN);
		break;

	case 'getTicket':
		$wxCode = $_GET['wx_code'];
		$wxCode = trim( $wxCode, "'" );
		$wxLogin = new wxLogin();
		$userInfo = $wxLogin->getUserInfo($wxCode);
		echo json_encode($userInfo['userInfo']);
		break;
}