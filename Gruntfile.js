module.exports = function (grunt) {
  'use strict'
  grunt.loadNpmTasks('grunt-standard')

  grunt.initConfig({
    standard: {
      app: {
        src: [
          '{assets/js/}*.js'
        ]
      }
    }
  })

  grunt.registerTask('default', [
    'standard'
  ])
}
