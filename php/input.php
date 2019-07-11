<?php

session_start();
$num="";
$vali=false;
$mysqli = new mysqli("localhost","s1811528","yaml","s1811528");
if ($mysqli->connect_error){
        echo $mysqli->connect_error;
        exit();
}else{
      	$mysqli->set_charset("utf-8");
}
$sql ="SELECT max(id) FROM question";
if ($stmt = $mysqli->prepare($sql))
 {
$stmt->execute();
$stmt->bind_result($num);
$stmt->fetch();
}
if(!empty($_POST["submit"])){
	$vali=true;
	if(!preg_match('/[0-9]{8}/',$_POST["qdate"])){
         echo "うーん質問日正しく入っとらんが.<br>";
         $vali=false;
        }
        if(!preg_match('/[0-9]{8}/',$_POST["adate"])){
          echo "うーん回答日正しく入っとらんが.<br>";
          $vali=false;
         }
		if(preg_match('/[<>]/',$_POST["qdate"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["qcontent"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["qbelonging"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["adate"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["acontent"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["abelonging"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["tag1"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["tag2"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(preg_match('/[<>]/',$_POST["tag3"])){
         echo "うーん不正なキャラクタ入っとるが.<br>";
         $vali=false;
        }
	if(empty($_POST["qdate"])){
	 echo "うーん質問日入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["qcontent"])){
	 echo "うーん質問内容入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["qbelonging"])){
	 echo "うーん質問者所属入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["adate"])){ 
	 echo "うーん回答日入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["acontent"])){ 
	 echo "うーん回答内容入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["abelonging"])){ 
	 echo "うーん回答者所属入っとらんが.<br>";
	 $vali=false;
	}
      	if(empty($_POST["tag1"])){ 
	 echo "うーんタグ1入っとらんが.<br>";
	 $vali=false;
	}
	if(empty($_POST["tag2"])){ 
	 echo "うーんタグ2入っとらんが.<br>";
	 $vali=false;
	}
	if(empty($_POST["tag3"])){ 
	 echo "うーんタグ3入っとらんが.<br>";
	 $vali=false;
	}

}
$num++;
if($vali==true){
$mysqli = new mysqli("localhost","s1811528","yaml","s1811528");
$mysqli->set_charset('utf8');
if ($mysqli->connect_error){
        echo $mysqli->connect_error;
        exit();
}else{
      	$mysqli->set_charset("utf-8");
}

//question
$sql= "insert into question values(${num},\"${_POST['qdate']}\",\"${_POST['qcontent']}\",\"${_POST['qbelonging']}\")";
$mysqli->query($sql);

//answer
$sql= "insert into answer values(${num},\"${_POST['adate']}\",\"${_POST['acontent']}\",\"${_POST['abelonging']}\")";
$mysqli->query($sql);
//category
$sql= "insert into category values(${num},\"${_POST['tag1']}\")";
$mysqli->query($sql);

$sql= "insert into category values(${num},\"${_POST['tag2']}\")";
$mysqli->query($sql);

$sql= "insert into category values(${num},\"${_POST['tag3']}\")";
$mysqli->query($sql);

//history
$date = date('Y-m-d');
$sql= "insert into input_history values(${_POST["id"]},${num},\"${date}\")";
$mysqli->query($sql);
header( "Location: ../html/comp.html" );
}
$nump= str_pad($num,3,0,STR_PAD_LEFT);
?>

<html>
<head>
  <title>入力画面</title>
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
<table>
<tr>
<td>質問日(yyyymmdd):</td>
<td><input type="text" name="qdate"></td>
</tr>
<tr>
<td>質問者所属(未記入なら「なし」):</td>
<td><input type="text" name="qbelonging"></td>
</tr>
<tr>
<td>回答日(yyyymmdd):</td>
<td><input type="text" name="adate"></td>
</tr>
<tr>
<td>解答者所属(未記入なら「なし」):</td>
<td><input type="text" name="abelonging"></td>
</tr>
<tr>
<td>タグ1:</td>
<td><input type="text" name="tag1" style="width:30%"></td>
</tr>
<tr>
<td>タグ2:</td>
<td><input type="text" name="tag2" style="width:30%"></td>
</tr>
<tr>
<td>タグ3:</td>
<td><input type="text" name="tag3" style="width:30%"></td>
</tr>
</table>
(タグというか検索語.3つぐらいひねり出して欲しい......m(_ _)m)<br>
質問内容:<br><textarea name="qcontent" cols="50" rows="10" ></textarea><br>
回答内容:<br><textarea name="acontent" cols="50" rows="10" ></textarea><br>
<input type="hidden" name="id" value="<?=$_SESSION["id"]?>"><br>
<input type="hidden" name="inputid" value="<?=$num?>">
<input type="submit" name="submit" value="submit">
<div style="float:right; display:inline-flex">
<img src="../meyasu_data/data<?=$nump?>.jpg" height="800" />
</div>
</form>
</fieldset>
</body>
</html>
