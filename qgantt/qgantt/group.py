import re

from genshi.builder import tag

from trac.core import *
from trac.web import IRequestHandler
from trac.web.chrome import INavigationContributor, ITemplateProvider, \
        add_stylesheet, add_javascript

from model import Task


class QGanttGroup(Component):
    implements(IRequestHandler, ITemplateProvider)

    def _get_tickets(self, mid):
        db = self.env.get_db_cnx()
        cursor = db.cursor()
        sql = "SELECT t.id, t.owner, t.summary, ts.name, ts.value \
              FROM ticket as t, ticket_custom as ts WHERE t.status != 'closed' \
              AND t.id = ts.ticket AND t.milestone = '%s' ORDER BY t.id DESC;" % mid
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

    # IRequestHandler methods
    def match_request(self, req):
        match = re.match(r'/qgantt/group/(.*)/(.*)$', req.path_info)
        if match:
            req.args['group_name'] = match.group(1)
            req.args['group_value'] = match.group(2)
            return True

    def process_request(self, req):
        data = {}
        #add_stylesheet(req, 'qgantt/fc/Style.css')
        add_javascript(req, 'qgantt/fc/FusionCharts.js')
        # This tuple is for Genshi (template_name, data, content_type)
        # Without data the trac layout will not appear.

        #data.update({'tickets': self._get_tickets()})
        data.update({'chart_xml': 'xml/' + req.args.get('group_name')
                     + '/' + req.args.get('group_value') + '.xml'})

        return 'chart.html', data, None


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
