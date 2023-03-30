<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('食べたもの日記一覧ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//現在ページを変数に
$currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1;
//ソート
$sort = (!empty($_GET['sort']))? $_GET['sort'] : 1;
debug('GET情報：'.print_r($_GET,true));

//表示件数
$span = 9;
//表示しているのが何件目か
$listspan = 9;
//現在ページの最少件数
$currentMinNum = ($currentPageNum - 1)* $listspan;
//日記情報を取得
$dbDiaryData = getFoodList($currentMinNum,$sort,$span);
debug('取得したDB情報:'.print_r($dbDiaryData,true));


 ?>

 <?php
$siteTitle = '食べたもの日記 ｜ 一覧';
require('head.php');
require('header.php');
  ?>
    <!--トップ-->
    <section id="top">
      <h2>食べたもの日記一覧</h2>
    </section>

    <!--メイン-->

    <div class="site-width" style="display:block;">

      <!--メインカラム-->
      <section id="index-main">
       <form class="" action="" method="get">

         <div class="search-container">
           <p>
             <span><?php echo sanitize($currentMinNum);?></span>〜
             <span><?php echo sanitize($currentMinNum + $listspan); ?></span>件/
             <span><?php echo sanitize($dbDiaryData['total']); ?></span>件中
           </p>
           <select class="sort" name="sort">
             <option value="1" <?php if(getFormData('sort',false) == 1){ echo 'selected';} ?>>新しい順</option>
             <option value="2" <?php if(getFormData('sort',false) == 2){ echo 'selected';} ?>>古い順</option>
             <option value="3" <?php if(getFormData('sort',false) == 3){ echo 'selected';} ?>>ピン留めのみ</option>
           </select>
           <input type="submit" name="" class="select-submit" value="並び替える">
         </div>

          <div class="list-content-container">
            <?php
            foreach($dbDiaryData['data'] as $key => $val){
              echo '<a class="list-content-box" href="foodEdit.php?id='.$val['id'].'">';
                echo '<h4>'.sanitize($val['title']).'</h4>';
                echo '<img src="'.$val['pic1'].'" style="width:80%; margin:1rem auto;border: 2px solid #f2dac3;">';
                echo '<p style="width:80%;margin:1rem auto;">'.mb_substr(sanitize($val['comment']),0,50).'...</p>';
                echo '<p style="margin-top:3rem;">'.$val['date'].'</p>';
              echo '</a>';
            }
             ?>
          </div>


       </form>
      </section>

      <section id="pagenation">
          <?php pagination($currentPageNum,$dbDiaryData['total_page']); ?>
      </section>



    </div>

    <!--フッター-->
<?php
require('footer.php');
 ?>
