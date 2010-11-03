== FLEXYADMIN 2009/2010 ==
(c) Jan den Besten
www.flexyadmin.com
for license see /sys/flexyadmin/flexyadmin_license.txt
All other libraries in /sys/ are LGPL or MIT licences.

== INSTALLING ==
Make sure .htaccess exists (is hidden) or rename htaccess.htaccess to .htaccess.
Set your database information in /site/config/database.php and /site/config/database_local.php

== DATABASE ==
Use the latest demo database from /db/ if you start. If you need it, change the database.
Log in as admin/admin or user/user


== FILES ==
.htacces													- url rewrite settings for apache, don't change if not needed!
htacces.htacces										- rename this to .htaccess if .htaccess isn't there.
index.php													- start of it all, don't change it!
readme.txt												- this file
robots.txt												- info for search engine robots (excludes sys)
todo.txt													- file to put some comments if you like
update.txt												- how to update FlexyAdmin to the latest version

db																- here are example database and updates (you can remove this if you don't need it anymore)

sys																- the system, don't change this

site															- your site, put all your assets and files here
site/controller.php								- controller of your site, put your PHP code here

site/assets/											- folder with all your assets (css/js/img and folders for uploads)

site/config/											- all config sits here
site/config/config.php						- several config settings, default settings are mostly ok, see CodeIgniter
site/config/config_local.php			- local config settings
site/config/database.php					- online database settings
site/config/database_local.php		- local database settings, you don't need to upload this
site/config/flexyadmin_config.php	- here you can override settings for flexyadmin (use only if you know what you're doing)

site/modules/											- here you can put frontend modules

site/plugins/											- here you can put admin plugins (backend)

site/views/												- folder with all your views, your html/php code!
site/views/home.php								- this is you core site

