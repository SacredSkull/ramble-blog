var gulp = require('gulp');
var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var ignore = require('gulp-ignore');
var order = require("gulp-order");
var gm = require('gulp-gm');
var plumber = require('gulp-plumber');
var browserSync = require('browser-sync').create();
var sourcemaps = require('gulp-sourcemaps');
var csso = require('gulp-csso');
var browserSync = require('browser-sync').create();

gulp.task('lint', function() {
    return gulp.src('js/app/*.js')
        .pipe(plumber())
        .pipe(jshint({
            laxcomma: true
        }))
        .pipe(jshint.reporter('default'));
});

// Compile Our Sass
gulp.task('sass', function() {
    gulp.src('scss/*.scss')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({
            // includePaths: require('node-bourbon').with('other/path', 'another/path')
            // - or -
            includePaths: [
                require('node-bourbon').includePaths,
                require('bourbon-neat').includePaths
            ],
            //outputStyle: 'compressed',
        }))
        .pipe(csso({
            //sourceMap: true,
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Public/css'))
        .pipe(browserSync.stream());
    // return gulp.src(['Public/css/*.css', '!Public/css/*.min.css'])
    //     .pipe(csso({
    //         //sourceMap: true,
    //     }))
    //     .pipe(gulp.dest('Public/css/min'));
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src(['js/**/*.js'])
        .pipe(plumber())
        .pipe(order([
            'lib/*.js',
            'app/*.js'
        ], {base: 'js'}))
        .pipe(sourcemaps.init())
        .pipe(concat('all.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('Public/js'))
        .pipe(browserSync.stream());
});

gulp.task('watch', ['browser-sync'], function() {
    gulp.watch('js/app/*.js', function() {
        setTimeout(function () {
            gulp.start('lint');
        }, 150);
    });
    gulp.watch(['js/**/*.js'], function() {
        setTimeout(function () {
            gulp.start('scripts');
        }, 150);
    });
    gulp.watch('scss/**/*.scss', function() {
        setTimeout(function () {
            gulp.start('sass');
        }, 150);
    });
    gulp.watch('Public/img/skull.svg', function() {
        setTimeout(function () {
            gulp.start('skull');
        }, 1000);
    });
});

gulp.task('browser-sync', function() {
    browserSync.init({
        proxy: "blog.io"
    });
    gulp.watch("src/Ramble/Templates/*.twig").on('change', browserSync.reload);
});

// Convert SVG to jpg
gulp.task('skull', function() {
    return gulp.src('Public/img/skull.svg')
        .pipe(plumber())
        .pipe(gm(function(skull){
            return skull.setFormat('png')
                        .resize(450, 450);
        }))
        .pipe(gulp.dest('Public/img'));
});

// Default Task
gulp.task('default', ['lint', 'sass', 'scripts', 'watch', 'skull']);

// Default Task
gulp.task('batch', ['sass', 'scripts', 'skull']);
