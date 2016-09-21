var gulp = require('gulp');

gulp.task('move', function () {
    gulp.src('assets/**/*')
        .pipe(gulp.dest('../../../../public/extensions/local/rossriley/formeditor/'));
});


gulp.task('default', function () {
    gulp.watch('assets/**/*', ['move']);
});

