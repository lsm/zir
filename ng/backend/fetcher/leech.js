var leech = {
    init: function(url, rules){
        this.url = url;
        this.rules = rules;
		this.links = [];
		return this;
    },
    
    getList: function(){
        dojo.forEach(dojo.query(this.rules.l.query, this.rules.l.root), function(item, idx, arr){
            url = item.getAttribute('href');
            re = leech.rules.l.pattern;
            if (url.match(re)) {
                console.log(url);
				leech.links.concat(url);
            }
        });
    },
	
	getContent: function(){
		data = {}
		data.title = dojo.query(this.rules.at.query, this.rules.at.root)[0].innerHTML;
        data.content = dojo.query(this.rules.ac.query, this.rules.ac.root)[0].innerHTML;
		alert(data.content);
	}
}
