import re

from genshi.builder import tag

from trac.core import *
from trac.web import IRequestHandler
from trac.web.chrome import INavigationContributor, ITemplateProvider, \
        add_stylesheet, add_javascript

from model import Task

class QGanttPlugin(Component):
    implements(INavigationContributor, IRequestHandler, ITemplateProvider)

    # INavigationContributor methods
    def get_active_navigation_item(self, req):
        return 'helloworld!'

    def get_navigation_items(self, req):
        yield ('mainnav', 'qgantt',
               tag.a('QGantt', href=req.href.qgantt()))

    # IRequestHandler methods
    def match_request(self, req):
        return re.match(r'/qgantt$', req.path_info)

    def process_request(self, req):
        data = {}
        # This tuple is for Genshi (template_name, data, content_type)
        # Without data the trac layout will not appear.
        db = self.env.get_db_cnx()
        cursor = db.cursor()
        sql = 'SELECT name FROM milestone';
        cursor.execute(sql)
        milestones = cursor.fetchall() or []

        sql = 'SELECT name, owner FROM component';
        cursor.execute(sql)
        components = cursor.fetchall() or []

        data.update({'milestones': milestones, 'components':components})
        return 'summary.html', data, 'text/html'


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
