<?php

$fID ="bohan";
$fPWD ="a1133313";

if(isset($_POST["uID"])&&isset($_POST["uPWD"])){
    $uID = $_POST["uID"];
    $uPWD = $_POST["uPWD"];

    if($fID==$uID && $fPWD==$uPWD){
        header("Location: camp.php");
        exit();
    }else{
        echo "登入失敗喔!";
        header("Refresh:2;url=login.php");
    }
}
?>
