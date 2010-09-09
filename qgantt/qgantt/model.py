
from datetime import datetime, tzinfo, timedelta

hours_per_day = 8

class Task(object):
    def __init__(self, data, cursor):
        columns = ['id', 'type', 'time', 'changetime', 'component', \
                   'severity', 'priority', 'owner', 'reporter', 'cc', \
                   'version', 'milestone', 'status', 'resolution', \
                   'summary', 'description', 'keywords']
        idx = 0
        for col in columns:
            self.__setattr__(col, data[idx])
            idx += 1
        sql = "SELECT * FROM ticket_custom WHERE ticket = '%d'" % self.id
        cursor.execute(sql)
        data = cursor.fetchall() or []
        if len(data) > 0:
            for ticket, name, value in data:
                self.__setattr__(name, value)
            self.totalhours = float(self.totalhours)
            self.estimatedhours = float(self.estimatedhours)

        self.actual_end = 0
        self.actual_start = 0
        self.percent = 0
        self.day_left = 0

        sql = "SELECT time FROM ticket_change \
              WHERE field = 'status' AND newvalue = 'accepted' \
              AND ticket = '%d' \
              ORDER BY time DESC LIMIT 1" % self.id
        cursor.execute(sql)
        time = cursor.fetchall() or []
        #TODO: figure out the timezone problem
        # the actual start time
        if time:
            d = datetime.utcfromtimestamp(time[0][0])
            self.actual_start = u'%s/%s/%s' % (d.year, d.month, d.day)

        if self.estimatedhours > 0 and self.status in ('accepted', 'assigned'):
            self.percent = int((self.totalhours / self.estimatedhours)*100)
            # estimated end time calculates base on actual_start time
            rest_day = self.estimatedhours / hours_per_day
            if rest_day - int(rest_day) > 0:
                rest_day += 1
            self.day_left = int(rest_day)
        elif self.status == 'closed':
            sql = "SELECT time FROM ticket_change WHERE field = 'status' AND newvalue = 'closed' \
                  ORDER BY time DESC LIMIT 1"
            cursor.execute(sql)
            time = cursor.fetchall()
            d = datetime.utcfromtimestamp(time[0][0])
            self.actual_end = u'%s/%s/%s' % (d.year, d.month, d.day)
            self.percent = 100

class Tasks(object):

    def __init__(self, com, group):
        db = com.env.get_db_cnx()
        self.cursor = db.cursor()
        self.group = group
        self.boundary = []

    def get_tasks(self, g_name = None, g_value = None):
        g = g_name or self.group['name']
        v = g_value or self.group['value']
        sql = "SELECT * FROM ticket WHERE `%s` = '%s'" % (g, v)
        self.cursor.execute(sql)
        data = self.cursor.fetchall() or []
        self.planned = {}
        if len(data) > 0:
            for d in data:
                task = Task(d, self.cursor)
                self.planned[task.id] = task
            for id in self.planned:
                self._deps(id)
            return self.planned
        return None

    def get_boundary(self):
        self.boundary.sort()
        left = self.boundary[0]
        right = self.boundary[-1]
        delta = []
        if left and right:
            left = left - timedelta(days=1)
            #right = right + timedelta(days=1)
            t = left
            while t <= right:
                delta.append({self._t2s(t): self._t2s(t + timedelta(days=7))})
                t += timedelta(days=8)
            #delta.pop()
            #delta.append({self._t2s(t - timedelta(days=8)): self._t2s(right)})
            right = t - timedelta(days=1)

        return (self._t2s(left), self._t2s(right), delta)

    def _t2s(self, t):
        return '%s/%s/%s' % (t.year, t.month, t.day)
    def _s2t(self, s):
        t = s.split('/')
        return datetime(int(t[0]), int(t[1]), int(t[2]))

    def _deps(self, id):
        def start_end(start, day_left):
            d = self._s2t(start)
            e = d + timedelta(days=day_left)
            return self._t2s(e)

        if not self.planned.has_key(id):
            return None

        self.planned[id].start = self.planned[id].due_start
        self.planned[id].end = self.planned[id].due_close

        if self.planned[id].estimatedhours > 0 and \
        self.planned[id].status in ('accepted', 'closed', 'new', 'assigned'):
            if self.planned[id].actual_start != 0:
                self.planned[id].start = self.planned[id].actual_start
                if self.planned[id].actual_end:
                    self.planned[id].end = self.planned[id].actual_end
                else:
                    self.planned[id].end = start_end(self.planned[id].start, \
                                                     self.planned[id].day_left)
            elif self.planned[id].depends_on:
                dep = self._deps(int(self.planned[id].depends_on))
                if dep:
                    if dep.actual_end:
                        # add extra one day for estimation
                        self.planned[id].start = self._t2s(self._s2t(dep.actual_end) \
                                                + timedelta(days=1))
                    else:
                        start = [dep.due_close, dep.end, dep.actual_end]
                        start.sort()
                        self.planned[id].start = start.pop()
                    self.planned[id].estimated_start = self.planned[id].start
                    day_left = self.planned[id].estimatedhours / hours_per_day
                    if day_left - int(day_left) > 0:
                        day_left += 1
                    self.planned[id].day_left = day_left
                    self.planned[id].end = start_end(self.planned[id].start, \
                                                     day_left)
                    self.planned[id].estimated_end = self.planned[id].end

        #for calculating boundaries
        self.boundary.append(self._s2t(self.planned[id].start))
        self.boundary.append(self._s2t(self.planned[id].end))
        if self.planned[id].due_start:
            self.boundary.append(self._s2t(self.planned[id].due_start))
        if self.planned[id].due_close:
            self.boundary.append(self._s2t(self.planned[id].due_close))
        return self.planned[id]
