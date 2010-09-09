# ProxySocket.py
# let socket support proxy
# Copyright (C) 2008 Samuel Chi
# E-mail: PrinceOfDataMining@gmail.com
# see more at http://code.google.com/p/proxysocket/

__version__ = (0, 8)
__all__ = ["__version__", "proxySocket"]

"""
######################################################################################################
#               proxysocket.connect(real_address)                                                    #
#                   |\_______________________________________________                                #
#                   |                                                \                               #
#                   | SOCKS mode                                     | NOSOCKS mode                  #
#                   v                                                v                               #
#               socket.socket.connect(proxy_address)             socket.socket.connect(real_address) #
#                   |\________________________________               |                               #
#                   |                                 \              |                               #
#                   | SOCKSv5 mode                    | SOCKSv4 mode |                               #
#                   v                                 |              |                               #
#               Negotiate                             |              |                               #
#      ____________/|\_______________                 |              |                               #
#     /             |                \                |              |                               #
#     | UNKNOWN     | SOCKS_AUTHUSER | SOCKS_AUTHNONE |              |                               #
#     v             v                |                |              |                               #
# handle_error  Authorize            |                |              |                               #
#      ____________/|                |                |              |                               #
#     /             |                |                |              |                               #
#     | AUTH_FAIL   | AUTH_SUCCESS   |                |              |                               #
#     v             \_______________ | _______________/              |                               #
# handle_error                      \|/                              |                               #
#                                    v                               |                               #
#                                Request                             |                               #
#      _____________________________/|                               |                               #
#     /                              |                               |                               #
#     | FAIL                         | SUCCESS                       |                               #
#     v                              \______________________________ |                               #
# handle_error                                                      \|                               #
#                                                                    v                               #
#                                                                connect ok/err                      #
######################################################################################################
"""
import socket
import socks
    
class proxySocket(socket.socket, socks.interfaceProxy):
    """proxySocket([family[, type, proto[, _sock[, proxytype, proxyhost]]]]) -> socket object

    The family, type, proto, _sock arguments just like socket.socket.__init__(...);
    The proxytype argument specifies proxy mode;
    The proxyhost argument specify proxy info like "[authuser[:authpass]@]proxyhost[:proxyport]".
    """
    def __init__(self, family = socket.AF_INET, type = socket.SOCK_STREAM, \
                 proto = 0, _sock = None, proxytype = None, proxyhost = ''):
        socket.socket.__init__(self, family, type, proto, _sock)
        self.set_proxy(proxytype, proxyhost)

    def connect(self, *args):
        """connect(real_address)

        connect the socket to proxy address if proxytype is valid, otherwise connect to real_address."""
        handler = socks.getHandlerClass(self.proxytype)        
        if handler:
            socket.socket.connect(self, (self.proxyhost, self.proxyport))
            handler(args[0], self.authuser, self.authpass, self)
        else:
            socket.socket.connect(self, *args)

if __name__ == "__main__":
    def usage():
        print "USAGE: python ProxySocket.py --realhost=host[:port] --proxytype=?? --proxyhost=[user[:pass]@]host[:port]"
    
    def test(args):
        import getopt, sys
        try:
            opts, args = getopt.getopt(args, '', ['realhost=', 'proxytype=', 'proxyhost='])
        except getopt.GetoptError:
            usage()
            sys.exit(2)
        #
        real_address = ''
        proxy = {'proxytype': None, 'proxyhost': ''}
        for o, a in opts:
            if o == '--realhost':
                real_address = a
            if o == '--proxyhost':
                proxy['proxyhost'] = a
            if o == '--proxytype':
                for l in socks.allproxies:
                    if a.lower() in l:
                        proxy['proxytype'] = l[-1]
        #
        sock = proxySocket(**proxy)
        sock.connect(socks.splitport(real_address))
        while sock:
            buf = sock.recv(1024)
            if buf:
                print buf
                break
        sock.close()
    
    def main():
        import sys
        args = sys.argv[1:]
        if not args: args = '--realhost=localhost:80 --proxytype=SOCKSv4 --proxyhost=localhost:1080'.split()
        test(args)
    
    main()
