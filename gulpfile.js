// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var ignore = require('gulp-ignore');
var order = require("gulp-order");
var gm = require('gulp-gm');
var plumber = require('gulp-plumber');
var livereload = require('gulp-livereload');
var test = require('bourbon-neat');

console.log(test.includePaths);

// Lint Task
gulp.task('lint', function() {
    return gulp.src('Public/include/js/app/*.js')
        .pipe(plumber())
        .pipe(jshint({
            laxcomma: true
        }))
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function() {
    return gulp.src('Public/include/scss/*.scss')
        .pipe(plumber())
        .pipe(sass({
            // includePaths: require('node-bourbon').with('other/path', 'another/path')
            // - or -
            includePaths: [
                require('node-bourbon').includePaths,
                require('bourbon-neat').includePaths
            ]
        }))
        .pipe(gulp.dest('Public/include/css'))
        .pipe(livereload());
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src(['Public/include/js/**/*.js', '!Public/include/js/out/*.js'])
        .pipe(plumber())
        .pipe(order([
            'lib/*.js',
            'app/*.js',
        ], {base: 'Public/include/js/minimal'}))
        .pipe(concat('all.js'))
        .pipe(gulp.dest('Public/include/js/out'))
        .pipe(rename('all.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('Public/include/js/out'))
        .pipe(livereload());
});

// Watch Files For Changes
gulp.task('watch', function() {
    livereload.listen({host: "0.0.0.0"});
    gulp.watch('Public/include/js/app/*.js', function() {
        setTimeout(function () {
            gulp.start(['lint', 'scripts']);
        }, 500);
    });
    gulp.watch(['Public/include/js/lib/*.js', 'Public/include/js/lib/min/*.js'], function() {
        setTimeout(function () {
            gulp.start('scripts');
        }, 500);
    });
    gulp.watch('Public/include/scss/**/*.scss', function() {
        setTimeout(function () {
            gulp.start('sass');
        }, 500);
    });
    gulp.watch('Public/include/img/skull.svg', function() {
        setTimeout(function () {
            gulp.start('skull');
        }, 2000);
    });
});

gulp.task('skull', function() {
    return gulp.src('Public/include/img/skull.svg')
        .pipe(plumber())
        .pipe(gm(function(skull){
            return skull.setFormat('jpg')
                        .resize(450, 450);
        }))
        .pipe(gulp.dest('Public/include/img'));
});

// Default Task
gulp.task('default', ['lint', 'sass', 'scripts', 'watch', 'skull']);

// Default Task
gulp.task('batch', ['sass', 'scripts', 'skull']);
