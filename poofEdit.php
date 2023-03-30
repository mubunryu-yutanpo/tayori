<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ウンチ日記編集page');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//GET情報、ユーザーIDを変数に
$d_id = (!empty($_GET['id']))? $_GET['id'] : '';
$u_id = (!empty($_SESSION['user_id']))? $_SESSION['user_id'] : '';
debug('日記のID：'.print_r($d_id,true));
debug('ユーザーID：'.print_r($u_id,true));


//DB情報を変数に
$dbFormData = getPoofDetail($d_id);
debug('getPoofDetail 日記情報:'.print_r($dbFormData,true));


//POSTされた場合
if(!empty($_POST)){
  debug('POST情報：'.print_r($_POST,true));

  //選択されているボタンの中身とセッションIDを変数に
  $date    = $_POST['date'];
  $time    = $_POST['time'];

  $color   = (!empty($_POST['poof-color-checked']))? $_POST['poof-color-checked'] : '';
  //DBにデータがあってPOSTされてない場合
  $color   = (empty($_POST['poof-color-checked']) && !empty($dbFormData['color']))? $dbFormData['color'] : $color;

  $shape   = (!empty($_POST['poof-shape-checked']))? $_POST['poof-shape-checked'] : '';
  $shape   = (empty($_POST['poof-shape-checked']) && !empty($dbFormData['shape']))? $dbFormData['shape'] : $shape;

  $smell   = (!empty($_POST['poof-smell-checked']))? $_POST['poof-smell-checked'] : '';
  $smell   = (empty($_POST['poof-smell-checked']) && !empty($dbFormData['smell']))? $dbFormData['smell'] : $smell;

  $title   = (!empty($_POST['title']))? $_POST['title'] : '';
  $comment = (!empty($_POST['comment']))? $_POST['comment'] : '';

  //削除なのか編集なのかの判別に使う変数を定義
  $save_flg   = (!empty($_POST['submit-save']))? $_POST['submit-save'] : '';
  $delete_flg = (!empty($_POST['submit-delete']))? $_POST['submit-delete'] : '';


  //DB情報と違う場合はバリデーション
  //日時(新規登録時に、未入力の場合は弾いてるので違う場合は...でOK)
  if($dbFormData['date'] !== $date || $dbFormData['time'] !== $time){
    validRequired($date,'date');
    validRequired($time,'time');
  }
  //タイトル
  if($dbFormData['title'] !== $title){
    validMaxLen($title,'title');
  }
  //コメント
  if($dbFormData['comment'] !== $comment){
    validCommentLength($comment,'comment');
  }

  validRequired($color,'color');
  validRequired($shape,'shape');
  validRequired($smell,'smell');


  //エラーがない場合
  if(empty($err_msg)){
    //例外処理
    try{
      $dbh = dbConnect();

      //クエリ作成。フラグによって内容を変える
      if(!empty($save_flg)){
        //更新
        $sql = 'UPDATE poof SET `date`=:date, `time`=:time, color=:color, smell=:smell, shape=:shape, title=:title, comment=:comment WHERE id =:d_id AND user_id =:u_id';
        $data = array(':date'=>$date, ':time'=>$time, ':color'=>$color, ':smell'=>$smell, ':shape'=>$shape, ':title'=>$title, ':comment'=>$comment, ':d_id'=>$d_id, ':u_id'=>$u_id);
      }
      if(!empty($delete_flg)){
        //削除
        $sql = 'UPDATE poof SET delete_flg = 1 WHERE id = :d_id';
        $data = array(':d_id'=> $d_id);
      }
      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        //クエリ成功の場合
        $_SESSION['msg_success'] = SUC02;
        debug('ウンチ日記情報を変更しました');
        header('refresh:2;url=mypage.php');

      }else{
        //失敗の場合
        $err_msg['common'] = MSG07;
      }

    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }

}

 ?>


