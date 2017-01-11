# FlexyAdmin

A Flexible and userfriendly CMS.
(c) Jan den Besten - www.flexyadmin.com


# Installing

In the examples below, replace `<dir>` with the folder you'd like to have FlexyAdmin installed

### Complete repository ###

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git <dir>`

### Shallow repository (for just a simple website) ###

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git --depth 10 <dir>`
  
  
# Update

Local URL: /admin/update


# Login

Two users exists with a fresh install in de demo database:

- admin/admin
- user/user

# Online

- Make sure the `public` is the root of the site.
- Or read below to return to normal install.

# From Safe install to (old) normal install

- Move files from `public` to root
- Merge `public/assets` to `site/assets`
- Use htaccess.htaccess
- Set `SAFE_INSTALL` in `index.php[83]` to `FALSE`
- Change `var assets` in `gulpfile.js[50]`

# License

For license see */sys/flexyadmin/flexyadmin_license.txt*.