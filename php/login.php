<?php

session_start();

$db['host'] = "localhost";
$db['user'] = "s1811528";
$db['pass'] = "yaml";
$db['dbname'] = "s1811528";

$errorMessage = "";

if (isset($_POST["login"])) {
    if (empty($_POST["username"])) {  
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $userid = $_POST["username"];

        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $stmt = $pdo->prepare('SELECT * FROM worker WHERE name = ?');
            $stmt->execute(array($userid));

            $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['hash'])) {
                    session_regenerate_id(true);
                    $id = $row['name'];
                    $sql = "SELECT * FROM worker WHERE name ='".$id."'";
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['name'];
                    }
                    $_SESSION["NAME"] = $row['name'];
                    header("Location: edit.php");
                    exit();
                } else {
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            $errorMessage = $sql;
            $e->getMessage() ;
        }
    }
}
?>

<html>
<head>
  <title>春日キャンパス目安箱データベース</title>
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
<div align="center">
<img src="../img/superlog.png" width="300px">
<p margin="10px"></p>
<form id="loginForm" name="loginForm" action="" method="POST">
<fieldset>
<input type="text" name="username" size="10" maxlength="30" placeholder="username"><br>
<input type="password" name="password" size="10" maxlength="30" placeholder="password"><br>
<input type="submit" name ="login" value = "ログイン"><br>
<h2><span style="color:#ff0000;"><?php echo $errorMessage ?></span>
</fieldset>
</form>
*仮パスワードはid:test,pw:testです.ㅤㅤㅤㅤㅤ<br>
*ご登録の際には<a href="mailto:harunawataru1016@yahoo.co.jp">管理者</a>に連絡してください.<br>
<p margin="10px"></p>
<a href="../index.html">>>トップへ<<</a>
</div>
</body>
</html>
