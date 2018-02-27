import WxRequest from '../assets/plugins/wx-request/lib/index'
import __config from '../etc/config'
class HttpService extends WxRequest {
    constructor(options) {
        super(options)
        this.$$prefix = ''
        this.$$path = {
            wechatSignUp: '/User/signin',
            wechatSignIn: '/User/signin',
            decryptData: '/User/init',
            signOut: '/User/signout',
            getUserInfo: '/User/getUserInfo',
            banner: '/Slide/lists',
            articleTerms: '/Nav/terms',
            articles: '/Content/lists',
            detail: '/Content/detail',
            articleDetail: '/Content/detail',
            config: '/Content/config',
            about: '/Tool/about',
            help: '/Tool/help',
            visit: '/Visit/visit',
            //签到
            signIn: '/SignIn/signIn',
            doSignIn: '/SignIn/do_signin',

            // 积分商城
            scoreSum: '/Score/sum',
            scoreList: '/Score/lists',
            scoreShop: '/Score/shop',
            scoreProduct: '/Score/product', // 积分产品详情
            scoreOrder: '/Score/order',

            // 邀请
            inviteLog: '/Invite/log',
            readLog: '/Content/do_read',
            shareLog: '/Content/do_share',

            retailUser: '/Retail/user',
            retailIndex: '/Retail/index',
            retailUserBind: '/Retail/bind',
            retailIntro: '/Retail/intro',
        }
        this.interceptors.use({
            request(request) {
                request.header = request.header || {}
                // if (request.url.indexOf('api') !== -1) {
                    let extConfig = wx.getExtConfigSync()
                    request.header.publicid = extConfig.publicid || __config.publicid
                    request.header.session = wx.getStorageSync('session')
                // }
                return request
            },
            requestError(requestError) {
                console.log(requestError, 'requestError')
                return Promise.reject(requestError)
            },
            response(response) {
                // wx.hideLoading()
                console.log(response, 'response')
                if (response.statusCode === 401) {
                    wx.removeStorageSync('session')
                    wx.redirectTo({
                        url: '/pages/login/index'
                    })
                }
                return response
            },
            responseError(responseError) {
                if (parseInt(responseError.statusCode) === 401) {
                    wx.removeStorageSync('session')
                    wx.navigateTo({
                        url: '/pages/login/index'
                    })
                    // return
                }
                return Promise.reject(responseError)
            },
        })
    }
    getGlobalConfig(params) {
        return this.getRequest(this.$$path.config, {
            data: params
        })
    }
    wechatSignUp(params) {
        return this.postRequest(this.$$path.wechatSignUp, {
            data: params
        })
    }
    wechatSignIn(params) {
        return this.postRequest(this.$$path.wechatSignIn, {
            data: params
        })
    }
    wechatDecryptData(params) {
        return this.postRequest(this.$$path.decryptData, {
            data: params
        })
    }

    getUserInfo(params) {
        return this.getRequest(this.$$path.getUserInfo, {data: params})
    }
    signOut() {
        return this.postRequest(this.$$path.signOut)
    }
    login(params) {
        return this.postRequest(this.$$path.login, {
            data: params
        })
    }
    visit(params) {
        return this.postRequest(this.$$path.visit, {
            data: params
        })
    }
    articles(params) {
        return this.getRequest(this.$$path.articles, {
            data: params
        })
    }
    getBanners(params) {
        return this.getRequest(this.$$path.banner, {
            data: params
        })
    }
    getTerms(params) {
        return this.getRequest(this.$$path.articleTerms, {
            data: params
        })
    }
    getDetail(params) {
        //return this.getRequest(`${this.$$path.goods}/${id}`)
        return this.getRequest(this.$$path.detail, {
            data: params
        })
    }
    about() {
        return this.getRequest(this.$$path.about)
    }
    getHelps() {
        return this.getRequest(this.$$path.help)
    }
    articles(params) {
        return this.getRequest(this.$$path.articles, {
            data: params
        })
    }
    articleDetail(params) {
        return this.getRequest(this.$$path.articleDetail, {
            data: params
        })
    }

    // 积分及积分商城

    getScoreSum(params){
        return this.getRequest(this.$$path.scoreSum, {
            data: params
        })
    }

    // 积分列表
    getScoreList(params){
        return this.getRequest(this.$$path.scoreList, {
            data: params
        })
    }
    // 积分列表
    getScoreOrderList(params){
        return this.getRequest(this.$$path.scoreOrder, {
            data: params
        })
    }
    // 积分商城
    getScoreShop(params){
        return this.getRequest(this.$$path.scoreShop, {
            data: params
        })
    }
    // 积分产品
    getScoreProduct(params){
        return this.getRequest(this.$$path.scoreProduct, {
            data: params
        })
    }
    // 签到历史
    signin(params){
        return this.getRequest(this.$$path.signin, {
            data: params
        })
    }
    // 每日签到
    doSignIn(params){
        return this.getRequest(this.$$path.doSignIn, {
            data: params
        })
    }
    // 邀请人
    inviteLog(params){
         return this.getRequest(this.$$path.inviteLog, {
            data: params
        })
    }
    // 阅读
    readLog(params){
         return this.getRequest(this.$$path.readLog, {
            data: params
        })
    }
    // 转发
    shareLog(params){
         return this.getRequest(this.$$path.shareLog, {
            data: params
        })
    }
    // 下级粉丝
    getRetailUserList(params) {
        // 获取一二级粉丝
        return this.getRequest(this.$$path.retailUser, {data: params})
    }
    // 下级粉丝
    getRetailIntro(params) {
        // 获取一二级粉丝
        return this.getRequest(this.$$path.retailIntro, {data: params})
    }
}
export default HttpService