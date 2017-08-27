// Config file for Grunt, which enables automatic style/script compilation
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				sourcemap: 'file'
			},
			all: {
				files: [{
					expand: true,
					cwd: 'styles/sass',
					src: '*.scss',
					dest: 'styles/css',
					ext: '.css'
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
					cwd: 'scripts',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'scripts',
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
