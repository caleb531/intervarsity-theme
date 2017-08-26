var path = require('path');

// Config file for Grunt, which enables automatic style/script compilation
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				cacheLocation: 'styles/sass/.sass-cache'
			},
			all: {
				options: {
					sourcemap: 'file'
				},
				files: [{
					expand: true,
					cwd: '.',
					src: ['styles/sass/*.scss'],
					rename: function (dest, src) {
						return path.join(
							path.dirname(path.dirname(src)),
							'css',
							path.basename(src).replace('.scss', '.css')
						);
					}
				}]
			}
		},

		postcss: {
			options: {
				map: true,
				processors: [
					require('autoprefixer')({
						browsers: '> 0.05%'
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
			all: {
				files: [{
					expand: true,
					cwd: '.',
					src: [
						'scripts/*.js',
						'!scripts/*.min.js'
					],
					ext: '.min.js'
				}]
			}
		},

		watch: {
			scripts: {
				files: [
					'scripts/*.js',
					'!scripts/*.min.js'
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
