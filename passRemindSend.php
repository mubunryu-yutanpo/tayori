<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('認証キー発行ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

//POSTがある場合
if(!empty($_POST)){
  debug('passRemindSend/POSTがありました。');
  debug('POST情報：'.print_r($_POST,true));

  //POSTを変数に
  $mail = $_POST['mail'];
  //バリデーションチェック
  validRequired($mail,'mail');

  if(empty($err_msg)){
    debug('未入力チェックOK');
    //バリデーション残り
    validEmail($mail,'mail');
    validMaxLen($mail,'mail');
    //エラーが無い場合
    if(empty($err_msg)){
      debug('バリデーションOK');

      //mailをもとにDBと照合
       //例外処理
       try{
         //DB接続
         $dbh = dbConnect();
         //クエリ作成
         $sql = 'SELECT count(*) FROM users WHERE mail = :mail AND delete_flg = 0';
         $data = array(':mail' => $mail);
         //クエリ実行
         $stmt = queryPost($dbh,$sql,$data);
         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         //クエリが成功し、データがある場合
         if($stmt && array_shift($result)){
           debug('取得したデータ:'.print_r($result,true));

           //認証キーを生成
           $auth_key = makeRandKey();

           //メール送信の準備
           $from    = 'TAYORI@info.com';
           $to      = $mail;
           $subject = '『認証キー発行』 | TAYORI';
           $comment = <<<EOT
ご入力のメールアドレスに認証キーをお送りしました。
下記の認証キーを、パスワード再発行ページにご入力頂くと、新しいパスワードを発行いたします。

////////////////////////////////////////////////////

認証キー：{$auth_key}
※認証キーの有効期限は30分です。

パスワード再発行ページ：http://localhost/web_seOP/passRemindRecieve.php

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
           //メッセージを表示
           $_SESSION['msg_success'] = SUC03;

           //セッションに情報を保存
           $_SESSION['auth_mail'] = $mail;
           $_SESSION['auth_key'] = $auth_key;
           //認証キーの有効期限を30分に
           $_SESSION['auth_key_limit'] = time() + 60*30;

           //セッション情報をデバッグ
           debug('セッション情報：'.print_r($_SESSION,true));
           debug('パス再発行ページへ遷移');
           header('refresh:3;url=passRemindRecive.php');


         }else{
           debug('マッチするデータが無いか、DB接続に失敗');
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
$siteTitle = '認証キー発行ページ';
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
          <h2 class="">パスワード再発行</h2>
        </div>

        <div class="area-msg"><?php echo getErrMsg('common'); ?></div><!--ここにエラーメッセージが入る-->

        <label>送信先E-mail
          <input type="text" class="input-style" name="mail" value="<?php if(!empty($_POST['mail'])) echo $_POST['mail']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('mail'); ?></div><!--ここにエラーメッセージが入る-->

        <input type="submit" name="submit" class="submit" value="送信">

      </form>


    <!--フッター-->
<?php
require('footer.php');
 ?>
