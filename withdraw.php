<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「退会ページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //SQL文
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
    $sql2 = 'UPDATE post SET delete_flg = 1 WHERE user_id = :us_id';
    //データ流し込み
    $data = array(':us_id' => $_SESSION['user_id']);
    //クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    //クエリ成功の場合
    if($stmt1 && $stmt2){
      //セッション削除
      session_destroy();
      debug('セッション変数の中身：' .print_r($_SESSION, true));
      debug('退会成功：トップページへ');
      header("Location:signup.php");
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了********************');
?>


<!-- ここからHTML部分 -->
<?php
  $title = '退会ページ';
  require('head.php');
?>

  <body>
    <!-- ヘッダー -->
    <?php
      require('header.php');
    ?>


    <!-- メインコンテンツ -->
    <main>
      <div class="withdraw">
        <form method="POST" class="withdraw__form">
          <h2 class="withdraw__form--ttl">退会画面</h2>
          <div class="area-message">
            <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <div class="withdraw__form--item">
            <input type="submit" value="退会する" name="submit">
          </div>
        </form>
      </div>
      <a href="mypage.php" class="link__txt">&lt; マイページへ戻る</a>
    </main>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>