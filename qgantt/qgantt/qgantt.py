import re

from genshi.builder import tag

from trac.core import *
from trac.web import IRequestHandler
from trac.web.chrome import INavigationContributor, ITemplateProvider, \
        add_stylesheet, add_javascript

class Task(object):
    def __init__(self, data):
        for k in data:
            if k.startswith('due_'):
                date = data[k].split('/')
                data[k] = date[1] + '/' + date[0] + '/' + date[2]
            self.__setattr__(k, data[k])
        self.complete = float(self. totalhours)/float(self.estimatedhours)
        self.complete = int(self.complete*100)

class QGanttPlugin(Component):
    #implements(INavigationContributor, IRequestHandler, ITemplateProvider)
    implements(IRequestHandler, ITemplateProvider)


    def _get_tickets(self, mid):
        db = self.env.get_db_cnx()
        cursor = db.cursor()
        sql = "SELECT t.id, t.owner, t.summary, ts.name, ts.value \
              FROM ticket as t, ticket_custom as ts WHERE t.status != 'closed' \
              AND t.id = ts.ticket AND milestone = '%s' ORDER BY t.id DESC;" % mid
        cursor.execute(sql)
        tickets = cursor.fetchall() or []
        return tickets

    def _get_data(self, mid):
        tickets = self._get_tickets(mid)
        tmp = {}
        for id, owner, name, key, value in tickets:
            if not tmp.has_key(id):
                tmp[id] = {'id': id, 'assignee': owner}
            tmp[id]['name'] = name
            tmp[id][key] = value

        tasks = []
        for k in tmp:
            tasks.append(Task(tmp[k]))
        return tasks


    # INavigationContributor methods
    #def get_active_navigation_item(self, req):
    #    return 'helloworld!'
    #
    #def get_navigation_items(self, req):
    #    yield ('mainnav', 'qgantt',
    #           tag.a('QGantt', href=req.href.qgantt()))

    # IRequestHandler methods
    def match_request(self, req):
        page = re.match(r'/qgantt/(\w+)$', req.path_info)
        xml = re.match(r'/qgantt/(\w+)\.xml$', req.path_info)
        if page:
            req.args['mid'] = page.group(1)
            req.args['type'] = 'page'
            return True
        if xml:
            req.args['mid'] = xml.group(1)
            req.args['type'] = 'xml'
            return True

    def process_request(self, req):
        data = {}
        add_stylesheet(req, 'qg/css/jsgantt.css')
        add_javascript(req, 'qg/js/jsgantt.js')
        # This tuple is for Genshi (template_name, data, content_type)
        # Without data the trac layout will not appear.

        #data.update({'tickets': self._get_tickets()})
        mid = req.args.get('mid')
        if req.args.get('type') == 'page':
            data.update({'project': '%s.xml' % mid, 'milestone': mid})
            return 'gantt_page.html', data, 'text/html'
        if req.args.get('type') == 'xml':
            data.update({'tickets': self._get_data(mid), \
                         'milestone': mid, 'mid': 1})
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
        return [('qg', resource_filename(__name__, 'htdocs'))]
