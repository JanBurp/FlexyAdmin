# FlexyAdmin Scripts

A collections of scripts that can run in the terminal.

## Running a script

All scripts must run from the root folder.

Shell scripts (.sh) must be executable, this is how:

`chmod a+rx script.sh`

Executing a shell script:

`./scripts/script.sh`

Executre scripts written in PHP (.php) like this:

`php scripts/script.php`

## Overview of the scripts

`hello.sh` 	  					- Simple hello world
`update.sh` 	  				- Pull & Merge latest FlexyAdmin (set branche variables first!)
`phpinfo.php` 					- Raw PHP info
`git_remove_tags.php` 	- Delete old git tags, example: `php scripts/git_remove_tags.php 3.5.0-beta. 1 19`
`old_install.php` 			- Change install from safe folder structure to normal.
`safe_install.php` 			- Change install from old folder structure to safe folder structure.


## Other terminal commands

### FlexyAdmin CLI

You need to be in the folder where index.php is. (`root` or `public`).
Only works if you're working local and logged in as super_admin.

`php index.php _cli` - Lists all CLI commands
`php index.php _cli hello` -  A simple Hello world


### Node, Bower & Gulp

When using LESS and JS Lint & Minify:

`npm install` - Installs node modules
`bower install` - Installs bower components

`gulp install` - Move bower components to assets folder
`gulp` - Run Gulp
`gulp watch` - Let Gulp watch changes, run the action, and livereload

See `gulpfile.js` for more `gulp` commands.