/*
 * AD extension control for Tudou seach
 * by Dexter.Yy 2008.04.25
 */
adExtension = {
	domain: "http://adextensioncontrol.tudou.com", //"http://192.168.1.252",
	site: "http://www.tudou.com",
	quest: "/util/tools/i_p.php?i=",
	category: function(data){
		var lib = [ [], [], [] ];
		var groupid = -1;
		
		var toGroup = function( a ) {
			var tmp = [];
			for(var i = 0, l = a.length; i<l; i++ ) {
				if (a[i].group == groupid)
					tmp.push( a[i] );
			}
			return tmp;
		};
		
		this.add_AD = function( item, type ) {
			lib[type].push(item);
		};
		
		this.get_random_AD = function( type, group ) {
			if( typeof group != "boolean" ) group = true;
			var all = group ? toGroup(lib[type]) : lib[type];
			var n = ( all.length > 1 ) ? TUI.rand(all.length) : 0;
			if( type == 0 ) groupid = all[n] ? all[n].group : -1;
			return all[n];
		};
		
		var _banner = function(main, fn){
			var apic = TUI.addElm("A", {className: 'bannerad', target: "_blank", href: main.link });
			var type = main.code || main.imgType;
			apic.innerHTML = type == 1 && ('<img src="' + ( main.pic || main.imgURL ) + '" />') || type == 2 && ('<span class="swfmark"></span><embed width="100%" height="100%" type="application/x-shockwave-flash"  src="' + ( main.pic || main.imgURL ) + '" wmode="opaque" />');	
			fn(apic);
			$(apic).click(function(){
				var params = {
					adPositionId: main.adPositionId,
					throwId: main.ID || main.id,
					ownerId: main.adOwnerId
				};
				if( main.isNielson == "1" )
					adExtension.nielson(params);
				adExtension.whenClick(this, params);
			});
		};
		
		this.get_banner = function(img){
			if(!img) return false;
			$.each(img, function(){
				if(this.adPositionId == 4901)
					_banner(this, function(apic){
						$("#pushlist").append(apic);
					});
			});
			
		};
		
		this.get_player = function(main, cb){
			var uuid = (new Date()).getTime();
			(function(){
				adExtension['_getAdData' + uuid] = function(){ return data || lib; }
			})(data);

			if( main && main.ID ) {
				
				if(!main.itemID){
					_banner(main, function(apic){
						$("#tplayer")[0].appendChild(apic);
					});
					cb(true);
					return false;
				}
			
				var params = {
					wmode: 'opaque',
					bgcolor: "#000000",
					allowFullScreen: 'true',
					allowScriptAccess: 'always'
				};
				var flashvars = {
					"uid": ( typeof uid=='undefined' && '0' ) || uid || '0',
					"referrer": document.referrer.replace(/&/g, "%26"),
					"href": location.href.replace(/&/g, "%26"),
					"USER_AGENT": encodeURIComponent(navigator.userAgent),
					"channel": '0',
					"juid": ( typeof juidStr == 'undefined' && '0' ) || juidStr || '0',
					"isRec": '0',
					"itemID": main.itemID,
					'fname': "adExtension." + '_getAdData' + uuid
				};
				
				swfobject.embedSWF( adExtension.main_player, "tplayer", "283", "263", "9.0.0", false, flashvars, params, { id: "tplayer", name: "tplayer" }, function(){cb(true)} );
			} else 
				cb(false);
		};
		
		if( data && data.items.constructor == Array )
			for(var i=0;i<data.items.length;i++)
				adExtension.add_AD( data.items[i], data.items[i].indexID );
	},
	
	
	load: function( op ) {
		if( !document.getElementById("tplayer") ) return false;
		this.main_player = op.main_player; //flash player file
		var cdate = new Date();
		TUI.getCacheJS( this.domain + "/adcontrol/specialadv.html", "cgi=adv_provider&location=search&date=" + ( cdate.getMonth() + 1 ) + "-" + cdate.getDate() + "-" + cdate.getHours() + "&jsoncallback=adExtension.callback", false, "script" );
		//"/adextensioncontrol/specialadv.html"
	},

	callback: function(data){
		adExtension.category(data);
		$("#pushlist .pack_clip").remove();
		var ad = [ adExtension.get_random_AD(0, false) ], q = [];
		
		for( var i=1; i<=2; i++) {
			ad[i] = adExtension.get_random_AD(i);
			if(ad[i]) q.push( ad[i].itemID );
		}
		
		var main = ad[0];
		adExtension.get_player( main, function(e){
			adExtension.get_banner(data.imgAdvs);
			
			if(e) {
				if(main.title){
					if (main.link) {
						var linkstr = 'http://adplay.tudou.com/adcontrol/adCPTClickServlet?juid=' + ( juidStr || '0' ) + '&userId=' + uid + '&throwId=' + main.ID + '&ownerId=' + main.adOwnerId + '&adPositionId=' + main.adPositionId + '&link=' + encodeURIComponent(main.link);
						$("#tarchor").html("<span>播客：<a href='" + linkstr + "' target='new'>" + main.userName + "</a></span><a href='" + linkstr + "' target='new'>" + main.title + "</a>");
					} else 
						$("#tarchor").html("<span>播客：" + main.userName + "</span>" + main.title + "");
				}else
					$("#tarchor").hide();
				if(main.desc)
					$("#tcmt").html(main.desc);
			}
			
			$("#pushlist").show();
			if( q.length != 0 )
				$.getJSON( adExtension.site + adExtension.quest + "[" + q.join(",") + "]&jsoncallback=?", function(info){
					$("#pushlist").append(adExtension.getHTML( ad[1], info ));
					$("#pushlist").append(adExtension.getHTML( ad[2], info ));
				});
			
		});
	},
	
	getHTML: function(item, info) {
		if( !item ) return "";
		var pic = item.pic || item.frame1;
		return '<div class="pack pack_video_brief" style="margin-bottom:10px;"><div class="pic"><a class="inner" target="new" href="' + adExtension.site + '/programs/view/'+ item.code +'/tid=' + item.actionID + '&aid='+ item.ID + '&pid=' + ( item.adPositionId || 0 ) + '&oid=' + ( item.adOwnerId || 0 ) + '&isNielson=' + ( item.isNielson || 0 ) + '" title="' + item.title + '" ><img class="pack_clipImg" alt="' + item.title + '" src="' + pic + '"/></a></div><div class="txt"><h6 class="caption"><a target="_blank" title="'+ item.title +'" href="' + adExtension.site + '/programs/view/' + item.code + '/tid=' + item.actionID + '&aid='+ item.ID + '&pid=' + ( item.adPositionId || 0 ) + '&oid=' + ( item.adOwnerId || 0 ) + '&isNielson=' + ( item.isNielson || 0 ) + '">' + item.title + ' </a>' + item.duration + '</h6><ul class="info"><li>播客：'+ item.userName  + ' </li>' + ( ( info && info[item.itemID] != 0 ) ? ( '<li>播放：' + info[item.itemID] + ' </li>' ) : '' ) + '<li>发布: ' + item.pubDate + ' </li></ul></div></div>';
		
	},
	
	whenClick: function(link,o){
		if( $(link).attr("href").match(/adplay\.tudou/) != null ) return;
		var url = "http://adplay.tudou.com/adcontrol/adCPTClickServlet";
		var params = {
			juid: juidStr,
			userId: uid || 0
		}
		if(o) $.extend(params, o);
		$(link).attr("href", url + (url.match(/\?/) ? "&" : "?") + ( typeof params == "string" ? params : $.param( params ) ) + '&link=' + encodeURIComponent( $(link).attr("href") ) );
	},
	
	nielson: function(o){
		var str = [ "", "ch00", "su01", "", "t2", "", "do01" ];
		str[0] = "ca" + o.adPositionId;
		str[3] = "ad" + ( !parseInt(o.ownerId) ? "" : o.ownerId );
		str[5] = "cr" + o.throwId;
		TUI.getRequest('http://secure-cn.imrworldwide.com/cgi-bin/m', 'ci=cn-vtudou&cg=0&si=' + str.join("/") );
	}
};
