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

// Lint Task
gulp.task('lint', function() {
    return gulp.src('include/js/minimal/app/*.js')
        .pipe(jshint({
            laxcomma: true
        }))
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function() {
    return gulp.src('include/scss/*.scss')
        .pipe(plumber())
        .pipe(sass({
            // includePaths: require('node-bourbon').with('other/path', 'another/path')
            // - or -
            includePaths: require('node-bourbon').includePaths
        }))
        .pipe(gulp.dest('include/css'));
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src(['include/js/minimal/**/*.js', '!include/js/minimal/out/*.js'])
        .pipe(order([
            'lib/*.js',
            'app/*.js',
        ], {base: 'include/js/minimal'}))
        .pipe(concat('all.js'))
        .pipe(gulp.dest('include/js/minimal/out'))
        .pipe(rename('all.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('include/js/minimal/out'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('include/js/minimal/app/*.js', function() {
        setTimeout(function () {
            gulp.start(['lint', 'scripts']);
        }, 500);
    });
    gulp.watch(['include/js/minimal/lib/*.js', 'include/js/minimal/lib/min/*.js'], function() {
        setTimeout(function () {
            gulp.start('scripts');
        }, 500);
    });
    gulp.watch('include/scss/*.scss', function() {
        setTimeout(function () {
            gulp.start('sass');
        }, 500);
    });
    gulp.watch('include/img/skull.svg', function() {
        setTimeout(function () {
            gulp.start('skull');
        }, 2000);
    });
});

gulp.task('skull', function() {
    return gulp.src('include/img/skull.svg')
    // .pipe(plumber())
    .pipe(gm(function(skull){
        return skull.setFormat('jpg')
                    .resize(450, 450);
    }))
    .pipe(gulp.dest('include/img'));
});

// Default Task
gulp.task('default', ['lint', 'sass', 'scripts', 'watch', 'skull']);

// Default Task
gulp.task('batch', ['sass', 'scripts', 'skull']);
