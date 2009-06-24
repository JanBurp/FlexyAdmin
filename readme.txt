== FLEXYADMIN 2009 ==
(c) Jan den Besten
www.flexyadmin.com
for license see /sys/flexyadmin/flexyadmin_license.txt
all other libraries in /sys/ are LGPL or MIT licences.

== INSTALLING ==
Make sure .htaccess exists (is hidden)
Set your database information in /site/database.php and /site/database_local.php
Use the latest demo database from /db/ if you need one. 

== FILES ==
.htacces											- url rewrite settings for apache, don't change if not needed!
htacces.htacces								- rename this to .htaccess if .htaccess isn't there.
robots.txt										- info for search engine robots (excludes sys)
index.php											- start of it all, don't change it!
db														- here are example database and updates (you can remove this if you don't need it anymore)
sys														- the system, don't change this
site													- your site, put all your assets and files here
site/controller.php						- controlle of your site, put your PHP code here
site/database.php							- online database settings
site/database_local.php				- local database settings, you don't need to upload this
site/assets/									- folder with all your assets (css/js/img and folders for uploads)
site/views/										- folder with all your views, your html/php code!
site/views/home.php						- this is you core site