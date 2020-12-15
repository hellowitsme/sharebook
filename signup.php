<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「新規登録ページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//================================
// 新規登録画面処理
//================================
//POST送信時
if(!empty($_POST)){
  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  //未入力チェック
  validRequired($name, 'name');
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  //未入力ではなかった場合
  if(empty($err_msg)){
    //名前の最大文字数チェック
    validMaxLen($name, 'name');
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //emailの重複チェック
    validEmailDup($email, 'email');

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //パスワード（再入力の）最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力の）最小文字数チェック
    validMinLen($pass_re, 'pass_re');

    if(empty($err_msg)){
      //パスワードと、再入力されたものがあっているか
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)){
        //例外処理
        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO users (username,email,password,login_time,create_date)
          VALUES(:name,:email,:pass,:login_time,:create_date)';
          $data = array(':name' => $name, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
          ':login_time' => date('Y-m-d H:i:s'),
          ':create_date' => date('Y-m-d H:i:s'));
          //クエリ実行
          $stmt = queryPost($dbh, $sql, $data);
          //クエリ成功の場合
          if($stmt){
            //セッション有効期限の更新
            $sesLimit = 60*60;
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：'.print_r($_SESSION, true));

            $_SESSION['msg_success'] = SUC01;
            header("Location:mypage.php");
          }
        }catch(Exception $e){
          error_log('エラー発生：' .$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}

?>


<!-- ここからHTML部分 -->
<?php
  $title = '新規登録';
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
          <h2 class="form__group--ttl">新規登録</h2>
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

          <!-- パスワード再入力 -->
          <div class="area-message">
            <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            <input class="form__group--item" type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re'] ?>" placeholder="パスワード（再入力）" autocomplete="off">
          </label>

          <div class="form__group--btn">
            <input type="submit" value="登録する">
          </div>
        </form>
      </div>

    </main>
    
    <!-- footer -->
    <?php
      require('footer.php');
    ?>