<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード再発行ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

//セッションに認証キーがある場合（ない場合はremindsendページへ）
if(!empty($_SESSION['auth_key'])){
  debug('passRemindRecive//認証キーがセッションにあります');
}else{
  debug('認証キーがなかったので遷移しちゃうよ');
  header('Location:passRemindSend.php');
}

  //POSTがある場合
  if(!empty($_POST)){
    $token = $_POST['token'];
    debug('POSTあり。POST情報：'.print_r($token,true));
    //バリデーション
    validRequired($token,'token');

    if(empty($err_msg)){
      debug('未入力チェックOK');

      //固定長チェック
      validLength($token,'token');

      if(empty($err_msg)){
        debug('バリデーションOK');

        //POSTの内容と認証キーが一致しない場合
        if($token !== $_SESSION['auth_key']){
          $err_msg['token'] = MSG12;
        }
        //認証キーの有効期限が切れている場合
        if($_SESSION['auth_key_limit'] < time()){
          $err_msg['common'] = MSG13;
        }

        //バリデーション、認証でエラーがない場合
        if(empty($err_msg)){
          debug('認証もOK');

          //新しいパスワードを作成
          $rand_pass = makeRandKey();
          debug('新しくランダム生成したパスワード：'.$rand_pass);

          //DBを上書きする。例外処理
          try{
            //DB接続
            $dbh = dbConnect();
            //クエリ作成
            $sql = 'UPDATE users SET pass = :pass WHERE mail = :mail';
            $data = array(':pass' => password_hash($rand_pass,PASSWORD_DEFAULT), ':mail' => $_SESSION['auth_mail']);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);

            //クエリが失敗している場合
            if(!$stmt){
              debug('DBに上書きするクエリ失敗');
              $err_msg['common'] = MSG07;

            }else{

              //メールの送信準備
              $from    = 'TAYORI@info.com';
              $to      = $_SESSION['auth_mail'];
              $subject = '『新規パスワード発行』 | TAYORI';
              $comment = <<<EOT
   新規パスワードを発行しました。
   下記URLのログインページより、お送りしたパスワードをご入力し、再度ログインしてください。

   ※パスワードはログイン後、マイページの「パスワード変更」のページよりご変更ください。

   ////////////////////////////////////////////////////

   新しいパスワード：{$rand_pass}
   ログインページ：http://localhost/web_seOP/login.php

   ////////////////////////////////////////////////////

   その他、ご不明点などございましたら、下記URLよりお問い合わせください。

   ---------------------------------------------------

    TAYORIカスタマーサポートセンター

   お問い合わせURL ：http://TAYORI.info

   E-mail : TAYORI@info.com

   ---------------------------------------------------

   なお、本メールに覚えのない場合は、お手数ですが破棄して頂くようお願い申し上げます。
   EOT;

              //メールを送信
              sendMail($from,$to,$subject,$comment);
              debug('DBの上書き、メール送信を完了');

              //セッションを削除する
              session_unset();
              $_SESSION['msg_success'] = SUC03;
              //ログインページに遷移
              header('refresh:3;url=login.php');

            }
          }catch(Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
          }

        }
      }
    }
  }

 ?>

<?php
$siteTitle = 'パスワード再発行ページ';
require('head.php');
require('header.php');
 ?>
    <!--トップ-->

    <!--メッセージを表示するエリア-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>


      <form class="form" action="" method="post">
        <div class="signup-title">
          <h2 class="">認証キーを入力してください</h2>
        </div>

        <div class="area-msg"><?php echo getErrMsg('common'); ?></div><!--ここにエラーメッセージが入る-->

        <label>
          <input type="text" class="input-style" name="token" value="<?php if(!empty($_POST['token'])) echo $_POST['token']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('token'); ?></div><!--ここにエラーメッセージが入る-->

        <input type="submit" name="submit" class="submit" value="送信">
      </form>


    <!--フッター-->
<?php
require('footer.php');
 ?>
