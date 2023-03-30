<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('食べたもの日記のAjax処理ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//ウェブカツではisLogin使ってたけど、自分の日記につけるだけだから多分いらない
require('auth.php');

//POSTとユーザーIDがある場合
if(isset($_POST['d_id']) && !empty($_SESSION['user_id'])){
  //変数に格納
  $d_id = $_POST['d_id'];
  $u_id = $_SESSION['user_id'];

  debug('d_id:'.print_r($d_id,true));
  debug('u_id:'.print_r($u_id,true));

  //例外処理
  try{
    $dbh = dbConnect();
    //まずは全部のデータを取得する
    $sql = 'SELECT * FROM keep_food WHERE d_id = :d_id AND user_id = :u_id';
    $data = array(':d_id'=> $d_id, ':u_id' => $u_id);
    $stmt = queryPost($dbh,$sql,$data);
    //取得したレコードを変数に
    $result = $stmt->rowCount();
    debug($result);

    //$resuleの中身がある＝既にピン留めされている場合は削除
    if(!empty($result)){
      $sql = 'DELETE FROM keep_food WHERE d_id = :d_id AND user_id = :u_id';
      $data = array(':d_id'=> $d_id, ':u_id'=> $u_id);
      $stmt = queryPost($dbh,$sql,$data);

    }else{
      //ない場合は登録
      $sql = 'INSERT INTO keep_food (d_id,user_id,create_date) VALUES (:d_id,:u_id,:date)';
      $data = array(':d_id' => $d_id, ':u_id' => $u_id, ':date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh,$sql,$data);
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
 ?>
