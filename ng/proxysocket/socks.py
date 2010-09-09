# socks.py
# a set of proxy handler: socks4, socks5, http, https
# Copyright (C) 2008 Samuel Chi
# E-mail: PrinceOfDataMining@gmail.com
# see more at http://code.google.com/p/proxysocket/

__version__ = (0, 8)
__all__ = ["__version__", "SOCKS_VER4", "SOCKS_VER5", "HTTP", "HTTPS", "getHandlerClass"]

import socket
import struct

SOCKS_VER4 = 4
SOCKS_VER5 = 5
HTTP       = 'http'
HTTPS      = 'https'

socksver4 = [SOCKS_VER4, str(SOCKS_VER4), chr(SOCKS_VER4), 'socks4', 'socksver4', 'socks ver4', 'socksv4', 'socks v4']
socksver5 = [SOCKS_VER5, str(SOCKS_VER5), chr(SOCKS_VER5), 'socks5', 'socksver5', 'socks ver5', 'socksv5', 'socks v5']
http      = [HTTP]
https     = [HTTPS]
allproxies = [socksver4, socksver5, http, https]

# SOCKS REQUEST COMMAND
SOCKS_CONNECT = 1
SOCKS_BIND    = 2
SOCKS_UDP     = 3 # only supported by socks v5

# following is for socks v4
SOCKS_NULL = 0
# response code
SOCKS4_FORWARD     = range(0x5A)
SOCKS4_SUCCESS     = 0x5A
SOCKS4_FAILURE     = 0x5B
SOCKS4_ERR_CONNECT = 0x5C
SOCKS4_ERR_USERID  = 0x5D
SOCKS4_BACKWARD    = range(0x5E, 0x100)
SOCKS4_MESSAGES = ['0x%.2x: ERROR - UNKNOWN' % i for i in SOCKS4_FORWARD] + \
                  ['0x5A: Succeeded', 
                   '0x5B: general SOCKS server failure',
                   '0x5C: ERROR - cannot connect to real_address',
                   '0x5D: ERROR - incorrect UserID'] + \
                  ['0x%.2x: ERROR - UNKNOWN' % i for i in SOCKS4_BACKWARD]
# following is for socks v5
# AUTHORIZE METHOD
SOCKS_AUTH_NONE    = 0
SOCKS_AUTH_GSSAPI  = 1
SOCKS_AUTH_NORMAL  = 2
SOCKS_AUTH_IANA    = range(0x03, 0x80)
SOCKS_AUTH_PRIVATE = range(0x80, 0xFF)
SOCKS_AUTH_NOSET   = 0xFF
SOCKS_AUTH_MESSAGES = ['0x00: Not need authorize',
                       '0x01: Unsupported GSSAPI method',
                       '0x02: normal username/password method'] + \
                      ['0x%.2x: Unsupported IANA method' % i for i in SOCKS_AUTH_IANA] + \
                      ['0x%.2x: Unsupported PRIVATE method' % i for i in SOCKS_AUTH_PRIVATE] + \
                      ['0xFF: Not set authorize']
# AUTHORIZE COMMAND
AUTHORIZE    = 1
# authorize: success / failure
AUTH_OK      = 0
AUTH_FAIL    = 1
AUTH_ERROR   = range(2, 256)
AUTH_MESSAGES = ['0x00: AUTH OK',
                 '0x01: AUTH FAIL'] + \
                ['0x%.2x: AUTH ERROR - UNKNOWN' % i for i in AUTH_ERROR]
SOCKS_RESERVED = 0
# ADDRESS TYPE
SOCKS_adrIPv4   = 1
SOCKS_adrName   = 3
SOCKS_adrIPv6   = 4
SOCKS_adrOthers = range(5, 256)
SOCKS_ADDRESS_MESSAGES = ['0x00: UNKNOWN ADDRESS TYPE',
                          '0x01: IPv4',
                          '0x02: UNKNOWN ADDRESS TYPE',
                          '0x03: Domain Namw',
                          '0x04: Unsupported IPv6 type'] + \
                         ['0x%.2x: UNKNOWN ADDRESS TYPE' % i for i in SOCKS_adrOthers]
# response code
SOCKS5_SUCCESS              = 0
SOCKS5_FAILURE              = 1
SOCKS5_OUT_OF_RULESET       = 2
SOCKS5_NETWORK_UNREACHABLE  = 3
SOCKS5_HOST_UNREACHABLE     = 4
SOCKS5_CONNECTION_REFUSED   = 5
SOCKS5_TTL_EXPIRED          = 6
SOCKS5_UNKNOWN_COMMAND      = 7
SOCKS5_UNKNOWN_ADDRESS_TYPE = 8
SOCKS5_UNASSIGNED           = range(9, 256)
SOCKS5_MESSAGES = ['0x00: Succeeded',
                   '0x01: ERROR - general SOCKS server failure',
                   '0x02: ERROR - connection not allowed by ruleset',
                   '0x03: ERROR - network unreachable',
                   '0x04: ERROR - host unreachable',
                   '0x05: ERROR - connection refused',
                   '0x06: ERROR - TTL expired',
                   '0x07: ERROR - command not supported',
                   '0x08: ERROR - address type not supported'] + \
                   ['0x%.2x: ERROR - unassigned' % i for i in SOCKS5_UNASSIGNED]
# SOCKS STATE
PS_ACCEPTABLE = -1
PS_WAIT       = 0
PS_NEGOTIATE  = 1
PS_AUTHORIZE  = 2
PS_REQUEST    = 3

