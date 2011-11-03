<?php
if (isset($_GET['since'])){
	$since=$_GET['since'];
} else {	
	$since='';
}


	header('Content-Type: text/xml');

	$array = scandir('./reports');
	rsort($array);


	$r  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$r .= "<snarl version=\"2\">\n";
	$r .= "<reports>\n";
	$p = "";

	foreach($array as $file){
		$d = explode(".", $file);
		if ( $d[0] > $since){
			switch ($d[1]){
				case 'xml':
					$p .=file_get_contents("reports/$file");
				break;
				case 'jpg':
				break;
			}
		} else {
			//we're done
			break;
		}
	}
	$r .= $p. "</reports>\n";
	$r .= "</snarl>";

	echo $r;
?>
