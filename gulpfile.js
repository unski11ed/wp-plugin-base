var gulp = require('gulp');
var path = require('path');
var replace = require('@yodasws/gulp-pattern-replace');
var mkdirp = require('mkdirp');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var cleanCss = require('gulp-clean-css');
var terser = require('gulp-terser');
var rollup = require('gulp-better-rollup');
var babel = require('rollup-plugin-babel');
var resolve = require('rollup-plugin-node-resolve');
var commonjs = require('rollup-plugin-commonjs');
var exec = require('child_process').exec;
var del = require('del');
var exitHook = require('exit-hook');
var util = require('util');
var sourcemaps = require('gulp-sourcemaps');

var appConfig = require('./plugin-configuration.json');

// CONSTS ======================================
var PLUGIN_SRC_DIR = path.resolve(__dirname, 'src');
var PLUGIN_DEST_DIR = path.resolve(__dirname, 'dist', appConfig.executableName);
var NODE_MODULES_DIR = path.resolve(__dirname, 'node_modules');

// BACKEND =====================================
gulp.task('backend:clean', function() {
    return del([
        path.resolve(PLUGIN_DEST_DIR, 'backend', '**', '*.php'),
    ]);
});

gulp.task('backend:code', function() {
    return gulp.src([
            path.resolve(PLUGIN_SRC_DIR, 'backend', '**', '*.php')
        ])
        .pipe(replace(/__PluginNamespace__/g, appConfig.namespace))
        .pipe(replace(/__PluginName__/g, appConfig.name))
        .pipe(replace(/__PluginDescription__/g, appConfig.description))
        .pipe(replace(/__PluginVersion__/g, appConfig.version))
        .pipe(replace(/__PluginAuthor__/g, appConfig.author))
        .pipe(
            gulp.dest(
                path.resolve(PLUGIN_DEST_DIR, 'backend'),
            )
        );
});

gulp.task('backend:executable', function() {
    return gulp.src([
            path.resolve(PLUGIN_SRC_DIR, 'wp-plugin.php')
        ])
        .pipe(replace(/__PluginNamespace__/g, appConfig.namespace))
        .pipe(replace(/__PluginName__/g, appConfig.name))
        .pipe(replace(/__PluginDescription__/g, appConfig.description))
        .pipe(replace(/__PluginVersion__/g, appConfig.version))
        .pipe(replace(/__PluginAuthor__/g, appConfig.author))
        .pipe(rename({ basename: appConfig.executableName }))
        .pipe(
            gulp.dest(
                path.resolve(PLUGIN_DEST_DIR)
            )
        );
});

gulp.task('backend', gulp.series([
    'backend:clean',
    gulp.parallel([
        'backend:code',
        'backend:executable'
    ])
]));


// FRONTEND ====================================
gulp.task('frontend:js', function() {
    return gulp.src([
        path.resolve(PLUGIN_SRC_DIR, 'frontend', 'js', 'main.js')
    ])
    .pipe(sourcemaps.init())
    .pipe(
        rollup(
            {
                plugins: [
                    resolve(),
                    commonjs(),
                    babel({
                        presets: ['@babel/preset-env'],
                    }),
                ]
            }, {
                format: 'umd'
            }
        ))
    .pipe(replace(/__PluginNamespace__/g, appConfig.namespace))
    .pipe(replace(/__PluginName__/g, appConfig.name))
    .pipe(replace(/__PluginDescription__/g, appConfig.description))
    .pipe(replace(/__PluginVersion__/g, appConfig.version))
    .pipe(replace(/__PluginAuthor__/g, appConfig.author))
    .pipe(gulp.dest(
        path.resolve(PLUGIN_DEST_DIR, 'frontend', 'js')
    ))
    .pipe(terser())
    .pipe(rename({
        extname: '.min.js'
    }))
    .pipe(sourcemaps.write(''))
    .pipe(gulp.dest(
        path.resolve(PLUGIN_DEST_DIR, 'frontend', 'js')
    ));
});

gulp.task('frontend:scss', function() {
    return gulp.src([
            path.resolve(PLUGIN_SRC_DIR, 'frontend', 'scss', 'main.scss'),
            path.resolve(PLUGIN_SRC_DIR, 'frontend', 'scss', 'admin.scss'),
        ])
        .pipe(sass({
            includePaths: [
                NODE_MODULES_DIR
            ]
        }))
        .pipe(gulp.dest(
            path.resolve(PLUGIN_DEST_DIR, 'frontend', 'css')
        ))
        .pipe(cleanCss())
        .pipe(rename({
            extname: '.min.css'
        }))
        .pipe(gulp.dest(
            path.resolve(PLUGIN_DEST_DIR, 'frontend', 'css')
        ));
});

gulp.task('frontend:img', function() {
    return gulp.src([
            path.resolve(PLUGIN_SRC_DIR, 'frontend', 'img', '**', '*.*')
        ])
        .pipe(gulp.dest(
            path.resolve(PLUGIN_DEST_DIR, 'frontend', 'img')
        ));
});

gulp.task('frontend:templates', function() {
    return gulp.src([
            path.resolve(PLUGIN_SRC_DIR, 'frontend', 'templates', '**', '*.*')
        ])
        .pipe(gulp.dest(
            path.resolve(PLUGIN_DEST_DIR, 'frontend', 'templates')
        ));
});

gulp.task('frontend', gulp.parallel([
    'frontend:js',
    'frontend:scss',
    'frontend:img',
    'frontend:templates'
]));

// UTILITIES ====================================
gulp.task('utility:permissions-data', function(cb) {
    exec('sudo chown -R www-data:www-data ./dist', function(err, stdout, stderr) {
        cb(err);
    });
});

gulp.task('utility:permissions-user', function(cb) {
    exec('sudo chown -R 1000:1000 ./dist', function(err, stdout, stderr) {
        cb(err);
    });
});

// BUILD ========================================
gulp.task('build', gulp.parallel([
    'backend',
    'frontend',
]));

// WATCHER ======================================
gulp.task('watch', function() {
    // Frontend
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'frontend', 'js', '**', '*.js')],
        gulp.series('frontend:js', 'utility:permissions-data')
    );
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'frontend', 'scss', '**', '*.scss')],
        gulp.series('frontend:scss', 'utility:permissions-data')
    );
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'frontend', 'img', '**', '*.*')],
        gulp.series('frontend:img', 'utility:permissions-data')
    );
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'frontend', 'templates', '**', '*.html')],
        gulp.series('frontend:templates', 'utility:permissions-data')
    );
    // Backend
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'backend', '**', '*.php')],
        gulp.series('backend:clean', 'backend:code', 'utility:permissions-data')
    );
    gulp.watch(
        [path.resolve(PLUGIN_SRC_DIR, 'wp-plugin.php')],
        gulp.series('backend:executable', 'utility:permissions-data')
    );
});

gulp.task('utility:plugin-dir', function(cb) {
    mkdirp(PLUGIN_DEST_DIR, cb);
});

// DEVELOPMENT ==================================
gulp.task('docker:wordpress', function(cb) {
    exec('docker-compose up', function (err, stdout, stderr) {
        cb(err);
    });
});

gulp.task('dev', gulp.parallel([
    gulp.task('docker:wordpress'),
    gulp.series([
        gulp.task('utility:plugin-dir'),
        gulp.task('build'),
        gulp.task('utility:permissions-data'),
        gulp.task('watch')
    ])
]));

// CLEANUP ======================================
exitHook(function() {
    exec('./cleanup.sh');
});