<?php
header('Access-Control-Allow-Origin: *');
include "createthumb.php";

$u = uniqid();
$report = "./reports/" . $u;

$xml = "<report id='$u'>\n";
$xml.= "<what>" . $_POST['what'] . "</what>\n";
$xml.= "<where>". $_POST['where']. "</where>\n";
$xml.= "<when>". $_POST['when'] . "</when>\n";
$xml.= "<gps>" . $_POST['gps'] . "</gps>\n";
$xml.= "<compass>'" . $_POST['compass'] . "'</compass>\n";
$xml.= "<GMT>" . gmdate(DATE_RFC822) . "</GMT>\n";

if (isset($_POST['pic1'])) {
  $pic = base64_decode( $_POST['pic1']);
  if (strlen($pic) > 9 ) {
    $fh = fopen("$report-1.jpg", 'w') or die("can't open file");
    fwrite($fh, $pic);
    fclose($fh);
	createthumb("$report-1.jpg","$report-1t.jpg");
    $xml.="<picture thumb='$u-1t.jpg'>$u-1.jpg</picture>\n";
  }
}

if (isset($_POST['pic2'])) {
  $pic = base64_decode( $_POST['pic2']);
  if (strlen($pic) > 9 ) {
    $fh = fopen("$report-2.jpg", 'w') or die("can't open file");
    fwrite($fh, $pic);
    fclose($fh);
	createthumb("$report-2.jpg","$report-2t.jpg");
    $xml.="<picture thumb='$u-2t.jpg'>$u-2.jpg</picture>\n";
  }
}
if (isset($_POST['pic3'])) {
  $pic = base64_decode( $_POST['pic3']);
  if (strlen($pic) > 9 ) {
    $fh = fopen("$report-3.jpg", 'w') or die("can't open file");
    fwrite($fh, $pic);
    fclose($fh);
	createthumb("$report-3.jpg","$report-3t.jpg");
    $xml.="<picture thumb='$u-3t.jpg'>$u-3.jpg</picture>\n";
  }
}
if (isset($_POST['pic4'])) {
  $pic = base64_decode( $_POST['pic4']);
  if (strlen($pic) > 9 ) {
    $fh = fopen("$report-4.jpg", 'w') or die("can't open file");
    fwrite($fh, $pic);
    fclose($fh);
	createthumb("$report-4.jpg","$report-4t.jpg");
    $xml.="<picture thumb='$u-4t.jpg'>$u-4.jpg</picture>\n";
  }
}
$xml.="</report>\n";
$fh = fopen("$report.xml", 'w') or die("can't open file");
fwrite($fh, $xml);
fclose($fh);
?>
