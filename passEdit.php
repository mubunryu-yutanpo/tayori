<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード変更');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//DBからセッションIDをもとにユーザー情報を取ってくる
$u_id = $_SESSION['user_id'];
$userData = getUser($u_id);
debug('DB情報：'.print_r($userData,true));


//POSTされていた場合
if(!empty($_POST)){
  debug('/passEdit/:POSTがあります');
  //POSTを変数に
  $pass_old    = $_POST['pass_old'];
  $pass_new    = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //未入力のバリデーションチェック
  validRequired($pass_old,'pass_old');
  validRequired($pass_new,'pass_new');
  validRequired($pass_new_re,'pass_new_re');

  //未入力がOKの場合
  if(empty($err_msg)){
    debug('未入力チェックOK');

    //各種バリデーションチェック
    validPass($pass_old,'pass_old');
    validPass($pass_new,'pass_new');
    validMatch($pass_new,$pass_new_re,'pass_new_re');

    //現在のパスワードとDBのパスワードが違う場合
    if(!password_verify($pass_old,$userData['pass'])){
      debug('入力された、現在のパスワードがDBデータと違います');
      $err_msg['pass_old'] = MSG14;
    }
    //新しいパスワードが現在のパスワードと一緒の場合
    if($pass_old === $pass_new){
      $err_msg['pass_new_re'] = MSG15;
    }

    //エラーが無い場合
    if(empty($err_msg)){
      debug('バリデーションOK');

      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //クエリ作成
        $sql = 'UPDATE users SET pass = :pass WHERE id = :u_id';
        $data = array(':pass' => password_hash($pass_new,PASSWORD_DEFAULT),'u_id' => $u_id);
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          debug('パスワードを変更しました');
          $_SESSION['msg_success'] = SUC02;

          //パスワード変更の旨を通知する
          $from     = 'TAYORI@info.com';
          $username = (!empty($userData['name']))? $userData['name'] : '名無し';
          $to       = $userData['mail'];
          $subject  = 'TAYORI || パスワード変更確認メール';
          $comment  = <<<EOT
{$username}さん
パスワードの変更を完了しました。

///////////////////////////////////////////////////////////
 TAYORI カスタマーサポートセンター
URL : http://TAYORI.com
E-mail : TAYORI@info.com
///////////////////////////////////////////////////////////
EOT;

          //メールを送信してマイページへ遷移
          sendMail($from,$to,$subject,$comment);
          header('refresh:3;url=mypage.php');

        }else{
          debug('パスワードの変更に失敗');
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
$siteTitle = 'パスワード変更';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->
    <p id="js-show-msg" class="msg-slide" style="display:none;">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

      <form class="form" action="" method="post">
        <div class="signup-title">
          <h2 class="">パスワード変更</h2>
        </div>

        <div class="area-msg"><?php echo getErrMsg('common'); ?></div>

        <label>現在のパスワード
          <input type="password" name="pass_old" class="input-style" placeholder="*半角英数字6文字以上" value="<?php if(!empty($_POST['pass_old'])) echo $_POST['pass_old']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('pass_old'); ?></div><!--ここにエラーメッセージが入る-->

        <label>新しいパスワード
          <input type="password" name="pass_new" class="input-style" value="<?php if(!empty($_POST['pass_new'])) echo $_POST['pass_new']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('pass_new'); ?></div><!--ここにエラーメッセージが入る-->


        <label>新しいパスワード(再入力)
          <input type="password" name="pass_new_re" class="input-style" value="<?php if(!empty($_POST['pass_new_re'])) echo $_POST['pass_new_re']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('pass_new_re'); ?></div><!--ここにエラーメッセージが入る-->

        <input type="submit" name="submit" class="submit" value="変更する">
      </form>


    <!--フッター-->
<?php
require('footer.php');
 ?>
