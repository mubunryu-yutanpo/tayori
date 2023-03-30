<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ウンチ日記　｜　新規登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//POSTされた場合
if(!empty($_POST)){
  //選択されているボタンの中身とセッションIDを変数に
  $u_id    = $_SESSION['user_id'];
  $date    = $_POST['date'];
  $time    = $_POST['time'];
  $color   = (!empty($_POST['poof-color-checked']))? $_POST['poof-color-checked'] : '';
  $shape     = (!empty($_POST['poof-shape-checked']))? $_POST['poof-shape-checked'] : '';
  $smell = (!empty($_POST['poof-smell-checked']))? $_POST['poof-smell-checked'] : '';
  $title   = (!empty($_POST['poof-title']))? $_POST['poof-title'] : '';
  $comment = (!empty($_POST['poof-comment']))? $_POST['poof-comment'] : '';

  debug('POST情報：'.print_r($_POST,true));

  //バリデーション
  validRequired($date,'date');
  validRequired($time,'time');
  validRequired($color,'color');
  validRequired($shape,'shape');
  validRequired($smell,'smell');
  validMaxLen($title,'title');
  validCommentLength($comment,'comment');

  //エラーがない場合
  if(empty($err_msg)){
    debug('バリデーションOK');

    //セッションのユーザーIDをもとにDB接続
    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //クエリ作成
      $sql = 'INSERT INTO poof (user_id,`date`,`time`,color,smell,shape,title,comment,create_date) VALUES (:u_id,:date,:time,:color,:smell,:shape,:title,:comment,:c_date)';
      $data = array(':u_id'=> $u_id, ':date'=> $date, ':time'=> $time, ':color'=> $color, ':smell'=> $smell, ':shape'=> $shape, ':title'=> $title, ':comment'=> $comment, 'c_date'=> date('Y-m-d H:i:s'));
      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        //クエリが成功している場合
        $_SESSION['msg_success'] = SUC04;
        debug('ウンチ日記登録完了！マイページへ遷移します');
        header('refresh:2;url=mypage.php');

      }else{
        //クエリ失敗
        debug('失敗したクエリ：'.$stmt);
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
$siteTitle = "ウンチ日記 | 登録";
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
      <section class="diary-content">
        <div class="area-msg"><?php echo getErrMsg('common'); ?></div><!--ここにエラーメッセージが入る-->

        <form class="diary-left" method="post">
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
                <button type="button" name="button" class="color-select-btn js-select-btn" style= "background: #fff;">
                  <input type="checkbox" name="poof-color-select" value="1" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">白色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#f7ed8c;">
                  <input type="checkbox" name="poof-color-select" value="2" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">薄黄色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#e4b901;">
                  <input type="checkbox" name="poof-color-select" value="3" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">黄土色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#e68a01;">
                  <input type="checkbox" name="poof-color-select" value="4" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">オレンジ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#a25c00;">
                  <input type="checkbox" name="poof-color-select" value="5" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">茶色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#572d03;">
                  <input type="checkbox" name="poof-color-select" value="6" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">こげ茶色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#2d4a00;">
                  <input type="checkbox" name="poof-color-select" value="7" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">緑色</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#8e9388;">
                  <input type="checkbox" name="poof-color-select" value="8" class="checkbox js-poof-color-check">
                </button>
                <p class="js-title">グレー</p>
              </div>

              <div class="">
                <button type="button" name="button" class="color-select-btn js-select-btn" style="background:#ba3207;">
                  <input type="checkbox" name="poof-color-select" value="9" class="checkbox js-poof-color-check">
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
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="1" class="checkbox js-poof-shape-check">
                  <img src="img/img3.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">コロコロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="2" class="checkbox js-poof-shape-check">
                  <img src="img/img4.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">カチカチ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="3" class="checkbox js-poof-shape-check">
                  <img src="img/img5.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">やや硬め</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="4" class="checkbox js-poof-shape-check">
                  <img src="img/img6.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">プリプリ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="5" class="checkbox js-poof-shape-check">
                  <img src="img/img7.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">柔らかめ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="6" class="checkbox js-poof-shape-check">
                  <img src="img/img8.png" alt="" class="poof-shape-img">
                </button>
                <p class="js-title">ドロドロ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="shape-select-btn js-select-btn">
                  <input type="checkbox" name="poof-shape-select" value="7" class="checkbox js-poof-shape-check">
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
                <button type="button" name="button" class="smell-select-btn js-select-btn">
                  <input type="checkbox" name="poof-smell-select" value="1" class="checkbox js-poof-smell-check">
                  <img src="img/img18.png" alt="" class="poof-smell-img">
                </button>
                <p class="js-title">ほぼ無臭</p>
              </div>

              <div class="">
                <button type="button" name="button" class="smell-select-btn js-select-btn">
                  <input type="checkbox" name="poof-smell-select" value="2" class="checkbox js-poof-smell-check">
                  <img src="img/img19.png" alt="" class="poof-smell-img">
                </button>
                <p class="js-title">チョイくさ</p>
              </div>

              <div class="">
                <button type="button" name="button" class="smell-select-btn js-select-btn">
                  <input type="checkbox" name="poof-smell-select" value="3" class="checkbox js-poof-smell-check">
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
            <input type="text" name="poof-title" value="<?php if(!empty($title)) echo $title ?>" placeholder="タイトルがある場合は入力してください" style="width:80%; margin:3rem auto; padding:1.5rem;">
            <div class="area-msg">
              <?php echo getErrMsg('title'); ?>
            </div>
          </div>


          <!--コメント-->
          <div class="content-container">

            <div class="comment-box">
              <p class="content-title">コメント</p>
              <div class="textarea-wrap">
                <textarea name="poof-comment" rows="10" cols="50" class="js-text-area"><?php if(!empty($comment)) echo $comment; ?></textarea>
                <p><span class="js-text-count">0</span>/ 500</p>
                <div class="area-msg">
                  <?php echo getErrMsg('comment'); ?>
                </div>
              </div>
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
