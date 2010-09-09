#from django.db import models
from django import forms
from django.forms import widgets
from django.db import models
from datetime import datetime


class Article(models.Model):
    title = models.CharField(max_length=128)
    editor = models.CharField(max_length=16)
    source = models.CharField(max_length=40)
    intro = models.CharField(max_length=255)
    picture = models.CharField(max_length=255)
    content = models.TextField()
    hash = models.CharField(max_length=32)
    status = models.CharField(max_length=16)
    modified = models.DateTimeField(auto_now=True)
    
    def __unicode__(self):
        return self.title
        
class Photo(models.Model):
    article = models.ForeignKey(Article)
    orig_url = models.CharField(max_length=255)
    new_url = models.CharField(max_length=255)
    status = models.CharField(max_length=16)
    
class Tag(models.Model):
    name = models.CharField(max_length=16)
    hash = models.CharField(max_length=32)
    
class TagArticle(models.Model):
    article = models.ForeignKey(Article)
    tag = models.ForeignKey(Tag)