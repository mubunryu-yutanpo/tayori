<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('食べたもの日記登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//POST送信がある場合
if(!empty($_POST)){
  debug('POSTがあります');
  debug('POST情報：'.print_r($_POST,true));
  debug('ファイル情報：'.print_r($_FILES,true));

  $u_id    = $_SESSION['user_id'];
  //POST情報を変数に
  $date    = $_POST['date'];
  $time    = $_POST['time'];
  $pic1    = (!empty($_FILES['pic1']['name']))? uploadImg($_FILES['pic1'],'pic1') : '';
  $pic2    = (!empty($_FILES['pic2']['name']))? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic3    = (!empty($_FILES['pic3']['name']))? uploadImg($_FILES['pic3'],'pic3') : '';
  $title   = $_POST['title'];
  $comment = $_POST['comment'];

  //バリデーションチェック
  validRequired($date,'date');
  validRequired($time,'time');
  validMaxLen($title,'title');
  validCommentLength($comment,'comment');

  if(empty($err_msg)){
    //エラーがない場合
    debug('バリデーションOK');
    //ユーザーIDをもとにDBに登録処理

    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //クエリ作成
      $sql = 'INSERT INTO food (user_id,`date`,`time`,pic1,pic2,pic3,title,comment,create_date) VALUES (:u_id,:date,:time,:pic1,:pic2,:pic3,:title,:comment,:c_date)';
      $data = array(':u_id'=>$u_id, ':date'=>$date, ':time'=>$time, ':pic1'=>$pic1, ':pic2'=>$pic2, ':pic3'=> $pic3, ':title'=>$title, ':comment'=>$comment, ':c_date'=>date('Y-m-d H:i:s'));
      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('食べたもの日記登録完了！');
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
$siteTitle = '食べ物日記 | 登録';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->
    <section id="top">
      <h2>食べたもの日記</h2>
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

        <div class="area-msg">
          <?php echo getErrMsg('common'); ?>
        </div>

        <form class="diary-left" enctype="multipart/form-data" method="post">
          <!--日時-->
          <div class="content-container">
            <p class="content-title">日時</p>
            <div class="datetime-box">
              <label for="" class="datetime-label">日付：<input type="date" name="date" value="<?php echo getFormData('date') ?>" class="datetime-form"></label>
              <label for="" class="datetime-label">時間：<input type="time" name="time" value="<?php echo getFormData('time') ?>" class="datetime-form"></label>
            </div>
            <div class="area-msg">
              <?php if(!empty($err_msg['date']) || !empty($err_msg['time'])) echo MSG01; ?>
            </div>
          </div>

          <!--写真-->
          <div class="content-container">
            <p class="content-title">画像</p>

            <div class="food-img-box">
              <p>画像1</p>
              <label for="" class="file-label">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic1" value="<?php showImg(getFormData('pic1')); ?>" class="js-file-input">
                <img src="" alt="" class="js-file-img">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic1'); ?>
              </div>
            </div>

            <div class="food-img-box">
              <p>画像2</p>
              <label for="" class="file-label">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic2" value="<?php showImg(getFormData('pic2')); ?>" class="js-file-input">
                <img src="" alt="" class="js-file-img">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic2'); ?>
              </div>
            </div>

            <div class="food-img-box">
              <p>画像3</p>
              <label for="" class="file-label">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic3" value="<?php showImg(getFormData('pic3')); ?>" class="js-file-input">
                <img src="" alt="" class="js-file-img">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic3'); ?>
              </div>
            </div>


          </div>

          <!--タイトル-->
          <div class="content-container">
            <p class="content-title">タイトル</p>
            <input type="text" name="title" value="<?php echo getFormData('title'); ?>"
            placeholder="タイトルがある場合は入力してください" style="width:80%; padding: 1.5rem; margin:3rem auto; border:1px solid #c8c2bc;">
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
              </div>
              <div class="area-msg">
                <?php echo getErrMsg('comment'); ?>
              </div>
            </div>

          </div>

          <!--記録するボタン-->
          <input type="submit" name="submit" value="記録する" class="submit">

        </form>

      </section>
    </main>

    <!--フッター-->
<?php
require('footer.php');
 ?>
