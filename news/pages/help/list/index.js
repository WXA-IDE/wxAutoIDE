const App = getApp()

Page({
    data: {
        helps: {
            items: [],
            params: {
                p : 1,
                limit: 10,
            },
            paginate: {}
        },
        prompt: {
            hidden: !0,
            title: '暂无相关文档'
        },
    },
    onLoad() {
        this.getHelps()
    },
    onShow() {
        // this.onPullDownRefresh()
    },
    initData() {
        this.setData({
            helps: {
                items: [],
                params: {
                    p : 1,
                    limit: 10,
                },
                paginate: {}
            }
        })
    },
    navigateTo(e) {
        console.log(e)
        App.WxService.navigateTo('/pages/content/details/index', {
            id: e.currentTarget.dataset.id
        })
    },
    getHelps() {
        let helps = this.data.helps
        let params = helps.params

        App.HttpService.getHelps()
        .then(data => {params.cate_id = data
            App.HttpService.articles(params).then(data =>{

                    helps.items = [...helps.items, ...data.data.items]
                    helps.paginate = data.data.paginate
                    helps.params.p = data.data.paginate.next
                    helps.params.limit = data.data.paginate.perPage
                    this.setData({
                        helps: helps,
                        'prompt.hidden': helps.items.length,
                    })
               
            })
        })
    },
    onPullDownRefresh() {
        this.initData()
        this.getHelps()
    },
    onReachBottom() {
        this.lower()
    },
    lower() {
        if (!this.data.helps.paginate.hasNext) return
        this.getHelps()
    },
})