module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    compass: {                  // Task
      options: {
        config: 'config.rb',
        sassDir: 'scss',
        cssDir: 'css',
      },

      dist: {                   // Target
          options: {              // Target options
            environment: 'production',
            outputStyle: 'compressed',
            force: true
          }
        },
        devel: {
          options: {
            environment: 'development',
            outputStyle: 'expanded',
            debugInfo: true
          }
        },

    },
    watch: {
      styles: {
        files: ['scss/**'],
        tasks: ['compass:devel']
      },

    },
  });

  grunt.registerTask('default',['watch:styles']);
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-jekyll');
};
