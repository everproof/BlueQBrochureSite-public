import sys
import os
from os import listdir
from os.path import isfile, join
import re
import time

if len(sys.argv) < 2:
    print("Invalid arguments. Must specify folder")
    sys.exit(1)

DIR = sys.argv[1]
VERSION_STRING = str(int(time.time()))

html_match = re.compile(r'\.html$')
is_html = lambda x: html_match.search(x)


# Iterate over all the files in DIR directory and find the HTML files
files = [join(DIR, f) for f in listdir(DIR) if isfile(join(DIR, f)) and is_html(f)]


# Match any string that ends in .css, doesn't end in min.css, and doesn't already
# have a query string
css_match = re.compile(r'(?<!min)\.css(?!\?)')
for filename in files:
    backup_name = filename + '.bak'
    os.rename(filename, backup_name)

    with open(backup_name, 'r', encoding='utf8') as in_file, open(filename, 'w+', encoding='utf8') as out_file:
        for line in in_file:
            out_file.write(css_match.sub('.css?' + VERSION_STRING, line))

    os.remove(backup_name)
