/*global module:false, require:true*/
module.exports = function(grunt) {
  // Project configuration.
  var config = {
    makepot: {
      target: {
          options: {
              domainPath: 'languages',          // Where to save the POT file.
              potHeaders: {
                  poedit: true,                 // Includes common Poedit headers.
                  'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
              },                                // Headers to add to the generated POT file.
              type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
              updateTimestamp: true             // Whether the POT-Creation-Date should be updated without other changes.
          }
      }
    }
  };

  grunt.initConfig(config);

  grunt.loadNpmTasks( 'grunt-wp-i18n' );
};