const App = getApp()
Page({
    data: {
        userInfo: {},
        items: [{
                icon: 'icon-call',
                text: '联系客服',
                path: '',
            },
            {
                icon: 'icon-help',
                text: '常见问题',
                path: '/pages/help/list/index',
            }, {
                icon: 'icon-us',
                text: '关于我们',
                path: ''
            },
        ],
        settings: [

        ],

        canIUse: wx.canIUse('button.open-type.contact')
    },
    onLoad() {
        App.login().then(() => {

            this.getUserInfo()
        })
        let config = wx.getExtConfigSync()
        this.setData({ backgroundColor: config.background })
        App.HttpService.getGlobalConfig().then(data => {
            let items = this.data.items
            if (data.phone)
                items[0].path = data.phone
            this.setData({ items: items, config: data })
        })
    },
    navigateTo(e) {
        const index = e.currentTarget.dataset.index
        const path = e.currentTarget.dataset.path
        switch (index) {
            case 0:
                App.WxService.makePhoneCall({
                    phoneNumber: path
                })
                break
            case 2:
                App.WxService.navigateTo('/pages/content/details/index', {
                    id: this.data.config.about
                })

                break
            default:
                App.WxService.navigateTo(path)
        }
    },
    getUserInfo() {
        const userInfo = App.globalData.userInfo
        if (!App.Tools.isEmptyObject(userInfo)) {
            this.setData({
                userInfo: userInfo
            })
            return
        }

        App.getUserInfo()
            .then(data => {
                console.log(data)
                this.setData({
                    userInfo: data
                })
            })
    },
    bindtap(e) {
        const index = e.currentTarget.dataset.index
        const path = e.currentTarget.dataset.path
        const that = this
        switch (index) {
            default: App.WxService.navigateTo(path)
        }
    },
})