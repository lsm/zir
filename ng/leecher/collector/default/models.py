from django.db import models


# Create your models here.


class Item(models.Model):
    id = models.IntegerField(primary_key=True)
    url = models.CharField(max_length=255, unique=True)
    channel = models.SmallIntegerField()
    type = models.CharField(max_length=255)
    rule_name = models.CharField(max_length=255)
    parent = models.IntegerField()
    title = models.CharField(max_length=255)
    content = models.TextField()
    created = models.IntegerField()
    