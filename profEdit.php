<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「プロフィール編集ページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// プロフィール編集画面処理
//================================
//DBからユーザー情報取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData, true));

//POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $email = $_POST['email'];
  //画像をアップロード。パスを格納
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  //画像をPOSTしていないが、既にDBに登録されている場合、DBのパスを入れる
  $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

  //DBの情報と入力内容が違っていればバリデーション実行
  if($dbFormData['username'] !== $name){
    validMaxLen($name, 'name');
    validRequired($name, 'name');
  }
  if($dbFormData['email'] !== $email){
    //Email最大文字数チェック
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      //email重複チェック
      validEmailDup($email);
    }
    //Email形式チェック
    validEmail($email, 'email');
    //Email未入力チェック
    validRequired($email, 'email');
  }
  if(empty($err_msg)){
    debug('バリデーションOK');

    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      $sql = 'UPDATE users SET username = :name, email = :email, pic = :pic WHERE id = :u_id';
      $data = array(':name' => $name, ':email' => $email, ':pic' => $pic, ':u_id' => $dbFormData['id']);

      debug('流し込みデータ：'.print_r($data, true));
      debug('$picの中身：'.print_r($pic, true));

      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      //クエリ成功の場合
      if($stmt){
        debug('マイページへ');
        $_SESSION['msg_success'] = SUC03;
        header("Location:mypage.php");
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }else{
    debug('バリデーションエラー');
  }
}
debug('画面表示処理終了********************');
?>


<!-- ここからHTML部分 -->
<?php
  $title = 'プロフィール編集';
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
        <form action="" method="POST" class="form__group" enctype="multipart/form-data">
          <h2 class="form__group--ttl">プロフィール編集</h2>
          <div class="area-message">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>

          <!-- お名前入力欄 -->
          <div class="area-message">
            <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            <input class="form__group--item" type="text" name="name" value="<?php echo getFormData('username'); ?>" placeholder="お名前" autocomplete="off">
          </label>

          <!-- email入力 -->
          <div class="area-message">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <input class="form__group--item" type="text" name="email" value="<?php echo getFormData('email'); ?>" placeholder="E-Mail" autocomplete="off">
          </label>

          <!-- 画像用 -->
          <div class="area-message">
            <?php 
              if(!empty($err_msg['pic'])) echo $err_msg['pic'];
            ?>
          </div>
          <div class="imgDrop">
            <label class="imgDrop__area <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic" class="imgDrop__input--file">
              <p class="imgDrop__text">画像をドラッグ＆ドロップ</p>

              <img src="<?php echo getFormData('pic'); ?>" alt="" style="<?php if(empty(getFormData('pic'))) echo 'display:none;'; ?>" class="prev-img">
                  
            </label>
          </div>

          <div class="form__group--btn">
            <input type="submit" value="変更する">
          </div>
        </form>
      </div>
    </main>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>