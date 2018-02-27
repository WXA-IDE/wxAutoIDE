const App = getApp()

Page({

	data: {
		list : [
			{"num": "20", "discount_price": "20,000", "origin_price": "21,000",
			"introduce": "20元手机充值卡，支持移动、联通、电信三网手机话费直充." ,"explain": "这是20元手机充值卡的说明"},
			{"num": '30', "discount_price": '29,500', "origin_price": '31,000', 
			"introduce": "30元手机充值卡，支持移动、联通、电信三网手机话费直充.", "explain": "这是30元手机充值卡的说明"},
			{"num": '50', "discount_price": '45,000', "origin_price": '47,000', 
			"introduce": '50元手机充值卡，支持移动、联通、电信三网手机话费直充.', "explain": '这是50元手机充值卡的说明'}
		]
  },

  	onLoad: function (options) {
  		// App.HttpService.getTongCheng().then(data => {
	   //      this.setData({ data : data }); 
	   //      this.setData({ award : data.award[options.id] })
  		// })
  		
      this.setData({
  			award : this.data.list[options.id],
  		})
  		
  	},
  	onShow: function(){
  		if( this.data.jindou >= this.data.award.discount_price.replace(/,/g, "") ) 
  			  this.setData({
  				  changeStatus : true
  		})
      this.setData({
          jindou : App.globalData.user_assets.jindou
      })  

  	}

  
})