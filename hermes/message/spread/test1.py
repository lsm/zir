import spread
from spreadutils import *

c = spread.connect('4804', 'mytest', 0, 0)
msg = Message({ 'header1' : 'val1', 'header2' : 'val2' }, 'this is a test message with headers')
c.multicast(spread.RELIABLE_MESS, '#mytest#machine1', str(msg))

smsg = c.receive()
recmsg = Message(parse_msg = smsg.message)
print 'sent == received == %s' % (recmsg == msg)
