== FLEXYADMIN ==
(c) Jan den Besten
www.flexyadmin.com

for license see /sys/flexyadmin/flexyadmin_license.txt
All other libraries in /sys/ are LGPL, MIT or Apache licences.

== INSTALLING ==
Make sure .htaccess exists (is hidden) or rename htaccess.htaccess to .htaccess.
Set your database information in /site/config/database.php and /site/config/database_local.php

== FILERIGHTS ==
These sites/maps needs to be writeable
- robots.txt  	(once to create the link to sitemap.xml)
- sitemap.xml
- bulk_upload
- site/stats
- site/cache
- site/assets/lists
- site/assets/_thumbcache
- site/assets/* (all the maps that have uploadable files)

== DATABASE ==
Use the latest demo database from /db/ if you start.
If you need it, change the database.
Log in as admin/admin or user/user

== FILES / MAPS ==
.htacces														- url rewrite settings for apache, don't change if not needed!
htacces.htacces											- rename this to .htaccess if .htaccess isn't there.
index.php														- start of it all, don't change it!
readme.txt													- this file
robots.txt													- info for search engine robots (excludes sys), points also to sitemap.xml
sitemap.xml													- sitemap of all the pages in menu
todo.txt														- file to put some comments if you like
update.txt													- how to update FlexyAdmin to the latest version
/db																	- here are example database and updates (you can remove this if you don't need it anymore)
/sys																- the system, don't change this
/site																- your site, put all your assets and files here
/site/controller.php								- controller of your site, put your PHP code here
/site/assets/												- folder with all your assets (css/js/img and folders for uploads)
/site/config/												- all config sits here
/site/config/config.php							- several config settings, default settings are mostly ok, see CodeIgniter
/site/config/config_local.php				- local config settings
/site/config/database.php						- online database settings
/site/config/database_local.php			- local database settings, you don't need to upload this
/site/config/flexyadmin_config.php	- here you can override settings for flexyadmin (use only if you know what you're doing)
/site/helpers/ 											- here you can put your own CodeIgniter helpers
/site/models/ 											- here you can put your own CodeIgniter models
/site/libraries/										- here you can put your own CodeIgniter libraries and frontend modules (which are special libraries)
/site/views/												- folder with all your views, where the html sits with some php
/site/views/site.php								- this is you main view
/userguide                          - here you can find all the info you need
