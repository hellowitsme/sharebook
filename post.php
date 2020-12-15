<?php

//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「投稿ページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 投稿画面処理
//================================

// 画面表示用データ取得
//================================
//GETデータを代入
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('$p_idの中身：'.print_r($p_id, true));
//DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getPost($_SESSION['user_id'], $p_id) : '';
//新規投稿か編集かの判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();
debug('商品ID：'.$p_id);
debug('フォーム用DBデータ：'.print_r($dbFormData, true));
debug('カテゴリーデータ：'.print_r($dbCategoryData, true));

//パラメータチェック
//================================
//GETパラメータはあるが、改ざんされている場合、マイページへ
if(!empty($p_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ。');
  header("Location:mypage.php");
}

//POST送信があった場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES, true));

  //変数にユーザー情報を代入
  $name = $_POST['name'];
  $category = $_POST['category_id'];
  $comment = $_POST['comment'];
  //画像をアップロード。パスを格納
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  //画像をPOSTしていないが、既にDBに登録されている場合、DBのパスを入れる
  $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

  //更新の場合はDBの情報と入力内容が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    validRequired($name, 'name');
    selectRequired($category, 'category_id');
    validRequired($comment, 'comment');
    //最大文字数チェック
    validMaxLen($name, 'name');
    //最大文字数チェック
    validMaxLen($comment, 'comment', 500);
  }else{
    if($dbFormData['name'] !== $name){
      validRequired($name, 'name');
      validMaxLen($name, 'name');
    }
    if($dbFormData['category_id'] !== $category){
      selectRequired($category, 'category_id');
      validSelect($category, 'category_id');
    }
    if($dbFormData['comment'] !== $comment){
      validMaxLen($comment, 'comment', 500);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOK');

    try{
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
      if($edit_flg){
        debug('DB更新');
        $sql = 'UPDATE post SET name = :name, category_id = :category, comment = :comment, pic = :pic WHERE user_id = :u_id AND id = :p_id';
        $data = array(':name' => $name, ':category' => $category, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
      }else{
        debug('DB新規登録');
        $sql = 'INSERT INTO post (name, category_id, comment, pic, user_id, create_date) values (:name, :category, :comment, :pic, :u_id, :date)';
        $data = array(':name' => $name, ':category' => $category, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data, true));
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ');
        header("Location:index.php");
      }
    }catch(Exception $e){
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['pic'] = MSG03;
      $err_msg['common'] = MSG07;
    }
  }
  debug('$err_msg'.print_r($err_msg, true));
  
}
?>

<?php
  $title = (!$edit_flg) ? '登録' : '編集';
  require('head.php');
?>
  <body>
    <!-- header -->
    <?php
      require('header.php');
    ?>

    <!-- メイン -->
    <div class="form">
      <form action="" method="post" class="form__group" enctype="multipart/form-data"     style="box-sizing:border-box;">
        <h2 class="form__group--ttl"><?php echo (!$edit_flg) ? '投稿する' : '編集する'; ?></h2>
          <div class="area-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>

          <!-- 本のタイトル -->
          <div class="area-message">
            <?php 
              if(!empty($err_msg['name'])) echo $err_msg['name'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
            <input class="form__group--item" type="text" name="name" value="<?php echo getFormData('name'); ?>" placeholder="本のタイトル" autocomplete="off">
          </label>

          <!-- カテゴリー -->
          <div class="area-message">
            <?php 
              if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
            <select class="form__group--item" name="category_id">
              <option value="0" <?php if(getFormData('category_id') == 0){echo 'selected';} ?>>カテゴリーを選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val):
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id']) {echo 'selected';} ?> >
                  <?php echo $val['name'] ?>
                    </option>
              <?php endforeach ?>
            </select>
          </label>

          <!-- 内容 -->
          <div class="area-message">
            <?php 
              if(!empty($err_msg['comment'])) echo $err_msg['comment'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
            <textarea class="form__group--item" name="comment" id="js-count" cols="10" rows="10" style="min-height:250px;margin-bottom:0;border-radius:10px;" placeholder="本の内容"><?php echo getFormData('comment'); ?></textarea>
          </label>
          <!-- カウントテキスト -->
          <p class="counter__text" style="text-align:center;margin-bottom:50px;"><span id="js-count-view">0</span>/500文字</p>
            
          <!-- 画像 -->
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

              <img src="<?php echo getFormData('pic'); ?>" alt="投稿イメージ" style="<?php if(empty(getFormData('pic'))) echo 'display:none;'; ?>" class="prev-img">
                  
            </label>
          </div>
            
          <!-- ボタン -->
          <div class="form__group--btn">
            <input type="submit" value="<?php echo (!$edit_flg) ? '投稿する' : '編集する'; ?>">
          </div>
      </form>
    </div>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>