module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    less: {
      development: {
        options: {
          paths: ["site/assets/css"],
        },
        files: {
          "site/assets/css/text.css": "site/assets/css/text.less",
          "site/assets/css/layout.css": "site/assets/css/layout.less",
        }
      },
    },

    concat : {
      dist : {
        src : [
          'site/assets/css/normalize.css',
          'site/assets/css/text.css',
          'site/assets/css/layout.css',
          'site/assets/css/style.css'
        ],
        dest : 'site/assets/css/styles.css',
      }
    },

    autoprefixer: {
      options: {
        options: {
          browsers:  ['> 1%']
        }
      },
        single_file: {
          options: {},
          src: 'site/assets/css/styles.css',
          dest: 'site/assets/css/styles.css'
      },
    },
    
    cssmin : {
      options : {
        banner: '/* Minified styles. Created with Grunt for <%= pkg.name %> (www.flexyadmin.com) <%= grunt.template.today("yyyy-mm-dd") %> */',
        keepSpecialComments:0
      },
      minify: {
        expand: true,
        cwd: 'site/assets/css/',
        src: ['styles.css', '!*.min.css'],
        dest: 'site/assets/css/',
        ext: '.min.css'
      }
    },
    
    clean: ["site/assets/css/styles.css"],
    
    watch: {
      styles: {
        options: { spawn: false },
        files: [ "site/assets/css/*.css","site/assets/css/*.less" ],
        tasks: [ "less","concat", "autoprefixer", "cssmin", "clean" ],
      }
    },
    
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-clean');
  // grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // the default task can be run just by typing "grunt" on the command line
  grunt.registerTask('default', ['watch']);
};