// var Zan = require('../../dist/index');
const App = getApp()
let lock = false
Page({
    data: {
        currentType: -1,
        alias: 'recommend',
        slides: [],
        bucket: [],
        articles: { items: [], paginate: {}, params: { p: 1, limit: 10 } },
        category: [{ id: -1, name: '推荐' }],

        articles: { items: [], paginate: {}, params: { p: 1, limit: 10 } },
        prompt: {
            hidden: !0
        },
        hidden: !0,
        noMoreHidden: !0,
        scrollTop: 0,
        scrollLeft: 0,
        color: '#000',
        viewId: '',

        nav_list: {
            nav_line: !1,
            nav_all: !1
        },

        tab: {
            index: '0',
            height: 45
        },
        scrollTop: 0
    },

    onLoad(options) {
        if (options.alias)
            this.setData({ alias: options.alias })
        var sysInfo = wx.getSystemInfoSync();
        var winHeight = sysInfo.windowHeight;

        let extConfig = wx.getExtConfigSync()
        let color = extConfig.color
        this.setData({ winHeight: winHeight, color: color })
        this.getNav()
        this.watchPage()
    },

    httpGet() {
        this.getSlides()
        this.articles()
    },

    handleZanTabChange(e) {
        var index = e.currentTarget.dataset.index;
        var id = e.currentTarget.dataset.id;
        var alias = e.currentTarget.dataset.alias

        this.setData({
            'tab.index': index,
            currentType: id,
            alias: alias,
            viewId: 'content-view'
        });

        this.watchPage()
    },

    setSwiperHight() {
        var that = this
        //创建节点选择器
        var query = wx.createSelectorQuery();
        //选择id
        query.select('#test0').boundingClientRect()
        query.exec(function(res) {
            // console.log(res[0].top);
            console.log(2333);
            console.log(res);
            // console.log(res[1].scrollTop+"res[1].scrollTop");
            // that.setData({navbarHeight:res[0].top})
        })
    },

    watchPage() {

        let index = this.data.tab.index
        let bucket = this.data.bucket
        if (bucket[index]) {
            return
        } else
            bucket[index] = {
                slides: [],
                articles: { items: [], paginate: {}, params: { p: 1, limit: 10 } },
                prompt: {
                    hidden: !0
                },
                hidden: !0,
                noMoreHidden: !0
            }
        this.setData({ bucket: bucket })
        this.getSlides()
        this.getArticles()
    },

    changePage(e) {
        console.log(e.detail.current)
        var index = e.detail.current
        let category = this.data.category
        let find = category.find( (n, i) => {
            return index == i
        })
        this.setData({
            'tab.index': index,
            currentType: find.id,
            alias: find.taxonomy
        });
        this.watchPage()
    },


    bindShowArticle(e) {
        App.WxService.navigateTo('/pages/content/details/index', {
            id: e.currentTarget.dataset.id,
            cid: this.data.currentType
        })
    },


    getSlides() {
        const alias = this.data.alias
        let index = this.data.tab.index
        let bucket = this.data.bucket
        App.HttpService.getBanners({ alias: alias }).then(data => {
            bucket[index].slides = data
            this.setData({ bucket: bucket })
        })
    },

    getArticles() {

        let index = this.data.tab.index
        let bucket = this.data.bucket
        bucket[index].prompt.hidden = !0
        bucket[index].hidden = !1
        bucket[index].noMoreHidden = !0
        this.setData({ bucket: bucket })
        const articles = bucket[index].articles
        const params = articles.params
        lock = true
        // if (this.data.alias)
        //     params.alias = this.data.alias
        // else
        params.cate_id = this.data.currentType
        App.HttpService.articles(params).then(data => {
            lock = false
            console.log(data)
            if (data.meta.code == 0) {
                articles.items = articles.items.concat(data.data.items)
                articles.paginate = data.data.paginate
                articles.params.limit = data.data.paginate.perPage
                articles.params.p = data.data.paginate.next
                bucket[index].articles = articles

                bucket[index].hidden = !0
                this.setData({ bucket: bucket })
                this.setSwiperHight()
            }
            if (!articles.items.length) {
                bucket[index].hidden = !0
                bucket[index].prompt.hidden = !1
                this.setData({ bucket: bucket })
            }

        }, fail => {
            lock = false
        })
    },


    getNav() {
        App.HttpService.getTerms().then(data => {
            let origin = this.data.category
            //缩字
            let category = this.dealCategory(data.data.items);
            category = origin.concat(category)
            this.setData({
                category: category,
            })

            console.log(this.data.types)
            console.log(this.data.currentType)

        })
    },

    dealCategory: function(e) {
        let newArray = Array();
        for (let i in e) {
            // console.log(e[i])
            e[i].name = e[i].name.substr(0, 4);
            newArray.push(e[i]);
        }
        return newArray;
    },
    init() {
        let index = this.data.tab.index
        let bucket = this.data.bucket

        bucket[index] = {
            slides: [],
            articles: { items: [], paginate: {}, params: { p: 1, limit: 10 } },
            prompt: {
                hidden: !0
            },
            hidden: !0,
            noMoreHidden: !0
        }
        this.setData({
            bucket: bucket
        })
    },

    onPullDownRefresh() {
        this.init()
        this.getSlides()
        this.getArticles()
        wx.stopPullDownRefresh()
    },
    onReachBottom: function() {
        console.log('waht')
        this.lower()
    },
    refresh() {
        wx.startPullDownRefresh()
        this.setData({scrollTop: 0})
    },


    lower: function() {
        // console.log(this.data.bucket[index].prompt.hidden)
        let index = this.data.tab.index
        let bucket = this.data.bucket
        if (this.data.bucket[index].prompt.hidden && !this.data.bucket[index].articles.paginate.hasNext) {
            bucket[index].noMoreHidden = !1
            this.setData({ bucket: bucket })
            return;
        } else if (!bucket[index].prompt.hidden) {
            console.log('here')
            return
        }
        console.log('next')
        if (lock == false)
            this.getArticles();
    },
    onShow: function() {},

    onShareAppMessage: function() {
        return {
            title: this.data.types[0].name,
            desc: this.data.types[0].description,
            path: '/pages/content/list/index?openid='+wx.getStorageSync('openid')
        }
    }


})