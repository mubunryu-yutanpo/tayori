<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('おしっこ日記　｜　編集');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//GET情報を取得し変数に
$d_id = (!empty($_GET['id']))? $_GET['id'] : '';
//セッションのユーザーIDを変数に
$u_id = (!empty($_SESSION['user_id']))? $_SESSION['user_id'] : '';

//IDをもとに日記情報を取得し変数に
$dbFormData = getPeeDetail($d_id);
debug('$dbFormDataの中身:'.print_r($dbFormData,true));

//POSTされた場合
if(!empty($_POST)){
  //選択されているボタンの中身とセッションIDを変数に
  $date    = $_POST['date'];
  $time    = $_POST['time'];

  $color   = (!empty($_POST['pee-color-checked']))? $_POST['pee-color-checked'] : '';
  //POSTされてなくて、DBには情報がある場合
  $color   = (empty($_POST['pee-color-checked']) && !empty($dbFormData['color']))? $dbFormData['color'] : $color;

  $vol     = (!empty($_POST['pee-vol-checked']))? $_POST['pee-vol-checked'] : '';
  $vol     = (empty($_POST['pee-vol-checked']) && !empty($dbFormData['volume']))? $dbFormData['volume'] : $vol;

  $numtime = (!empty($_POST['pee-numtime-checked']))? $_POST['pee-numtime-checked'] : '';
  $numtime = (empty($_POST['pee-numtime-checked']) && !empty($dbFormData['number_times']))? $dbFormData['number_times'] : $numtime;

  $title   = (!empty($_POST['title']))? $_POST['title'] : '';
  $comment = (!empty($_POST['comment']))? $_POST['comment'] : '';

  debug('POST情報：'.print_r($_POST,true));

  //更新なのか削除なのかを変数に
  $save_flg = (!empty($_POST['submit-save']))? $_POST['submit-save'] : '';
  $del_flg   = (!empty($_POST['submit-delete']))? $_POST['submit-delete'] : '';

  //DB情報が変更されていた場合は各種バリデーションチェック

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
  validRequired($vol,'vol');
  validRequired($numtime,'numtime');

  //エラーがない場合
  if(empty($err_msg)){
    //例外処理
    try{
      $dbh = dbConnect();
      //フラグによってクエリを変える

      if(!empty($save_flg)){
        //変更の場合
        $sql = 'UPDATE pee SET `date`=:date,`time`=:time, color=:color, volume=:vol, number_times=:nt, title=:title, comment=:comment WHERE id =:d_id AND user_id = :u_id';
        $data = array(':date'=>$date,':time'=>$time,':color'=>$color,':vol'=>$vol,':nt'=>$numtime,':title'=>$title,':comment'=>$comment, ':d_id'=> $d_id, ':u_id' => $u_id);
      }

      if(!empty($del_flg)){
        $sql = 'UPDATE pee SET delete_flg = 1 WHERE id = :d_id';
        $data = array(':d_id'=> $d_id);
      }

      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('おしっこ日記情報を変更しました');
        header('refresh:2;url=mypage.php');

      }else{
        debug('登録に失敗。失敗したクエリ：'.$stmt);
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
$siteTitle = 'おしっこ日記 | 編集';
require('head.php');
require('header.php');
 ?>

    <!--トップ-->
    <section id="top">
      <h2>今日のおしっこ日記</h2>
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
          <i class="fa-solid fa-thumbtack js-keep-pee <?php if(isKeepPee($d_id)){echo 'active';}?>" aria-hidden="true" data-diaryid="<?php echo $d_id; ?>"></i>
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
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 1){echo 'checked';}?>" style= "background:#eeffff;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 1){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="1" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">透明</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 2){echo 'checked';}?>" style="background:#fbf6bf;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 2){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="2" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">薄黄色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 3){echo 'checked';}?>" style="background:#fce16d;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 3){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="3" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">黄色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 4){echo 'checked';}?>" style="background:#f0c081;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 4){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="4" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">茶褐色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 5){echo 'checked';}?>" style="background:#cc8b36;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 5){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="5" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">茶色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn <?php if($dbFormData['color'] == 6){echo 'checked';}?>" style="background:#ba3207;">
                  <input type="checkbox" name="<?php if($dbFormData['color'] == 6){echo 'pee-color-checked';}else{echo 'pee-color-selct';} ?>" value="6" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">赤色</p>
              </div>

            </div>
            <div class="area-msg"><?php echo getErrMsg('color'); ?></div><!--ここにエラーメッセージが入る-->
          </div>

          <!--形-->
          <div class="content-container">
            <p class="content-title">尿量</p>
            <div class="select-box">

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn <?php if($dbFormData['volume'] == 1){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['volume'] == 1){echo 'pee-vol-checked';}else{echo 'pee-vol-selct';} ?>" value="1" class="checkbox js-pee-vol-check">
                  <img src="img/img17.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ポタポタ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn <?php if($dbFormData['volume'] == 2){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['volume'] == 2){echo 'pee-vol-checked';}else{echo 'pee-vol-selct';} ?>" value="2" class="checkbox js-pee-vol-check">
                  <img src="img/img16.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">チョロチョロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn <?php if($dbFormData['volume'] == 3){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['volume'] == 3){echo 'pee-vol-checked';}else{echo 'pee-vol-selct';} ?>" value="3" class="checkbox js-pee-vol-check">
                  <img src="img/img15.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ふつう（約2~300cc）</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn <?php if($dbFormData['volume'] == 4){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['volume'] == 4){echo 'pee-vol-checked';}else{echo 'pee-vol-selct';} ?>" value="4" class="checkbox js-pee-vol-check">
                  <img src="img/img14.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ジョボジョボ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn <?php if($dbFormData['volume'] == 5){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['volume'] == 5){echo 'pee-vol-checked';}else{echo 'pee-vol-selct';} ?>" value="5" class="checkbox js-pee-vol-check">
                  <img src="img/img13.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">大量（ふつうの倍ほど）</p>
              </div>

            </div>
            <div class="area-msg"><?php echo getErrMsg('vol'); ?></div><!--ここにエラーメッセージが入る-->
          </div>

          <!--回数-->
          <div class="content-container">
            <p class="content-title">回数</p>
            <div class="select-box">

              <div class="">
                <button type="button" name="button" class="numtime-select-btn js-select-btn <?php if($dbFormData['number_times'] == 1){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['number_times'] == 1){echo 'pee-numtime-checked';}else{echo 'pee-numtime-selct';} ?>" value="1" class="checkbox js-pee-numtime-check">
                  <img src="img/img10.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">3回以下</p>
              </div>

              <div class="">
                <button type="button" name="button" class="numtime-select-btn js-select-btn <?php if($dbFormData['number_times'] == 2){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['number_times'] == 2){echo 'pee-numtime-checked';}else{echo 'pee-numtime-selct';} ?>" value="2" class="checkbox js-pee-numtime-check">
                  <img src="img/img11.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">４~7回程</p>
              </div>

              <div class="">
                <button type="button" name="button" class="numtime-select-btn js-select-btn <?php if($dbFormData['number_times'] == 3){echo 'checked';}?>">
                  <input type="checkbox" name="<?php if($dbFormData['number_times'] == 3){echo 'pee-numtime-checked';}else{echo 'pee-numtime-selct';} ?>" value="3" class="checkbox js-pee-numtime-check">
                  <img src="img/img12.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">8回以上</p>
              </div>

            </div>
            <div class="area-msg"><?php echo getErrMsg('numtime'); ?></div><!--ここにエラーメッセージが入る-->
          </div>

          <!--タイトル-->
          <div class="content-container">
            <p class="content-title">タイトル</p>
            <input type="text" name="title" placeholder="タイトルがある場合は入力してください" style="width:80%; margin:3rem auto; padding:1.5rem;border: 1px solid #c8c2bc;"
            value="<?php echo getFormData('title'); ?>">
            <div class="area-msg"><?php echo getErrMsg('title'); ?></div><!--ここにエラーメッセージが入る-->
          </div>


          <!--コメント-->
          <div class="content-container">

            <div class="comment-box">
              <p class="content-title">コメント</p>
              <div class="textarea-wrap">
                <textarea name="comment" rows="10" cols="50" class="js-text-area"><?php echo getFormData('comment'); ?></textarea>
                <p><span class="js-text-count">0</span>/ 500</p>
              </div>
              <div class="area-msg"><?php echo getErrMsg('comment'); ?></div><!--ここにエラーメッセージが入る-->
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
            <a href="mypage.html">&lt;戻る</a>
          </div>

        </form>

      </section>
    </main>

    <!--フッター-->
<?php
require('footer.php');
 ?>
