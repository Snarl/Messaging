<?php
/* Routine for bypassing API rate limiters by using a web proxy when result comes back limited
 * Works with glype v1.0
 */ 

if (!class_exists('Snoopy', false)) { include 'Snoopy.class.php';}

require("params.php");

function getremotefile($u){

	if (!isset($u)){
		$u = "http://search.twitter.com/search.atom?page=1&q={$twitter_searchterm}&rpp=100";
	}
	
	$raw = @file_get_contents($u);
	if (!$raw) {return false;}
//	$myFile = "../tweets.txt";
//	$fh = fopen($myFile, 'w') or die("can't open file");
//	fwrite($fh, $raw);
//	fclose($fh);
	if (strpos($raw,'ate limit exceeded') == 0){
//		echo $raw;
		return $raw . "<!-- engine=raw -->";
	}

	$arrserver = array(
			"http://thespeedproxy.info",
			"http://upstair-stander.tk",
			"http://www.docoja.com/blue",
			"http://www.anonymtube.com"
	);
	$serverid=rand(0,count($arrserver)-1);

	$counter = 0;
	while ($counter < count($arrserver)-1){
		$server = $arrserver[$serverid];
		$snoopy = new Snoopy;
		$snoopy->fetch($server);
		$submit_url = "$server/includes/process.php?action=update";
		$submit_vars["u"] = $u;
		$submit_vars["submit"] = "Go!";
		$snoopy->submit($submit_url,$submit_vars);
		//echo $snoopy->results;
		if (strpos($snoopy->results,'ate limit exceeded') == 0 && strpos($snoopy->results,'400 Bad Request') ==0){
			return $snoopy->results . "<!-- engine=$server -->";
		} else {
			$counter++;
			$serverid = $counter;
			if ($serverid == count($arrserver)){ $serverid=0;}
		}	
		
	}
	return false;	
}
?>
