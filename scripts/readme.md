# FlexyAdmin Scripts

A collections of scripts that can run in the terminal.
All use PHP.

## Running a script

The scripts are written in PHP, so running a script works like this:

`php script.php`

## Scripts

`php hello.php` - Simple hello world (for testing)
`php phpinfo.php` - Raw PHP info

These need to be run from root:

'php scripts/old_install.php` - Change install from safe folder structure to normal.
'php scripts/safe_install.php` - Change install from old folder structure to safe folder structure.


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