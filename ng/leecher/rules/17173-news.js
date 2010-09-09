/**
 * @author root
 */
var common = {
    l: {
        query: 'table.cmsTable tbody tr td a',
        filter: function(c) {
            return c.toString().match(/http:\/\/news.17173.com\/content.*$/) ? c : false;
        }
    },
    ap: {
        is_mul: function(doc) {
            var splited = doc.URL.split(',');
            if (splited.length == 2 && splited[1] == '1.shtml') {
                var total = doc.body.innerHTML.match(/totalrecord=(\d+)/)[1];
                var page_links = [];
                for (var i = 2; i <= total; i++) {
                    page_links.push(splited[0] + ',' + i + '.shtml');
                }
                return page_links;
            }
            return false;
        }
    },
    at: {
        query: 'div#newsbiaoti'
    },
    ac: {
        query: 'div#news',
        filter: function(c) {
            c = c.replace(/<div.*>.*<\/div>/g, '').replace(/<iframe.*<\/iframe>/, '').replace(/<font.*>.*<\/font>/g, '').replace(/<span\s+class="cmsHotLink">.*<\/span>/g, '');
						c = c.replace(/<p.*>.*17173.*<\/p>/g, '')
            do {
                var m = c.match(/<a.*>(<img.*)<\/a>/);
                if (m) {
                    c = c.replace(m[0], m[1]);
                }
            } while (m)
            return c;
        }
    }
}

var rules = [{
    link: 'http://news.17173.com/main/no1.shtml',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '2',
        mid: '1'
    }
}, {
    link: 'http://news.17173.com/main/no2.shtml',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '2',
        mid: '1'
    }
}, {
    link: 'http://news.17173.com/top/',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '2',
        mid: '1'
    }
}, ]
