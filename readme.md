# FlexyAdmin

A Flexible and userfriendly CMS.

(c) Jan den Besten - www.flexyadmin.com (2006-2021)

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

You can find a basic userguide in the folder `userguide`

# License

For license see */sys/flexyadmin/flexyadmin_license.txt*.
