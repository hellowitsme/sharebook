<?php
//================================
// ログイン認証・自動ログアウト
//================================
//ログイン済
if(!empty($_SESSION['login_date'])){
  debug('ログイン済ユーザーです');

  //ログイン有効期限外だった場合
  if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限を過ぎています。');

    //セッションを削除（ログアウト）
    session_destroy();
    //ログインページへ
    header("Location:login.php");
  }else{
    debug('ログイン有効期限内です。');
    //最終ログイン日時を現在日時に更新
    $_SESSION['login_date'] = time();

    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイページへ遷移します');
      header("Location:mypage.php");
    }
  }
}else{
  debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header("Location:login.php");
  }
}
?>