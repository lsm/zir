from django.conf.urls.defaults import *
from couchcms.article import views as bv
from trumpet import views as tv
from django.contrib import admin
admin.autodiscover()

# Uncomment the next two lines to enable the admin:
# from django.contrib import admin
# admin.autodiscover()

urlpatterns = patterns('',
    # Example:
    # (r'^couchcms/', include('couchcms.foo.urls')),

    # Uncomment the admin/doc line below and add 'django.contrib.admindocs'
    # to INSTALLED_APPS to enable admin documentation:
    # (r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    (r'^admin/(.*)', admin.site.root),
    #(r'^article/new/$', bv.new),
    url(r'^article/(?P<title>.+)/(?P<id>[a-zA-Z0-9]+)/$', bv.content, name='article_content'),
    url(r'^$', bv.index, name='article_index'),
    #(r'^article/type/(?P<cat>\w+)/$', bv.index),
    url(r'^tag/(?P<tag>[-\w]+)/$', bv.tag, name='article_tag'),
    url(r'^trumpet/save/$', tv.new, name='trumpet_new'),
    url(r'^trumpet/map/$', tv.map, name='trumpet_map'),
    url(r'^trumpet/load/(?P<term>[,\w]+)/(?P<type>\d+)/$', tv.load, name='trumpet_load'),
    #url(r'^block/(?P<tag>\w+)/list.html$', bv.block, name='article_block'),
    #(r'^article/author/(?P<author>\b[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b)/$', bv.index),
    #(r'^article/delete/(?P<id>\w+)/$', bv.delete),
    #(r'^article/notice/(?P<msg>\w+)/(?P<id>\w+)/$', bv.notice),
)
