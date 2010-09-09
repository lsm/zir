var hint_domain = hint_domain || "http://hint.tudou.com/hint";

(function(){

TUI.searchTips = TUI.Class.create();

TUI.searchTips.prototype = {
	
	init: function( opt ) {
		this.timer = null;
		this.lastn = null;
		this.loaded = false;
		this.input = opt.input;
		this.url = opt.url;
		this.key = opt.key;
		this.param = opt.param;
		this.bodyHeight = document.body.offsetHeight;
		
		var box = this.tipbox = document.createElement('DIV');
		document.body.appendChild(box);
		box.innerHTML='<ul></ul>';
		box.className="searchTipsList";
		box.style.display="none";
		var w=this.input.offsetWidth;
		if(w>238){
			box.style.width=w-2+'px';
			box.style.left=TUI.pos.elementLeft(this.input)+'px';
		}else{
			box.style.width='238px';
			box.style.left=TUI.pos.elementLeft(this.input)-238+w-2+'px';
		}
		box.style.top=TUI.pos.elementTop(this.input)+23+'px';
		var o=this;
		$(this.input).keyup(function(e){
			o.control(e);
		});
		$(this.input).blur(function(){
			o.timer=setTimeout(function(){o.hide();},200);
		});
	},
	
	get: function(){
		if( !this.param.type || this.input.value.match(/\S+/ig)==null )
			return this.hide();
		var query = "jsoncallback=?&" + this.key + '=' + encodeURIComponent(this.input.value);
		for( var name in this.param )
			query += '&' + name + '=' + this.param[name];
		var me=this;
		TUI.getCacheJS( this.url, query, function(data){
			if( data.length == 0 ) return me.hide();
			me.show(data);
		});
	},
	
	show: function(data){
		var content=[];
        if(this.param.type=="programs")
            var text="个视频";
        else if(this.param.type=="playlist")
            var text="个豆单";
		for(var i=0;i<data.length;i++){
			var c=data[i].split(/#(?=\d+$)/ig);
			content.push('<li><span>'+c[0]+'</span><em>'+c[1]+text+'</em></li>');
		}
		this.tipbox.getElementsByTagName('UL')[0].innerHTML=content.join('');
		this.toggleList();

		
		if( this.bodyHeight != document.body.offsetHeight ) {
			this.tipbox.style.top=TUI.pos.elementTop(this.input)+22+'px';
			this.bodyHeight == document.body.offsetHeight
		}
		
		this.tipbox.style.display="block";	
	},
	
	control: function(e){
		e.preventDefault();
		if( this.loaded && ( e.keyCode==38 || e.keyCode==40 ) ) {
			var list=this.tipbox.getElementsByTagName('UL')[0].getElementsByTagName('LI');
			if(this.lastn!=null){
				var n=(e.keyCode==38) ? ((this.lastn-1<0)?list.length-1:this.lastn-1) : ((this.lastn+1<list.length)?this.lastn+1:this.lastn-list.length+1);
				list[this.lastn].className="";
			}else
				var n=0;
			list[n].className="chosen";
			this.lastn=n;
			this.input.value=list[n].getElementsByTagName('SPAN')[0].innerHTML;
			this.input.select();
		}else{
			if(this.timer!=null) clearTimeout(this.timer);
			var o = this;
			this.loaded = false;
			this.lastn = null;
			this.timer = setTimeout(function(){ o.get(); },100);
		}
		
	},
	
	toggleList: function(){
		var list=this.tipbox.getElementsByTagName('UL')[0].getElementsByTagName('LI');
		var o=this;
		for(var i=0;i<list.length;i++){
			list[i].onmouseover=function(){
				if(o.lastn!=null)
					list[o.lastn].className="";
				this.className="chosen";
				for(var j=0;j<list.length;j++){
					if(list[j]==this)
						o.lastn=j;
				}
			};
			list[i].onclick=function(){
				o.input.value=this.getElementsByTagName('SPAN')[0].innerHTML;
				clearTimeout(o.timer);
				o.input.select();
				o.hide();
			};
		}
		this.loaded=true;
	},
	
	hide: function(){
		this.tipbox.getElementsByTagName('UL')[0].innerHTML='';
		this.tipbox.style.display="none";
		this.loaded=false;
		this.lastn=null;
		clearTimeout(this.timer);
		return false;
	}
};


$(function(){
		
	$('.nav_searchform').submit(function(a){
		if( !this.getElementsByTagName('INPUT')[0].value ) return false;
	});
	
	try{
		var searchTips = [];
		
		$(".nav_search_input").each(function(){
			var searchOpt = { type: 'programs' };
			var args = {
				input : this,
				url : hint_domain,
				key : 'q',
				param : searchOpt
			};
			
			this.onclick = function(){
				var t = $(this.parentNode).attr("action").match( /\w(?=search\.do$)/g ) || [0];
					searchOpt.type = ( (t[0]=='i') && 'programs' ) || ( (t[0]=='p') && 'playlist' ) || ( (t[0]=='u') && 'user' ) || null;
			};
			
			searchTips.push( TUI.searchTips( args ) );
		});
	
	}catch(e){}

});


TUI.getCacheJS("http://www.tudou.com/util/tools/total_programs.txt", false, false, "script");


})();


