<?php
include 'params.php';
if (!isset($_GET['name'])) die;
$name = $_GET['name'];
$w = $_GET['w'];
$m = $_GET['m'];

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);

$sql = "select count(*) as count from SM_scoringProfile where name='$name'";
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
while ($row = mysql_fetch_assoc($result)) {
	$count= $row['count'];
} 
if ($count == 0){
	$sql = "insert into SM_scoringProfile (name, w, m) values ('$name','$w','$m')";
} else {
	$sql = "update SM_scoringProfile set w='$w', m='$m' where name='$name'";
}	
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
echo "Reputation scoring profile:$name saved OK";
?>
