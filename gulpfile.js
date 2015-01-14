var gulp = require('gulp');
var zip = require('gulp-zip');

gulp.task('mv', function () {
    return gulp.src(['src/*','src/**/*'], {base: "./src"})
        .pipe(gulp.dest('./build/core_translation'));
});

gulp.task('zip', ['mv'], function () {
    return gulp.src(['build/**/*'], {base: "./build"})
        .pipe(zip('core_translation.zip'))
        .pipe(gulp.dest('./build'));
});

gulp.task('default', ['mv','zip']);