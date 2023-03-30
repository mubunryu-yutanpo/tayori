<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('おしっこ日記新規登録');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

//POSTされた場合
if(!empty($_POST)){
  //選択されているボタンの中身とセッションIDを変数に
  $u_id    = $_SESSION['user_id'];
  $date    = $_POST['date'];
  $time    = $_POST['time'];
  $color   = (!empty($_POST['pee-color-checked']))? $_POST['pee-color-checked'] : '';
  $vol     = (!empty($_POST['pee-vol-checked']))? $_POST['pee-vol-checked'] : '';
  $numtime = (!empty($_POST['pee-numtime-checked']))? $_POST['pee-numtime-checked'] : '';
  $title   = (!empty($_POST['title']))? $_POST['title'] : '';
  $comment = (!empty($_POST['comment']))? $_POST['comment'] : '';

  debug('POST情報：'.print_r($_POST,true));


  //バリデーション
  validRequired($date,'date');
  validRequired($time,'time');
  validRequired($color,'color');
  validRequired($vol,'vol');
  validRequired($numtime,'numtime');
  validMaxLen($title,'title');
  validCommentLength($comment,'comment');

  if(empty($err_msg)){
    debug('バリデーションOK');

    //ユーザーIDをもとにDB接続
    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //クエリ作成
      $sql = 'INSERT INTO pee (user_id,`date`,`time`,color,volume,number_times,title,comment,create_date)
              VALUES (:u_id,:date,:time,:color,:vol,:numtime,:title,:comment,:c_date)';
      $data = array(':u_id'=> $u_id, ':date'=> $date, ':time'=> $time, ':color'=> $color, ':vol'=> $vol, ':numtime'=> $numtime, ':title'=> $title,
                   ':comment'=> $comment, ':c_date'=> date('Y-m-d H:i:s') );
      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        //クエリが成功している場合はメッセージを表示し、マイページへ
        debug('おしっこ日記登録完了！マイページへ遷移します');
        $_SESSION['msg_success'] = SUC04;
        header('refresh:2;url=mypage.php');

      }else{
        debug('クエリ失敗。失敗したSQL：'.$stmt);
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
$siteTitle = 'おしっこ日記 | 登録';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->

    <!--メッセージを表示するエリア-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <section id="top">
      <h2>今日のおしっこ日記</h2>
    </section>

    <div style="text-align:center;">
      <img src="img/img2.png" alt="" class="">
    </div>


    <!--メイン-->
    <main id="main">
      <section class="diary-content">

        <form class="diary-left" method="post">
          <div class="area-msg"><?php echo getErrMsg('common'); ?></div><!--ここにエラーメッセージが入る-->
          <!--日時-->
          <div class="content-container">
            <p class="content-title">日時</p>
            <div class="datetime-box">
              <label for="" class="datetime-label">日付：<input type="date" name="date" value="" class="datetime-form"></label>
              <label for="" class="datetime-label">時間：<input type="time" name="time" value="" class="datetime-form"></label>
            </div>
            <div class="area-msg"><?php if(!empty(getErrMsg('date')) || !empty(getErrMsg('time')) ) echo MSG01;?></div><!--ここにエラーメッセージが入る-->
          </div>

          <!--色-->
          <div class="content-container">
            <p class="content-title">色</p>
            <div class="select-box">

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style= "background:#eeffff;">
                  <input type="checkbox" name="pee-color-select" value="1" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">透明</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style="background:#fbf6bf;">
                  <input type="checkbox" name="pee-color-select" value="2" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">薄黄色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style="background:#fce16d;">
                  <input type="checkbox" name="pee-color-select" value="3" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">黄色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style="background:#f0c081;">
                  <input type="checkbox" name="pee-color-select" value="4" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">茶褐色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style="background:#cc8b36;">
                  <input type="checkbox" name="pee-color-select" value="5" class="checkbox js-pee-color-check">
                </button>
                <p class="js-title">茶色</p>
              </div>

              <div class="">
                <button type="button" class="color-select-btn js-select-btn" style="background:#ba3207;">
                  <input type="checkbox" name="pee-color-select" value="6" class="checkbox js-pee-color-check">
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
                <button type="button" name="button" class="vol-select-btn js-select-btn">
                  <input type="checkbox" name="pee-vol-select" value="1" class="checkbox js-pee-vol-check">
                  <img src="img/img17.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ポタポタ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn">
                  <input type="checkbox" name="pee-vol-select" value="2" class="checkbox js-pee-vol-check">
                  <img src="img/img16.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">チョロチョロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn">
                  <input type="checkbox" name="pee-vol-select" value="3" class="checkbox js-pee-vol-check">
                  <img src="img/img15.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ふつう（約2~300cc）</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn">
                  <input type="checkbox" name="pee-vol-select" value="4" class="checkbox js-pee-vol-check">
                  <img src="img/img14.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">ジョボジョボ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="vol-select-btn js-select-btn">
                  <input type="checkbox" name="pee-vol-select" value="5" class="checkbox js-pee-vol-check">
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
                <button type="button" name="button" class="numtime-select-btn js-select-btn">
                  <input type="checkbox" name="pee-numtime-select" value="1" class="checkbox js-pee-numtime-check">
                  <img src="img/img10.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">3回以下</p>
              </div>

              <div class="">
                <button type="button" name="button" class="numtime-select-btn js-select-btn">
                  <input type="checkbox" name="pee-numtime-select" value="1" class="checkbox js-pee-numtime-check">
                  <img src="img/img11.png" alt="" class="poof-btn-img">
                </button>
                <p class="js-title">４~7回程</p>
              </div>

              <div class="">
                <button type="button" name="button" class="numtime-select-btn js-select-btn">
                  <input type="checkbox" name="pee-numtime-select" value="1" class="checkbox js-pee-numtime-check">
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
            <input type="text" name="title" placeholder="タイトルがある場合は入力してください" style="width:80%; margin:3rem auto; padding:1.5rem;"
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

          <!--記録するボタン-->
          <input type="submit" name="submit" value="記録する" class="submit">

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
