# socksprotocol.py
# let twisted support proxy
# Copyright (C) 2008 Samuel Chi
# E-mail: PrinceOfDataMining@gmail.com
# see more at http://code.google.com/p/proxysocket/

__version__ = (0, 8)
__all__ = ["installProxy", "Proxy"]

from twisted.internet import reactor
import socks

class BasicProxyProtocol:
    def dataReceived(self, data):
        send = self.factory.proxyHandler.parse(data)
        if send:
            self.transport.write(send)
        else:
            self.factory.proxyState = socks.PS_ACCEPTABLE
            self.connectionMade()
        
    def connectionMade(self):
        send = self.factory.proxyHandler.firstCommand()
        self.transport.write(send)

class BasicProxyFactory:
    proxyEnable = False
    proxyState = socks.PS_ACCEPTABLE

def installProxy(ProtocolClass, FactoryClass):
    class ProxyProtocol(ProtocolClass, BasicProxyProtocol):
        def dataReceived(self, data):
            if self.factory.proxyState == socks.PS_ACCEPTABLE:
                ProtocolClass.dataReceived(self, data)
            else:
                BasicProxyProtocol.dataReceived(self, data)
        def connectionMade(self):
            if self.factory.proxyState == socks.PS_ACCEPTABLE:
                ProtocolClass.connectionMade(self)
            else:
                BasicProxyProtocol.connectionMade(self)
        def connectionLost(self, reason):
            if self.factory.proxyEnable: self.factory.proxyState == socks.PS_WAIT
            ProtocolClass.connectionLost(self)
    class ProxyFactory(FactoryClass, BasicProxyFactory, socks.interfaceProxy):
        protocol = ProxyProtocol
    return ProxyProtocol, ProxyFactory
        
class Proxy():
    def __init__(self, factory, proxytype = None, proxyhost = ''):
        self.factory = factory
        self.factory.set_proxy(proxytype, proxyhost)
        
    def connectTCP(self, host, port, timeout = 30, bindAddress = None):
        handler = socks.getHandlerClass(self.factory.proxytype)
        if handler:            
            self.factory.proxyEnable = True
            self.factory.proxyState = socks.PS_WAIT
            self.factory.proxyHandler = handler((host, port), self.factory.authuser, self.factory.authpass)
            host, port = self.factory.proxyhost, self.factory.proxyport
        reactor.connectTCP(host, port, self.factory, timeout, bindAddress)

if __name__ == "__main__":    
    from twisted.internet.protocol import Protocol, ClientFactory
    from twisted.protocols.basic import LineOnlyReceiver
    class ChatProtocol(LineOnlyReceiver):
        def getId(self): return str(self.transport.getPeer())
        def connectionMade(self):
            print "ChatProtocol.connectionMade"
            print "%s/%s logged" % (self.factory.username, self.getId())
            self.transport.write('i am %s\r\n' % self.factory.username)
            reactor.stop()
        def lineReceived(self, data):
            print "%s/recv: %s" % (self.factory.username, data)
    class ChatFactory(ClientFactory):
        protocol = ChatProtocol
        def __init__(self, username):
            self.username = username
        def startedConnecting(self, connector):
            print "ClientFactory/startedConnecting"        
        def clientConnectionFailed(self, connector, reason):
            print "ClientFactory/clientConnectionFailed:", reason
        def clientConnectionLost(self, connector, reason):
            print "ClientFactory/clientConnectionLost:", reason
    ChatProtocol, ChatFactory = installProxy(ChatProtocol, ChatFactory)
    factory = ChatFactory('superman')
    proxy = Proxy(factory, proxytype = 4, proxyhost = 'localhost:1080')
    proxy.connectTCP('localhost', 51243)
    reactor.run()
