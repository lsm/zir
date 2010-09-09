from setuptools import find_packages, setup

PACKAGE = 'qgantt'

# name can be any name.  This name will be used to create .egg file.
# name that is used in packages is the one that is used in the trac.ini file.
# use package name as entry_points
setup(
    name='QGantt', version='0.1',
    author = "Senmiao Liu",
    author_email = "liusenmiao@360quan.com",
    description = "Render gantt chart from ticket system using FusionCharts.",
    license = "GPLv3",
    packages=find_packages(exclude=['*.tests*']),
    entry_points={'trac.plugins': '%s = %s' % (PACKAGE, PACKAGE)},
    package_data={'qgantt': ['templates/*', 'htdocs/fc/*',
                                 'htdocs/css/*', 'htdocs/js/*']},
)
