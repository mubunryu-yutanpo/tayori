<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('新規会員登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POSTされた場合
if(!empty($_POST)){
  debug('登録ボタンを押したよ');

  //変数定義
  $name =    $_POST['name'];
  $email =   $_POST['email'];
  $pass =    $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  //未入力バリデーション
  validRequired($name,'name');
  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_re');

  //エラーがない場合
  if(empty($err_msg)){
    //各種バリデーションチェック
    validMaxLen($name,'name');

    validEmail($email,'email');
    validEmailDup($email);

    validPass($pass,'pass');
    validPass($pass_re,'pass_re');
    validMatch($pass,$pass_re,'pass_re');

    //全てのバリデーションでエラーが無い場合
    if(empty($err_msg)){

      //例外処理
      try{
        //DB接続
        $dbh = dbConnect();
        //クエリ作成
        $sql = 'INSERT INTO users (name,mail,pass,login_time,create_date) VALUES(:name,:mail,:pass,:login,:date)';
        $data = array(':name'=> $name, ':mail'=> $email, ':pass'=> password_hash($pass,PASSWORD_DEFAULT), ':login'=> date('Y-m-d H:i:s'), ':date'=> date('Y-m-d H:i:s'));
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          //セッションの有効期限を設定する
          //デフォルトを1時間に
          $ses_limit = 60*60;
          //現在日時をログイン日時に記録
          $_SESSION['login_date'] = time();
          //ログイン有効期限を記録
          $_SESSION['login_limit'] = ($ses_limit + time());
          //ユーザーIDを格納
          $_SESSION['user_id'] = $dbh->lastInsertId();

          debug('登録成功。マイページに遷移しまっしょう');
          debug('セッション情報：'.print_r($_SESSION,true));
          //マイページへ遷移
          header('Location:mypage.php');
        }

      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}


 ?>

  <!--ヘッド、ヘッダー-->
<?php
  $siteTitle = 'ユーザー登録';
  require('head.php');
  require('header.php');
 ?>

    <!--トップ-->

      <form class="form" action="" method="post">
        <div class="signup-title">
          <h2 class="">ユーザー登録</h2>
        </div>

        <div class="area-msg"><?php if(isset($err_msg['common'])) echo $err_msg['common']; ?></div>

        <label>氏名（ニックネーム可） <!--エラーがある場合は'err'をクラスに追加-->
          <input type="text" name="name" class="input-style <?php if(!empty($err_msg['name']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['name']) ) echo $_POST['name']; ?>"> <!--POSTがある場合はそれを残す-->
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?></div>

        <label>E-mail <!--エラーがある場合は'err'をクラスに追加-->
          <input type="text" name="email" class="input-style <?php if(!empty($err_msg['email']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['email']) ) echo $_POST['email']; ?>"> <!--POSTがある場合はそれを残す-->
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['email']) ) echo $err_msg['email']; ?></div>

        <label>パスワード <!--エラーがある場合は'err'をクラスに追加-->
          <span class="form-txt">※半角英数字6文字以上</span>
          <input type="password" name="pass" class="input-style <?php if(!empty($err_msg['pass']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['pass']) ) echo $_POST['pass']; ?>"> <!--POSTがある場合はそれを残す-->
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['pass']) ) echo $err_msg['pass']  ?></div>

        <label>パスワード（再入力） <!--エラーがある場合は'err'をクラスに追加-->
          <input type="password" name="pass_re" class="input-style <?php if(!empty($err_msg['pass_re']) ) echo 'valid-err'; ?>" value="<?php if(!empty($_POST['pass_re']) ) echo $_POST['pass_re']; ?>"> <!--POSTがある場合はそれを残す-->
        </label>
        <div class="area-msg"><?php if(!empty($err_msg['pass_re']) ) echo $err_msg['pass_re']; ?></div>

        <!--とりあえずリンクにしてる-->
        <input type="submit" name="submit" class="submit" value="登録">
      </form>


    <!--フッター-->
<?php
require('footer.php')
 ?>
