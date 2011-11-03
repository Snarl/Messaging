<?php
$xml = $_POST["xml"];
$xml = str_ireplace('\\','',$xml);
$myFile = "smcsettings.xml";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $xml);
fclose($fh);
echo "Settings saved OK";
?>
