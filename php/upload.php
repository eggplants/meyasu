<?php
session_start();

$db['host']   = "localhost";
$db['user']   = "s1811528";
$db['pass']   = "yaml";
$db['dbname'] = "s1811528";
$dsn          =  sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
$pdo          = new PDO($dsn, $db['user'], $db['pass'], array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
));
$stmt         = $pdo->prepare('SELECT MAX(id) as n FROM images');
$stmt->execute();
$newestid     = "";

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if (isset($row['n'])) {
           $newestid = $row['n'];
    }
}

extract($_REQUEST);
$msg      = "";
$err      = array();
$now      = date("YmdHis");
$_FILES   = isset($_FILES) ? $_FILES : array();
$action   = isset($action) ? $action : 0;
$files    = isset($files) ? $files : array();
$form     = isset($form) ? $form : array();
$delfiles = isset($delfiles) ? $delfiles : array();

foreach($form as $key => $val) { 
$form[$key] = htmlspecialchars_decode($val);
}

if ($action) {
    

    foreach ($delfiles as $key => $val) {
        @unlink($files[$key]["tmp_name"]);
        unset($files[$key]);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    
    foreach ($_FILES as $key => $val) {
        if (!$val["tmp_name"])
            continue;
        $files[$key]["name"] = $val["name"];

        list($mime, $ext) = explode("/", finfo_file($finfo, $val["tmp_name"]));
        if ($mime != "image")
            $err[] = "ファイル{$key} は画像を選択してください";
        if ($mime != "image")
            unset($files[$key]);
        if ($mime != "image")
            continue;

        copy($val["tmp_name"], "/home/2019/s1811528/public_html/meyasu/meyasu_data/tmp/{$now}_{$key}.{$ext}");
        $files[$key]["tmp_name"] = "/home/2019/s1811528/public_html/meyasu/meyasu_data/tmp/{$now}_{$key}.{$ext}";
        $files[$key]["url"]      = "http://{$_SERVER['SERVER_NAME']}/~s1811528/meyasu/meyasu_data/tmp/{$now}_{$key}.{$ext}";
    }
    
    finfo_close($finfo);
    

    if (!count($files))
        $err[] = "少なくとも一つはファイルをアップロードしてください.\n";
    
    if (!count($err)) {
        
        if ($action == 2) {
            
            // 本アップロード
            foreach ($files as $key => $val) {
                $newestid++;
                $newestid = str_pad($newestid, 3, 0, STR_PAD_LEFT);
                rename($val["tmp_name"], "/home/2019/s1811528/public_html/meyasu/meyasu_data/data" . $newestid . ".jpg");
                $files[$key]["url"] = "http://{$_SERVER['SERVER_NAME']}/~s1811528/meyasu/meyasu_data/data" . $newestid . ".jpg";
                $path               = "./meyasu_data/data".$newestid.".jpg";
                $stmt               = $pdo->prepare("INSERT INTO images (img) VALUES (?)");
                $stmt->execute(array($path));
            }
            
            $subject = "ファイルの送信が完了しました.";
            $text = "以下のファイルの送信が完了いたしました.\n";
            foreach ($files as $key => $val) {
                $text .= "<a href=\"{$val['url']}\" data-lightbox=\"files\" target=\"_blank\">{$val['name']}</a>";
            }
        }
        
    } else {
        
        // エラーメッセージ
        $msg    = $err;
        $action = 0;
        
    }
    
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>アップロード</title>
</head>
<body>
<h1>画像アップロード</h1>
<?php
if (!$action) {
?>

<form action="#" method="post" enctype="multipart/form-data">
<table>
<?php
    for ($i = 1; $i <= 10; $i++) {
?>
<tr><th>ファイル<?= $i ?></th><td><input type="file" name="<?= $i ?>" />
<?php
        if (isset($files[$i])) {
?> <a href="<?= $files[$i]["url"] ?>" data-lightbox="files" target="_blank"><?= $files[$i]["name"] ?></a>
<input type="checkbox" name="delfiles[<?= $i ?>]" value="1" id="delfiles<?= $i ?>" /><label for="delfiles<?= $i ?>">このファイルを削除</label><?php
        }
?>
</td></tr>
<?php
    }
?>
</table>
<p><input type="submit" value="送信内容を確認する" class="button" /></p>
<p><a href="edit.php">編集画面に戻る</a></p>
<input type="hidden" name="action" value="1" />
<?php
    foreach ($files as $key1 => $val1) {
?>
<?php
        foreach ($val1 as $key2 => $val2) {
?>
<input type="hidden" name="files[<?= $key1 ?>][<?= $key2 ?>]" value="<?= $val2 ?>" /><?php
        }
?><?php
    }
?>
</form>

<?php
} elseif ($action == 1) {
?>

<p>下記の内容でファイルを送信します。よろしければ「送信する」ボタンを押してください。</p>
<table>
<?php
    for ($i = 1; $i <= 10; $i++) {
?>
<tr><th>ファイル<?= $i ?></th><td><?
        if (isset($files[$i])) {
?><a href="<?= $files[$i]["url"] ?>" data-lightbox="files" target="_blank"><?= $files[$i]["name"] ?></a><?php
        }
?></td></tr>
<?php
    }
?>
</table>

<form action="upload.php" method="post">
<p><input type="submit" value="送信する" class="button" /></p>
<input type="hidden" name="action" value="2" />
<?php
    foreach ($files as $key1 => $val1) {
?>
 <?php
        foreach ($val1 as $key2 => $val2) {
?>
   <input type="hidden" name="files[<?= $key1 ?>][<?= $key2 ?>]" value="<?= $val2 ?>" /><?php
        }
?><?php
    }
?>
<?php
    foreach ($form as $key => $val) {
?><input type="hidden" name="form[<?= $key ?>]" value="<?= $val ?>" /><?php
    }
?>
</form>

<form action="#" method="post">
<p><input type="submit" value="訂正する" class="button" /></p>
<input type="hidden" name="action" value="0" />
<?php
    foreach ($files as $key1 => $val1) {
?>
 <?php
        foreach ($val1 as $key2 => $val2) {
?>
   <input type="hidden" name="files[<?= $key1 ?>][<?= $key2 ?>]" value="<?= $val2 ?>" />
  <?php
        }
?><?php
    }
?>
<?
    foreach ($form as $key => $val) {
?><input type="hidden" name="form[<?= $key ?>]" value="<?= $val ?>" /><?php
    }
?>
</form>

<?php
} elseif ($action == 2) {
?>

<p class="html pre"><?= $subject ?></p>
<p class="html pre"><?= $text ?></p>
<p><a href="upload.php">送信フォームへ戻る</a></p>
<p><a href="edit.php">編集画面に戻る</a></p>
<?php
}
?>
</body></html>
