<?php
//==========================================================
//     ログ
//==========================================================

//ログを取るか
  ini_set('log_errors','on');
//ログの出力ファイル
  ini_set('error_log','php.log');
//==========================================================


//==========================================================
//    デバッグ
//==========================================================
//デバッグフラグ
$debug_flg = true;
//デバッグ出力関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}
//==========================================================

//==========================================================
//        セッションの準備、セッション・クッキーの有効期限
//==========================================================
//セッションの置き場所を変更する（/var/tmp以下に置くと30日は削除されない）
session_save_path('/var/tmp/');
//セッションのガーベージコレクションの有効期限を30日に
ini_set('session.gc_maxlifetime',60*60*24*30);
//クッキーの有効期限を30日に
ini_set('session.cookie_lifetime',60*60*24*30);
//セッションをスタートする
session_start();
//セッションのIDを新しいものにする（なりすまし対策）
session_regenerate_id();

//==========================================================

//==========================================================
//　画面表示処理ログ吐き出し
//==========================================================

//デバッグログ
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション情報：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit']) ){
    debug('ログイン有効期限日時：'.($_SESSION['login_date'] + $_SESSION['login_limit']) );
  }
}
//==========================================================

//==========================================================
//　定数定義
//==========================================================
define('MSG01','入力必須ですよ');
define('MSG02','E-mailの形式じゃ無いっすよ');
define('MSG03','パスワード(再入力)が一致しません');
define('MSG04','6文字以上で入力しろください');
define('MSG05','255文字以内で入力してクレメンス');
define('MSG06','半角英数字のみ');
define('MSG07','エラー発生。しばらく経ってからやり直してね');
define('MSG08','そのE-mailは登録済みだ！！');
define('MSG09','E-mailまたはパスワードが違います');
define('MSG10','半角数字のみ');
define('MSG11','文字で入力してくださいまし。');
define('MSG12','これ違いますね');
define('MSG13','有効期限が切れてます');
define('MSG14','現在のパスワードと違います');
define('MSG15','現在のパスワードと同じです');
define('MSG16','500文字以内で入力してください');
define('SUC01','退会が完了しました');
define('SUC02','変更しました');
define('SUC03','メールを送信しました');
define('SUC04','登録しました！');


//==========================================================


//==========================================================
//グローバル変数
//==========================================================
//エラーメッセージ格納用の変数
$err_msg = array();

//==========================================================


//==========================================================
//バリデーション関数
//==========================================================

