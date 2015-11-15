module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				sourcemap: 'none',
				cacheLocation: 'styles/sass/.sass-cache'
			},
			styles: {
				files: {
					'styles/css/site.css': 'styles/sass/site.scss',
					'styles/css/customizations.css': 'styles/sass/customizations.scss',
					'styles/css/editor.css': 'styles/sass/editor.scss',
					'styles/css/maintenance.css': 'styles/sass/maintenance.scss'
				}
			}
		},

		uglify: {
			options: {
				sourceMap: false
			},
			scripts: {
				files: {
					'scripts/customize-preview.min.js': 'scripts/customize-preview.js',
					'scripts/site.min.js': 'scripts/site.js',
				}
			}
		},

		watch: {
			scripts: {
				files: [
					'scripts/site.js',
					'scripts/customize-preview.js'
				],
				tasks: [
					'uglify'
				]
			},
			styles: {
				files: [
					'styles/sass/*.scss'
				],
				tasks: [
					'sass:styles'
				]
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

};
