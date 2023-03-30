<?php
//================================================================
//　ログイン認証（切り分けした）ファイル
//================================================================

//セッションがある場合
if(!empty($_SESSION['login_date'])){
  debug('ログインしたことあります！');

  //ログイン有効期限内か
  if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ){
    //ログイン期限が切れている場合はログインページへ遷移
    debug('有効期限が切れてますね');
    //セッションを削除する
    session_destroy();
    header('Location:login.php');

  }else{
    debug('お帰りなさい');
    //有効期限内であれば、有効期限を現在日時に更新する
    $_SESSION['login_date'] = time();
    //現在実行中のスクリプトファイル名がlogin.phpの場合
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      //マイページに遷移させる(まだhtmlにしてる)
      debug('マイページに飛ばしますわ');
      header('Location:mypage.php');
    }

  }
}else{
  //未ログインの場合
  debug('未ログインユーザーです');

  //今いるページがログインページじゃない場合は、ログインページに遷移する
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    debug('ログインページに遷移します');
    header('Location:login.php');
  }
}
 ?>
