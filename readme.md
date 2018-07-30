# FlexyAdmin

A Flexible and userfriendly CMS.
(c) Jan den Besten - www.flexyadmin.com

# Installing

In the examples below, replace `<dir>` with the folder you'd like to have FlexyAdmin installed

### Complete repository ###

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git <dir>`

### Shallow repository (for just a simple website) ###

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git --depth 10 <dir>`

### Shallow copy of branch (3.5.0 for example)

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git --branch <branch> --single-branch --depth 10 <dir>`
`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git --branch feature/3.5.0 --single-branch --depth 10 <dir>`
    
### Shallow repository a branch ###

`git clone https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git --depth 10 -b <branch> <dir>`
  
## More git

### Removing (old) tags

`git push https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git :refs/tags/3.5.0-beta.x`
Or use the script: `php scripts/git_remove_tags.php _root_ _from_ _to_`

git push https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git :refs/tags/3.2.3
git push https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git :refs/tags/3.3.5
git push https://Jan_db@bitbucket.org/Jan_db/flexyadmin.git :refs/tags/3.4.7
php scripts/git_remove_tags.php schoool-2.0. 0 5
php scripts/git_remove_tags.php 3.5.0-beta. 5 25
 
### Removing all assets in all the commits in history (See: https://dalibornasevic.com/posts/2-permanently-remove-files-and-folders-from-a-git-repository)

`git filter-branch --tree-filter 'rm -rf site/assets' HEAD`
After that push all tags and branches with `--force`

# Update

Local URL: /admin/update

# Login

Two users exists with a fresh install in de demo database:

- admin/admin
- user/user

# Security

Change these items in site/config/config.php for you're website:
- sess_cookie_name
- encryption_key


# License

For license see */sys/flexyadmin/flexyadmin_license.txt*.