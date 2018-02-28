<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi "> 
<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi "> 
<meta charset="utf-8">
<script src="https://cdn.bootcss.com/vue/2.4.2/vue.min.js"></script> 
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
    width: 260px;
    height: 260px;
}
</style>
</head>
<body>
<div class="bg">
<div class="login_box"><br>
<div id="app">
    <img :src="qrImg" />
    <div class="tips">{{tips}}</div>
</div>

<br>
<a href="main.php?uuid=<?=end($qr)?>">我已经确认登陆</a>
</div></div>
<script>
new Vue({
  el: '#app',
  data: {
    qrImg: 'resources/loading.gif',
    tips: '请扫描二维码进行登陆'
  },
  methods:{
    ajax(opt) {
        opt = opt || {};  
        opt.method = opt.method.toUpperCase() || 'POST';  
        opt.url = opt.url || '';  
        opt.async = opt.async || true;  
        opt.data = opt.data || null;  
        opt.success = opt.success || function () {};  
        var xmlHttp = null;  
        if (XMLHttpRequest) {  
            xmlHttp = new XMLHttpRequest();  
        }  
        else {  
            xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');  
        }var params = [];  
        for (var key in opt.data){  
            params.push(key + '=' + opt.data[key]);  
        }
        var postData = params.join('&');  
        if (opt.method.toUpperCase() === 'POST') {  
            xmlHttp.open(opt.method, opt.url, opt.async);  
            xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf-8');  
            xmlHttp.send(postData);  
        }  
        else if (opt.method.toUpperCase() === 'GET') {  
            xmlHttp.open(opt.method, opt.url + '?' + postData, opt.async);  
            xmlHttp.send(null);  
        }
        xmlHttp.onreadystatechange = function () {  
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {  
                opt.success(xmlHttp.responseText);  
            }  
        };  
    },
    wait(){
        let uuid = this.qrImg.split("/").pop()
        let _this = this;
        this.ajax({ 
            method: 'GET', 
            url: 'api.php', 
            data: { 
                act: 'check',
                uuid: uuid
            }, 
            success: function (response) { 
                response = eval('(' + response + ')');
                const STATE_REFRESH = "402";       //超时，需要重新获取数据
                const STATE_CANCEL = "403";        //用户点击取消
                const STATE_WAIT_CONFIRM = "404";  //用户扫描成功，等待用户确认
                const STATE_CONFIRM = "405";       //用户点击确认登陆
                const STATE_WAIT = "408";          //继续等待用户扫码
                switch(response['window.wx_errcode']){
                    case STATE_REFRESH:
                        _this.init();
                    break;
                    case STATE_CANCEL:
                        _this.tips = "用户取消了扫描"
                        setTimeout(()=>{
                            _this.wait();
                            _this.tips = "请扫描二维码进行登陆"
                        },1000);
                    break;
                    case STATE_WAIT_CONFIRM:
                        _this.tips = "扫描成功，请确认"
                        setTimeout(()=>{
                            _this.wait();
                        },1000);
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
                            success: function (response) { 
                                response = eval('(' + response + ')');
                                // console.log("upload?newTicket=" + response['Debugger-NewTicket']);
                                window.location.href = "upload.php?newTicket=" + response['Debugger-NewTicket'];
                            }
                        });  
                    break;
                    case STATE_WAIT:
                        _this.wait();
                    break;
                }
               console.log(response);
               console.log(response['window.wx_errcode']);
            }
        });  
    },
    init(){
        _this = this
        this.ajax({ 
            method: 'GET', 
            url: 'api.php', 
            data: { 
                act: 'getUUID'
            }, 
            success: function (response) { 
                response = eval('(' + response + ')');
                console.log(response)
               _this.qrImg = response.uuid
               _this.wait()
            }
        });  
        console.log("created")
    }
  },
    created(){
        this.init();
  }
})
</script>
</body>
</html>