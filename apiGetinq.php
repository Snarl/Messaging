<?php
include 'params.php';
$r=22;
if (isset($_GET['r'])){
	$r = $_GET['r'];
}	
$outputarray=array();
mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);
$sql = "select q.*, s.score from sm_inq q left join sm_score s on s.screenName = q.uid where s.isTroll <> 'Y' and dealtwith is NULL  order by msg_id DESC limit $r";
$result = mysql_query($sql) or die(mysql_error(). $sql);
while ($row = mysql_fetch_assoc($result)) {
	$outputarray[]=$row;
}
//echo json_encode($outputarray,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
$j= json_encode($outputarray);
$j=str_replace('# ', '! ', $j );
echo $j;
?>
