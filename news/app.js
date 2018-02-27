import WxValidate from 'assets/plugins/wx-validate/WxValidate'
import WxService from 'assets/plugins/wx-service/WxService'
import HttpResource from 'helpers/HttpResource'
import HttpService from 'helpers/HttpService'
import Tools from 'helpers/Tools'
import Config from 'etc/config'
import WxParse from 'helpers/wxParse/wxParse.js'


App({
    onLaunch: function(options) {
        // 记录用户进入场景，绑定邀请人记录
        if(options.openid)
        this.WxService.login()
            .then(data => {
                return this.HttpService.wechatSignIn({
                    code: data.code
                })
            }).then(data => {
                // 记录openID
                let params = {}
                params.openid = data.data.openid
                params.invite_openid = options.openid
                this.HttpService.inviteLog(params)
            })
            

        var that = this

        wx.getSystemInfo({
            success: function(res) {
                that.screenWidth = res.windowWidth;
                that.screenHeight = res.windowHeight;
                that.pixelRatio = res.pixelRatio;
            }
        });
    },


    login: function() {
        return this.wechatSignIn()
    },

    showModal() {
        this.WxService.showModal({
            title: '友情提示',
            content: '获取用户登录状态失败，请重新登录',
            showCancel: !1
        })
    },
    wechatDecryptData() {

        return this.WxService.getUserInfo()
            .then(data => {
                return this.HttpService.wechatDecryptData({
                    encryptedData: data.encryptedData,
                    iv: data.iv,
                    session: this.WxService.getStorageSync('session')
                })
            }, data => {
                return this.retryLogin().then(data => {
                    return new Promise((resolve, reject) => {
                        resolve(data)
                    })
                })
            })
    },
    wechatSignIn() {
        let that = this
        if (this.WxService.getStorageSync('session')) {
            // return this.WxService.checkSession().then(function() {
            //     return new Promise((resolve, reject) => {
            //       resolve(wx.getStorageSync('userInfo'))
            //     })
            // }, function() {
            //     console.log('login fail')
            //     that.WxService.removeStorageSync('session')
            //     return that.wechatSignIn()
            // })
            return new Promise((resolve, reject) => {
              resolve(wx.getStorageSync('userInfo'))
            })
        }
        wx.showToast({
            title: '登录中',
            icon: 'loading'
        })
        return this.WxService.login()
            .then(data => {
                return this.HttpService.wechatSignIn({
                    code: data.code
                })
            })
            .then(data => {
                console.log(data)
                if (data.meta.code == 0) {
                    this.WxService.setStorageSync('openid', data.data.openid)
                    this.WxService.setStorageSync('session', data.data.session)
                    if (data.data.init === true)
                        return this.wechatDecryptData().then(data => {
                            this.globalData.userInfo = data.data.userInfo
                            wx.setStorageSync('userInfo', data.data.userInfo)
                            return new Promise((resolve, reject) => {
                                resolve(data)
                            })
                        })
                    else {
                        this.globalData.userInfo = data.data.userInfo

                        wx.setStorageSync('userInfo', data.data.userInfo)
                    }

                    return new Promise((resolve, reject) => {
                        resolve(data)
                    })

                } else if (data.meta.code == 40029) {
                    // 登陆失败，重新授权
                    this.showModal()
                }
            })
    },
    retryLogin: function() {
        let that = this
        return this.WxService.openSetting().then(res => {
            if (res.authSetting['scope.userInfo'] == true)
                return that.wechatDecryptData()
            // else
            //     this.retryLogin()
        })
    },
    retryGetUserInfo: function() {
        let that = this
        return this.WxService.openSetting().then(res => {
            if (res.authSetting['scope.userInfo'] == true)
                return that.getUserInfo()
            else
                return this.retryGetUserInfo()
        })
    },
    recordLocation() {

        App.WxService.getLocation().then(data => {
            App.HttpService.recordLocation(data).then(data => {
                console.log(data)
            })
        })
    },
    getUserInfo() {
        let userInfo = wx.getStorageSync('userInfo');
        // console.log(userInfo)
        if (userInfo)
            return new Promise((resolve, reject) => {
                resolve(userInfo)
            })
        return this.HttpService.getUserInfo().then(data => {
            wx.setStorageSync('userInfo', data)
            return new Promise((resolve, reject) => {
                resolve(data)
            })
        })
    },
    getLocation(that) {
        let city = wx.getStorageSync('location')
        console.log(city)
        if (!city) {
            return this.WxService.getLocation().then(data => {
                console.log(data)
                that.setData(data)
                // 引入SDK核心类
                let obj = {
                    location: {
                        latitude: data.latitude,
                        longitude: data.longitude,
                    }
                }
                return new Promise((resolve, reject) => {
                    obj.success = (res) => resolve(res)
                    obj.fail = (res) => reject(res)
                    qqmap.reverseGeocoder(obj)
                })
            }).then(res => {
                console.log(res)
                let ad_info = res.result.ad_info
                let city = ad_info.province + ',' + ad_info.city + ',' + ad_info.district // 
                // city = res.result.ad_info.city+'，'+res.result.ad_info.district
                that.setData({
                    location: ad_info.city,
                })
                wx.setStorageSync('location', ad_info.city)

                return new Promise((resolve, reject) => {
                    resolve(res)
                })

            }, data => {

                that.setData({
                    location: '全国'
                })
                return new Promise((resolve, reject) => {
                    reject('定位失败')
                })
            })
        } else {

            console.log(city)
            that.setData({ location: city })
            return new Promise((resolve, reject) => {
                resolve(city)
            })
        }
    },
    getGlobalConfig() {
        if (!this.globalData.config)
            return this.HttpService.getGlobalConfig()
        return new Promise((resolve, reject) => {
            resolve(this.globalData.config)
        })
    },



    WxValidate: (rules, messages) => new WxValidate(rules, messages),
    HttpResource: (url, paramDefaults, actions, options) => new HttpResource(url, paramDefaults, actions, options).init(),
    HttpService: new HttpService({
        baseURL: Config.basePath,
    }),
    WxService: new WxService,
    Tools: new Tools,
    Config: Config,
    WxParse: WxParse,
    showToast(message) {
        wx.showToast({
            title: message,
            icon: 'success',
            duration: 1000,
        })
    },
    globalData: {
        focus: false,
        shop_id: 1,
        hasLogin: false,
        cartList: [],
        userInfo: false,
        shops: []
    },
})