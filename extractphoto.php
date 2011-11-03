<?php
//get Photo from Flickr
include "params.php";
include "createthumb.php";
set_time_limit ( 0 );
if (!class_exists('Snoopy', false)) { include 'Snoopy.class.php';}

function getPicsFromDom( $dom ){
	$pics = array();
	$domxpath = new DOMXPath($dom);
	$filtered = $domxpath->query("//img");
	$i = 0;
	while( $myItem = $filtered->item($i++) ){
		$src=$myItem->getAttribute('src');
		array_push($pics, $src);
	}
	return $pics;
}


function getfromURL($url){
	$snoopy = new Snoopy;
	//echo "Fetching page: $url<BR/>";
	if ($snoopy->fetch( $url )) {
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = false;
		@$dom->loadHTML($snoopy->results);
		return getPicsFromDom( $dom );
	}	
	return false;
}


function getfromFlickr($url, $filename){
	$contents= file_get_contents($url);
	$divstart=strpos($contents, '<div class="photo-div">');
	if ($divstart > 0){
		$fotostart = strpos($contents,'<img src="', $divstart) + 10;
		$fotoend = strpos($contents,'"',$fotostart);
		$fotourl = substr($contents,$fotostart, $fotoend - $fotostart);
		//echo "divstart: $divstart, fotostart: $fotostart, fotoend: $fotoend, fotourl: $fotourl <BR/>";
		$picture = file_get_contents($fotourl);
		$fh = fopen($filename, "w");
		fwrite($fh, $picture);
		fclose($fh);
		return true;
	} else {
		return false;
	}
}

function getfromYfrog($url, $filename){
	$pics= getfromURL($url, $filename);
	$res=false;
	foreach($pics as $pic){
		if (strpos($pic, "xsize")>0){
			//echo "Found pic: $pic, writing as $filename<BR/>";
			$picture = file_get_contents($pic);
			$fh = fopen($filename, "w");
			fwrite($fh, $picture);
			fclose($fh);
			$res=true;			
		}
	}
	return $res;
}


//getfromYfrog("http://yfrog.com/h7xbahmj", "./tweetedpics/test.jpg");
//exit;

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);
//$sql = "select msg_id, msg_plain from sm_inq where msg_plain like '%http://flic.kr%'";
$sql = "select msg_id, msg_plain from sm_inq where msg_plain like '%http://%' and msg_id > '51914207452602368'";

//$sql = "select msg_id, msg_plain from sm_inq where msg_id = '51223586517696512'";
$result = mysql_query($sql) or die(mysql_error(). $sql);
while ($row = mysql_fetch_assoc($result)) {
//	$pattern = '~http://flic.kr/[^ \n]+~i';
//	$pattern = '~http://yfrog.com/[^ \n]+~i';
	$pattern = '~http://[^ \n]+~im';
	$msg=$row['msg_plain'];
	echo $row['msg_id'] . ":$msg<BR/>";
	//preg_match($pattern, $row['msg_plain'], $matches);
	$offset = 0;
	$c=0; 
	$id= "./tweetedpics/" .$row['msg_id'];
	while (preg_match($pattern,$msg , $matches,PREG_OFFSET_CAPTURE, $offset)){
		$arr=$matches[0];
		$link = $arr[0];
		$linkoff = $arr[1];
		$offset = $linkoff + 1;
		echo "... $link<BR/>";
		$picarray = getFromURL($link);
		foreach ($picarray as $pic){
			//echo $pic . "length: " . strlen($pic) . ", pos: " . stristr($pic, ".jpg") . "<BR/>";
			if (strlen($pic) > 0 && stristr($pic, ".jpg")){
				echo "...... $pic";
				if (substr($pic,1) === "/"){
					if (substr($link, -1) === "/"){
						$pic = $link . substr($pic,1);
					} else {
						$pic = $link . $pic;
					}	
							
				}
				$picture = @file_get_contents($pic);
				if (strlen($picture) > 40000){					
					$c++;
					echo "...... saved as $id-$c.jpg<BR/><img src='$id-$c.jpg'><BR/>";
					$fh = fopen("$id-$c.jpg", "w");
					fwrite($fh, $picture);
					fclose($fh);
				} else {
					echo " .... too small<BR/>";
				}
			}
		}
	}
	echo "<HR/>";
}
set_time_limit ( 30 );

?>
