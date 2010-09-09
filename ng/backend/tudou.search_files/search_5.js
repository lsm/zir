// search stat
var so_stat = {
	
	init: function(){

		var state = 0;
		var notfound = $('.search_notfound');
		
		var pname = window.location.pathname;
		var isearch = pname.match(/isearch/);
		var psearch = pname.match(/psearch/);
		
		if(isearch && !notfound[0]) state = 1;
		if(psearch && !notfound[0]) state = 2;
		if(notfound[0]) state = 3;
		
		switch(state){
			case 1: // 视频搜索
				(function(){
					var item = $('.search_result .pack');
					var plist = $('.list_1 .pack');
					var hdlst = $('.search_hd .pack');
					so_stat.bind(item, 1);
					so_stat.bind(plist, 4);
					if(hdlst) so_stat.bind(hdlst, 7);
				})();
				break;
			
			case 2: // 豆单搜索
				(function(){
					var plist = $('.search_result .pack');
					var hdlst = $('.search_hd .pack');
					so_stat.bind(plist, 2);
					if(hdlst) so_stat.bind(hdlst, 7);

					var xlist = $('.extra a');
					$.each(xlist, function(i,c){
						xlist[i].setAttribute('item_type', 3);
						xlist[i].onclick = so_stat.request;
					});
					
					$.each(plist, function(i,c){
						$(plist[i]).find('.extra a').attr('item_index', i + 1);
					});

				})();
				break;
			
			case 3: // 推荐列表
				(function(){
					var plist = $('.search_result .pack');
					var rlist = $('.list_1 .pack');
					so_stat.bind(plist, 5);
					so_stat.bind(rlist, 6);
				})();
				break;
		}
		
	},
	
	getType: function(obj){
		return obj.getAttribute('item_type');
	},
	
	getPos: function(obj){
		return obj.getAttribute('item_index');
	},
	
	getPage: function(){
		var page = window.location.search.match("page=([^&]*)");
		if(!page || page[1] == '') return 1;
		else return parseInt(page[1]);
	},
	
	getKeyword: function(){
		var kw = $('.search_filter input[name=kw]').val() || '';
		return kw;
	},
	
	bind: function(obj, type){
		var item = obj;
		var i_pic = item.find('.pic a');
		var i_lnk = item.find('h6 a');
		
		$.each(item, function(i,c){
			i_pic[i].setAttribute('item_index', i + 1);
			i_lnk[i].setAttribute('item_index', i + 1);
			i_pic[i].setAttribute('item_type', type);
			i_lnk[i].setAttribute('item_type', type);
			i_pic[i].onclick = so_stat.request;
			i_lnk[i].onclick = so_stat.request;
		});
	},
	
	request: function(){
		//alert("type\t: " + so_stat.getType(this) + "\nf\t: " + so_stat.getPos(this) + "\npage\t: " + so_stat.getPage() + "\nkw\t: " + so_stat.getKeyword());
		TUI.getRequest("http://yiqiso.tudou.com/collect.do", {
			type: so_stat.getType(this),
			f: so_stat.getPos(this),
			page: so_stat.getPage(),
			kw: so_stat.getKeyword()
		});
		return true;
	}
	
};


// 问卷调查
function survey_init(k){
	if ( k || TUI.rand(1000) < 5 ) {
		window.open('http://clearmale.adchina.com/survey.htm?acasid=40106', '_blank');
	}
}


