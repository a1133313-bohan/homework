<body style="background-color: #c1dad6eb; font-size: 20px;">
<?php

$nName=$_POST["nName"];
$nphone=$_POST["nphone"];
$nIDNUM=$_POST["nid"];
$nemail=$_POST["nEmail"];
$nGender=$_POST["nGender"];
$nFavor=$_POST["nDeal"];
$nCity=$_POST["nCity"];
$nComment=$_POST["comment"];

echo "您的名字是:" .$nName. "<br/>";
echo "您是電話是:" .$nphone. "<br/>";
echo "身份證字號為:" .$nIDNUM. "<br/>";
echo "E-mail:" .$nemail. "<br/>";

if($nGender == "m"){
    echo "你的性別是:男性<br/>";
}else{
    echo "妳的性別是:女性<br/>";
}

if($nFavor == "m"){
    echo "你的口味偏好是:葷食<br/>";
}else{
    echo "你的口味偏好是:素食<br/>";
}

echo "你來自:" .$nCity. "<br/>";
echo "你想對主辦單位說~ " .$nComment. "<br/>";

?>