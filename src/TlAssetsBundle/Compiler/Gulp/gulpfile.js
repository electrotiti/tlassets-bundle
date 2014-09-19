var gulp = require('gulp');
var gutil = require('gulp-util');
var less = require('gulp-less');
var rename = require("gulp-rename");
//var watch = require('gulp-watch');
var argv = require('yargs').argv;
//var Stream = require("stream");
var plumber = require("gulp-plumber");
var concat = require('gulp-concat');
//var JSONStream = require('JSONStream');
var debug = require('gulp-debug');
var tap = require('gulp-tap');
var path = require('path');
var uglify = require('gulp-uglify');
var filesize = require('gulp-filesize');

var checkArgv = function()
{
    if(typeof argv.buffer == "undefined") {
        throw TypeError("Missing argument --buffer=");
    }
};

var compileAssets = function(assets)
{
    var needConcat = assets.filters.indexOf('concat') !== -1;

    var subCompile = function(assets, source, needConcat, dest)
    {

        gulp.src(source)
            .pipe(plumber({
                errorHandler: function (err) {
                    gutil.log(gutil.colors.red(err));
                    process.exit(1);
                }
            }))
            .pipe(
                assets.filters.indexOf('less') !== -1 && assets.type == 'stylesheet' ?
                    less({
                        cleancss : assets.filters.indexOf('minify') !== -1,
                        paths: [ path.join(__dirname, 'less', 'includes') ]
                    }) :
                    gutil.noop()
            )
            .pipe(
                assets.filters.indexOf('minify') !== -1 && assets.type == 'javascript' ?
                    uglify() :
                    gutil.noop()
            )
            .pipe(needConcat ? concat(assets.concatDest) : rename(dest))
            .pipe(gulp.dest(assets.rootWebPath))
            .pipe(typeof argv.verbose != "undefined" ? debug() : gutil.noop())
            .pipe(typeof argv.verbose != "undefined" ?
                tap(function(file,t){ gutil.log(gutil.colors.green('File created:'), file.path) }) :
                gutil.noop()
            )
            .pipe(typeof argv.verbose != "undefined" ? filesize() : gutil.noop());
    };


    if(needConcat) {

        var srcList = [];

        assets.files.forEach(function(file){
            srcList.push(file.src);
        });

        subCompile(assets, srcList, true);

    } else {
        assets.files.forEach(function(file){
            subCompile(assets, file.src, false, file.dest);
        });
    }
};

gulp.task('dump', function () {
    checkArgv();
    gulp.src(argv.buffer + (argv.buffer.substr(-5, 5) == '.json' ? '' : '*.json')).pipe(tap(function(file, t){
        var assets =  JSON.parse(file.contents.toString());
        compileAssets(assets);
    }));
});

gulp.task('default',['dump']);

//gulp.task('watch', function () {
//    gulp.watch('./web/bundles/**/less/*.less', ['default']);
//});
