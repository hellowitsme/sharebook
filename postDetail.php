<?php
//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「投稿詳細ページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//投稿IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('$p_idの中身'.print_r($p_id,true));
//投稿データ取得
$viewData = getPostOne($p_id);
debug('$viewDataの中身：'.print_r($viewData,true));
//DBからユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);
debug('ユーザー情報：'.print_r($dbFormData,true));
//不正値チェック
if(empty($viewData)){
  error_log('不正な値が入力されました');
  header("Location:index.php");
}
debug('取得したデータ：'.print_r($viewData, true));
debug('画面表示処理終了********************');
?>


<!-- ここからHTML部分 -->
<?php
  $title = '投稿詳細';
  require('head.php');
?>

  <body>
    <!-- ヘッダー -->
    <?php
      require('header.php');
    ?>

    <!-- セッションフラッシュ -->
    <p id="js-show-msg" style="display:none;" class="msg__slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <main class="detail">
      <div class="detail__user">
        <p><?php if($viewData['user_id'] == $dbFormData['id']) echo '投稿者：'.$dbFormData['username']; ?></p>
      </div>
      <div class="detail__ttl">
        <p><?php echo '本のタイトル：'.sanitize($viewData['name']); ?></p>
      </div>
      <div class="detail__img">
        <img src="<?php echo showImg(sanitize($viewData['pic'])); ?>" alt="<?php echo sanitize($viewData['name']);?>">
      </div>
      <div class="detail__comment">
        <p>本の内容：</p>
        <p><?php echo sanitize($viewData['comment']); ?></p>
      </div>
      <div class="detail__link">
        <a href="index.php<?php echo appendGetParam(array('p_id'));?>">&lt; 投稿一覧に戻る</a>
      </div>
    </main>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>