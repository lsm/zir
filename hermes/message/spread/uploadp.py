#! /usr/bin/python

import popen2
import spread
from spreadutils import *
import tempfile

c = spread.connect('4804', 'uploadp', 0, 0)
c.join('uploadfiles')

while True:
    smsg = c.receive()
    print 'received %s ' % smsg
    recmsg = Message(parse_msg = smsg.message)

    print 'Received message with id #%s' % recmsg['f_id']
    
    # write the message content to a temporary file
    fname = tempfile.mktemp()
    fout = file(fname, 'w')
    fout.write(recmsg.content)
    fout.close()
    
    # convert to text using antiword
    r, w, e = popen2.popen3('antiword %s' % fname)
    text = r.readlines()
    for line in text:
        print line,
    
    # return the message response
    resmsg = Message({ 'f_id' : recmsg['f_id'], 'size' : len(recmsg.content), 'lines' : len(text) })
    c.multicast(spread.RELIABLE_MESS, 'uploadresponses', str(resmsg))
