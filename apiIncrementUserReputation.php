<?php
//apiIncrementUserReputation.php?sname=fred&delta=-10&type=p&msgid=m
//type: p = percent, a=actual
include 'params.php';
if (!isset($_GET['sname'])) die;
$sname = $_GET['sname'];
$delta = $_GET['delta'];
$m = $_GET['msgid'];

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);

$sql = "update sm_inq set dealtwith = 'Y' where msg_id='$m'";
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
$sql = "update sm_score set score = score + (score * $delta /100) where screenName='$sname'";
$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
echo "OK.  User $sname, reputation adjusted by $delta"
?>
