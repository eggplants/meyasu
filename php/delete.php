<?php

session_start();
$num="";
$mysqli= new mysqli("localhost","s1811528","yaml","s1811528");
$mysqli->set_charset('utf8');
if ($mysqli->connect_error){
        echo $mysqli->connect_error;
        exit();
}else{
      	$mysqli->set_charset("utf-8");
}
if(isset($_POST['id'])){
//question
$sql= "delete from question where id=${_POST['id']}";
if($mysqli->query($sql)){
echo "成功しました！";
}else{
echo "idと形式違うか存在しないidだが.";
}
//answer
$sql= "delete from answer where id=${_POST['id']}";
$mysqli->query($sql);
//category
$sql= "delete from category where id=${_POST['id']}";
$mysqli->query($sql);
}
?>

<html>
<head>
  <title>削除画面</title>
  <meta charset="utf-8">
  <meta name="description" content="春日クラ代が7A棟入り口に設置している目安箱の,過去に投稿された質問と回答のデータベース.">
  <meta name="keywords" content="春日クラ代,目安箱,筑波大学">
  <meta name="author" content="春名航亨">
  <meta name="generator" content="atom">
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:site" content="@egpl0" />
  <!-- <meta property="og:url" content="" /> -->
  <meta property="og:title" content="春日キャンパス目安箱データベース" />
  <meta property="og:description" content="春日クラ代が7A棟入り口に設置している目安箱の,過去に投稿された質問と回答のデータベース" />
  <meta property="og:image" content="../img/sei.jpg" />
</head>
<body>
<fieldset>
<form action="" method="post">
削除したい入力内容のidを入力してください.<br>
<input type="text" name="id">
<input type="submit" name="submit">
</form>
</fieldset>
</body>
</html>
