<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('poofList.php:ウンチ日記一覧');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//==============================
//表示用のデータを取得する
//==============================

//現在のページを取得し変数に(デフォルトは１（ページ目）)
$currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1;

//ソートを変数に(デフォルトは新しい順) -> 作る気無くなったからとりあえず空で
$sort = (!empty($_GET['sort']))? $_GET['sort'] : 1;

debug('GET情報：'.print_r($_GET,true));

//表示件数
$span = 9;
//表示しているのが何件目か
$listspan = 9;
//そのページでDBの何件目から表示するか
$currentMinNum = ($currentPageNum - 1) * $listspan;
//ページに表示する日記のデータを取得
$dbDiaryData = getPoofList($currentMinNum,$sort);
debug('取得したデータ情報：'.print_r($dbDiaryData,true));


//ページのGETパラメータに不正な値が入った場合
if(empty($dbDiaryData['data'])){
  debug('GETパラメータに不正な値が入りました');
  header('Location:mypage.php');
}

 ?>

<?php
$siteTitle = 'ウンチ日記 | 一覧';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->
    <section id="top">
      <h2>ウンチ日記一覧</h2>
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
             //foreachによって、登録してあるDB情報を表示
             foreach($dbDiaryData['data'] as $key => $val){
               echo '<a class="list-content-box" href="poofEdit.php?id='.$val['id'].'">';
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
