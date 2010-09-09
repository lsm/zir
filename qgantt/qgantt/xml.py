import re

#from genshi.builder import tag

from trac.core import *
from trac.web import IRequestHandler
from trac.web.chrome import ITemplateProvider, add_stylesheet, add_javascript

import model

class QGanttXML(Component):
    implements(IRequestHandler, ITemplateProvider)

    # IRequestHandler methods
    def match_request(self, req):
        match = re.match(r'/qgantt/xml/(.*)/(.*)\.xml$', req.path_info)
        if match:
            req.args['group_name'] = match.group(1)
            req.args['group_value'] = match.group(2)
            return True

    def process_request(self, req):
        data = {}
        # This tuple is for Genshi (template_name, data, content_type)
        # Without data the trac layout will not appear.
        g_name = req.args.get('group_name')
        g_value = req.args.get('group_value')
        mo = model.Tasks(self, {'name':g_name, 'value':g_value})
        tickets = mo.get_tasks()

        data.update({'tickets': tickets, 'boundary': mo.get_boundary()})
        return 'project.xml', data, 'application/xml'


    # ITemplateProvider methods
    # Used to add the plugin's templates and htdocs
    def get_templates_dirs(self):
        from pkg_resources import resource_filename
        return [resource_filename(__name__, 'templates')]

    def get_htdocs_dirs(self):
        """Return a list of directories with static resources (such as style
        sheets, images, etc.)

        Each item in the list must be a `(prefix, abspath)` tuple. The
        `prefix` part defines the path in the URL that requests to these
        resources are prefixed with.

        The `abspath` is the absolute path to the directory containing the
        resources on the local file system.
        """
        from pkg_resources import resource_filename
        return [('qgantt', resource_filename(__name__, 'htdocs'))]
