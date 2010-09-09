#-*- coding: utf-8 -*-
# Create your views here.
from django.http import Http404,HttpResponseRedirect
from django.shortcuts import render_to_response
from couchdb.client import ResourceNotFound
from couchcms.article.models import *
from datetime import datetime
from office.models import Ad
from article.models import Article
import cjson

from article import db_article, db_tag

import lightcloud as lc


def content(request, title, id):
    #post = Post.objects.filter(id=pid)[0]
    article = db_article.get(id)
    return render_to_response('article/content.html', {'article': article, 'ads':_get_ads()})

def index(request):
	return render_to_response('article/index.html', {'articles': _get_list('article_views/list_all'), 'ads':_get_ads()})

def tag(request, tag):
    map_fun = by_tag(tag)
    posts = cjson.encode(_get_list(map_fun))
    return render_to_response('article/list.html', {'post_list': posts})

def _get_list(map_function):
    articles = list()
    for row in db_article.view(map_function, count=10):
        row.value['id'] = row.id
        articles.append(row.value)
    return articles

def _get_ads():
	return {'header': Ad.objects.get(id=1).content, \
			'middle': Ad.objects.get(id=3).content, 'right': Ad.objects.get(id=2).content}

'''
def new(request):
    v = {}
    if request.method == 'POST':
        form = PostForm(request.POST)
        if form.is_valid():
            p = request.POST
            tags = str(p['tags']).strip().split(' ')
            author = p.get('author')
            if author == '':
                author = 'anonymous@example.com'
            # build model from form values
            post = Post(id=None, type=p['type'], title=p['title'], author=author, \
                        content=p['content'], tags=tags)
            # save it into database
            id = post.save(db)
            return HttpResponseRedirect('/blog/detail/%s' % id)
    else:
        form = PostForm()
    v['form'] = form
    return render_to_response('blog_new.html', v)

def delete(request, id):
    item = db.get(id, 0)
    if item is 0:
        return HttpResponseRedirect('/blog/notice/%s/%s/' % ('notfound', id))
    else:
        del db[id]
        return HttpResponseRedirect('/blog/notice/%s/%s/' % ('deleted', id))

def notice(request, msg, id):
    if msg == 'notfound':
        message = 'Item %s not found' % id
    elif msg == 'deleted':
        message = 'Item %s has been deleted' % id
    return render_to_response('blog_notice.html', {'msg': message})
    
def import():
    from django.db import connection
    cursor = connection.cursor()
    sql = """
    select c.content, c.author, i.cid, i.title, i.photo 
    from office_content1 c, office_contentindex i
    where c.tid = i.tid;
    """
    cursor.execute(sql)
    rows = cursor.fetchall()
    cat = {1: u'单机', 2: u'网游', 5:u'手机', 6:u'主机', 7:u'新闻', 8:u'新闻', 9:u'国际', 10:u'国内'}
    
    for row in rows:
            item = Article(title=u'%s'%row[3], \
                           tags=u',%s,' % cat.get(int(row[2])), \
            content=u'%s'%row[0], photo=row[4], editor=u'%s'%row[1])
            item.save()
'''
