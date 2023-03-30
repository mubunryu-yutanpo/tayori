<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('食べたもの日記編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

debug('GET情報：'.print_r($_GET,true));
//GET情報を変数に
$d_id = (!empty($_GET['id']))? $_GET['id'] : '';
$u_id    = $_SESSION['user_id'];


//GETのIDをもとに日記情報を取得
$dbFormData = getFoodDetail($d_id);
debug('取得したデータ：'.print_r($dbFormData,true));


//POST送信がある場合
if(!empty($_POST)){
  debug('POSTがあります');
  debug('POST情報：'.print_r($_POST,true));
  debug('ファイル情報：'.print_r($_FILES,true));

  //POST情報を変数に
  $date    = $_POST['date'];
  $time    = $_POST['time'];
  $pic1    = (!empty($_FILES['pic1']['name']))? uploadImg($_FILES['pic1'],'pic1') : '';
  //画像がPOSTされてないがDBに登録されている場合はDBのパスを入れる
  $pic1    = (empty($pic1) && !empty($dbFormData['pic1']) )? $dbFormData['pic1'] : $pic1;

  $pic2    = (!empty($_FILES['pic2']['name']))? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2    = (empty($pic2) && !empty($dbFormData['pic2']) )? $dbFormData['pic2'] : $pic2;

  $pic3    = (!empty($_FILES['pic3']['name']))? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3    = (empty($pic3) && !empty($dbFormData['pic3']) )? $dbFormData['pic3'] : $pic3;

  $title   = $_POST['title'];
  $comment = $_POST['comment'];

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

  //エラーがない場合
  if(empty($err_msg)){
    debug('バリデーションOK');


    //例外処理
    try{
      //DB接続
      $dbh = dbConnect();
      //クエリ作成

      //更新の場合
      if(!empty($save_flg) && empty($delete_flg)){
        $sql = 'UPDATE food SET `date`=:date, `time`=:time, pic1=:pic1, pic2=:pic2, pic3=:pic3, title=:title, comment=:comment WHERE id=:d_id AND user_id=:u_id';
        $data = array(':date'=>$date,':time'=>$time,':pic1'=>$pic1,':pic2'=>$pic2,':pic3'=>$pic3,':title'=>$title,':comment'=>$comment,':d_id'=>$d_id,':u_id'=>$u_id);
      }
      //削除の場合
      if(!empty($del_flg) && empty($save_flg)){
        $sql = 'UPDATE food SET delete_flg = 1 WHERE id = :d_id';
        $data = array('d_id'=> $d_id);
      }

      //クエリ実行
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('食べたもの日記情報を変更しました');
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
$siteTitle = '食べ物日記 | 編集';
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
          <!--アイコンがクリックされたらつけ外しをする用のクラス名、jsで拾うためのデータ属性をつける-->
          <i class="fa-solid fa-thumbtack js-keep-food <?php if(isKeepFood($d_id) ){echo 'active';}?>" aria-hidden="true" data-diaryid="<?php echo $d_id; ?>">
          </i>
        </div>

        <form class="diary-left" enctype="multipart/form-data" method="post">

          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <!--日時-->
          <div class="content-container">
            <p class="content-title">日時</p>
            <div class="datetime-box">
              <label for="" class="datetime-label">日付：<input type="date" name="date" class="datetime-form" value="<?php echo getFormData('date'); ?>"></label>
              <label for="" class="datetime-label">時間：<input type="time" name="time" class="datetime-form" value="<?php echo getFormData('time'); ?>"></label>
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
                <input type="file" name="pic1" value="" class="js-file-input">
                <img src="<?php echo showImg(getFormData('pic1')); ?>" alt="" class="js-file-img js-del-img1">
                ドラッグ＆ドロップ
              </label>
              <button type="button" name="" class="img-del-btn js-del-btn1" data-delid="<?php echo $d_id; ?>">画像を削除</button>
              <div class="area-msg">
                <?php echo getErrMsg('pic1'); ?>
              </div>
            </div>

            <div class="food-img-box">
              <p>画像2</p>
              <label for="" class="file-label">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic2" value="" class="js-file-input">
                <img src="<?php echo showImg(getFormData('pic2')); ?>" alt="" class="js-file-img js-del-img2">
                   ドラッグ＆ドロップ
              </label>
              <button type="button" name="" class="img-del-btn js-del-btn2" data-delid="<?php echo $d_id; ?>">画像を削除</button>
              <div class="area-msg">
                <?php echo getErrMsg('pic2'); ?>
              </div>
            </div>

            <div class="food-img-box">
              <p>画像3</p>
              <label for="" class="file-label">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic3" value="" class="js-file-input">
                <img src="<?php echo showImg(getFormData('pic3')); ?>" alt="" class="js-file-img js-del-img3">
                   ドラッグ＆ドロップ
              </label>
              <button type="button" name="" class="img-del-btn js-del-btn3" data-delid="<?php echo $d_id; ?>">画像を削除</button>
              <div class="area-msg">
                <?php echo getErrMsg('pic3'); ?>
              </div>
            </div>


          </div>

          <!--タイトル-->
          <div class="content-container">
            <p class="content-title">タイトル</p>
            <input type="text" name="title" value="<?php echo getFormData('title'); ?>" placeholder="タイトルがある場合は入力してください" style="width:80%; padding: 1.5rem; margin:3rem auto; border:1px solid #c8c2bc;">
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
