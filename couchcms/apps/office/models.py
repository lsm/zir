# Create your models here.
from django.forms import forms, fields, widgets
from django.forms.models import ModelForm
from django.db import models


class Ad(models.Model):
    id = models.IntegerField(primary_key=True)
    name = models.CharField(max_length=60)
    content = models.TextField()
    position = models.CharField(max_length=100, db_index=True)
    
    def __unicode__(self):
        return self.name



class Contentindex(models.Model):
	tid = models.IntegerField(primary_key=True)
	cid = models.SmallIntegerField()
	title = models.CharField(max_length=255)
	photo = models.CharField(max_length=255)

class Content1(models.Model):
	tid = models.ForeignKey(Contentindex)
	content = models.TextField()
	author = models.CharField(max_length=255)