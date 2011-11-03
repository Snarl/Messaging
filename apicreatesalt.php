<?php
$salt='';
for ($i=0; $i<256; $i++){
$salt .= chr(rand(32,126));
}
echo $salt;
?>
