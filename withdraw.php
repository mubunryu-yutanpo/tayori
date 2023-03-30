<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会ページです');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

//POST（退会ボタンが押された場合）
if(!empty($_POST)){
  debug('POSTがあります。退会処理を始めます');

  //セッションIDを変数に
  $u_id = $_SESSION['user_id'];
  //例外処理
  try{
    $dbh = dbConnect();
    //それぞれ（情報残しておかないDBテーブル）のクエリ作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
    $sql2 = 'UPDATE food SET delete_flg = 1 WHERE user_id = :id';
    $sql3 = 'UPDATE poof SET delete_flg = 1 WHERE user_id = :id';
    $sql4 = 'UPDATE pee SET delete_flg = 1 WHERE user_id = :id';

    $data = array(':id' => $u_id);
    //それぞれクエリ実行
    $stmt1 = queryPost($dbh,$sql1,$data);
    $stmt2 = queryPost($dbh,$sql2,$data);
    $stmt3 = queryPost($dbh,$sql3,$data);
    $stmt4 = queryPost($dbh,$sql4,$data);

    //
    if($stmt1 && $stmt2 && $stmt3 && $stmt4){
      debug('ユーザー：'.$u_id.'の退会処理をしました');
      //セッションを削除
      session_destroy();
      //トップページへ
      header('refresh:4;url=topLp.php');
      //完了メッセージを表示
      $_SESSION['msg_success'] = SUC01;

    }else{
      debug('クエリ失敗');
      $err_msg['common'] = MSG07;
    }


  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
 ?>


<?php
$siteTitle = '退会ページ';
require('head.php');
require('header.php');
 ?>

    <!--トップ-->

      <!--メッセージを表示するエリア-->
      <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
      </p>

      <form class="form" action="" method="post">

        <div class="withdraw-item">
          <h2>退会しますか？</h2>
          <p>※退会すると今までの記録が全て消えます</p>
        </div>

        <input type="submit" name="submit" class="submit" value="退会する">

      </form>


    <!--フッター-->
<?php
require('footer.php');
 ?>
