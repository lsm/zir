from django import forms
from django.forms import widgets
from couchdb import schema as s
from datetime import datetime
from couchdb import design, view, client


from couchdb import Server
from django.conf import settings

SERVER = Server(getattr(settings,'COUCHDB_SERVER','http://127.0.0.1:5984'))

db_trumpet = SERVER[getattr(settings,'COUCHDB_PREFIX','couchcms_') + 'trumpet']

class Message(s.Document):
	author = s.TextField()
	regions = s.TextField()
	content = s.TextField()
	published = s.DateTimeField()
	def save(self):
		self.store(db_trumpet)
		return self.id

class MessageForm(forms.Form):
    author = forms.CharField(max_length=120, required=False)
    content = forms.CharField(widget=forms.Textarea())
    regions = forms.CharField(max_length=1024, required=True)