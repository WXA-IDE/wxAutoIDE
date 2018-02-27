// pages/visit/index.js
const App = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
      form:{},
      loading: !1
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    App.login()

    this.WxValidate = App.WxValidate({
      name: {
        title: '姓名',
        required: true,
      },
      phone: {
        title: '电话',
        required: true,
        tel: true,
      }
    }, {
        name: {
          required: '请输入联系人姓名',
        },
        phone: {
          required: '请输入联系人电话',
        },
      })


    let extConfig = wx.getExtConfigSync()
    let background = extConfig.background
    this.setData({ background: background })
  },

    chooseLocation() {
        console.log('choose');
        App.WxService.chooseLocation()
            .then(data => {
                console.log(data)
                this.setData({
                    'form.address': data.address,
                })
            })
    },

    bindRemarkChange(e) {
        console.log(e)
        this.setData({'form.remark': e.detail.value})
    },

  submit: function(e){
    var that = this
    var params = e.detail.value
    if (!this.WxValidate.checkForm(e)) {
      const error = this.WxValidate.errorList[0]
      console.log(error)
      App.WxService.showModal({
        title: '友情提示',
        content: `${error.msg}`,
        showCancel: !1,
      })
      return false
    }

    params.remark = this.data.form.remark
    // params.address = this.data.form.address
      this.setData({loading: !0})
    App.HttpService.visit(params).then(data => {
        this.setData({loading: !1})
      if(data.status)
      {
        wx.showModal({
              title: '预约成功',
              content: '我们将尽快与您联系',
              showCancel: false
          })
          this.setData({form: {}})
      }
      else
        wx.showModal({
            title: '预约失败',
            content: '请稍后重试',
            showCancel: false
        })
    })

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  }
})