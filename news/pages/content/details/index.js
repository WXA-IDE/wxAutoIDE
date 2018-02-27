// pages/online-mm/article-details/index.js
const App = getApp()
let read_lock = false
let share_lock = false
Page({
    data: {
        id: 0,
        cid: 0,
        article: {},
        prev: {},
        next: {},
        articles: { items: [] },
        dialog: {},
        showDialog: 0
    },
    onLoad: function(options) {
        console.log(options)
        this.setData({
            id: options.id,
            cid: options.cid || 0
        })

        this.getArticle(this.data.id)
        // 页面显示

        // 页面初始化 options为页面跳转所带来的参数
    },

    scroll(e) {
        console.log('scroll')
        var that = this;
        let contentHeight = this.data.contentHeight || 0
        console.log(contentHeight)
        console.log(e.detail.scrollTop)
        if (contentHeight && e.detail.scrollTop > contentHeight) {
            if (read_lock)
                return
            read_lock = true
            App.HttpService.readLog({ id: this.data.article.id }).then(data => {
                if (data.status) {
                    that.setData({
                            showDialog: 1,
                            'dialog.info': data.info
                        })
                    setTimeout(function() {
                        that.setData({
                            showDialog: 0
                        })
                    }, 2000)
                }
            })
        }


    },
    getArticle(id) {
        console.log(id, 'id')
        if (!id) return;
        let that = this
        wx.showNavigationBarLoading()
        App.HttpService.articleDetail({ id: id, cid: that.data.cid }).then(data => {
            wx.hideNavigationBarLoading()
            that.setData({
                article: data.data,
                prev: data.prev,
                next: data.next,
                'articles.items': data.items
            })
            var content = data.data.post_content;

            App.WxParse.wxParse('content', 'html', content, that, 0);
            // let that = this
            setTimeout(function() {
                var query = wx.createSelectorQuery()
                query.select('#content').boundingClientRect()
                query.selectViewport().scrollOffset()
                query.exec(function(res) {
                    console.log(res)
                    that.setData({ contentHeight: res[0].height / 2 })
                })
            }, 500)

        })
    },
    readmore() {
        wx.switchTab({
            url: '/pages/content/list/index'
        })
    },
    showMine() {
        wx.switchTab({
            url: '/pages/tongcheng/index'
        })
    },


    callTel: function(e) {
        let tel = e.currentTarget.dataset.tel
        wx.makePhoneCall({
            phoneNumber: tel, //仅为示例，并非真实的电话号码
        })
    },

    bindpreview: function(e) {
        let src = e.currentTarget.dataset.src
        wx.previewImage({
            current: src,
            urls: [src] // 需要预览的图片http链接列表
        })
    },

    closeDialog(){
        this.setData({
                showDialog: 0
            })
    },
    onShareAppMessage: function() {
        let that = this
        wx.showShareMenu({withShareTicket: true})
        return {
            title: this.data.article.post_title,
            desc: this.data.article.post_excerpt,
            imageUrl: this.data.article.smeta.thumb,
            path: '/pages/content/details/index?id=' + this.data.id + '&openid=' + wx.getStorageSync('openid'),
            success: function(res) {

                if (share_lock)
                    return
                share_lock = true
                App.HttpService.shareLog({ id: that.data.article.id }).then(data => {
                    if (data.status) {
                        that.setData({
                            showDialog: 1,
                            'dialog.info': data.info
                        })
                        setTimeout(function() {
                            that.setData({
                                showDialog: 0
                            })
                        }, 2000)
                    }
                })
            },
        }

    },
    onPullDownRefresh() {
        let id = this.data.article.id
        this.getArticle(id)
        wx.stopPullDownRefresh()
    },
    // onReachBottom(){
    // if(this.data.next&&!this.data.next.object_id)return
    //   this.bindAutonextSet()
    // },
    nextArticle() {
        App.WxService.redirectTo('/pages/content/details/index', {
            id: this.data.next.object_id,
            cid: this.data.cid
        })
    },

    bindShowArticle(e) {
        App.WxService.navigateTo('/pages/content/details/index', {
            id: e.currentTarget.dataset.id,
            cid: this.data.cid
        })
    }

})