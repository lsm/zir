import spread
from spreadutils import *

c = spread.connect('4804', 'mytest', 0, 0)
c.join('testgroup')

smsg = c.receive()
recmsg = Message(parse_msg = smsg.message)
print str(recmsg)
