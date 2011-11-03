<?php
if (isset($_REQUEST['_SESSION'])) die("Muppet!"); //handle session injection attack
include 'getremotefile.php';
include 'tagservices.php';
include 'params.php';
ob_start();
//Twitter Feed
//Pull In New Messages - api: http://apiwiki.twitter.com/w/page/22554756/Twitter-Search-API-Method:-search
$searchterm=urlencode($twitter_searchterm);
$sinceid=urlencode("36886867152400000");
$languages="en,id,is,es";
$notlanguages="ja,";
$url= "http://search.twitter.com/search.atom?since_id=$sinceid&page=1&q=$searchterm&rpp=100";
$path = "http://127.0.0.1/" . substr($_SERVER['REQUEST_URI'],0,-strlen('twitter.php'));

$raw = getremotefile($url);
$raw = str_replace('twitter:','twitter_',$raw);
$raw = str_replace('google:','google_',$raw);
//Write to file
/*$myFile = "../tweets.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $raw);
fclose($fh);*/
//echo $raw;
$xml = simplexml_load_string($raw);

echo "<table>";

foreach ($xml->entry as $entry) {
	$id = explode(':',$entry->id);
	if ($id > $sinceid){
		$sinceid = $id;
	}
	$published = date('d/m/Y H:i:s', strtotime($entry->published));
	$title = $entry->title;
	$contenthtml= $entry->content;
	$updated =  date('d/m/Y H:i:s', strtotime($entry->updated));
	$geo = $entry->twitter_geo;
	$locn = $entry->google_location;
	$source = $entry->twitter_source;
	$lang = $entry->twitter_lang;
	$author=$entry->author->name;
	$tags = tagservices($title,1);
	echo "<TR>";
	echo "<td>" . $id[2] . "</td>";
	echo "<td>" . $published . "</td>";
	echo "<td>" . $title . "</td>";
	echo "<td>" . $contenthtml . "</td>";
	echo "<td>" . $updated . "</td>";
	echo "<td>" . $geo . "</td>";
	echo "<td>" . $locn . "</td>";
	echo "<td>" . $source . "</td>";
	echo "<td>" . $lang . "</td>";
	echo "<td>" . $author . "</td>";
	echo "<td>" . $tags. "</td>";
	echo "</TR>";
	ob_flush();
	flush();
}
echo "</table>";
?>
