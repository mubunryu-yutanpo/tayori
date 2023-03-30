<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//POSTがある場合
if(!empty($_POST)){
  debug('POSTがあります');

  //POSTを変数に
  $email = $_POST['email'];
  $pass  = $_POST['pass'];
  $save = (!empty($_POST['save-log']))? true : false;

  //未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');

  if(empty($err_msg)){
    debug('未入力チェックOK');

    //各種バリデーションチェック
    validEmail($email,'email');
    validMaxLen($email,'email');

    validPass($pass,'pass');

    if(empty($err_msg)){
      debug('バリデーションOK');

      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //クエリ生成(パスワードとIDを取ってくる)
        $sql = 'SELECT pass , id FROM users WHERE mail = :mail AND delete_flg = 0';
        $data = array('mail'=> $email);
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //password_verifyでパスワードを照合
        if(!empty($result) && password_verify($pass,array_shift($result)) ){
          debug('パスワードが一致しました');

          //ログインの有効期限を再設定する
          $sesLimit = 60*60;
          $_SESSION['login_date'] = time();
          //ユーザーIDを保存
          $_SESSION['user_id'] = $result['id'];
          //ログイン保持チェック保持がある場合は有効期限を30日に
          if($save){
            debug('ログイン保持にチェックあり');
            $_SESSION['login_limit'] = $_SESSION['login_date'] + ($sesLimit*24*30);
          }else{
            debug('ログイン保持チェックなし');
            $_SESSION['login_limit'] = $_SESSION['login_date'] + $sesLimit;
          }

          debug('セッション情報'.print_r($_SESSION,true));
          debug('マイページへいきますね');
          header('Location:mypage.php');

        }else{
          debug('パスワードが一致しませんでした');
          $err_msg['common'] = MSG09;
        }
      }catch(Exception $e){
        error_log($e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }

}
 ?>


<?php
$siteTitle = 'TAYORI || ログインページ';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

      <form class="form" action="" method="post">
        <div class="signup-title">
          <h2 class="">ログイン</h2>
        </div>

        <div class="area-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div><!--common-->

        <label>E-mail
          <input type="text" name="email" class="input-style <?php if(!empty($err_msg['email']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['email']) ) echo $_POST['email']; ?>">
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['email']) ) echo $err_msg['email']; ?></div><!--ここにエラーメッセージが入る-->

        <label>パスワード<span class="form-txt">※半角英数字6文字以上</span>
          <input type="password" name="pass" class="input-style <?php if(!empty($err_msg['pass']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['pass']) ) echo $_POST['pass']; ?>">
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['pass']) ) echo $err_msg['pass']; ?></div><!--ここにエラーメッセージが入る-->

        <!--ログイン保持-->
        <label><input type="checkbox" name="save-log" value="">ログインしたままにする</label>

        <!--submit-->
        <input type="submit" name="submit" class="submit" value="ログインする">

        <!--パスワードリマインダー-->
        <div class="remind-item">
          パスワードを忘れた場合は
          <a href="passRemindSend.php" class="remind-link">こちら</a>
        </div>

      </form>


    <!--フッター-->
    <?php
    require('footer.php');
     ?>
