var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var postcss = require('gulp-postcss')
var autoprefixer = require('autoprefixer')
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');

gulp.task('sass', () => {
	return gulp.src(['styles/sass/*.scss', '!styles/sass/_*.scss'])
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(postcss([
			autoprefixer({browsers: '> 0.05%'})
		]))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('styles/css'));
});
gulp.task('sass:watch', () => {
  return gulp.watch('styles/**/*.scss', gulp.series('sass'));
});

gulp.task('uglify', function () {
	return gulp.src(['scripts/*.js', '!scripts/*.min.js'])
		.pipe(sourcemaps.init())
		.pipe(uglify())
		.pipe(sourcemaps.write('.'))
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('scripts/'));
});

gulp.task('uglify:watch', function () {
	return gulp.watch(['scripts/*.js', '!scripts/*.min.js'], gulp.series('uglify'));
});

gulp.task('build', gulp.parallel(
	'sass',
	'uglify'
));
gulp.task('build:watch', gulp.series(
  'build',
  gulp.parallel(
    'sass:watch',
    'uglify:watch'
  )
));
