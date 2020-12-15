<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「マイページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
$u_id = $_SESSION['user_id'];
$postData = getMyPost($u_id);
$user = getUser($u_id);

debug('$postDataの中身：'.print_r($postData, true));
debug('$userの中身：'.print_r($user, true));
debug('画面表示処理終了-------------------');
?>

<!-- ここからHTML部分 -->
<?php
  $title = 'マイページ';
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

    <h2 class="ttl">投稿一覧</h2>
    <!-- メインコンテンツ -->
    <main class="main" style="margin-bottom:50px;">
      <section class="main__contents">
        <?php
          foreach($postData as $key => $val):
        ?>
        <a class="main__contents--item" href="post.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>">
          <div class="main__contents--head">
            <img src="<?php echo sanitize($val['pic']); ?>" alt="<?php echo sanitize($val['name']); ?>">
          </div>
          <div class="main__contents--body">
            <p><?php echo sanitize($val['comment']); ?></p>
          </div>
        </a>
        <?php
          endforeach;
        ?>
      </section>

      <section class="mypage__sidebar">
        <img src="<?php echo $user['pic'];?>" alt="プロフィール画像" class="mypage__sidebar--img" style="<?php if(empty($user['pic'])){echo 'display:none;';}?>">
        <p class="mypage__sidebar--name"><?php echo $user['username'].'さん';?></p>
        <a href="index.php" class="mypage__sidebar--item">トップページへ</a>
        <a href="profEdit.php" class="mypage__sidebar--item">プロフィールを編集する</a>
        <a href="post.php" class="mypage__sidebar--item">投稿する</a>
        <a href="withdraw.php" class="mypage__sidebar--item">退会する</a>
      </section>
    </main>


    <!-- footer -->
    <?php
      require('footer.php');
    ?>