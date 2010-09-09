#!/usr/bin/env python

from addressbook_pb2 import Person


person = Person()

f = open('test_size.pb')

for line in f:
    #print person.ParseFromString(line)
    print line