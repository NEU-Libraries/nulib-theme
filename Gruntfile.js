module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    compass: {                  // Task
        dist: {                   // Target
          options: {              // Target options
            sassDir: 'scss',
            cssDir: 'css',
            environment: 'production'
          }
        },
        development: {
          options: {
            sassDir: 'scss',
            cssDir: 'css',
            environment: 'development'
          }
        }
        docs:{
          options: {
            sassDir: 'scss',
            cssDir: 'docs/assets/css',
            environment: 'development' 
          }
        },
      },
  });

  
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-jekyll');
};