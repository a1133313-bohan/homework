<?php
session_start();
// 清除所有對應的 Session 資料
$_SESSION = array();
session_destroy();
// 重新導引回登入大門
header("Location: login.php");
exit;
?>