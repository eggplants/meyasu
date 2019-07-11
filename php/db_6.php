<?php

//データベース接続
$server = "localhost";  
$userName = "s1811528"; 
$password = "yaml"; 
$dbName = "s1811528";
 
$mysqli = new mysqli($server, $userName, $password,$dbName);
 
if ($mysqli->connect_error){
	echo $mysqli->connect_error;
	exit();
}else{
	$mysqli->set_charset("utf-8");
}

$sql = "SELECT * FROM images";

$result = $mysqli -> query($sql);

//クエリー失敗
if(!$result) {
	echo $mysqli->error;
	exit();
}

//レコード件数
$row_count = $result->num_rows;

//連想配列で取得
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$rows[] = $row;
}

//結果セットを解放
$result->free();

// データベース切断
$mysqli->close();
 
?>

<!DOCTYPE html>
<html>
<head>
<title>imageテーブル表示</title>
<meta charset="utf-8">
</head>
<body>
<h1>imageテーブル表示</h1> 

レコード件数：<?php echo $row_count; ?>

<table border='1'>
<tr><th>id</th><th>image_data_path</th></tr>

<?php 
foreach($rows as $row){
?> 
<tr> 
	<td><?php echo $row['id']; ?></td> 
	<td><?php echo '<a href="../'.substr($row['img'],0,25).'">'.substr($row['img'],0,25).'</a>'; ?></td> 
</tr> 
<?php 
} 
?>
 
</table>

</body>
</html>
