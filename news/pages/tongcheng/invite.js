const App = getApp()
Page({
	data : {
		show_toInvite : true,

        list: [],
        prompt: {
            hidden: !0,
            title: '暂无粉丝',
        },
        hidden: !0,
        noMoreHidden: !0,
	},
	onLoad(){
		App.HttpService.getRetailIntro().then(data => {
			if(!data)
				return
            var content = data.data.post_content;

            App.WxParse.wxParse('content', 'html', content, that, 0);
		})
	},
	changeBar(){
      	this.setData({
          	show_toInvite : !this.data.show_toInvite
      	})
      	if(!this.data.show_toInvite)
      		this.getRetailUserList()
    },

    getRetailUserList() {
        this.setData({hidden: !1, 'prompt.hidden': !0})
        App.HttpService.getRetailUserList().then(data => {
            if (data.length>0)
                this.setData({list: data, hidden: !0})
            else
                this.setData({
                    hidden: !0, 'prompt.hidden': !1
                })
        })
    },
})