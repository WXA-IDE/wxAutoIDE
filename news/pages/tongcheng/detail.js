const App = getApp()

Page({
    data: {
        show_gain : true,
        gain_exist : false,
        pain_type1 : [
            { 
                "img" : "http://img0.imgtn.bdimg.com/it/u=846113950,2578320506&fm=27&gp=0.jpg",
                "content" : "我是一个商品一个商品字数最多两行",
                "jifen" : "1000",
                "addMoney" : "0.01",
                "origin_price" : "10.00"
            },
             { 
                "img" : "http://img2.imgtn.bdimg.com/it/u=910995950,689499192&fm=27&gp=0.jpg",
                "content" : "我是一个商品一个商品字数最多两行",
                "jifen" : "1500",
                "addMoney" : "0.01",
                "origin_price" : "15.00"
            },
            { 
                "img" : "http://img0.imgtn.bdimg.com/it/u=846113950,2578320506&fm=27&gp=0.jpg",
                "content" : "我是一个商品一个商品字数最多两行",
                "jifen" : "1000"
            },
             { 
                "img" : "http://img2.imgtn.bdimg.com/it/u=910995950,689499192&fm=27&gp=0.jpg",
                "content" : "我是一个商品一个商品字数最多两行",
                "jifen" : "1500"
            }
        ],
        score: {
            items: [],
            params: {
                p : 1,
                limit: 10,
            },
            paginate: {}
        },
        order: {
            items: [],
            params: {
                p : 1,
                limit: 10,
            },
            paginate: {}
        },
        hidden: !0,
        noMoreHidden: !0,
        prompt: {
            hidden: !0,
            title: '暂无金豆记录',
        },
    },
    onLoad: function () {
        App.login().then(data => {
            this.getScoreSum()
            this.getScoreList()
        })
    },

    getScoreSum() {
        App.HttpService.getScoreSum().then(data => {
            this.setData({ config: data })
        })
    },

    getScoreList() {
        const score = this.data.score
        const params = score.params
        this.setData({hidden: !1, noMoreHidden: !0, 'prompt.hidden': !0})
        App.HttpService.getScoreList(params).then(data => {
             this.setData({ hidden: !0})
                if (data.data.items && data.data.items.length > 0) {
                    score.items = [...score.items, ...data.data.items]
                    score.paginate = data.data.paginate
                    score.params.p = data.data.paginate.next
                    score.params.limit = data.data.paginate.perPage
                    this.setData({
                        score: score,
                        'prompt.hidden': score.items.length,
                    })
                }
                else{
                    if(score.items.length == 0)
                        this.setData({'prompt.hidden': !1})
                    else
                        this.setData({noMoreHidden: !1})
                }

        })
    },

    getExchangeOrderList() {
        const order = this.data.order
        const params = order.params
        this.setData({hidden: !1, noMoreHidden: !0, 'prompt.hidden': !0})
        App.HttpService.getScoreOrderList(params).then(data => {
             this.setData({ hidden: !0})
                if (data.data.items && data.data.items.length > 0) {
                    order.items = [...order.items, ...data.data.items]
                    order.paginate = data.data.paginate
                    order.params.p = data.data.paginate.next
                    order.params.limit = data.data.paginate.perPage
                    this.setData({
                        order: order,
                        'prompt.hidden': order.items.length,
                    })
                }
                else{
                    if(order.items.length == 0)
                        this.setData({'prompt.hidden': !1})
                    else
                        this.setData({noMoreHidden: !1})
                }

        })
    },

    changeBar(){
      this.setData({
          show_gain : !this.data.show_gain
      })
      this.getExchangeOrderList()
    },
    onReachBottom(){
        this.lower()
    },

    onPullDownRefresh(){
        if(this.data.show_gain)
            {
                this.initScore()
                this.getScoreList()
            }
        else
            {
                this.initOrder()
                this.getExchangeOrderList()
            }
    }

})