<?php
$siteTitle = 'ウンチ日記 | 編集';
require('head.php');
require('header.php');
?>

    <!--トップ-->
    <section id="top">
      <h2>今日のウンチ日記</h2>
    </section>

    <!--メッセージを表示するエリア-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <div style="text-align:center;">
      <img src="img/img2.png" alt="" class="">
    </div>

    <!--メイン-->
    <main id="main">

      <!--ユーザーname-->
      <div class="">
        <p style="margin:3rem;font-size:1.8rem;">
          製作者 ：
          <span><?php if(!empty($dbFormData['name'])){echo sanitize($dbFormData['name']);}else{echo '名無し';}?></span>
          さん
        </p>
        <div class="area-msg">
          <?php echo getErrMsg('name'); ?>
        </div>
      </div>

      <section class="diary-content">

        <!--ピン留め-->
        <div class="">
          <i class="fa-solid fa-thumbtack js-keep-poof <?php if(isKeepPoof($d_id)){echo 'active';}?>" aria-hidden="true" data-diaryid="<?php echo $d_id;?>"></i>
        </div>


        <form class="diary-left" method="post">
          <div class="area-msg"><?php echo getErrMsg('common'); ?></div><!--ここにエラーメッセージが入る-->

          <!--日時-->
          <div class="content-container">
            <p class="content-title">日時</p>
            <div class="datetime-box">
              <label for="" class="datetime-label">日付：<input type="date" name="date" value="<?php echo getFormData('date'); ?>" class="datetime-form"></label>
              <label for="" class="datetime-label">時間：<input type="time" name="time" value="<?php echo getFormData('time'); ?>" class="datetime-form"></label>
            </div>
            <div class="area-msg"><?php if(!empty(getErrMsg('date')) || !empty(getErrMsg('time')) ) echo MSG01;?></div><!--ここにエラーメッセージが入る-->
          </div>

          <!--色-->
          <div class="content-container">
            <p class="content-title">色</p>
            <div class="select-box">

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 1){echo 'checked';}?>" style= "background: #fff;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 1){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="1" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">白色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 2){echo 'checked';}?>" style="background:#f7ed8c;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 2){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="2" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">薄黄色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 3){echo 'checked';}?>" style="background:#e4b901;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 3){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="3" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">黄土色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 4){echo 'checked';}?>" style="background:#e68a01;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 4){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="4" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">オレンジ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 5){echo 'checked';}?>" style="background:#a25c00;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 5){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="5" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">茶色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 6){echo 'checked';}?>" style="background:#572d03;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 6){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="6" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">こげ茶色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 7){echo 'checked';}?>" style="background:#2d4a00;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 7){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="7" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">緑色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 8){echo 'checked';}?>" style="background:#8e9388;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 8){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="8" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">グレー</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 9){echo 'checked';}?>" style="background:#ba3207;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 9){echo 'poof-color-checked';}else{echo 'poof-color-select';} ?>" value="9" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">赤色</p>
              </div>

            </div>
            <div class="area-msg">
              <?php echo getErrMsg('color'); ?>
            </div>
          </div>

          <!--形-->
          <div class="content-container">
            <p class="content-title">かたち</p>
            <div class="select-box">

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 1){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 1){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="1" class="checkbox js-poof-shape-check">
                  <img src="img/img3.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">コロコロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 2){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 2){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="2" class="checkbox js-poof-shape-check">
                  <img src="img/img4.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">カチカチ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 3){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 3){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="3" class="checkbox js-poof-shape-check">
                  <img src="img/img5.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">やや硬め</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 4){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 4){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="4" class="checkbox js-poof-shape-check">
                  <img src="img/img6.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">プリプリ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 5){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 5){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="5" class="checkbox js-poof-shape-check">
                  <img src="img/img7.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">柔らかめ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 6){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 6){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="6" class="checkbox js-poof-shape-check">
                  <img src="img/img8.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">ドロドロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn <?php if($dbFormData['shape'] == 7){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 7){echo 'poof-shape-checked';}else{echo 'poof-shape-select';} ?>" value="7" class="checkbox js-poof-shape-check">
                  <img src="img/img9.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">ほぼ水状</p>
              </div>

            </div>
            <div class="area-msg">
              <?php echo getErrMsg('shape'); ?>
            </div>
          </div>

          <!--ニオイ-->
          <div class="content-container">
            <p class="content-title">ニオイ</p>
            <div class="select-box">

              <div class="">
                <button type="button" name="button" class="smell-select-btn js-select-btn <?php if($dbFormData['smell'] == 1){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 1){echo 'poof-smell-checked';}else{echo 'poof-smell-select';} ?>" value="1" class="checkbox js-poof-smell-check">
                  <img src="img/img18.png" alt="" class="poof-smell-img">
                </button>
                <p class="js-title">ほぼ無臭</p>
              </div>

              <div class="">
                <button type="button" name="button" class="smell-select-btn js-select-btn <?php if($dbFormData['smell'] == 2){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 2){echo 'poof-smell-checked';}else{echo 'poof-smell-select';} ?>" value="2" class="checkbox js-poof-smell-check">
                  <img src="img/img19.png" alt="" class="poof-smell-img">
                </button>
                <p class="js-title">チョイくさ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="smell-select-btn js-select-btn <?php if($dbFormData['smell'] == 3){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['shape'] == 3){echo 'poof-smell-checked';}else{echo 'poof-smell-select';} ?>" value="3" class="checkbox js-poof-smell-check">
                  <img src="img/img20.png" alt="" class="poof-smell-img">
                </button>
                <p class="js-title">結構キツイ</p>
              </div>
            </div>
            <div class="area-msg">
              <?php echo getErrMsg('smell'); ?>
            </div>
          </div>

          <!--タイトル-->
          <div class="content-container">
            <p class="content-title">タイトル</p>
            <input type="text" name="title" value="<?php echo getFormData('title'); ?>" placeholder="タイトルがある場合は入力してください" style="width:80%; margin:3rem auto; padding:1.5rem;border:1px solid #c8c2bc;">
            <div class="area-msg">
              <?php echo getErrMsg('title'); ?>
            </div>
          </div>


          <!--コメント-->
          <div class="content-container">

            <div class="comment-box">
              <p class="content-title">コメント</p>
              <div class="textarea-wrap">
                <textarea name="comment" rows="10" cols="50" class="js-text-area"><?php echo getFormData('comment'); ?></textarea>
                <p><span class="js-text-count">0</span>/ 500</p>
                <div class="area-msg">
                  <?php echo getErrMsg('comment'); ?>
                </div>
              </div>
            </div>

          </div>

          <!--記録するボタン。作成者の場合はボタンを表示し、違うユーザーの場合は見るのみ-->
          <?php
          if($dbFormData['user_id'] === $u_id){
           ?>
          <input type="submit" name="submit-save" value="保存する" class="submit">
          <input type="submit" name="submit-delete" value="削除する" class="submit js-delete-btn" style="margin-right:2rem;">

          <?php }?>

          <div class="page-back">
            <a href="mypage.php">&lt;戻る</a>
          </div>

        </form>

      </section>
    </main>

    <!--フッター-->
<?php
require('footer.php');
 ?>
