//ライブラリを変数に格納
const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
const changed = require('gulp-changed');

//画像圧縮
const paths = {
  srcDir : 'src',
  dstDir : 'dist'
}
//画像の圧縮タスク
gulp.task('imagemin', function(){
  let srcGlob = paths.srcDir + '/**/*.+(jpg|jpeg|png|gif)';
  let dstGlob = paths.dstDir;
  gulp.src(srcGlob)
    .pipe(changed(dstGlob))
    .pipe(imagemin([
      imagemin.gifsicle({interlaced: true}),
      imagemin.mozjpeg({progressive: true}),
      imagemin.optipng({optimizationLevel: 5})
    ]))
    .pipe(gulp.dest(dstGlob));
});
//画像圧縮の監視
gulp.task('watch', function(){
  gulp.watch(paths.srcDir + '/**/*', ['imagemin']);
});