def splithost(host):
    if '@' in host:
        return host.split('@', 1)
    else:
        return '', host

def splituser(user):
    if ':' in user:
        return user.split(':', 1)
    else:
        return user, ''

def splitport(host, default = 80):
    host, port = splituser(host)
    try:
        port = int(port)
    except:
        port = default
    return host, port

class interfaceProxy:
    def set_proxy(self, proxytype, proxyhost):
        self.proxytype = proxytype
        user, host = splithost(proxyhost)
        if self.proxytype in socksver4 + socksver5:
            default = 1080
        elif self.proxytype in http:
            default = 80
        elif self.proxytype in https:
            default = 443
        else:
            default = 0
        self.proxyhost, self.proxyport = splitport(host, default)
        self.authuser, self.authpass = splituser(user)

def socks_request(ver, host, port, user = ''):
    if ver in socksver4:
        host = socket.inet_aton(socket.gethostbyname(host))
        return struct.pack('!BBH',  SOCKS_VER4, SOCKS_CONNECT, port) + \
               host + user + chr(SOCKS_NULL)
    elif ver in socksver5:
        return struct.pack('BBBBB', SOCKS_VER5, SOCKS_CONNECT, SOCKS_RESERVED, SOCKS_adrName, len(host)) + \
               host + struct.pack('!H', port)
    else:
        raise Exception, "socks version is not supported."

def socks_response(text):
    """return (next_state, code).
       process successfully if code is zero, otherwise failed.
    """    
    head, text = text[:2], text[2:]
    ver, code = struct.unpack('BB', head)
    if ver == 0:
        port = struct.unpack('!H', text[:2])[0]
        host = socket.inet_ntoa(text[2:6])
        return PS_ACCEPTABLE, code - SOCKS4_SUCCESS, SOCKS4_MESSAGES[code], host, port
    if ver == SOCKS_VER5:
        if not text:
            if code == SOCKS_AUTH_NORMAL:
                return PS_AUTHORIZE, 0,    SOCKS_AUTH_MESSAGES[code]
            else:
                return PS_REQUEST,   code, SOCKS_AUTH_MESSAGES[code]
        else:
            reserved, type = struct.unpack('BB', text[:2])
            port = struct.unpack('!H', text[-2:])[0]
            if type == SOCKS_adrIPv4:
                host = socket.inet_ntoa(text[2:-2])
            elif type == SOCKS_adrName:
                host = text[3:-2]
            else:
                return PS_ACCEPTABLE, -1, SOCKS_ADDRESS_MESSAGES[type]
            return PS_ACCEPTABLE, code, SOCKS5_MESSAGES[code], host, port
    elif ver == AUTHORIZE:
        return PS_REQUEST, code, AUTH_MESSAGES[code]

def socks_negotiate(username = ''):
    l = [SOCKS_AUTH_NONE]
    if username: l.append(SOCKS_AUTH_NORMAL)
    l.insert(0, len(l))
    l.insert(0, SOCKS_VER5)
    return ''.join(map(chr, l))

def socks_authorize(username, password):
    l = map(lambda s: chr(len(s)), [username, password])
    l.insert(0, chr(AUTHORIZE))
    l.insert(2, username)
    l.append(password)
    return ''.join(l)

def http_request(host, port, username = '', password = ''):
    return 'CONNECT %s:%d HTTP/1.1\r\n\r\n' % (host, port)

def http_response(text):
    text = text.split('\n', 1)[0]
    if text and text[-1] == '\r': text = text[:-1]
    return text.split(' ', 2)

https_request  = http_request
https_response = http_response

class BaseHandler:
    def __init__(self, real_address, authuser = '', authpass = '', sock = None):
        self.real_address = real_address
        self.authuser, self.authpass = authuser, authpass
        if sock:
            self.sock = sock
            self.buf = ''
            self.send(self.firstCommand())

    def firstCommand(self):
        raise NotImplemented

    def send(self, data):
        if data:
            self.sock.send(data)
            while 1:
                buf = self.sock.recv(128)
                if buf:
                    self.buf = buf
                    break
            self.send(self.parse(self.buf))

    def parse(self, buf):
        raise NotImplemented
        
class SocksHandler(BaseHandler):
    def parse(self, buf):
        state, code, reason = socks_response(buf)[:3]
        if code:
            raise Exception, reason
        elif state == PS_AUTHORIZE:
            return socks_authorize(self.authuser, self.authpass)
        elif state == PS_REQUEST:
            return socks_request(SOCKS_VER5, self.real_address[0], self.real_address[1])
        elif state == PS_ACCEPTABLE:
            return ''
        else:
            raise Exception, "0x%.2x: INCORRECT PROXY STATE" % (state,)

class Socks4Handler(SocksHandler):
    def firstCommand(self):
        return socks_request(SOCKS_VER4, self.real_address[0], self.real_address[1], self.authuser)
            
class Socks5Handler(SocksHandler):
    def firstCommand(self):
        return socks_negotiate(self.sock.authuser)

# Http & Https are incompleted. 
class HttpHandler(BaseHandler):
    def firstCommand(self):
        return http_request (self.real_address[0], self.real_address[1], self.authuser, self.authpass)

class HttpsHandler(HttpHandler):
    def firstCommand(self):
        return https_request(self.real_address[0], self.real_address[1], self.authuser, self.authpass)

def getHandlerClass(proxytype):
    if proxytype in socksver4:
        return Socks4Handler
    elif proxytype in socksver5:
        return Socks5Handler
    elif proxytype in http:
        return HttpHandler
    elif proxytype in https:
        return HttpsHandler
    else:
        return None
