module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    compass: {                  // Task


        options: {
          //config: 'config.rb
          sassDir: 'scss',
          cssDir: 'css',
        },
        dist: {                   // Target
          options: {              // Target options
            environment: 'production'
          }
        },
        devel: {
          options: {
            environment: 'development'
          }
        },
        docs:{
          options: {
            environment: 'development'
          }
        },
      },
    watch: {
      styles: {
        files: ['scss/**'],
        tasks: ['comapss:devel']
      },

    },
  });

  grunt.registerTask('default',['compass:devel']);
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-jekyll');
};
