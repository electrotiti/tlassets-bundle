var gulp = require('gulp');
var gutil = require('gulp-util');
var less = require('gulp-less');
var rename = require("gulp-rename");
var argv = require('yargs').argv;
var plumber = require("gulp-plumber");
var concat = require('gulp-concat');
var debug = require('gulp-debug');
var tap = require('gulp-tap');
var path = require('path');
var uglify = require('gulp-uglify');
var filesize = require('gulp-filesize');
var gulpif = require('gulp-if');
var lazypipe = require('lazypipe');


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

        // Init some boolean
        var isLess = assets.filters.indexOf('less') !== -1 && assets.type == 'stylesheet';
        var showDebug = typeof argv.verbose != "undefined";
        var needMinify = assets.filters.indexOf('minify') !== -1;
        var needMinifyJs = needMinify && assets.type == 'javascript';

        // Less channel definition
        var lessChannel = lazypipe()
            .pipe(less,{
                cleancss : needMinify/*,
                paths: [ path.join(__dirname, 'less', 'includes') ]*/
            });

        // Javascript channel definition
        var jsChannel = lazypipe().pipe(uglify);


        //// Debug channel definition
        var debugChannel = lazypipe()
            .pipe(debug)
            .pipe(tap,function(file,t){
                gutil.log(gutil.colors.green('File created:'), file.path)
            })
            .pipe(filesize);


            /* Compiler main flow*/
            gulp.src(source)

            // Prevent crash of script : when an error occur this display error and stop script
            .pipe(plumber({
                errorHandler: function (err) {
                    gutil.log(gutil.colors.red(err));
                    process.exit(1);
                }
            }))

            //Compile Less file to css file
            .pipe(gulpif(isLess,lessChannel()))

            // Compile javascript (Minify, etc.)
            .pipe(gulpif(needMinifyJs,jsChannel()))

            // Concat all file together or just move new file to the destination folder with new name
            .pipe(needConcat ? concat(assets.concatDest) : rename(dest))

            // Destination folder of compiled files
            .pipe(gulp.dest(assets.rootWebPath))

            // Show more log info, if debug is enable
            .pipe(gulpif(showDebug,debugChannel()));
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

gulp.task('compile', function () {
    checkArgv();
    gulp.src(argv.buffer + (argv.buffer.substr(-5, 5) == '.json' ? '' : '*.json')).pipe(tap(function(file, t){
        var assets =  JSON.parse(file.contents.toString());
        compileAssets(assets);
    }));
});

gulp.task('default',['compile']);

//gulp.task('watch', function () {
//    gulp.watch('./web/bundles/**/less/*.less', ['default']);
//});
