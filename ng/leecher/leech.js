/**
 * @author zai
 */
function $(id) {
    return dojo.byId(id);
}

var leech = {
    init: function(options) {
        this.options = options;
        this.data = {};
        this.proxy = 'http://lo/zir/ng/backend/fetcher/convert.php?url=';
        this.log = 'console';
        this.filter_it = -1;
        return this;
    },
    
    parseListPage: function(max) {
        var doc = this._get(this.options.link);
        var links = [];
        dojo.forEach(dojo.query(this.options.l.query, doc), function(item, idx, arr) {
            if (links.length < max) {
                var url = leech.options.l.filter(item);
                if (url) {
                    links.push(url + '');
                    leech._log(url);
                }
            }
        });
        this._log('Link number: ' + links.length);
        return links;
    },
    
    parsePaginator: function(url) {
        var doc = this._get(url);
        //detect multiple pages
        return this.options.ap.is_mul != null ? this.options.ap.is_mul(doc) : false;
    },
    
    parseContentPage: function(url) {
        var doc = this._get(url);
        var t = dojo.query(this.options.at.query, doc);
        var c = dojo.query(this.options.ac.query, doc);
  
        if (t.length > 0 && c.length > 0) {
            title = t[0].innerHTML;
            content = c[0].innerHTML;            
            if (this.options.ac.filter != null) {
                content = this.options.ac.filter(content);
            }
            this._log('Parsed article: ' + title);
            return {
                title: title,
                content: content
            };
        }
        return null;
    },
    
    _get: function(url) {
        var ua = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.3) Gecko/2008092414 Firefox/3.0.3';
        var headers = {
            'User-Agent': ua,
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language': 'en-us,en;q=0.5',
            'Accept-Encoding': 'gzip,deflate',
            'Accept-Charset': 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive': '30',
            'Connection': 'keep-alive'
        };
        var op = new Jaxer.Sandbox.OpenOptions();
        op.headers = headers;
        var sandbox = new Jaxer.Sandbox(null, null, op);
        sandbox.open(url);
        return sandbox.document;
    },
    
    _log: function(msg) {
        $(this.log).innerHTML += msg + '<br />';
    },
    
    setLog: function(e) {
        this.log = e;
    },
    
    post: function(url, data) {
        return Jaxer.Web.post(url, data, {
            as: "text",
            async: false,
            onsuccess: function(response) {
                leech._log(response);
            }
        });
    },
}
