<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ajax : 食べたもの日記のpic2削除');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');

//POSTがある場合
if(!empty($_POST['d_id']) && !empty($_SESSION['user_id'])){
  $d_id = $_POST['d_id'];
  debug('d_id：'.print_r($d_id,true));

  //例外処理
  try{
    //DB接続し、pic1情報だけ削除
    $dbh = dbConnect();
    $sql = 'UPDATE food SET pic2 = null WHERE id = :d_id AND user_id = :u_id';
    $data = array(':d_id'=> $d_id, 'u_id'=> $_SESSION['user_id']);

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
 ?>2
