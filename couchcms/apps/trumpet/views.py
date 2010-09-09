#-*- coding: utf-8 -*-
# Create your views here.
from django.http import Http404, HttpResponse
from django.shortcuts import render_to_response
from couchdb.client import ResourceNotFound
from trumpet.models import *
from datetime import datetime
import cjson


def load(request, term, type = 0):
    #if type == '0':
    query = 'var idx = doc.regions.indexOf("%s")' % term;
    #if type == '1':
    #    query = 'var idx = doc.regions == "%s" ? 0 : -1' % term;
    
    map_func = u'''function(doc) {
            %s;
            if (idx != -1) {
                emit(doc.published, {'regions':doc.regions, 'content': doc.content, 'author': doc.author, 'time': doc.published});
            }
        };''' % query
    msg = _get_list(map_function=map_func)
    return HttpResponse(cjson.encode(msg), 200)
    
    
def _get_list(map_function=None, view=None):
    articles = list()
    if view:
        rows = db_trumpet.view(view, count=10, descending=True)
    if map_function:
        rows = db_trumpet.query(map_function, count=10, descending=True)
    for row in rows:
        row.value['id'] = row.id
        articles.append(row.value)
    return articles

def map(request):
    return render_to_response('trumpet/map.html')

def new(request):
    if request.method == 'POST':
        form = MessageForm(request.POST)
        if form.is_valid():
            p = request.POST
            author = p.get('author')
            if author == '':
                author = 'anonymous'
            # build model from form values
            m = Message(author=author, content=p['content'], regions=p.get('regions'), published=datetime.now())
            # save it into database
            id = m.save()
            return HttpResponse(id, status=200);
        return HttpResponse('error', status=200);