$(function(){
	
	// 搜索统计
	so_stat.init();
	
	// 问卷调查	
	survey_init();
		   
	var combo = $('.combo_box');
	
	combo.mouseover(function(){
		$(this).addClass('on').find('ul').show();
	}).mouseout(function(){
		$(this).removeClass('on').find('ul').hide();
	});

	// 搜索历史记录
	var location    = window.location.pathname;
	var search_url  = location.substr(location.lastIndexOf('/') + 1);
	var search_path = location.substr(0, location.lastIndexOf('/') + 1);
	var search_kw   = $('.search_filter input[name=kw]')[0].value || '';
	search_kw = $.trim(search_kw);
	
	var empty_str = '<span style="color:#BBB;">暂无搜索历史记录</span>';
	var cookie_settings = { expires: 365, domain: window.location.hostname, path: search_path };
	
	var search_history_user  = (uid == 0) ? '' : '_' + uid;
	var search_history_state = $.cookie('search_history_state' + search_history_user) || 0;
	var search_history_str   = $.cookie('search_history_str' + search_history_user) || '';
	var search_history_list  = search_history_str != '' ? search_history_str.split('|') : [];
		
	var history_panel = $('.search_history');
	var btn_bar = history_panel.find('.heading span');
	var content = history_panel.find('.container');
	getHistoryList(content[1]);
			 
	if (search_history_state == 0) {
		hideHistory();
	} else {
		showHistory();
		
		if (search_history_str == '')
			content[1].innerHTML = empty_str;
		else
			getHistoryList(content[1]);
		
		if(search_kw != '')
			setHistoryList(search_kw);
	}
	
	history_panel.fadeIn();
			 
	var btn_on  = $('#search_history_on');
	var btn_off = $('#search_history_off');
	var btn_clr = $('#clear_search_history');
			 
	btn_on.click(function(){
		search_history_state = 1;
		$.cookie('search_history_state' + search_history_user, search_history_state, cookie_settings);
		showHistory();
		getHistoryList(content[1]);
		return false;
	});
	
	btn_off.click(function(){
		search_history_state = 0;
		$.cookie('search_history_state' + search_history_user, search_history_state, cookie_settings);
		hideHistory();
		return false;
	});
	
	btn_clr.click(function(){
		if(confirm('确认清空搜索历史记录?')){
			search_history_str = '';
			search_history_list = [];
			$.cookie('search_history_str' + search_history_user, '', cookie_settings);
			content[1].innerHTML = empty_str;
		}
		return false;
	});
	
	function showHistory(){
		$(btn_bar[0]).hide();
		$(content[0]).hide();
		$(btn_bar[1]).show();
		$(content[1]).show();
	}
	
	function hideHistory(){
		$(btn_bar[0]).show();
		$(content[0]).show();
		$(btn_bar[1]).hide();
		$(content[1]).hide();
	}
	
	function getHistoryList(content){
		var tmp = [];
		$.each(search_history_list, function(i, c){
			var kw = c.substr(1, c.length).replace(/z!_~/g, '|');
			if(i > 9) return false;
			tmp.push('<a href="' + c.substr(0,1) + 'search.do?kw=' + kw + '&f=6">' + kw + '</a>');
		});
		content.innerHTML = tmp.length > 0 ? tmp.join(' | ') : empty_str;
	}
	
	function setHistoryList(kw){
		var arr = [];
		var pre = search_url.substr(0,1)
		var pre2 = (pre == 'i' ? 'p' : 'i');
		
		kw = kw.replace(/\|/g, 'z!_~');
		
		$.each(search_history_list, function(i, c){
			if(c != pre2 + kw){
				arr.push(c);
			}								
		});
		
		search_history_list = arr;
		
		var tmp = '|' + search_history_list.join('|') + '|';
			
		if(tmp.replace(/\|(i|p){1}/g, '|').indexOf('|' + kw + '|') < 0) {
			search_history_list.unshift(pre + kw);
			search_history_str = search_history_list.splice(0,10).join('|');
			$.cookie('search_history_str' + search_history_user, search_history_str, cookie_settings);
		}
	}
	
	$('#high').click(function(){
		if($('.search_filter input.text')[0].value != ''){
			$('#hd_video')[0].value = this.checked ? 1 : 0;
			$(this).parent().parent().submit();
		}			
	});
	
	$('.btn_search_filter').click(function(){
		var kw = $('.search_filter input[name=kw]')[0].value;
		if(kw == '') return false;
	});
	
	// 娱乐搜索
	$('.search_relinfo_link').mouseover(function(){
		var relCard = $("<div class=\"search_relinfo_card\">载入中...</div>").insertAfter($(this));
		
		$.get( $(this).attr("rel"), function(e) {
			relCard.html(e);
		});
	}).mouseout(function(){
		$(".search_relinfo_card").remove();
	});
	
	//google搜索开新窗口
	$(".nav_search_submit").click(function(){
		var f = this.parentNode;
		if($(f).attr("searchtype")=="web")
		    $(f).attr("target","_blank");
		else
		    $(f).attr("target","");
	});
	
});


