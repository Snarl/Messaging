<?php

include 'params.php';
$what  = (isset($_GET['what'])) ?addslashes($_GET['what']):"";
$where = (isset($_GET['where']))?addslashes($_GET['where']):"";
$uuid  = (isset($_GET['where']))?addslashes($_GET['uuid']):"";
$id    = ($uuid=="")?uniqid():$uuid.time();
$id = "9".$id;

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);
$sql= "insert into sm_inq (msg_plain, msg_html, tags, sender, istagged, msg_id, sender_score, published, geo, locn, lang, uid)
   values ('$what - $where', '$what - $where', 'sukeyreport $where', '$uuid', 'Y', '$id', 0, '" . date('d/m/Y H:i:s') . "', '', '', '', '$uuid' )";
$result = mysql_query($sql) or die(mysql_error(). $sql);
echo 'Reported: OK';

?>
