<?php
/* Requires a phrase to tag (?text=) and an engineID (&engine=) and a timer flag (&timer=)
 * Returns as set of tags in the format [type1: value1, value2, .. valuen], ...[typen: ,, ..]
 * Engine IDs are:
 * 				0 (default)	Tagthe.net
 * 				1 Yahoo
 * 				2 Alchemy
 * 
 * To Do: 1. Add more engines
 *        2. Manage Error handling - drop into another engine when call fails
 * 
 * 
 */
function tagservices($text, $engine){
	
	
//if (isset($_REQUEST['_SESSION'])) die("Muppet!"); //handle session injection attack
//$timer = (isset($_GET['timer']));
//$text = $_GET['text'];
//$engine = $_GET['engine'];
	$tags="";
    $timer=false;
	$msc=microtime(true);

	switch ($engine){
	case 1: //Yahoo YQL
		$tags = '[';
		$text = str_replace('"',"'", $text); //yahoo objects to quotes
		$q = urlencode("select * from search.termextract where context =\"$text\"");
		$raw=getremotefile("http://query.yahooapis.com/v1/public/yql?q=$q");

		if ($raw != false){
			if (strpos($raw, "<!---------------->")>0){
				$tags='YQL ERROR';
				break;
			}
			libxml_use_internal_errors(true);
			$tagxml=simplexml_load_string($raw);
			if (!$tagxml) {
				$myFile = "tagxmlerror.txt";
				$fh = fopen($myFile, 'w') or die("can't open file");
				foreach(libxml_get_errors() as $error) {
					fwrite( $fh, $error->message);
				}
				fwrite($fh, $raw);
				fclose($fh);
			}
			$results = $tagxml->results[0];
			foreach ($results->Result as $result){
				$tags .= $result . ", ";
			}
			if (strlen($tags) > 2) {$tags =  substr($tags, 0, -2). "]";}
			break;
		}
	case 2: //Alchemy
		$key="28f7145e6d69472fb7faf22ae45bc429a9ac52e5";
		$raw= getremotefile("http://access.alchemyapi.com/calls/text/TextGetRankedKeywords?apikey=$key&text=".urlencode($text));
		if ($raw != false){
			$tagxml=simplexml_load_string($raw);
			$results = $tagxml->keywords[0];
			foreach ($results->keyword as $result){
				$tags .= $result->text . ", ";
			}
			if (strlen($tags) > 2) {$tags =  substr($tags, 0, -2). "]";}
			break;
		}
	case 3: //awaiting code
		break;
	default:
		$tags= "[";
		$raw= getremotefile("http://tagthe.net/api/?text=".urlencode($text));
		if ($raw != false){
			$tagxml=simplexml_load_string($raw);
			$meme = $tagxml->meme[0];
			foreach($meme->dim as $dim){
				if ($dim['type'] != 'language'){
					$tags .= "[" . $dim['type'].": ";
					foreach ($dim->item as $item){
						$tags .= $item . ", ";
					}
					if (strlen($tags) > 2) {$tags =  substr($tags, 0, -2). "]";}
				}
			}
		} else {$tags = "[tagthenet unavailable]";}
	}
	if ($timer){
		$tags.= "[". intval((microtime(true)-$msc) * 1000). " msecs]";
	}
	return $tags;

}
?>
