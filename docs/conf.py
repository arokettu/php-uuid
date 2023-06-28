from datetime import datetime

# import specific project config
import os, sys
sys.path.append(os.curdir)
from conf_project import *

author = 'Anton Smirnov'
copyright = '{} {}'.format(datetime.now().year, author)
language = 'en'

html_title = project
html_theme = 'sphinx_book_theme'
templates_path = ["_templates"]
html_sidebars = {
    "**": ["navbar-logo.html", "rtd-version.html", "icon-links.html", "sbt-sidebar-nav.html"]
}
