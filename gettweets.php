<?php
if (isset($_REQUEST['_SESSION'])) die("Muppet!"); //handle session injection attack
set_time_limit(300); 
include 'getremotefile.php';
include 'tagservices.php';
include 'params.php';
$tagservice=1;
      
      $mtime = microtime();
      $mtime = explode(' ', $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $starttime = $mtime;
      
mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);

//Sukey Twitter Feed
//Pull In New Messages - api: http://apiwiki.twitter.com/w/page/22554756/Twitter-Search-API-Method:-search
//$searchterm=urlencode("-gucci sukey OR sukeydata OR sukeysms OR sukeydating OR demo2011 OR March26 OR Solidarity");
$searchterm=urlencode("-gucci sukey OR sukeydata OR sukeysms OR J30 OR strike OR June30 OR 30Jun OR solidari");
//$sinceid="36886867152400000";//returns tweets from this id....
$maxid = "99999999999999999";//...to this one
$sinceid="40979075593936896";
$languages="en,id,is,es";
$notlanguages="ja,";
$url= "http://search.twitter.com/search.atom?since_id=$sinceid&page=1&q=$searchterm&maxid=$maxid&rpp=100&result_type=recent";
//$url = "http://search.twitter.com/search.atom?geocode=51.50055%2C-0.127%2C25km";
$path = "http://127.0.0.1/" . substr($_SERVER['REQUEST_URI'],0,-strlen('gettweets.php'));
      $mtime = microtime();
      $mtime = explode(" ", $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $endtime = $mtime;
      $totaltime = ($endtime - $starttime);

echo "$totaltime Fetching File $url<BR/>";
$raw = getremotefile($url);
//print $raw;
$raw = str_replace('twitter:','twitter_',$raw);
$raw = str_replace('google:','google_',$raw);
//Write to file
$myFile = "tweets.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $raw);
fclose($fh);
//echo $raw;
$xml = simplexml_load_string($raw);
      $mtime = microtime();
      $mtime = explode(" ", $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $endtime = $mtime;
      $totaltime = ($endtime - $starttime);
	echo "$totaltime Loading Tweets<BR/>";
foreach ($xml->entry as $entry) {
	$lang = $entry->twitter_lang;
	if ($lang != 'ja'){
       if (substr($entry->content,0,3) == "RT "){
                    //skip
       } else {

		$id = explode(':',$entry->id);
		$id =  mysql_real_escape_string($id[2]);
		$sql = "select msg_id from sm_inq where msg_id='$id' limit 1";
		$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
		$row = mysql_fetch_assoc($result);
		if (mysql_num_rows($result) == 0){
			if ($id > $sinceid){
				$sinceid = $id;
			}
			$published = date('d/m/Y H:i:s', strtotime($entry->published));
			$title =  mysql_real_escape_string(str_ireplace('sukey',  " <strong style='background: yellow'>sukey</strong> ", $entry->title));
			$contenthtml=  mysql_real_escape_string($entry->content);
			$updated =   mysql_real_escape_string(date('d/m/Y H:i:s', strtotime($entry->updated)));
			$geo =  mysql_real_escape_string($entry->twitter_geo);
			$locn =  mysql_real_escape_string($entry->google_location);
			$source = $entry->twitter_source;
			$author= mysql_real_escape_string($entry->author->name);
			$uid = mysql_real_escape_string(trim(substr($entry->author->name,0,strpos($entry->author->name,'(')-1)));
			$sql = "select screenName from sm_score where screenName='$uid'";
			$result = mysql_query($sql) or die(mysql_error() . "[$sql]");
			$row = mysql_fetch_assoc($result);
			if (mysql_num_rows($result) == 0){
				$sql = "insert into sm_score (screenName, uid, score, isTroll, color) 
				values ('$uid', '$uid', 50.00, 'N', '000000')";
				$result = mysql_unbuffered_query($sql) or die(mysql_error() . "[$sql]");				
			}
			$tags="";
			/*
			$tags =  mysql_real_escape_string(tagservices($title,$tagservice));
			if ($tags == 'YQL ERROR'){
				$tagservice=2;
				$tags =  mysql_real_escape_string(tagservices($title,2));
			}
			$tags = trim($tags,'[');
			$tags = trim($tags,']');
			*/
      $mtime = microtime();
      $mtime = explode(" ", $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $endtime = $mtime;
      $totaltime = ($endtime - $starttime);
			echo "<p>$totaltime $title</p>";
			$sql= "insert into sm_inq (msg_plain, msg_html, tags, sender, istagged, msg_id, sender_score, published, geo, locn, lang, uid)
				   values ('$title', '$contenthtml', '$tags', '$author', 'Y', '$id', 0, '$updated', '$geo', '$locn', '$lang', '$uid' )";
			$result = mysql_unbuffered_query($sql) or die(mysql_error() . "[$sql]");
		} else {
      $mtime = microtime();
      $mtime = explode(" ", $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $endtime = $mtime;
      $totaltime = ($endtime - $starttime);
			echo "$totaltime found duplicate $id<br/>";
			break;
		}
	}
}
}

      $mtime = microtime();
      $mtime = explode(" ", $mtime);
      $mtime = $mtime[1] + $mtime[0];
      $endtime = $mtime;
      $totaltime = ($endtime - $starttime);

echo "$totaltime Done<BR/>";
set_time_limit(30); 
?>