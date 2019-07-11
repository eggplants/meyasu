<?php
$server = "localhost";  
$userName = "s1811528"; 
$password = "yaml"; 
$dbName = "s1811528";
$mysqli = new mysqli($server, $userName, $password,$dbName);
$rows=[];
if ($mysqli->connect_error){
	echo $mysqli->connect_error;
	exit();
}else{
	$mysqli->set_charset("utf-8");
}
if (!isset($_POST["search"])){
$sql = <<<SQL
SELECT 
DISTINCT question.id as qid,
question.date as qdate,
question.content as qcontent,
question.belonging as qbelong,
answer.date as adate,
answer.content as acontent,
answer.belonging as abelong
FROM question,answer,category 
where question.id=answer.id 
and answer.id=category.id;
SQL;
}else{
$key = $_POST["search"];
$sql = <<<SQL
SELECT DISTINCT question.id as qid, 
 question.date as qdate, 
 question.content as qcontent, 
 question.belonging as qbelong, 
 answer.date as adate, 
 answer.content as acontent, 
 answer.belonging as abelong
FROM            question
JOIN answer
ON question.id = answer.id
JOIN category
ON answer.id = category.id
WHERE question.id = ANY         ( 
                                       SELECT id 
                                       FROM   question 
                                       WHERE  date LIKE '%{$key}%' 
                                       OR     content LIKE '%{$key}%' 
                                       OR     belonging LIKE '%{$key}%') 
OR question.id  = ANY 
				(
                                       SELECT id 
                                       FROM answer 
                                       WHERE date LIKE '%{$key}%' 
                                       OR content LIKE '%{$key}%' 
                                       OR belonging like '%{$key}%') 
OR question.id = ANY 
				(
                                       SELECT id 
                                       FROM category 
                                       WHERE tag LIKE '%{$key}%') 
SQL;
}
$result = $mysqli -> query($sql);

if(!$result) {
	echo $mysqli->error;
	exit();
}

//レコード件数
$row_count = $result->num_rows;

//連想配列で取得
while($row = $result->fetch_array(MYSQLI_ASSOC)){
$rows[]=$row;
}
$result->free();
$mysqli->close();
 
?>

<!DOCTYPE html>
<html>
<head>
<title>データ一覧</title>
<meta charset="utf-8">
</head>
<body>
<h1>データ一覧</h1> 
検索:<br>
<form action="" method="post">
   <input class="search" type="text" name="search" placeholder="Please enter any keyword(s)">
   <p></p>
   <input class="btn-square" type="submit" name="sub" value="検索する"></button>
</form>
レコード件数：<?php echo $row_count; ?>

<table border='1'>
<tr><th>質問番号</th><th>質問日時</th><th>質問</th><th>所属</th>
<th>回答日</th><th>回答内容</th><th>所属</th>


<?php 
  foreach($rows as $row){
?> 
<tr> 
	<td><?php echo '<a href="download.php?id=' . $row['qid'] . '">'.$row['qid'].'</a>'; ?></td> 
	<td><?php echo $row['qdate']; ?></td>
	<td><?php echo $row['qcontent']; ?></td>
	<td><?php echo $row['qbelong']; ?></td>
        <td><?php echo $row['adate']; ?></td>
        <td><?php echo $row['acontent']; ?></td>
        <td><?php echo $row['abelong']; ?></td>
 
</tr> 
<?php 
} 
?>
 
</table>

</body>
</html>

