<?php
include "params.php";


mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);


if (isset($_GET['p'])){
	$profile = $_GET['p'];
} else { 
	$profile='default'; 
}	
$sql="select w,m from SM_scoringProfile where name='$profile' limit 1";
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	$w= $row['w'];
	$m= $row['m'];
} 
echo "$w|$m";
?>
