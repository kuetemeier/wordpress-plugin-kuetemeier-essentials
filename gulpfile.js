/*
 * Gulp file for WordPress Plugin kuetemeier-essentials.
 *
 * Copyright 2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

'use strict';

var gulp = require('gulp');
var gulpif = require('gulp-if');
var zip = require('gulp-zip');
var fs = require('fs');
var rename = require('gulp-rename');

// styles
var cleanCSS = require('gulp-clean-css');
var sass = require('gulp-sass');

// scripts
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

// replace
var replace = require('gulp-replace');

var del = require('del');

// lint
var sassLint = require('gulp-sass-lint');


// get config
var pkg = JSON.parse(fs.readFileSync('./package.json'));

var scripts_sourcemaps = false;


// path informations
var paths = {
  styles: {
    src: 'assets-src/styles/**/*.scss',
    dest: 'assets/styles/'
  },
  scripts_admin: {
    src: 'assets-src/scripts/admin/**/*.js',
    dest: 'assets/scripts/'
  },
  scripts_public: {
    src: 'assets-src/scripts/public/**/*.js',
    dest: 'assets/scripts/'
  },
  images: {
  	src: 'assets-src/images/**/*',
  	dest: 'assets/images/'
  }
};


gulp.task('clean', function() {
	return del([ 'assets', 'phpdocs' ]);
})


gulp.task('replace_readme_txt', function() {
  return gulp.src(["./readme.txt"], {base: './'})
    .pipe(replace(/(Tags: )(.*)/, '$1' + pkg.wordpress.tags))
    .pipe(replace(/(Requires at least: )(.*)/, '$1' + pkg.wordpress.requires_at_least))
    .pipe(replace(/(Tested up to: )(.*)/, '$1' + pkg.wordpress.tested_up_to))
    .pipe(gulp.dest('./'))
})


gulp.task('replace_kuetemeier_essentials_php', function() {
  return gulp.src(["./kuetemeier-essentials.php"], {base: './'})
    .pipe(replace(/([v,V]ersion: )(.*)/, '$1' + pkg.version))
    .pipe(replace(/(Description: )(.*)/, '$1' + pkg.description))
    .pipe(replace(/('KUETEMEIER_ESSENTIALS_VERSION', ')(.*)(')/, '$1' + pkg.version + '$3'))
    .pipe(replace(/('KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION', ')(.*)(')/, '$1' + pkg.wordpress.minimal_php_version + '$3'))
    .pipe(gulp.dest('./'))
});


gulp.task('replace_readme_md', function() {
  return gulp.src(["./readme.md"], {base: './'})
    .pipe(replace(/(Description: )(.*)/, '$1' + pkg.description))
    .pipe(replace(/([v,V]ersion: )(.*)/, '$1' + pkg.version))
    .pipe(replace(/(Latest stable version: )(.*)/, '$1' + pkg.version_stable))
    .pipe(replace(/(Requires at least: )(.*)/, '$1' + pkg.wordpress.requires_at_least))
    .pipe(replace(/(Tested up to: )(.*)/, '$1' + pkg.wordpress.tested_up_to))
    .pipe(replace(/(Minimum PHP Version: )(.*)/, '$1' + pkg.wordpress.minimal_php_version))
    .pipe(gulp.dest('./'))
})


gulp.task('replace_src_config_php', function() {
  return gulp.src(["./src/config.php"], {base: './'})
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


gulp.task('scripts-admin', function() {
  return gulp.src(paths.scripts_admin.src, { sourcemaps: scripts_sourcemaps })
    .pipe(gulpif(scripts_sourcemaps, sourcemaps.init()))
    .pipe(babel())
    .pipe(uglify())
    .pipe(concat('kuetemeier-essentials-admin.min.js'))
    .pipe(gulpif(scripts_sourcemaps, sourcemaps.write('.')))
    .pipe(gulp.dest(paths.scripts_admin.dest));
});


gulp.task('scripts-public', function() {
  return gulp.src(paths.scripts_public.src, { sourcemaps: scripts_sourcemaps })
    .pipe(gulpif(scripts_sourcemaps, sourcemaps.init()))
    .pipe(babel())
    .pipe(uglify())
    .pipe(concat('kuetemeier-essentials-public.min.js'))
    .pipe(gulpif(scripts_sourcemaps, sourcemaps.write('.')))
    .pipe(gulp.dest(paths.scripts_public.dest));
});


gulp.task('scripts', ['scripts-public', 'scripts-admin']);


gulp.task('styles', function() {
  return gulp.src(paths.styles.src)
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(rename({
      // basename: 'main',
      suffix: '.min'
    }))
    .pipe(gulp.dest(paths.styles.dest));
});


gulp.task('images', function() {
  return gulp.src(paths.images.src)
  	.pipe(gulp.dest(paths.images.dest));
  });


gulp.task('assets-index', function() {
  return gulp.src('./assets-src/index.php')
  	.pipe(gulp.dest('./assets/'));
});


gulp.task('watch', function() {
  scripts_sourcemaps = true;
  gulp.start('build-without-release')

  gulp.watch(paths.styles.src, ['styles']);

  gulp.watch(paths.scripts_admin.src, ['scripts-admin']);
  gulp.watch(paths.scripts_public.src, ['scripts-public']);

  gulp.watch(paths.images.src ['images']);

  gulp.watch([
  	'./package.json'
  ], function() {
	pkg = JSON.parse(fs.readFileSync('./package.json'));
	gulp.start('replace');
  });
});


gulp.task('sass-lint', function() {
  return gulp.src(paths.styles.src)
    .pipe(sassLint())
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError())
});


gulp.task('lint', ['sass-lint']);


gulp.task('assets', ['styles', 'scripts', 'images', 'assets-index']);


gulp.task('build-without-release', ['replace', 'assets']);


gulp.task('build', ['clean', 'build-without-release', 'zip']);


gulp.task('default', ['build-without-release']);
