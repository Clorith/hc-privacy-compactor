/*jslint node: true */
"use strict";

var $            = require( "gulp-load-plugins" )();
var argv         = require( "yargs" ).argv;
var gulp         = require( "gulp" );
var browserSync  = require( "browser-sync" ).create();
var cleanCSS     = require( "gulp-clean-css" );
var concat       = require( "gulp-concat" );
var merge        = require( "merge-stream" );
var sequence     = require( "run-sequence" );
var del          = require( "del" );
var uglify       = require( "gulp-uglify" );
var pump         = require( "pump" );
var sass         = require( "gulp-sass" );
var sourcemaps   = require( "gulp-sourcemaps" );
var autoprefixer = require( "gulp-autoprefixer" );
var zip          = require( "gulp-zip" );
var config       = require( "./config.json" );

gulp.task( "javascript", function() {
    return sequence( [ 'javascript:frontend', 'javascript:backend'] );
});

gulp.task( "javascript:frontend", function() {
    return pump([
        gulp.src( config.paths.javascript.frontend )
            .pipe( concat( config.project.slug + '.js' ) ),
        gulp.dest( config.dev.destination + 'assets/js' )
    ]);
});

gulp.task( "javascript:backend", function() {
    return pump([
        gulp.src( config.paths.javascript.backend )
            .pipe( concat( 'backend.js' ) ),
        gulp.dest( config.dev.destination + 'assets/js' )
    ]);
});

gulp.task( 'css', function() {
    return gulp.src( config.paths.css )
        .pipe( sourcemaps.init() )
        .pipe( sass().on( 'error', sass.logError ) )
        .pipe( sourcemaps.write() )
        .pipe( autoprefixer() )
        .pipe( concat( 'style.css' ) )
        .pipe( gulp.dest( config.dev.destination + 'assets/css' ) )
        .pipe( browserSync.stream() );
});

gulp.task( 'publish:prepare', function() {
    del( [ config.project.zip ] );

    var publish_css = gulp.src( config.dev.destination + 'style.css' )
        .pipe( cleanCSS() )
        .pipe( gulp.dest( config.dev.destination ) );

    var publish_js = pump([
        gulp.src( config.paths.javascript.frontend )
            .pipe( concat( config.project.slug + '.js' ) ),
        uglify(),
        gulp.dest( config.dev.destination + 'assets/js' )
    ]);

    var publish_backend_js = pump([
        gulp.src( config.paths.javascript.backend )
            .pipe( concat( 'backend.js' ) ),
        uglify(),
        gulp.dest( config.dev.destination + 'assets/js' )
    ]);

    return merge( publish_css, publish_js, publish_backend_js );
});

gulp.task( 'publish:copy', function() {
    return gulp.src( [ config.dev.destination + '**' ] )
        .pipe( gulp.dest( config.project.slug ) );
});

gulp.task( 'publish:make:zip', function() {
    return gulp.src(
        [ config.project.slug + '/**' ],
        {
            base : '.'
        } )
        .pipe( zip( config.project.zip ) )
        .pipe( gulp.dest( '.' ) );
});

gulp.task( 'publish:cleanup', function() {
    return del( [ config.project.slug + '/**' ] );
});

gulp.task( 'publish', function() {
    sequence( 'publish:prepare', 'publish:copy', 'publish:make:zip', 'publish:cleanup' );
});

gulp.task( 'copy', function() {
    sequence( [ 'copy:php' ] )
});

gulp.task( 'copy:php', function() {
    return gulp.src( 'src/**' )
        .pipe( gulp.dest( config.dev.destination ) );
});

gulp.task( 'copy:i18n', function() {
    return gulp.src( 'translations/**' )
        .pipe( gulp.dest( config.dev.translations ) );
});

gulp.task( "build", [ "clean:all" ], function(done) {
    sequence( 'copy', [ "css", "javascript" ], done );
});

gulp.task( "clean:all", function() {
    return del([
        config.dev.destination + "**"
    ]);
});

gulp.task( "clean:javascript", function() {
    return del([
        config.dev.destination + "assets/js/"
    ]);
});

gulp.task( "clean:css", function() {
    return del([
        config.dev.destination + "assets/css/"
    ]);
});


gulp.task( "watch", [ 'css', 'copy', 'javascript' ], function() {
    browserSync.init({
        proxy : config.dev.proxy
    });

    gulp.watch( 'src/**', [ 'copy' ] ).on('change', browserSync.reload);

    gulp.watch( config.paths.javascript.frontend, [ 'javascript:frontend' ] ).on('change', browserSync.reload);

    gulp.watch( config.paths.javascript.backend, [ 'javascript:backend' ] ).on('change', browserSync.reload);

    gulp.watch( config.paths.watch.css, [ 'css' ] );
});