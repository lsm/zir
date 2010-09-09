#! /usr/bin/python

import MySQLdb
import spread
import sys
from spreadutils import *

db = MySQLdb.connect(host = "localhost",
                     user = "spread",
                     passwd = "password",
                     db = "spreaddb")
cursor = db.cursor()
c = spread.connect('4804', 'uploadp', 0, 0)
c.join('uploadresponses')

while True:
    smsg = c.receive()
    recmsg = Message(parse_msg = smsg.message)

    try:
        f_id = recmsg['f_id']
        size = recmsg['size']
        lines = recmsg['lines']
        print 'Received message response with id #%s' % f_id
        cursor.execute("update uploaded_files set size = %s, num_lines = %s where id = %s" % ( size, lines, f_id ))
        db.commit()
    except:
        print 'invalid message %s' % recmsg.debug()
        sys.exit(1)
