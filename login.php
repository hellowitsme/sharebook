<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「ログインページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //未入力チェック
  validRequired($name, 'name');
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  //emailバリデーション
  validEmail($email, 'email');
  validMaxLen($email, 'email');

  //パスワードのバリデーション
  validHalf($pass, 'pass');
  validMaxLen($pass, 'pass');
  validMinLen($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションOK');

    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：' .print_r($result, true));

      //パスワードの照合
      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました');

        //ログイン有効期限
        $sesLimit = 60*60;
        $_SESSION['login_date'] = time();

        //ログイン保持にチェックがあった場合
        if($pass_save){
          debug('ログイン保持実行');
          //ログイン有効期限を30日にセット
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持しない');
          $_SESSION['login_limit'] = $sesLimit;
        }

        //ユーザーIDを格納
        $_SESSION['user_id'] = $result['id'];

        //メッセージ表示用
        if($stmt){
          $_SESSION['msg_success'] = SUC02;
        }

        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('マイページへ');
        header("Location:index.php");

      }else{
        debug('パスワードが合っていません');
        $err_msg['common'] = MSG09;
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了********************');
?>


<!-- ここからHTML部分 -->
<?php
  $title = 'ログイン';
  require('head.php');
?>

  <body>
    <!-- ヘッダー -->
    <?php
      require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <main>
      <div class="form">
        <form action="" method="POST" class="form__group">
          <h2 class="form__group--ttl">ログイン</h2>
          <div class="area-message">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>

          <!-- お名前入力 -->
          <div class="area-message">
            <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            <input class="form__group--item" type="text" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name'] ?>" placeholder="お名前" autocomplete="off">
          </label>

          <!-- email入力 -->
          <div class="area-message">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <input class="form__group--item" type="text" name="email"value="<?php if(!empty($_POST['email'])) echo $_POST['email'] ?>" placeholder="E-Mail" autocomplete="off">
          </label>

          <!-- パスワード入力 -->
          <div class="area-message">
            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            <input class="form__group--item" type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'] ?>" placeholder="パスワード" autocomplete="off">
          </label>

          <!-- ログイン保持 -->
          <label class="form__group--check">
            <input type="checkbox" name="pass_save">ログインしたままにする
          </label>

          <div class="form__group--btn">
            <input type="submit" value="ログイン">
          </div>
        </form>
      </div>

    </main>
    


    <!-- footer -->
    <?php
      require('footer.php');
    ?>