//未入力チェック
function validRequired($str,$key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//E-mail形式チェック
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//パスワード確認チェック
function validMatch($str1,$str2,$key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//最少文字数チェック
function validMinLen($str,$key,$min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

//最大文字数チェック
function validMaxLen($str,$key,$max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

//コメント欄の最大文字数
function validCommentLength($str,$key,$length = 500){
  if(mb_strlen($str) > $length){
    global $err_msg;
    $err_msg[$key] = MSG16;
  }
}

//半角英数字チェック
function validHalf($str,$key){
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//E-mail重複チェック
function validEmailDup($email){
  global $err_msg;
   try{
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE mail = :mail AND delete_flg = 0';
    $data = array(':mail'=> $email);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //DBに既に登録されている場合
    if(!empty(array_shift($result) ) ){
      $err_msg['common'] = MSG08;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

//パスワードチェックを1つの関数に
function validPass($str,$key){
  //最少・最大文字数チェック
  validMinLen($str,$key);
  validMaxLen($str,$key);
  //半角チェック
  validHalf($str,$key);
}

//半角数字チェック
function validNumber($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}

//固定長チェック
function validLength($str,$key,$length = 8){
  if(mb_strlen($str) !== $length){
    global $err_msg;
    $err_msg[$key] = $length.MSG11;
  }
}


//==========================================================


//==========================================================
//　処理の関数定義
//==========================================================

//DB接続関数
function dbConnect(){
  $dsn      = 'mysql:dbname=tayori;host=localhost;charset=utf8';
  $user     = 'root';
  $password = 'root';
  $options  = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト生成
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}

//クエリ実行関数
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);

  if(!$stmt->execute($data)){
    //クエリ失敗
    debug('queryPost失敗');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }

    //クエリ成功
    debug('クエリ成功');
    return $stmt;
}

//セッションを一度だけ取得する関数（そうしないとリロードした時に再度メッセージが表示されてしまう）
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

//フォーム入力保持＆DBデータ表示
function getFormData($str, $flg = true){
  debug('getFormData発動！');
  global $dbFormData;

  //メソッドによって分岐
  if($flg){
    //trueならPOST
    $method = $_POST;
  }else{
    //falseならGET
    $method = $_GET;
  }

  //DBデータがある場合
  if(!empty($dbFormData[$str])){
    //エラーがある場合
    if(!empty($err_msg[$str])){
      //フォーム入力がある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
        debug('パターン１');
      }

      //エラーが無い場合
    }else{
      //DBとフォームの情報が違う場合はフォームの情報を優先
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
        debug('パターン２');
      }
    }

  }else{
    //DBデータがなく、フォーム入力がある場合
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}

//ユーザー情報取得関数
function getUser($u_id){
  debug('getUser:ユーザー情報を取得します');

  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result){
      debug('クエリ成功');
      return $result;
    }else{
      debug('クエリ失敗');
      debug('失敗したSQL：'.print_r($stmt,true));
      return 0;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//エラーメッセージを取得する関数
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//認証キー等のランダム生成
function makeRandKey($length = 8){
  debug('makeRandKey:ランダムキーの作成処理');
  $str = '';
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
  for($i = 0; $i < $length; ++$i){
    $str .= $chars[mt_rand(0,61)];
  }
  return $str;
}

//メール送信関数
function sendMail($from,$to,$subject,$comment){
  debug('sendMail:メール送信処理');
  if(!empty($to) && !empty($subject) && !empty($comment)){
    //メール送信準備
    //現在使っている言語の設定
    mb_language('Japanese');
    //文字化け予防
    mb_internal_encoding('UTF8');

    //メールを送信
    $result = mb_send_mail($to,$subject,$comment,'from:'.$from);
    //結果を判定
    if($result){
      debug('メールを送信しました');
    }else{
      debug('メールの送信に失敗しました！');
      global $err_msg;
      $err_msg['common'] = MSG07;
    }
  }
}

//画像のアップロード
function uploadImg($file,$key){
  debug('uploadImg:画像アップロード処理');
  debug('ファイル情報：'.print_r($file,true));

  //$fileが画像ファイル形式だった場合
  if(isset($file['error']) && is_int($file['error'])){
    //例外処理
    try{
      //画像の各種エラーを設定

      switch($file['error']){
        //OK（正常）
         case UPLOAD_ERR_OK;
         debug('アップロードエラーOK');
        break;
        //ファイル未選択
         case UPLOAD_ERR_NO_FILE:
        throw new RuntimeException('ファイルが選択されていません');
        //サイズオーバー
         case UPLOAD_ERR_INI_SIZE:
         case UPLOAD_ERR_FORM_SIZE:
        throw new RuntimeException('ファイルサイズが大きすぎます');
        //その他
         default:
        throw new RuntimeException('エラーが発生しました');
      }

      //画像(拡張子)の形式が非対応のときのエラー設定
      //ファイルの形式を判別し、変数に
      $type = @exif_imagetype($file['tmp_name']);
      //それが以下のタイプのどれかでない（非対応の）場合。＊厳密にチェックするため第3引数にtrueをつける
      if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
        throw new RuntimeException('非対応のファイル形式です');
      }

      //アップロードされたファイルのパス移動、その際のエラーを設定
      //ファイル名を生成し、変数に格納
      $path = 'upload/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      //パスの移動が失敗した場合
      if(!move_uploaded_file($file['tmp_name'],$path)){
        throw new RuntimeException('ファイルの保存に失敗しました');
      }

      //ファイルの権限（パーミッション）を変更
      chmod($path,0644);

      //デバッグして、パスをリターン
      debug('ファイルのアップロード完了！');
      debug('パス情報：'.$path);
      return $path;


    }catch(RuntimeException $e){
      debug('エラー発生：'.$e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}

//ページネーション
function pagination($currentPageNum,$totalPageNum,$link = '',$pageColNum = 5){
  //現在のページが１の場合
  if($currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = $currentPageNum + 4;

  //現在ページが２だった場合
  }elseif($currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;

  //現在ページが４（設定したpageColNumのひとつ手前）だった場合
  }elseif($currentPageNum == ($pageColNum - 1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;

  //現在ページが最終ページの場合
  }elseif($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;

  //現在ページが最終ページの1つ手前だった場合
  }elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;

  //pageColNum よりも表示数が少ない場合
  }elseif($pageColNum > $totalPageNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;

  //現在のページが真ん中（３）の場合
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  //htmlの出力
  echo '<div>';
    echo '<ul class="pagenation-list">';
    //今いるページが１じゃない場合
    if($currentPageNum != 1){
      //一番左に　＜　をつける
      echo '<li class="pagenation-link"><a href="?p=1'.$link.'">&lt;</a></li>';
    }
    //最初と最後のページ以外は繰り返し処理で、設定したMAXページまで表示。今いるページにはアクティブをつけてスタイル変更
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
      echo '<li class="pagenation-link ';
      if($currentPageNum == $i){ echo 'active';}
      echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
    }
    //今いる最後のページ以外の場合
    if($currentPageNum != $maxPageNum && $maxPageNum > 1){
      //一番右に　＞　をつける
      echo '<li class="pagenation-link"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
    }
    echo '</ul>';
  echo '</div>';

}

//日記一覧表示用関数（ウンチ）
function getPoofList($currentMinNum,$sort,$span = 9){
  debug('getPoofList:ウンチ日記（一覧表示）情報を取得');

  //まずは全データのIDだけを取ってくる
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT id FROM poof WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT p.id FROM poof AS p INNER JOIN keep_poof AS k ON p.id = k.d_id WHERE  p.delete_flg = 0';
       break;
    }
    $data = array();

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    //全データの件数と、１ページ何件表示して、何ページなるか。の情報を変数に格納
    $rst['total']      = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total'] / $span);

    //クエリが失敗している場合はfalseを返す
    if(!$stmt){
      return false;
    }

    //ページネーション用に新たにクエリを作成
    $sql = 'SELECT * FROM poof WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT * FROM poof AS p INNER JOIN keep_poof AS k ON p.id = k.d_id WHERE  p.delete_flg = 0';
       break;

    }

    //１ページあたり何件表示するか($spanが表示件数、$currentMinNumが今のページの何件目からか)
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    debug('実行したSQL：'.$sql);

    if(!$stmt){
      return false;

    }else{
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }


  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//日記一覧表示用関数（食べたもの）
function getFoodList($currentMinNum,$sort,$span){
  debug('getFoodList:食べたもの日記（一覧表示）情報を取得');

  //まずは全データのIDだけを取ってくる
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT id FROM food WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT f.id FROM food AS f INNER JOIN keep_food AS k ON f.id = k.d_id WHERE  f.delete_flg = 0';
       break;

    }

    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    //全データの件数と、１ページ何件表示して、何ページなるか。の情報を変数に格納
    $rst['total']      = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total'] / $span);

    //クエリが失敗している場合はfalseを返す
    if(!$stmt){
      return false;
    }

    //ページネーション用に新たにクエリを作成
    $sql = 'SELECT * FROM food WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT * FROM food AS f INNER JOIN keep_food AS k ON f.id = k.d_id WHERE  f.delete_flg = 0';
       break;

    }

    //１ページあたり何件表示するか($spanが表示件数、$currentMinNumが今のページの何件目からか)
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    debug('実行したSQL：'.$sql);

    if(!$stmt){
      return false;

    }else{
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }


  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getPeeList($currentMinNum,$sort,$span = 9){
  debug('getPeeList:オシッコ日記（一覧表示）情報を取得');

  //まずは全データのIDだけを取ってくる
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT id FROM pee WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT p.id FROM pee AS p INNER JOIN keep_pee AS k ON p.id = k.d_id WHERE  p.delete_flg = 0';
       break;

    }

    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    //全データの件数と、１ページ何件表示して、何ページなるか。の情報を変数に格納
    $rst['total']      = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total'] / $span);

    //クエリが失敗している場合はfalseを返す
    if(!$stmt){
      return false;
    }

    //ページネーション用に新たにクエリを作成
    $sql = 'SELECT * FROM pee WHERE delete_flg = 0 ';

    //ソートがある場合
    switch($sort){
      case 1:
       $sql .= 'ORDER BY id DESC';
      break;

      case 2:
       $sql .= 'ORDER BY id ASC';
       break;

       case 3:
        $sql = 'SELECT * FROM pee AS p INNER JOIN keep_pee AS k ON p.id = k.d_id WHERE  p.delete_flg = 0';
       break;

    }

    //１ページあたり何件表示するか($spanが表示件数、$currentMinNumが今のページの何件目からか)
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();

    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    debug('実行したSQL：'.$sql);

    if(!$stmt){
      return false;

    }else{
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }


  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}


//GETパラメーターを条件によって付与する
//$del_key は　付与から取り除きたいキー
function appendGetParam($arr_del_key = array()){
  $str = '?';
  foreach($_GET as $key => $val){
    //GET情報のなかに取り除きたいキーがない場合、URLにくっつけるパラメータを作る
    if(!in_array($key,$arr_del_key,true)){
      $str .= $key.'='.$val.'&';
    }
  }
  //mb_substr(第一：文字列の何番目を取得するか。第二：どの位置から取得するか。マイナスを指定している場合は後ろから何番目かという意味。)
  //ここでは一番後ろの文字（＆）を消してる
  $str = mb_substr($str,0,-1,'UTF8');
  return $str;
}


//日記詳細取得関数（食べ物）
function getFoodDetail($d_id){
  debug('getFoodDetail:食べたもの日記の情報を取得します');

  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT f.date,f.time,f.user_id,f.pic1,f.pic2,f.pic3,f.title,f.comment, u.name FROM food AS f LEFT JOIN users AS u ON f.user_id = u.id WHERE f.id = :d_id';
    $data = array(':d_id' => $d_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);

    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//日記詳細取得関数（オシッコ）
function getPeeDetail($d_id){
  debug('getPeeDetail:おしっこ日記の情報を取得します');

  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT p.date,p.time,p.user_id,p.color,p.volume,p.number_times,p.title,p.comment, u.name FROM pee AS p LEFT JOIN users AS u ON p.user_id = u.id WHERE p.id = :d_id';
    $data = array(':d_id' => $d_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);

    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}


//日記詳細取得関数（ウンチ）
function getPoofDetail($d_id){
  debug('getPoofDetail:ウンチ日記の情報を取得します');

  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    //クエリ作成
    $sql = 'SELECT o.date,o.time,o.user_id,o.color,o.smell,o.shape,o.title,o.comment, u.name FROM poof AS o LEFT JOIN users AS u ON o.user_id = u.id WHERE o.id = :d_id';
    $data = array(':d_id' => $d_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);

    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//画像表示
function showImg($path){
  if(!empty($path)){
    return sanitize($path);
  }else{
    return '';
  }
}

//ピン留めした日記情報を取得（オシッコ）
function isKeepPee($d_id){
  debug('ピン留めしたオシッコ日記を取り出します');

  //例外処理
  try{
    $dbh  = dbConnect();
    $sql  = 'SELECT * FROM keep_pee WHERE d_id = :d_id AND delete_flg = 0';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt->rowCount()){
      //データがある場合
      debug('ピン留めされています');
      return true;

    }else{
      //データがない
      debug('ピン留めはされてませんね');
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }

}

//ピン留めした日記情報を取得（食べたもの）
function isKeepFood($d_id){
  debug('ピン留めした食べたもの日記を取り出します');

  //例外処理
  try{
    $dbh  = dbConnect();
    $sql  = 'SELECT * FROM keep_food WHERE d_id = :d_id AND delete_flg = 0';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt->rowCount()){
      //データがある場合
      debug('ピン留めされています');
      return true;

    }else{
      //データがない
      debug('ピン留めはされてませんね');
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }

}

//ピン留めした日記情報を取得（ウンチ）
function isKeepPoof($d_id){
  debug('ピン留めしたウンチ日記を取り出します');

  //例外処理
  try{
    $dbh  = dbConnect();
    $sql  = 'SELECT * FROM keep_poof WHERE d_id = :d_id AND delete_flg = 0';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt->rowCount()){
      //データがある場合
      debug('ピン留めされています');
      return true;

    }else{
      //データがない
      debug('ピン留めはされてませんね');
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}


//マイページ用ウンチリスト
function myPoofList($u_id,$span){
  debug('myPoofList:マイページのウンチ日記リスト取得');

  //例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM poof WHERE user_id = :u_id AND delete_flg = 0 ORDER BY date DESC LIMIT '.$span.' OFFSET 0';
    $data = array(':u_id'=> $u_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//マイページ用オシッコリスト
function myPeeList($u_id,$span){
  debug('myPeeList:マイページのオシッコ日記リスト取得');

  //例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM pee WHERE user_id = :u_id AND delete_flg = 0 ORDER BY date DESC LIMIT '.$span.' OFFSET 0';
    $data = array(':u_id'=> $u_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//マイページ用オシッコリスト
function myFoodList($u_id,$span){
  debug('myFoodList:マイページの食べたもの日記リスト取得');

  //例外処理
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM food WHERE user_id = :u_id AND delete_flg = 0 ORDER BY date DESC LIMIT '.$span.' OFFSET 0';
    $data = array(':u_id'=> $u_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}


//==========================================================



 ?>
