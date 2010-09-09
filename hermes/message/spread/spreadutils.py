import re

header_parse_re = re.compile(r'^([^:\s]*)\s*:\s*(.*)', re.MULTILINE)

class Message:

    def __init__(self, headers={}, content='', parse_msg=None):
        self.headers = headers
        self.set_content(content)
        if parse_msg:
            self.__parse(parse_msg)

        
    def __setitem__(self, name, value):
        self.headers[name] = value
        
        
    def __delitem__(self, name):
        del self.headers[name]
        
        
    def __getitem__(self, name):
        return self.headers[name]
        
        
    def __eq__(self, msg):
        if not hasattr(msg, 'headers') or not hasattr(msg, 'content') or self.content != msg.content:
            return False            
        for key in self.headers.keys():
            if not msg.headers.has_key(key) or self.headers[key] != msg.headers[key]:
                return False
        return True
        
        
    def __str__(self):
        out = [ ]
        for key in self.headers.keys():
            out.append('%s: %s' % (key, self.headers[key]))
        out.append('')
        out.append(self.content)
        return '\n'.join(out)

        
    def debug(self):
        out = [ ]
        for key in self.headers.keys():
            out.append('header "%s" = "%s"' % (key, self.headers[key]))
        out.append('content = %s' % self.content)
        return '\n'.join(out)

        
    def __parse(self, s):
        split = s.split('\n\n')
        for mat in header_parse_re.finditer(split[0]):
            self.headers[mat.group(1)] = mat.group(2)
        self.content = '\n\n'.join(split[1:])
        

    def parse(self, s):
        self.headers.clear()
        self.__parse(s)
        
        
    def set_content(self, content):
        self.content = content
        

