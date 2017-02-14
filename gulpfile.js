var gulp = require('gulp')
var sass = sass = require('gulp-sass')
var copy = copy = require('gulp-copy')
var minify = require('gulp-minify')
var concat = require('gulp-concat')

const RESOURCES_PATH = 'app/Resources'
const COMPILED_PATH = 'web'

gulp.task('scss', function () {
  return gulp.src(RESOURCES_PATH + '/scss/*.scss')
              .pipe(sass({sourceComments: 'map', errLogToConsole: true}))
              .on('error', function (err) {
                  console.log(err.toString());

                  this.emit('end');
              })
              .pipe(gulp.dest(COMPILED_PATH + '/css'))

})

gulp.task('fonts', function () {
  return gulp.src(RESOURCES_PATH + '/fonts/*')
              .pipe(copy(COMPILED_PATH + '/fonts', {prefix: 7}))
})

gulp.task('js', function () {
  return gulp.src(RESOURCES_PATH + '/js/*.js')
              .pipe(minify({
                ext: {
                  src: '-debug.js',
                  min: '.js'
                },
                ignoreFiles: []
              }))
              .pipe(concat('main.js'))
              .pipe(gulp.dest(COMPILED_PATH + '/js'))
})

gulp.task('watch', function () {
  gulp.watch(RESOURCES_PATH + '/scss/*.scss', ['scss'])
  gulp.watch(RESOURCES_PATH + '/js/*.js', ['js'])
  gulp.watch(RESOURCES_PATH + '/fonts/*', ['fonts'])
})

gulp.task('default', ['scss', 'fonts', 'js'])
