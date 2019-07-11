<?php
session_start();

// ログイン状態チェック
if (!isset($_SESSION["NAME"])) {
    header("Location: logout.php");
    exit;
}
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>メイン</title>
    </head>
    <body>
        <h1>編集画面</h1>
        <p>ようこそ<u><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></u>さん</p>
<fieldset>
<?php

$mysqli = new mysqli('localhost', 's1811528', 'yaml', 's1811528');
if ($mysqli->connect_error) {
  echo $mysqli->connect_error;
  exit();
} else {
  $mysqli->set_charset("utf8");
}
$sql= <<<SQL
SELECT count(*) FROM images where id not in (select id from question)
SQL;
if ($stmt = $mysqli->prepare($sql))
 {
$stmt->execute();
$stmt->bind_result($num);
$stmt->fetch();
$stmt->close();
}
$sql= 'SELECT nameid FROM worker where name="'.htmlspecialchars($_SESSION["NAME"], ENT_QUOTES).'";';
if ($stmt = $mysqli->prepare($sql))
 {
$stmt->execute();
$stmt->bind_result($id);
$stmt->fetch();
$stmt->close();
}
$_SESSION["id"]=$id;
?>

<b>残り<?php echo $num?>枚.</b>
        <ul>
　　　　　　<li><a href="upload.php">画像アップロード</a></li>
	　　<li><a href="input.php">入力作業</a></li>
	　　<li><a href="delete.php">削除作業</a></li><br>
	    <li><a href="logout.php">ログアウト</a></li>
        </ul>
</fieldset>
    </body>
</html>
