<?php
include 'params.php';
if (!isset($_GET['sname'])) die;
$sname = $_GET['sname'];
$id = $_GET['id'];
$score = $_GET['score'];

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);

$sql = "insert into sm_score (uid, screenName, score, isTroll, color)
values ('$id', '$sname', $score, 'N', '000000')";
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
echo "User $sname added OK"
?>
