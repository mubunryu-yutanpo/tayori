<header id="header">
  <div class="header-left">
    <a href="mypage.php" style="height:50px;"><img src="img/unkokko.png" class="logo"></a>
    <h3 class="logo-title">TAYORI</h3>
  </div>
  <div class="header-right">

  <?php
  //セッション（ログイン)しているかでヘッダーの内容をかえる
  if(!empty($_SESSION['user_id'])){
     ?>
      <a href="logout.php"><button type="button" name="button" class="top-link-item">ログアウト</button></a>
    <?php
  }else{
     ?>
       <a href="signup.php"><button type="button" name="button" class="top-link-item">新規会員登録</button></a>
       <a href="login.php"><button type="button" name="button" class="top-link-item">　ログイン　</button></a>
     <?php
   }
      ?>

  </div>
</header>
