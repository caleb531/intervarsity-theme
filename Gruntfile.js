// Config file for Grunt, which enables automatic style/script compilation
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				cacheLocation: 'styles/sass/.sass-cache'
			},
			external: {
				options: {
					sourcemap: 'file'
				},
				files: {
					'styles/css/site.css': 'styles/sass/site.scss',
					'styles/css/editor.css': 'styles/sass/editor.scss',
				}
			},
			internal: {
				options: {
					sourcemap: 'inline'
				},
				files: {
					'styles/css/customizations.css': 'styles/sass/customizations.scss',
					'styles/css/maintenance.css': 'styles/sass/maintenance.scss'
				}
			}
		},

		postcss: {
			options: {
				map: true,
				processors: [
					require('autoprefixer')({
						browsers: '> 0.1%'
					})
				]
			},
			styles: {
				src: 'styles/css/*.css'
			}
		},

		uglify: {
			options: {
				sourceMap: true
			},
			scripts: {
				files: {
					'scripts/customize-preview.min.js': 'scripts/customize-preview.js',
					'scripts/site.min.js': 'scripts/site.js'
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
					'sass',
					'postcss'
				]
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('build', [
		'sass',
		'postcss',
		'uglify'
	]);

	grunt.registerTask('serve', [
		'build',
		'watch'
	]);

};
