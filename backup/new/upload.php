<?php
$newTicket = $_REQUEST['newTicket'];
if ($_FILES["file"]["error"] > 0) {
    $errorTip = "上传没有成功，错误代码：" . $_FILES["file"]["error"];
    // echo "Error: " . $_FILES["file"]["error"] . "<br />";
} elseif ($_FILES["file"]["type"] == "application/zip") {
    $folder = "upload/" . date("YmdHis") . rand(1, 999);
    unZip($_FILES["file"]["tmp_name"], $folder);
    include 'lib/wxPacker.class.php';
    include 'lib/wxUpload.class.php';
    $InputFolder = $folder;
    $appid       = $_REQUEST['appid'];
    $userVersion = $_REQUEST['version'];
    $userDesc    = $_REQUEST['describe'];
    $wxPacker    = new wxPacker($InputFolder);
    // echo $folder."<br>";
    $pack = $wxPacker->Gzip()->ES625()->getPack();
    // exit("调试");
    $wxUpload = new wxUpload($newTicket, $appid);
    $act      = $_REQUEST['act'];
    // echo $act,$appid,$newTicket;
    // echo $userVersion, $userDesc;
    if ($act == "preview") {
        $src = $wxUpload->preview($pack);
    } else {
        $src = $wxUpload->upload($pack, $userVersion, $userDesc);
        // echo $src;
    }
} else {
    $errorTip = "文件类型不被支持";
}

function unzip($zip, $path)
{
    $z   = new ZipArchive;
    $res = $z->open($zip);
    if ($res === true) {
        $z->extractTo($path);
        $z->close();
    } else {
        echo 'failed, code:' . $res;
    }
}
?>



