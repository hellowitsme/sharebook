<?php
//共通ファイル
require('function.php');

debug('----------------------------------------------------------------------------');
debug('「トップページ」');
debug('----------------------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//現在ページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
debug('$currentPageNumの中身：'.print_r($currentPageNum, true));
//カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
debug('$categoryの中身：'.print_r($category, true));
debug('$_GETの中身：'.print_r($_GET, true));
//ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
debug('$sortの中身：'.print_r($sort, true));

//DBからユーザー情報を取得
$dbFormData = getUser($_SESSION['user_id']);
debug('$dbFormDataの中身：'.print_r($dbFormData, true));

//不正値チェック
if(!is_int((int)$currentPageNum)){
  error_log('不正な値が入力されました');
  header("Location:index.php");
}
//１ページの表示件数
$listSpan = 10;
//現在ページ
$currentMinNum = (($currentPageNum-1) * $listSpan);
//投稿データ取得
$dbPostData = getPostList($currentMinNum, $category, $sort);
debug('$dbPostDataの中身：'.print_r($dbPostData, true));
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();

debug('現在のページ：'.$currentPageNum);
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<!-- ここからHTML部分 -->
<?php
  $title = 'トップページ';
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
    <main class="main">
      <section class="main__contents">
        <?php
          foreach($dbPostData['data'] as $key => $val):
        ?>
        <a class="main__contents--item" href="postDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>">
          <div class="main__contents--head">
            <p>投稿者：<?php if(!empty($dbFormData['username'])) echo $dbFormData['username']; ?></p>
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

      <section class="main__sidebar">
        <form class="main__sidebar--form" method="get" action="">
          <h3 class="main__sidebar--ttl">カテゴリー</h3>
          <div class="main__sidebar--select">
            <select name="c_id" id="">
              <option value="0" <?php if(getCategory('c_id', true) == 0){echo 'selected';} ?>>選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val):
              ?>
                <option value="<?php echo $val['id']; ?>" <?php if(getCategory('c_id', true) == $val['id']){echo 'selected';} ?>>
                  <?php echo $val['name']; ?>
                </option>
              <?php
                endforeach;
              ?>
            </select>
          </div>
          <h3 class="main__sidebar--ttl">表示順</h3>
          <div class="main__sidebar--select">
            <select name="sort">
              <option value="0" <?php if(getCategory('sort',true) == 0 ){ echo 'selected'; } ?> >選択してください</option>
              <option value="1" <?php if(getCategory('sort',true) == 1 ){ echo 'selected'; } ?> >投稿が新しい順</option>
              <option value="2" <?php if(getCategory('sort',true) == 2 ){ echo 'selected'; } ?> >投稿が古い順</option>
            </select>
          </div>
          <input class="main__sidebar--btn" type="submit" value="検索">
        </form>
      </section>
    </main>
    
    <!-- ページネーション -->
    <?php pagination($currentPageNum, $dbPostData['total_page']); ?>


    <!-- footer -->
    <?php
      require('footer.php');
    ?>