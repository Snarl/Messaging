<?php
//apiSetUserTrollFlag?sname=fred&to=true
include 'params.php';
if (!isset($_GET['sname'])) die;
$sname = $_GET['sname'];
$tf = $_GET['to'];

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);

$sql = "select count(*) as count from sm_score where screenName='$sname'";
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
$row = mysql_fetch_assoc($result);
if ($row['count'] == 0){
	$sql = "insert into sm_score (screenName, uid, isTroll, score, color) values
								 ('$sname', 0, '$tf', 0.00,'000000')";
} else {			
	$sql = "update sm_score set isTroll='$tf' where screenName='$sname'";
}
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
echo "OK.  User $sname, Troll set to $tf"
?>
