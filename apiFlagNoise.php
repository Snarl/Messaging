<?php
//Deletes messages from INq, does not affect users' reputations 
include 'params.php';
if (isset($_GET['msg'])){
	$m = $_GET['msg'];
}	
mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);
$sql = "delete from sm_inq where msg_id in ($m)";
$result = mysql_query($sql) or die(mysql_error(). $sql);
echo 'Flagged as Noise: OK';
?>
