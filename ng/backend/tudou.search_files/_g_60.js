(function(){


//autodomain in dev/test
var uiurl=$('link[@rel=stylesheet]')[0].href.match(/^http\:\/\/\S*\.tudou\.com/i);
if( uiurl != "http://ui.tudou.com" )
	$.getScript( uiurl+ '/autodomain.js' );


/*
 * Tudou lib
 */
var userAgent = navigator.userAgent.toLowerCase();
TUI = {	
	
	Browser : {
    	MobileSafari: !!userAgent.match(/apple.*mobile.*safari/),
    	ie: /msie/.test(userAgent) && !/opera/.test(userAgent)
  	},
	
	elm: function(i){
		return document.getElementById(i);
	},
  	
  	getFlashMC : function(m){
         return this.Browser.ie ? window[m] : document[m];
	},
	
	getCacheJS: function( url, data, callback, type ){
		if ( jQuery.isFunction( data ) ) {
			callback = data;
			data = null;
		}
		jQuery.ajax({
			type:"GET",
			url: url,
			data: data || '',
			success: callback || function(){},
			cache: true,
			dataType: type || "jsonp"
		});
	},
	
	ajaxCache: {},
	
	getCache: function(url, fn){
		var cache = TUI.ajaxCache, key = encodeURI(url);
		if(cache[key])
			fn(cache[key]);
		else {
			$.get(url, function(data){
				cache[key] = data;
				fn(data);
			});
		}
	},
	
	Class: {
		create: function(){
  			return function(args){
    			if ( this instanceof arguments.callee ) {
      				if ( typeof this.init == "function" )
        				this.init.apply( this, args.callee ? args : arguments );
    			} else
      				return new arguments.callee( arguments );
  			};
		}
	},
	
	animate : function(obj,o,p,fun){
		var duration=p.duration||p;
		var s=duration/30;
		var step={};
		var n=[];
		for(var i in o){
			if(!obj.style[i])obj.style[i]=0;
			step[i]=( o[i] - parseFloat( obj.style[i] ) )/s;
			n.push(i);
		}
		var m=n[0];
		if(p.buffer)var d0=Math.abs( ( o[m] - parseFloat( obj.style[m] ) )/3 );
		var l=n.length;
		var timer=setInterval(function(){
			var u=parseFloat(obj.style[m]);
			var d=o[m]-u;
			if(d<0)d=-d;
			if(step[m]>0&&d<step[m] || step[m]<0&&d<-step[m]){
				clearInterval(timer);
				for(var k=0;k<l;k++){
					obj.style[n[k]]=o[n[k]]+"px";
				}
				if(fun)fun();
				timer=null;
				return false;
			}
			for(var j=0;j<l;j++){
				m=n[j];
				u=parseFloat(obj.style[m]);
				if(d<=d0) step[m]=step[m]*p.buffer;
				obj.style[m]=u+step[m]+"px";
			}
		},30);
		return timer;
	},
	
	uEvent : function(e){
        var e=e||window.event;
		if(window.ActiveXObject){
			e.pageX=e.clientX+TUI.pos.scrollLeft();
			e.pageY=e.clientY+TUI.pos.scrollTop();
			e.target=e.srcElement;
			e.time=(new Date).getTime();
			e.charCode=(e.type=="keypress") ? e.keyCode : 0;
            e.preventDefault = function() {
                e.returnValue = false;
    		};
    		e.stopPropagation = function() {
                e.cancelBubble = true;
    		};
		}
		return e;
	},
	
	pos : {
		scrollTop : function(){
			return self.pageYOffset 
					|| document.documentElement && document.documentElement.scrollTop
					|| document.body && document.body.scrollTop;
		},
		scrollLeft : function(){
			return self.pageXOffset
					|| document.documentElement && document.documentElement.scrollLeft
					|| document.body && document.body.scrollLeft;
		},
		windowHeight : function(){
			return self.innerHeight
					|| document.documentElement && document.documentElement.clientHeight
					|| document.body && document.body.clientHeight;
		},
		windowWidth : function(){
			return self.innerWidth
					|| document.documentElement && document.documentElement.clientWidth
					|| document.body && document.body.clientWidth;
		},
		elementLeft : function(o){
			for( var x = 0; o; o = o.offsetParent )        
				x += o.offsetLeft;   
	    	return x;
		},
		elementTop : function(o){
			for( var y = 0, i = o.parentNode; o; o = o.offsetParent ) 
				y += o.offsetTop;
		    for( ; i && i != document.body; i = i.parentNode) 
				if (i.scrollTop) y -= i.scrollTop;
		    return y;
		}
	},
	
	parseUrlHash: function(s){
		if(typeof s != "string") s = $(s).attr("href");
		s = s.replace(/^\S*#/, "");
		var a = s.replace(/[\/\?]*([^\?\/]+\=[^\?\/]+)*$/, "").split("/");
		var p = s.match(/[\/\?]*([^\?\/]+\=[^\?\/]+)$/);
		if(p==null) return a;
		p = p[1].split("&");
		for(var i = 0, params = {}, l = p.length;i<l;i++){
			k = p[i].match(/(&|\?|)([^=]+)(=)?([^=]*)/);
			params[k[2]] = k[4] || null;
		}
		a.push(params);
		return a[0] ? a : [params];
	},
	
	rand: function( max ) {
		return parseInt( Math.random() * max );
	},
	
	getRequest: function(url,params){
		$(function(){
			var i = document.getElementById("TUI_requestTmp");
			if(!i) {
				i = document.createElement("iframe");
				i.id = "TUI_requestTmp";
				i.style.display = "none";
				document.body.appendChild(i);
			}
			i.src = url + (url.match(/\?/) ? "&" : "?") + ( typeof params == "string" ? params : $.param( params ) );
		});
	},
	
	addElm: function( tag, attr, cb ){
		var attr = attr || {}, elm = document.createElement(tag);
		$.each(attr, function(n){
			if(n == "className")
				elm.className = this;
			else
				$(elm).attr(n,this);
		});
		if(cb) cb.call(elm);
		return elm;
	},
	
	copyToClip : function(url,succ){
		if (window.clipboardData) {
			window.clipboardData.setData('text', url);
			if(succ) alert(succ);
		} else {
			alert('抱歉，在你的游览器里不能自动复制到剪贴板～');
		}
	},
	
	addBookmark : function(n,u){
		if (window.sidebar)
			window.sidebar.addPanel(n,u,"");
		else if(document.all)
			window.external.AddFavorite(u, n);
		else 
			TUI.panel("你的浏览器只能手动添加哦～")
		return false;
	}
};

/*
 * Tudou API
 */
TUI.api = {
	subscribe: function(type,id,cb,r){
		if(type.constructor == Object) {
			var arg = type;
			r = arguments[2];
			cb = arguments[1];
			type = arg.type;
			id = arg.id;
			var op = $.param(arg);
		} else
			var op = "type=" + type + "&id=" + id;
			
		if( typeof r != "boolean" ) r = true; 
		var url = r ? "/my/sub/subscribe.php" : "/my/sub/unsubscribe.php";
			
		$.getJSON( url, op, function(e) {
			if(cb){
				cb(e);return false;
			} 
			if( e.status == 0 )
				location = "http://www.tudou.com/my/sub/";
			else
				alert(e.message);
		});
	},
	dig: function(itemid, cb, send){
		var method = send ? "dig" : "diginfo";
		$.getJSON(dig_domain + '/api.srv?method=' + method + '&itemId=' + itemid + '&userId=' + uid + "&jsoncallback=?", function(e){
			if(!send || e.status==1) 
				cb(e);
			else
				TUI.panel("不好意思，你已经挖过鸟");
		});
	},
	bury: function(op, cb, send){
		var method = send ? "bury" : "buryinfo";
		var itemid =(typeof op == "string") ? op : op.itemId;
		$.getJSON(dig_domain + '/api.srv?method=' + method + '&itemId=' + itemid + '&userId=' + uid + "&reason=" + op.reason, function(){
			if(!send || e.status==1) 
				cb(e);
			else
				TUI.panel("不好意思，你已经埋过鸟");
		});
	}
};

/*
 * Tudou UI Component
 */
TUI.panel = function( content, title, button, fn ) {
	var btns = [], hasMask = false, tmp, btnbox, args = arguments.length, dialog = $('#tudou_panel')[0];
	if( args <= 4 && typeof arguments[2] != "object" ){
		if( !title || typeof title == "function" ){
			tmp = {
				value: "确定",
				handler: title || function(){}
			};
			title = "土豆提示！";
		} else 
			tmp = {
				value: typeof button == "string" ? button : "确定",
				handler: ( button && typeof button == "function" ) && button || ( fn && typeof button == "string" ) && fn || function(){}
			};
		btns.push( tmp );
	}
	
	if ( dialog ) closeDialog();
	dialog = document.createElement('div');
	dialog.id = 'tudou_panel';
	dialog.innerHTML = '<div class="caption"><h3>' + title + '</h3>' +
		'<a href="#" id="dlg_close">关闭</a></div>' +
		'<div class="container"><p id="dlg_msg" class="' + 'r' + ( TUI.rand(3) + 1 ) + '">' + content + 
		'</p></div>' +
		'<div class="footbtns" id="dlg_btn"></div><span></span>';
	document.body.appendChild(dialog);
	if(TUI.maskAllPage())
		hasMask = true;
	else
		TUI.maskAllPage(0.4);
	dialog.style.top = ( TUI.pos.windowHeight() - dialog.offsetHeight )/2 + TUI.pos.scrollTop() + "px";
	
	btnbox = $('#dlg_btn')[0];
	
	for( var i=2; i<args; i++ ){
		if( typeof arguments[i] == "object" )
			btns.push( arguments[i] );
	}	
	for( var i=0, l=btns.length; i<l; i++ )
		btnbox.appendChild( addBtn( btns[i].value, btns[i].handler, ( i == 0 ) ? "on" : "" ) );
	
	dialog.className = l == 1 && 'alert' || l == 2 && 'confirm' || l == 3 && 'custom';
	$('#dlg_btn')[0].lastChild.focus();
	$("#dlg_close")[0].onclick = function(){
		closeDialog();
		if(l>1){
			var cb = btns[l-1].handler;
		 	if(cb) cb.apply( this, arguments );
		}
		return false;
	};
		
		
	function addBtn(v,cb,c){
		var btn = document.createElement('a');
		btn.setAttribute("href","#");
		btn.innerHTML = v;
		if(c) btn.className = c;
		btn.onclick = function(){
			closeDialog();
			if(cb) cb.apply( this, arguments );
			return false;
		};
		return btn;
	}
	
	function closeDialog(){
		if( !hasMask )
			TUI.maskAllPage(-1);
		document.body.removeChild(dialog);
		dialog = null;
	}
};
	
TUI.noAboveFloater = function(action,p) {
	p = p || document.body, status = action ? "hidden" : "";
	$(p).find("select").each(hideElm);
	$(p).find("object").each(function(){
		this.style.position = action ? "absolute" : "";
		this.style.left = action ? "-2000px" : "";
	});
	function hideElm(){
		this.style.visibility = status;
	}
};

TUI.maskAllPage = function( o, bg ) {
	if( arguments.length == 0 )
		return document.getElementById('tudou_masklayer');
		
	if( o < 0 ){
		document.body.removeChild( document.getElementById('tudou_masklayer') );
		TUI.noAboveFloater(false);
		return false;
	}
	if( !document.getElementById('tudou_masklayer') ){
		var m = document.createElement('DIV');
		m.id = 'tudou_masklayer';
		m.style.width = document.body.offsetWidth+"px";
		m.style.height = document.body.offsetHeight+"px";
		m.style.position = "absolute";m.style.top = "0";m.style.left = "0";m.style.zIndex="10000";
		document.body.appendChild(m);
	}else
		var m = document.getElementById('tudou_masklayer');
	TUI.noAboveFloater(true);
	m.style.opacity = o;
	m.style.filter = 'alpha(opacity='+o*100+')';
	m.style.background = bg || "#000000";
	return m;
};

TUI.switchTabNoAjax = function(tag, list){
	var ct = null;
	$.each(tag, function(){
		if(this.className.indexOf("current")!=-1)
			ct = this;
	});
	if (!ct) {
		ct = tag[0];
		$(ct).addClass("current");
	}
	var c = TUI.parseUrlHash( ct.nodeName == "A" ? ct : ct.getElementsByTagName("A")[0] )[0];
	if(list)
		list[c-1].style.display = "block";
	else  
		TUI.elm(c).style.display = "block";
	return {
		go: function(t, cb){
			var n = TUI.parseUrlHash(t)[0];
			var t1 = list ? list[--n] : TUI.elm(n);
			var t2 = list ? list[--c] : TUI.elm(c);
			if( c == n || !t1 ) return false;
			if(cb) cb.call(t, ct, t2);
			t2.style.display = "none";
			c = list ? (n+1) : n;
			t1.style.display = "block";
			$(ct).removeClass("current");
			if(ct.nodeName != "A") t = t.parentNode;
			ct = t;
			$(t).addClass("current");
			return false;
		}
	}
};


/*
 * Tudou's native extend
 */
var _st = window.setTimeout;
window.setTimeout = function(fRef, mDelay) {
	if( arguments[2] && typeof fRef == 'function' )
		return _st( function(){ 
			fRef.apply( null, Array.prototype.slice.call(arguments,2) ); 
		}, mDelay);
	return _st(fRef,mDelay);
};

String.prototype.trim = function() { 
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
};

Array.prototype.unique = function(){
	var a={},b=[];
	for(var i=0;i<this.length;i++)
		a[this[i]]=this[i];
	for(var c in a)
		b[b.length]=a[c];
	return b;
};


/*
 * Tudou's jQuery plugin
 */
$.cookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toGMTString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toGMTString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

$.fn.eventProxy = function(event,op){
	op = $.extend({ hasClass: {}, hasId: {}, hasAttr: {} }, op);
	this.bind(event, function(e){
		var t = e.target;
		var handler = op.hasId[t.id] || op.hasClass[t.className] || null;
		if(handler)
			return handler.call(t) || false;
		else {
			var result = true;
			$.each(op.hasAttr, function(n){
				if ($(t).attr(n)) {
					result = op.hasAttr[n].call(t) || false;
					return false;
				}
			});
			return result;
		}
	});
	return this;
};

$.fn.cleanSpaces = function(){
	this.each(function(){
		$(this).html(String(""+$(this).html()).replace(/[\t ]/g,''));
	});
	return this;
};

$.fn.hideDetail = function(n){
    this.each(function(){
        var o = $(this), s = o.html().trim(), m = (new Date).getTime();
        if(s.length > n)
            o.html('<div id="summary'+m+'">' + o.text().trim().substr(0,n) + ((o.text().trim()+'').length>n ? ' <a href="#" onclick="$(\'#detail'+m+'\').show();$(this).parent().hide();return false;">显示全部</a>' : '' ) + '</div>' + 
			'<div id="detail'+m+'" style="display:none">' + s + ' <a href="#" onclick="$(\'#summary'+m+'\').show();$(this).parent().hide();return false;">隐藏</a></div>');
    });
    return this;
}

$.fn.hoverClass = function(c) {
	this.each(function(){
		$(this).hover( 
			function() { $(this).addClass(c);  },
			function() { $(this).removeClass(c); }
		);
	});
	return this;
}

$.fn.link2Radio = function(action){
	this.click(function(){
		var r=this.parentNode.getElementsByTagName('A');
		for(var i=0;i<r.length;i++){
			if(r[i].className.indexOf("nav_type_on")!=-1)r[i].className="nav_type";
		}
		this.className="nav_type nav_type_on";
		if(typeof action=="function") action(this);
		this.blur();
        return false;
	});
	return this;
};

$.fn.diyselect = function(action){
	this.each(function(o){
		var me = $(this),
			cur = me.find(".diyselect_current")[0],
			t = me.find("input")[0],
			ul = me.find("ul")[0];
		$(t).click(function(){
			if( ul.style.display != "block" ){
				ul.style.display="block";
				$(document.body).bind("click", whenClick);
			} else {
				whenClose();
			}
			
		});
		function whenClick(e){
			var a = e.target, b = a.parentNode;
			if(!b.parentNode.parentNode){
				whenClose();
				return false;
			}
			if( b.parentNode.parentNode.className == "diyselect" ){
				t.value=a.innerHTML;
				if(typeof action=="function") action.call(me[0],a);
				whenClose();
				cur.className = "";
				b.className = "diyselect_current";
				cur = b;
			}else if( b.className != "diyselect" ){
				whenClose();
			}
			return false;
		}
		function whenClose(){
			ul.style.display="none";
			$(document.body).unbind("click", whenClick);
		}
	});
	return this;
};

$.fn.tudouDig = function(){
	this.each(function(o){
		var d = $(this);
		if ( !d.attr('id') ){
			d.html('<strong>' + (d.html()||0) + '</strong><span>挖过了</span>').addClass('digBtn').addClass("diged").fadeIn(500);
			if(typeof iid!='undefined') $(".shareButton").fadeIn();
		}else
			d.html('<a href="/need_login.php"><strong>' + (d.html()||0) + '</strong><span>挖它</span></a>').addClass('digBtn');
		d.find('a').click(function(){
			if( !uid )
				return confirm('只有注册用户才能使用这个小功能，请先注册或者登录。');
			else{
				var p = $(this).parent();
				var _id = p.attr('id').substr(3)||0;
				if(_id<=0)
					_id = iid;
				else {
					if(typeof digupPortal!='undefined')
						$.get(digupPortal,{itemID:_id,iid:_id,action:'w'});
					else
						$.get('/util/zajax/wm_ops.php',{itemID:_id,iid:_id,action:'w'});
				}
				p.html($(this).html()).find('span').html('挖过了').parent().addClass("diged").fadeIn(500);
				var p2 = p.find('strong');
				p2.html( parseInt( p2.html() || 0 ) + 1 );
				if(typeof iid!='undefined') {
					$(".shareButton").fadeIn();
					TUI.copyToClip( $("#clipURL").val() ); 
					if (document.all)
						$(".shareIM").fadeIn();
				}
				return false;
			}
		});
	});
	return this;
};


})();


/*
 * Tudou global config
 */
var juidStr = juid();
if( !$.cookie('juid') )
	$.cookie('juid',juidStr,{expires:365*100,domain:'tudou.com',path:'/'});
else
    juidStr = $.cookie('juid');
	
var uid = isLogined() ? ( $.cookie("u_id") || 0 ) : 0,
	_usr = $.cookie("u_user"),
	_nic = $.cookie("u_nick") || _usr,
	initTimestamp = new Date().getTime(),
	main_domain = main_domain || "http://www.tudou.com",
	login_domain = login_domain || "http://login.tudou.com",
	dig_domain = dig_domain || "http://dig.tudou.com";



/*
 * Tudou init
 */
$(function(){
	
	initLogined();
	
	if(uid)
    	$.getScript( "http://www.tudou.com/util/uusmc.php?user_id=" + uid );
		
	TUI.addElm("A", {
		id: "specialogo",	//黑豆上线。放黑豆图标，链接到Gary信
		href: "http://hd.tudou.com/",
		target: "_blank",
		title: "黑豆来啦~"
	}, function(){
		var b = $(".nav_login")[0];
		if(b) b.parentNode.insertBefore(this, b);
	});
		
	$('.nav_search .diyselect').diyselect(function(a){
		var fm = this.parentNode.getElementsByTagName('FORM')[0], gkey = this.parentNode.getElementsByTagName('LABEL')[0], i = fm.getElementsByTagName('INPUT')[0];
		if( /google\.cn/.test(a.href) ){
			if( $(fm).attr('searchtype') != 'web' ){
				i.name="q";
				$(i).addClass("google_bg");
				fm.appendChild(gkey);
			}
			$(fm).attr('searchtype', 'web');
		}else{
			if( $(fm).attr('searchtype') == 'web' ){
				i.name="kw";
				$(i).removeClass("google_bg");
				this.parentNode.appendChild(gkey);
			}
			$(fm).attr('searchtype', '');
		}
		fm.setAttribute('action',a.href);
	});
	
	
	$(".nav_dropdown").each(function(){
		var ul = $(this).find(".nav_sub")[0], a = $(this).find(".nav_dropdown_root");
		this.onmouseover = function(){
			ul.style.display = "block";
			a.addClass("hover");
		};
		this.onmouseout = function(e){
			ul.style.display = "none";
			a.removeClass("hover");
		};
	});

    try{
		initHover();
		setTimeout(function(){
			initQuick()
		},200);
	
	}catch(e){}
	
	tudouPvStat();
	
});




/*
 * Tudou's global behaviors
 */
function juid(){return((new Date().getTime()*10000+Math.random(1)*10000).toString(32)+(new Date().getTime()*10000+Math.random(1)*10000).toString(32)).substr(0,11)}

function isLogined(){
	return ( ( $.cookie("u_passport_info") || $.cookie("u_login") ) && $.cookie("u_user") && $.cookie("u_id")  );
}

function initLogined() {
	window.isGuest = isLogined() ? false : true;
	if( !isGuest && !document.getElementById("msg_num") )
		showLoginStatus();
}

function showLoginStatus(){
	$(".nav_login").html('<span class="quicklink_login"><span class="sayhi">你好，</span><a href="' + main_domain + '/home/' + _usr + '" target="_blank">' + _nic + '</a> | <a href="' + login_domain + '/loginOut.do?r=' + Math.random() + '">退出</a></span><span id="nav_quick"> | <a href="' + main_domain + '/my/messages/" target="_blank" id="msg_num">短信箱</a> | <a href="' + main_domain + '/playlist/played/history.do?method=getqui">便利豆单(<span id="quickNum">0</span>)</a> | <a href="' + main_domain + '/playlist/played/history.do?method=gethis">看过的</a> | <a href="' + main_domain + '/service/" target="_blank">土豆客服</a><a class="quicklink_upload" href="' + main_domain + '/my/program/publish.php">上传视频</a></span>');
}

function tudouPvStat(){
	if(typeof juidStr == 'undefined') return false;
	var pvPageStep = $.cookie('pageStep');
    var pageStep = pvPageStep ? Math.max(1, pvPageStep) : 1;
    $.cookie('pageStep', String(pageStep + 1), {path: "/", domain: 'tudou.com'});
    var statStr = [1, juidStr, encodeURIComponent(location.href), encodeURIComponent(document.referrer), pageStep, new Date().getTime(), (typeof uid != 'undefined' ? uid: 0), (typeof initTimestamp != 'undefined' ? (new Date().getTime() - initTimestamp) : 0)];
    window.pageUUID = '';
	$.getScript('http://static.tudou.com/newstat/pv?s=' + statStr.join('~_~'), function(){
		$.cookie('pageUUID', pageUUID + '~_~' + (new Date().getTime() - initDate), {path: "/", domain: 'tudou.com'});
        pageUUID = '';
	});
    var initDate = new Date().getTime();
	var pvPageUUID = $.cookie('pageUUID');
    if (pvPageUUID) {
		$.getScript('http://static.tudou.com/newstat/pv?s=2~_~' + pvPageUUID);
        $.cookie('pageUUID', '', {expires: -1, path: "/", domain: "tudou.com"});
    }
}

function showSCount(c){
    $('.nav_search_input').each(function(){
    	var cache=c+"个视频";
    	var me=this;
    	if(!this.value||this.value==cache){
    		$(this).val(cache);
    		$(this).addClass("nav_scount");
    	}
    
    	this.parentNode.onsubmit=function(){
    		if(me.value==cache)return false;
    	};
    
    	$(this).blur(function(){
    		if( $(this).val().match(/\S/)==null ){
    			$(this).val(cache);
    			$(this).addClass("nav_scount");
    		}
    	});
    	$(this).focus(function(){
    		if( $(this).val()==cache ){
    			$(this).val('');
    			$(this).removeClass("nav_scount");
    		}
    	});
    });
}



/*
 * Tudou's old functions
 */
function redirectSignIn(){
    var _i='';
    if(typeof uid!='undefined')
        _i='userid='+(uid|0)+'&';
	if(typeof loginURL=='undefined')
		setTimeout(function(){location.href = login_domain + '/login.do' },100);
	else
		setTimeout(function(){location.href = loginURL },100);
	return false;
}

function copyText(id) {
	try{
		var targetText = document.getElementById(id);
		targetText.focus();
		targetText.select();
		var clipeText = targetText.createTextRange();
		clipeText.execCommand("Copy");
	}catch(e){}
}

function ReSizeImg(ImgW){
	var Allimg=document.getElementsByTagName("img");
	for (var i in Allimg) {
		DrawImage(Allimg[i],ImgW);
	}
}
function DrawImage(ImgD,ImgW){ 
	var image=new Image(); 
	image.src=ImgD.src;
	if(image.width>0 && image.height>0){ 
		flag=true;
		if(image.width>=ImgW){ 
			ImgD.width=ImgW; 
			ImgD.height=(image.height*ImgW)/image.width; 
		}else{ 
			ImgD.width=image.width; 
			ImgD.height=image.height; 
		}
	}
}


function markAsPlayed(id){
	var n='playedClips';
	id=id|0;
	if(id<=0)return false;
	var c=($.cookie(n)==null)?'':$.cookie(n);
	var l='';
	if(typeof lid!='undefined' && typeof iid!='undefined'){
		id=iid|0;
		if(id<=0 || lid<=0)return false;
		l='/'+lid.toString(36);
		var r=new RegExp("\\d+/"+lid.toString(36),'g');
		var _m=c.match(r);
		if(_m!=null && _m.length>=5){
			c=c.split('').reverse().join('');
			r=new RegExp("(,?\\d+/"+lid.toString(36)+'|\\d+/"'+lid.toString(36)+',?)');
			c=c.replace(r,'');
			c=c.split('').reverse().join('');
		}
	}
	var p=c.split(',') || c;
	if(p.length>0){
		p.reverse().push((id).toString(36)+l);
		p.reverse();
		if(p[0]==null)p.shift(0);
		p=p.unique();
	}else if(!p.length)
		p=(id).toString(36)+l
	else
		p=(id).toString(36)+l+','+p;
	if(p.length>=40)
		p.pop();
	$.cookie(n,p.toString().replace(/,$/,''),{expires:7,domain:'.tudou.com',path:'/'});
}

function markAsPlayedList(id){try{
    markAsPlayed(id,1);
}catch(e){}}

function toggleQuick(code,obj){
	var quick = $.cookie("quickListClips");
	var na=$("#quickNum");
	var n = na.html()|0;

	if (quick==null) {
		$.cookie('quickListClips',code+',',{domain:'tudou.com',path:'/'});
		na.html(n+1)
	}else{
		var index = quick.indexOf(code);
		if (index>=0) {
			//remove
			if (obj!=null) {
				$.cookie('quickListClips',quick.replace(code+',',""),{domain:'tudou.com',path:'/'});
				na.html(n-1);
				if( $(".saveListAs").attr("rel")==1 ) 
					obj.parent().parent().parent().remove();
			}else{
				setTimeout(playQuick,100)
				return false;
			}
		}else{
			if (n>=40) {
				if (confirm("你只能在便利豆单中暂存40个节目，你可以删除一些，然后再继续添加。\n想去便利豆单删除一些节目吗？")) {
					setTimeout(function(){location.href="/playlist/played/history.do?method=getqui"},100);
				}
				return false;
			}else{
				//add
				$.cookie('quickListClips',quick+code+',',{domain:'tudou.com',path:'/'});
				na.html(n+1)
			}
		}
	}
	return true;
	function playQuick(){
		location="http://www.tudou.com/playlist/quick.do?code="+code;
	}
}

function initQuick(target){
try{
	var dom = target || ".pack_clipImg";
	var quick = $.cookie("quickListClips") || "";
	var n = quick ? (quick.length/12) : 0;
	$("#quickNum").html(n.toString());
	$(dom).each(function(){
		var box = this.parentNode.parentNode, 
			snap = $(box).find("a"), 
			q = TUI.addElm( "div", {className:"quick"} ),
			code = $(snap).attr("href").replace(/.*\/view\/([^\/]+)\/.*/g,"$1");
		if(snap.length>1) return true;
		box.insertBefore( q, snap[0]);
		q.innerHTML = "<a href='#' title='添加到便利豆单'></a>";
		var qa = $(q).find('a');
		$(box).hover( function(){
			$(this).addClass("quickAhover");
		}, function(){
			$(this).removeClass("quickAhover");
		});
		if (quick.indexOf(code) >=0)
			qa.attr("title","播放便利豆单").parent().toggleClass("quickAdded");
			
		qa.click(function(){
			if (toggleQuick(code,null))
				$(this).attr("title","播放便利豆单").parent().toggleClass("quickAdded");
			return false;
		});
	});
}catch(e){}
}

function initHover() {
	$("input[@type='text'].input, input[@type='password'], textarea").addClass("input").focus(function(){
		$(this).addClass("textFocus")
	}).blur(function(){
		$(this).removeClass("textFocus")
	});
	$(".nav_tabs li").hoverClass("nav_tabsHover");  //fixd IE6
}


/* $Id: _g.js 2495 2008-09-25 09:13:41Z tracy $ */