<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">
    <meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi ">
    <meta charset="utf-8">
    <script src="resources/vue.min.js"></script>
    <script src="resources/classie.js"></script>
    <link rel="stylesheet" type="text/css" href="resources/component.css" />
    <link rel="stylesheet" type="text/css" href="resources/normalize.css" />
    <link rel="stylesheet" type="text/css" href="resources/demo.css" />
    <style>
    html,
    body {
        font-size: 25px;
        text-align: center;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .bg {
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

    .tip {
        margin-top: 25px;
    }

    .tips {
        color: #888;
        font-size: 18px;
        padding: 20px;
        margin-top: 20px;
        /*display: inline-block;*/
    }

    img {
        width: 260px;
        height: 260px;
    }

    .qrimg {
        position: relative;
    }

    .logo {
        position: absolute;
        left: 80px;
        top: 50px;
        opacity: 0.32;
    }

    .web_wechat_login_logo {
        display: inline-block;
        vertical-align: middle;
        width: 140px;
        height: 150px;
        background: url(http://qingtingtech.com/Public/static/portal/images/logo.png)no-repeat;
    }
    /*a  upload */

    .a-upload {
        padding: 4px 10px;
        height: calc(80px);
        width: calc(100% - 8px - 2em);
        line-height: 80px;
        position: relative;
        cursor: pointer;
        color: #888;
        background: #fafafa;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        display: inline-block;
        *display: inline;
        *zoom: 1
    }

    .a-upload input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        cursor: pointer
    }

    .a-upload:hover {
        color: #444;
        background: #eee;
        border-color: #ccc;
        text-decoration: none
    }

    .input__label--ichiro::before {
        border: 1px solid #ddd;
    }

    .submit {
        margin-top: 20px;
        width: calc((100% - 8px - 2em) / 2);
        height: 80px;
        background: #fff;
        border-radius:4px;
    }
    .submit:hover,.input__label-content:hover,.input__label--ichiro:hover{
        background: #eee;
    }
    .input__label-content--ichiro {
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="logo">
        <i class="web_wechat_login_logo"></i>
    </div>
    <div class="bg">
        <div class="login_box">
            <br>
            <div id="app">

            <?
if (empty($src)) {
    ?>
            <img src="resources/error.jpg">
            <div class="tip">上传失败</div>
            <div class="tips"><?=isset($errorTip) && $errorTip ? $errorTip : "很抱歉，上传过程中发生了异常"?></div>
            <div class="tips"><a href="main.php?newTicket=<?=$newTicket?>">继续上传</a></div>
            <?
} else {
    ?>
            <img src="<?=$src?>">
            <div class="tip">请扫描二维码</div>
            <div class="tips">恭喜，你的操作成功</div>
            <div class="tips"><a href="main.php?newTicket=<?=$newTicket?>">继续上传</a></div>
            <?}?>

            </div>
            <br>
            <!-- <a href="main.php?uuid=<?=end($qr)?>">我已经确认登陆</a> -->
        </div>
    </div>
    <script>
    new Vue({
        el: '#app',
        data: {
            header: false,
            qrImg: 'resources/loading.gif',
            tips: '请扫描二维码进行登陆'
        },
        methods: {
            ajax(opt) {
                opt = opt || {};
                opt.method = opt.method.toUpperCase() || 'POST';
                opt.url = opt.url || '';
                opt.async = opt.async || true;
                opt.data = opt.data || null;
                opt.success = opt.success || function() {};
                var xmlHttp = null;
                if (XMLHttpRequest) {
                    xmlHttp = new XMLHttpRequest();
                } else {
                    xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
                }
                var params = [];
                for (var key in opt.data) {
                    params.push(key + '=' + opt.data[key]);
                }
                var postData = params.join('&');
                if (opt.method.toUpperCase() === 'POST') {
                    xmlHttp.open(opt.method, opt.url, opt.async);
                    xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf-8');
                    xmlHttp.send(postData);
                } else if (opt.method.toUpperCase() === 'GET') {
                    xmlHttp.open(opt.method, opt.url + '?' + postData, opt.async);
                    xmlHttp.send(null);
                }
                xmlHttp.onreadystatechange = function() {
                    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                        opt.success(xmlHttp.responseText);
                    }
                };
            },
            wait() {
                let uuid = this.qrImg.split("/").pop()
                let _this = this;
                this.ajax({
                    method: 'GET',
                    url: 'api.php',
                    data: {
                        act: 'check',
                        uuid: uuid
                    },
                    success: function(response) {
                        response = eval('(' + response + ')');
                        const STATE_REFRESH = "402"; //超时，需要重新获取数据
                        const STATE_CANCEL = "403"; //用户点击取消
                        const STATE_WAIT_CONFIRM = "404"; //用户扫描成功，等待用户确认
                        const STATE_CONFIRM = "405"; //用户点击确认登陆
                        const STATE_WAIT = "408"; //继续等待用户扫码
                        switch (response['window.wx_errcode']) {
                            case STATE_REFRESH:
                                _this.header = false;
                                _this.init();
                                break;
                            case STATE_CANCEL:
                                _this.header = false;
                                _this.tips = "请扫描二维码进行登陆"
                                setTimeout(() => {
                                    _this.wait();
                                    _this.tips = "请扫描二维码进行登陆"
                                }, 2000);
                                break;
                            case STATE_WAIT_CONFIRM:
                                _this.header = true;
                                _this.tips = "扫描成功，请确认"
                                setTimeout(() => {
                                    _this.wait();
                                }, 1500);
                                break;
                            case STATE_CONFIRM:
                                _this.tips = "正在跳转，请稍后"
                                _this.ajax({
                                    method: 'GET',
                                    url: 'api.php',
                                    data: {
                                        act: 'getTicket',
                                        wx_code: response['window.wx_code']
                                    },
                                    success: function(response) {
                                        response = eval('(' + response + ')');
                                        // console.log("upload?newTicket=" + response['Debugger-NewTicket']);
                                        window.location.href = "upload.php?newTicket=" + response['Debugger-NewTicket'];
                                    }
                                });
                                break;
                            case STATE_WAIT:
                            case STATE_CANCEL:
                                _this.tips = "请扫描二维码进行登陆"
                                _this.header = false;
                                _this.wait();
                                break;
                        }
                        console.log(response);
                        console.log(response['window.wx_errcode']);
                    }
                });
            },
            init() {
                // _this = this
                // this.ajax({
                //     method: 'GET',
                //     url: 'api.php',
                //     data: {
                //         act: 'getUUID'
                //     },
                //     success: function (response) {
                //         response = eval('(' + response + ')');
                //         console.log(response)
                //        _this.qrImg = response.uuid
                //        _this.wait()
                //     }
                // });
                // console.log("created")
            }
        },
        created() {
            // this.init();
        }
    })


    function preview(ele){
        document.getElementById("hidden").value = "preview";
        ele.parentNode.parentNode.submit();

    }
    function upload(ele){
        document.getElementById("hidden").value = "upload";
        ele.parentNode.parentNode.submit();
    }
    </script>
    <script>
    (function() {
        // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
        if (!String.prototype.trim) {
            (function() {
                // Make sure we trim BOM and NBSP
                var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                String.prototype.trim = function() {
                    return this.replace(rtrim, '');
                };
            })();
        }

        [].slice.call(document.querySelectorAll('input.input__field')).forEach(function(inputEl) {
            // in case the input is already filled..
            if (inputEl.value.trim() !== '') {
                classie.add(inputEl.parentNode, 'input--filled');
            }

            // events:
            inputEl.addEventListener('focus', onInputFocus);
            inputEl.addEventListener('blur', onInputBlur);
        });

        function onInputFocus(ev) {
            classie.add(ev.target.parentNode, 'input--filled');
        }

        function onInputBlur(ev) {
            if (ev.target.value.trim() === '') {
                classie.remove(ev.target.parentNode, 'input--filled');
            }
        }
    })();
    </script>
</body>

</html>