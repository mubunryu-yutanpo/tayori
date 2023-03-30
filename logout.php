<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウトページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

//セッションを削除
session_destroy();
debug('ログアウトしました。ログインページへ');
header('Location:login.php');
 ?>
