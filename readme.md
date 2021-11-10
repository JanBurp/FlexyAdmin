# FlexyAdmin

A Flexible and userfriendly CMS.

(c) Jan den Besten - www.flexyadmin.com (2006-2021)

# Goals

This CMS was made to make life of my clients more enjoyable. Most of the existing CMSes (at the time i started this) are unfriendly or bloated.
This CMS keeps everything simple for the user and meanwhile make it possible to build all kinds of websites due to the design priciples of not using standard tenmplates, but easy to use building blocks. For an example see www.develop.schoool.nl.

# Local installation

- Install the repo in its own folder
- Create a new MySQL database and fill the database login in `config/database_local.php`
- Call the (local) URL thats pointed to the folder (use a local server with PHP for example Laravels Valet)
- The database is automatically filled with the core tables for a simple website
- Goto to `{your_local_url}/_admin` to show the CMS admin panel

# Login

Two users exists with a fresh install:

- admin/admin
- user/user

# Security

Change these items in site/config/config.php for you're website. With a normal installation process these should be changed automatically.
- sess_cookie_name
- encryption_key

# Userguide

You can find a basic userguide in the folder `userguide` with all the basics covered.
Apart from the userguide, just look at documentation in the code itself. For example `sys\flexyadmin\models\data\Data_Core.php` for the data model.

# License

For license see */sys/flexyadmin/flexyadmin_license.txt*.
