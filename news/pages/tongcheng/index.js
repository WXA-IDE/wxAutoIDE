var util = require('../../utils/util')
const App = getApp()

Page({
    data: {
        award_data: [],
        award_total: { 'inviteTotalNum': 200, 'shareTotalNum': 100, 'readTotalNum': 100, 'singInNum': 50 },
        award_require: { 'reqShare_num': 5, 'reqRead_num': 5 },
        user_assets: { 'jindou': 0, 'value': 0, 'share_num': 0, 'read_num': 0, 'signin': 0 },
        card: [
            { 'price': '20', 'type': '手机充值卡', 'number': '20,000', 'disable': false },
            { 'price': '30', 'type': '手机充值卡', 'number': '29,500', 'disable': false },
            { 'price': '50', 'type': '手机充值卡', 'number': '48,000', 'disable': true }
        ],
        signin: {},
        loading: false,
    },
    onLoad: function() {
        var that = this;
        this.setData({
            scr_height: App.screenHeight
        })

        App.login().then(data => {
            console.log(data)
            this.getUserInfo()
            this.getScoreSum()
            this.getScoreShop()

        })

    },

    getScoreSum() {
        App.HttpService.getScoreSum().then(data => {
            this.setData({ config: data })
        })
    },
    getScoreShop() {
        App.HttpService.getScoreShop().then(data => {
            this.setData({ shop: data })
        })
    },
    getUserInfo() {
        App.getUserInfo().then(data => {
          console.log(data)
            this.setData({ userInfo: data })
            console.log(this.data.userInfo)
        })
    },
    onShow: function() {
        if(this.data.userInfo)
        this.getScoreSum()
    },
    onShareAppMessage: function(res) {
        var that = this;
        return {
            title: this.data.userInfo.nickName + "在用在线茂名",
            path: '/pages/tongcheng/index',
            success: function(res) {
                if (that.data.user_assets.share_num < that.data.award_require.reqShare_num) {
                    that.data.user_assets.share_num++;
                    that.data.user_assets.jindou = that.data.user_assets.jindou + that.data.award_total.shareTotalNum / that.data.award_require.reqShare_num;
                    that.setData({
                        user_assets: that.data.user_assets,
                        showInvite: 0,
                        showShareGet: 1
                    })
                    App.globalData.user_assets = that.data.user_assets;
                    /*将字符串转json数据时，外面是单引号，key和value是双引号*/
                    var award_list = '{ "type" : "分享奖励", "date" : ' +
                        '"' + util.formatDate(new Date().getTime()) + '"' + ', "num" : ' +
                        '"' + that.data.award_total.shareTotalNum / that.data.award_require.reqShare_num + '" }';
                    award_list = JSON.parse(award_list);

                    that.setAwardStorage(award_list);

                    // setTimeout(function() {
                    //     that.setData({
                    //         showShareGet: 0
                    //     })
                    // }, 1500)
                } else {
                    that.setData({
                        showInvite: 0,
                    })
                }
            },
            fail: function(res) {

            }
        }
    },
    SignIn_fn() {
        wx.navigateTo({
            url: 'detail'
        })
    },
    toWallet() {
        this.setData({
            showWallet: 1
        })
    },
    toInvite() {
        wx.navigateTo({
            url: 'invite'
        })
    },
    toIndex() {
        wx.switchTab({
            url: '/pages/content/list/index'
        })
    },
    toDetailPage() {
        wx.navigateTo({
            url: 'detail'
        })
    },
    toAwardPage(e) {
        wx.navigateTo({
            url: 'award?id=' + e.target.dataset.idx
        })
    },
    test() {
        this.setData({
            showInvite: 1
        })
    },
    cancelScreen() {
        this.setData({
            showInvite: 0
        })
    },
    SignIn_fn() {
        var that = this;
        this.setData({loading: !0})
        App.HttpService.doSignIn().then(data => {
        this.setData({loading: !1})
            if (data.status) {
                that.setData({
                    showSigninGet: 1,
                    haveSingin: 1,
                    signin: data
                })
            }else{
                wx.showModal({
                    title: data.info || '您已签到，请明天再来'
                })
            }
        })
    },
    closeDialog(){
        this.setData({
                showSigninGet: 0
            })
    },
    article_lower() {
        var that = this;
        if (that.data.user_assets.read_num < that.data.award_require.reqRead_num) {
            that.data.user_assets.read_num++;
            that.data.user_assets.jindou = that.data.user_assets.jindou + that.data.award_total.readTotalNum / that.data.award_require.reqRead_num;
            that.setData({
                user_assets: that.data.user_assets,
                showReadGet: 1
            })
            App.globalData.user_assets = that.data.user_assets;
            /*将字符串转json数据时，外面是单引号，key和value是双引号*/
            var award_list = '{ "type" : "阅读奖励", "date" : ' +
                '"' + util.formatDate(new Date().getTime()) + '"' + ', "num" : ' +
                '"' + that.data.award_total.readTotalNum / that.data.award_require.reqRead_num + '" }';
            award_list = JSON.parse(award_list);

            that.setAwardStorage(award_list);

            setTimeout(function() {
                that.setData({
                    showReadGet: 0
                })
            }, 1500)
        }
    },

    setAwardStorage(award_list) {
        var that = this;
        if (wx.getStorageSync('award') != '') {
            wx.getStorage({
                key: "award",
                success: function(res) {
                    that.data.award_data = res.data;
                    that.data.award_data.push(award_list);
                    wx.setStorage({
                        key: "award",
                        data: that.data.award_data
                    })
                }
            })
        } else {
            that.data.award_data.push(award_list);
            wx.setStorage({
                key: "award",
                data: that.data.award_data
            })
        }
    }

})