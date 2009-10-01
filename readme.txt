== FLEXYADMIN 2009 ==
(c) Jan den Besten
www.flexyadmin.com
for license see /sys/flexyadmin/flexyadmin_license.txt
All other libraries in /sys/ are LGPL or MIT licences.

== INSTALLING ==
Make sure .htaccess exists (is hidden) or rename htaccess.htaccess to .htaccess.
Set your database information in /site/config/database.php and /site/config/database_local.php
Use the latest demo database from /db/ if you need one.

== FILES ==
.htacces													- url rewrite settings for apache, don't change if not needed!
htacces.htacces										- rename this to .htaccess if .htaccess isn't there.
robots.txt												- info for search engine robots (excludes sys)
index.php													- start of it all, don't change it!
db																- here are example database and updates (you can remove this if you don't need it anymore)
sys																- the system, don't change this
site															- your site, put all your assets and files here
site/config/											- all config sits here
site/config/database.php					- online database settings
site/config/database_local.php		- local database settings, you don't need to upload this
site/config/config.php						- several config settings, default settings are mostly ok, see CodeIgniter
site/config/config_local.php			- local config settings
site/controller.php								- controller of your site, put your PHP code here
site/assets/											- folder with all your assets (css/js/img and folders for uploads)
site/views/												- folder with all your views, your html/php code!
site/views/home.php								- this is you core site
