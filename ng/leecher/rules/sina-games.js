/**
 * @author root
 */
var common = {
    l: {
        query: 'td.linkBlue a',
        filter: function(c) {
            return c.toString().match(/^http:\/\/games.sina.com.cn(.*)$/) ? c : false;
        }
    },
    at: {
        query: 'div#artibodyTitle h1',
    },
    ac: {
        query: '#artibody',
        filter: function(c) {
            return c.replace(/<p class=.*<\/p>/, '').replace(/<p align="right".*<\/p>/, '');
        }
    },
    ap: {
        is_mul: null
    }
}

var rules = [{
    link: 'http://games.sina.com.cn/news/wlyx/',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '2',
        mid: '1'
    }
}, {
    link: 'http://games.sina.com.cn/news/sjyx/',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '5',
        mid: '1'
    }
}, {
    link: 'http://games.sina.com.cn/news/djyx/',
    l: common.l,
    at: common.at,
    ac: common.ac,
    ap: common.ap,
    type: {
        cid: '1',
        mid: '1'
    }
}]
