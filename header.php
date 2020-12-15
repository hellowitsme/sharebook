<header class="header">
  <h1 class="header__ttl"><a href="index.php">Share Book</a></h1>
  <nav class="nav">
    <ul class="nav__list">
      <?php
        if(empty($_SESSION['user_id'])){
      ?>
        <li class="nav__list--item"><a href="signup.php">ユーザー登録</a></li>
        <li class="nav__list--item"><a href="login.php">ログイン</a></li>
      <?php
        }else{
      ?>
          <li class="nav__list--item"><a href="mypage.php">マイページ</a></li>
          <li class="nav__list--item"><a href="logout.php">ログアウト</a></li>
      <?php
        }
      ?>
    </ul>
  </nav>
</header>