'use strict';

var gulp = require('gulp');
var zip = require('gulp-zip');
var fs = require('fs');

// styles
var minifycss = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');
var sass = require('gulp-sass');

// replace
var replace = require('gulp-replace');

// get config
var pkg = JSON.parse(fs.readFileSync('./package.json'));

// more information for
gulp.task('replace_readme_txt', function() {
  gulp.src(["./readme.txt"], {base: './'})
    .pipe(replace(/(Tags: )(.*)/, '$1' + pkg.wordpress.tags))
    .pipe(replace(/(Requires at least: )(.*)/, '$1' + pkg.wordpress.requires_at_least))
    .pipe(replace(/(Tested up to: )(.*)/, '$1' + pkg.wordpress.tested_up_to))
    .pipe(gulp.dest('./'))
})

gulp.task('replace_kuetemeier_essentials_php', function() {
  gulp.src(["./kuetemeier-essentials.php"], {base: './'})
    .pipe(replace(/([v,V]ersion: )(.*)/, '$1' + pkg.version))
    .pipe(replace(/(Description: )(.*)/, '$1' + pkg.description))
    .pipe(replace(/('KUETEMEIER_ESSENTIALS_VERSION', ')(.*)(')/, '$1' + pkg.version + '$3'))
    .pipe(replace(/('KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION', ')(.*)(')/, '$1' + pkg.wordpress.minimal_php_version + '$3'))
    .pipe(gulp.dest('./'))
});

gulp.task('replace_readme_md', function() {
  gulp.src(["./readme.md"], {base: './'})
    .pipe(replace(/(Description: )(.*)/, '$1' + pkg.description))
    .pipe(replace(/([v,V]ersion: )(.*)/, '$1' + pkg.version))
    .pipe(replace(/(Latest stable version: )(.*)/, '$1' + pkg.version_stable))
    .pipe(replace(/(Requires at least: )(.*)/, '$1' + pkg.wordpress.requires_at_least))
    .pipe(replace(/(Tested up to: )(.*)/, '$1' + pkg.wordpress.tested_up_to))
    .pipe(replace(/(Minimum PHP Version: )(.*)/, '$1' + pkg.wordpress.minimal_php_version))
    .pipe(gulp.dest('./'))
})

gulp.task('replace_src_config_php', function() {
  gulp.src(["./src/config.php"], {base: './'})
    .pipe(replace(/(PLUGIN_VERSION = ')(.*)(')/, '$1' + pkg.version + '$3'))
    .pipe(replace(/(PLUGIN_VERSION_STABLE = ')(.*)(')/, '$1' + pkg.version_stable + '$3'))
    .pipe(gulp.dest('./'))
});

gulp.task('replace', ['replace_readme_txt', 'replace_kuetemeier_essentials_php', 'replace_readme_md', 'replace_src_config_php']);

gulp.task('zip', function () {
  return gulp.src([
	'./{src,assets,languages}/**/*',
	'./index.php',
	'./LICENSE.txt',
	'./readme.txt',
	'./uninstall.php',
	'./kuetemeier-essentials.php',
	'!./languages/*backup*',
  ])
	.pipe(zip('kuetemeier-essentials.zip'))
	.pipe(gulp.dest('./release/'));
});

gulp.task('js', function() {
});

gulp.task('sass', function () {
  return gulp.src('./sass/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./css'));
});

gulp.task('sass:watch', function () {
  gulp.watch('./sass/**/*.scss', ['sass']);
});

gulp.task('styles', function() {

});

gulp.task('watch', ['styles'], function() {
  gulp.watch([
    './app/assets/sass/**/*.scss',
    './app/modules/**/*.scss'
  ], ['styles']);
});


gulp.task('build-without-release', ['replace'])

gulp.task('build', ['build-without-release', 'zip']);

gulp.task('default', ['build-without-release']);
