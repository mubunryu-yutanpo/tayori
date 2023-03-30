<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//ユーザーIDを変数に
$u_id = (!empty($_SESSION['user_id']))? $_SESSION['user_id'] : '';
//表示件数
$span = 3;

//ユーザー情報
$userData = getUser($u_id);
debug('ユーザー情報'.print_r($userData,true));

//ウンチ日記データ
$poofData = myPoofList($u_id,$span);
debug('$poofData'.print_r($poofData,true));
//オシッコ日記データ
$peeData = myPeeList($u_id,$span);
debug('$peeData'.print_r($peeData,true));
//食べたもの日記データ
$foodData = myFoodList($u_id,$span);
debug('$foodData'.print_r($foodData,true));

 ?>

<?php
$siteTitle = 'マイページ';
require('head.php');
require('header.php');
 ?>

    <!--トップ-->
    <div class="userdata">
      <div class="avatar">
        <img src="img/<?php echo showImg(sanitize($userData['pic1'])); ?>" style="width: 100%;box-sizing: border-box;padding: 0.5rem;border-radius:50%;">
      </div>
      <p><?php echo sanitize($userData['name']); ?>　さん</p>
    </div>

    <section id="top">
      <h2>マイページ</h2>
    </section>

    <!--メイン-->

    <div class="site-width">

      <!--メインカラム-->
      <section id="main">

        <div class="main-content">
          <h3 class="main-content-title"><i class="fa-solid fa-poo fa-fw" style="color:#8a3a00;"></i> ウンチ日記</h3>

          <div class="main-content-container">

          <?php foreach($poofData as $key => $val){
             echo '<div class="main-content-box">';
               echo '<a href="poofEdit.php?id='.$val['id'].'">';
               echo '<h4>'.sanitize($val['title']).'</h4>';
               echo '<p>'.mb_substr(sanitize($val['comment']),0,40).'...</p></a>';
             echo '</div>';
          } ?>
          </div>

        <div class="main-content">
          <h3 class="main-content-title"><i class="fa-solid fa-toilet fa-fw" style="color:#25659f;"></i> オシッコ日記</h3>

          <div class="main-content-container">

            <?php foreach($peeData as $key => $val){
               echo '<div class="main-content-box">';
                 echo '<a href="peeEdit.php?id='.$val['id'].'">';
                 echo '<h4>'.sanitize($val['title']).'</h4>';
                 echo '<p>'.mb_substr(sanitize($val['comment']),0,40).'...</p></a>';
               echo '</div>';
            } ?>
          </div>

        <div class="main-content">
          <h3 class="main-content-title"><i class="fa-solid fa-utensils fa-fw" style="color:#d275a2;"></i> 食べたもの日記</h3>

          <div class="main-content-container">

            <?php foreach($foodData as $key => $val){
               echo '<div class="main-content-box">';
                 echo '<a href="foodEdit.php?id='.$val['id'].'">';
                 echo '<img src="'.showImg(sanitize($val['pic1'])).'" class="diary-img">';
                 echo '<h4>'.sanitize($val['title']).'</h4>';
                 echo '<p>'.mb_substr(sanitize($val['comment']),0,40).'...</p></a>';
               echo '</div>';
            } ?>
        </div>

      </section>

      <!--サイドバー-->
      <section id="side-bar">

        <div class="diary-width">
          <p class="side-title">日記MENU</p>

          <ul>
            <button type="button" name="button" class="menu-button1 js-slide-menu">ウンチ日記<i class="fa-solid fa-angle-down"></i><i class="fa-solid fa-angle-up off"></i></button>
            <div class="js-menu-box">
              <li class="side-menu-link"><a href="registPoof.php">記録する</a></li>
              <li class="side-menu-link"><a href="poofList.php">編集する</a></li>
            </div>
          </ul>

          <ul>
            <button type="button" name="button" class="menu-button2 js-slide-menu">オシッコ日記<i class="fa-solid fa-angle-down"></i><i class="fa-solid fa-angle-up off"></i></button>
            <div class="js-menu-box">
              <li class="side-menu-link"><a href="registPee.php">記録する</a></li>
              <li class="side-menu-link"><a href="peeList.php">編集する</a></li>
            </div>
          </ul>

          <ul>
            <button type="button" name="button" class="menu-button3 js-slide-menu">食べたもの日記<i class="fa-solid fa-angle-down"></i><i class="fa-solid fa-angle-up off"></i></button>
            <div class="js-menu-box">
              <li class="side-menu-link"><a href="registFood.php">記録する</a></li>
              <li class="side-menu-link"><a href="foodList.php">編集する</a></li>
            </div>
          </ul>

        </div>

          <div class="symptoms-index">
            <p class="side-title">症状を調べる</p>
            <form class="serch-parent">
              <input type="search" name="" value="" placeholder="検索する">
              <button type="button" name="button" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <ul>
              <p class="index-title">- 症状一覧 -</p>
              <li class="list-items list-item1"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
              <li class="list-items"><a href="#">サンプル症状</a></li>
            </ul>
          </div>

          <div class="link-item">
            <a href="profEdit.php" class="side-title">プロフィール編集</a>
          </div>

          <div class="link-item">
            <a href="passEdit.php" class="side-title">パスワード変更</a>
          </div>


          <div class="link-item">
            <a href="withdraw.php" class="side-title">退会する</a>
          </div>

      </section>
    </div>

<?php
require('footer.php');
 ?>
