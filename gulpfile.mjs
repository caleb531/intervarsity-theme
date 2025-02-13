import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import * as dartSass from 'sass';
import esbuild from 'gulp-esbuild';
import rename from 'gulp-rename';

const sass = gulpSass(dartSass);

gulp.task('styles', () => {
	return gulp.src(['styles/sass/*.scss', '!styles/sass/_*.scss'])
		.pipe(sass({outputStyle: 'compressed', sourcemaps: true}).on('error', sass.logError))
		.pipe(gulp.dest('styles/css'));
});
gulp.task('sass:watch', () => {
  return gulp.watch('styles/**/*.scss', gulp.series('styles'));
});

gulp.task('scripts', function () {
	return gulp.src(['scripts/*.js', '!scripts/*.min.js'])
		.pipe(esbuild({ sourcemap: true }))
		.pipe(rename({suffix: '.min'}))
		.pipe(gulp.dest('scripts'));
});

gulp.task('scripts:watch', function () {
	return gulp.watch(['scripts/*.js', '!scripts/*.min.js'], gulp.series('scripts'));
});

gulp.task('build', gulp.parallel(
	'styles',
	'scripts'
));
gulp.task('build:watch', gulp.series(
  'build',
  gulp.parallel(
    'sass:watch',
    'scripts:watch'
  )
));
