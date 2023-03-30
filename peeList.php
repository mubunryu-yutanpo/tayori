<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('peeList.php:オシッコ日記一覧');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//現在のページを変数に
$currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1;
//ソート
$sort = (!empty($_GET['sort']))? $_GET['sort'] : 1;
debug('GET情報：'.print_r($_GET,true));

//表示件数
$span = 9;
//表示しているのが何件目か
$listspan = 9;
//何件目〜何件目まで表示させるかの基準値
$currentMinNum = ($currentPageNum - 1)* $listspan;
//日記情報の取得
$dbDiaryData = getPeeList($currentMinNum,$sort);
debug('取得した日記情報：'.print_r($dbDiaryData,true));

 ?>

 <?php
$siteTitle = 'オシッコ日記 | 一覧';
require('head.php');
require('header.php');
  ?>

    <!--トップ-->
    <section id="top">
      <h2>おしっこ日記一覧</h2>
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
            <input type="submit" name="" value="並び替える" class="select-submit">
          </div>

          <div class="list-content-container">
            <?php
            foreach($dbDiaryData['data'] as $key => $val){
              echo '<a class="list-content-box" href="peeEdit.php?id='.$val['id'].'">';
                echo '<h4>'.sanitize($val['title']).'</h4>';
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
