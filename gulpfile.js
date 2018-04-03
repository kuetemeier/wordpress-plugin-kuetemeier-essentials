var gulp = require('gulp');
var zip = require('gulp-zip');

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
