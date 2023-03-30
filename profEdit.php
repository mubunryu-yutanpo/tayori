<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//DBからセッションIDをもとにユーザー情報を取ってくる
$u_id = $_SESSION['user_id'];
$dbFormData = getUser($u_id);
debug('DB情報：'.print_r($dbFormData,true));


//POSTされていた場合
if(!empty($_POST)){
  debug('/profEdit/:POSTがあります');
  //POSTを変数に
  $name    = $_POST['name'];
  $mail    = $_POST['mail'];
  $age     = (!empty($_POST['age']))? $_POST['age'] : 0;
  $pic1    = (!empty($_POST['pic1']))? $_POST['pic1'] : 'img30.png';

  //未入力のバリデーションチェック
  validRequired($name,'name');
  validRequired($mail,'mail');
  validRequired($age,'age');

  //未入力がOKの場合
  if(empty($err_msg)){
    debug('未入力チェックOK');

    //各種バリデーションチェック

    //名前がDBと違う場合
    if($dbFormData['name'] !== $name){
      validMaxLen($name,'name');
    }

    //e-maiがDBの情報と違う場合
    if($dbFormData['mail'] !== $mail){
      validMaxLen($email,'mail');
      if(empty($err_msg['email'])){
        validEmailDup($email);
      }
      validEmail($mail,'mail');
    }

    //年齢がDB情報と違う場合
    if($dbFormData['age'] !== $age){
      validNumber($age,'age');
    }

    //エラーが無い場合
    if(empty($err_msg)){
      debug('バリデーションOK');

      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //クエリ作成
        $sql = 'UPDATE users SET name = :name, mail = :mail, age = :age, pic1 = :pic1 WHERE id = :u_id';
        $data = array(':name' => $name, ':mail' => $mail, ':age' => $age, ':pic1'=> $pic1, 'u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          debug('プロフィールを変更しました');
          $_SESSION['msg_success'] = SUC02;
          header('refresh:3;url=mypage.php');


        }else{
          debug('プロフィールの変更に失敗');
          $err_msg['common'] = MSG07;
        }

      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
 ?>


<?php
$siteTitle = 'プロフィール編集';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->
    <p id="js-show-msg" class="msg-slide" style="display:none;">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

      <form class="form" action="" method="post">
        <div class="signup-title">
          <h2 class="">プロフィール編集</h2>
        </div>

        <div class="area-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

        <label>氏名（ニックネーム可）
          <input type="text" name="name" class="input-style" value="<?php echo getFormData('name'); ?>">
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?></div><!--ここにエラーメッセージが入る-->

        <label>E-mail
          <input type="text" name="mail" class="input-style" value="<?php echo getFormData('mail'); ?>">
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['mail'])) echo $err_msg['mail']; ?></div><!--ここにエラーメッセージが入る-->

        <label>年齢
          <input type="text" name="age" class="input-style" value="<?php echo getFormData('age'); ?>">
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['age'])) echo $err_msg['age'];?></div><!--ここにエラーメッセージが入る-->

        <div class="avatar-box" style="text-align:center;">
          <p style="text-align:left;font-size:1.8rem;">アバター画像</p>
          <label class="file-label" style="width:100%;">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic1" value="<?php showImg(getFormData('pic1')); ?>" class="js-file-input">
            <img src="" alt="" class="js-file-img" style="width:100%;height:100%;display:none;">
            ドラッグ＆ドロップ
          </label>
          <button type="button" name="" class="img-del-btn js-del-btn1" data-delid="<?php echo $u_id; ?>">画像を削除</button>
          <div class="area-msg">
            <?php echo getErrMsg('pic1'); ?>
          </div>
        </div>


        <input type="submit" name="submit" class="submit" value="変更する">
      </form>


    <!--フッター-->
<?php
require('footer.php');
 ?>
