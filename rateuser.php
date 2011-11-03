<?php
include "params.php";
include 'getremotefile.php';

//Get stuff about a user
$path = "./";
$sname='';

mysql_connect($servername,$userid,$password) or die ("couldn't connect to MYSQL");
mysql_select_db($database);


if (isset($_GET['sname'])){
	$sname = $_GET['sname'];
}	
if (isset($_GET['p'])){
	$profile = $_GET['p'];
} else { $profile='default'; }	
$followstring="";
$selstring="";
$sql = "select name from SM_scoringProfile order by name";
$result = mysql_query($sql) or die(mysql_error(). $sql);
while ($row = mysql_fetch_assoc($result)) {
	$n = $row['name'];
	if ($n==$profile){
		$selstring .= "<option selected value='$n'>$n</option>\n";
	} else {
		$selstring .= "<option value='$n'>$n</option>\n";
	}
}




$url = "http://api.twitter.com/1/users/show.xml?screen_name=" . urlencode($sname);

$raw = getremotefile($url);
if (!$raw){
	$myFile = "./tweeternotfound.xml";
	$fh = fopen($myFile, 'r') or die("can't open file");
	$raw = fread($fh, filesize($myFile));
	fclose($fh);
}
if (strlen($raw)> 0 )
{
	$knownfol= 0;
	$maxfol= 0;
	$minfol = 0;
	$avgfol = 0;
	$raw = str_replace('twitter:','twitter_',$raw);
	$raw = str_replace('google:','google_',$raw);
	$userXML = @simplexml_load_string($raw);
	if (!isset($userXML->protected)) die("<H1>Press F5 to reload</H1>unexpected error loading 1st file:\n$raw");
	if ( $userXML->protected != 'true'  && $userXML->followers_count > 0){
		$url= "http://api.twitter.com/1/followers/ids/$sname.xml?cursor=-1";
		$rawf = getremotefile($url);
		
		if (strlen($rawf)>0){
			//echo $rawf;
			$folxml = @simplexml_load_string($rawf);
			
			if (strpos($rawf,"Rate limit exceeded") > 0){
				$myFile = "followers.txt";
				$fh = fopen($myFile, 'w') or die("can't open file");
				fwrite($fh, $raw);
				fclose($fh);
				echo "Error Message from Twitter: $rawf<BR>Press F5 to retry page.</BR>";
			} else {	
				$followstring = "( ";
				foreach ($folxml->ids->id as $follower){
					$followstring .= "'$follower',";
				}
				$followstring = substr($followstring,0,-1) . ")";
				//echo $followstring;
				if ($followstring != '()'){
					$sql = "select count(*) as fol, max(score) as max, min(score) as min, avg(score) as avg 
							from SM_score where uid in ".$followstring;
					$result = mysql_query($sql) or die(mysql_error(). $sql);
					while ($row = mysql_fetch_assoc($result)) {
						$knownfol= $row['fol'];
						$maxfol= $row['max'];
						$minfol = $row['min'];
						$avgfol = $row['avg'];
					}
					$sql = "select uid, screenName, score, isTroll, color 
							from SM_score where uid in ".$followstring;
					$result = mysql_query($sql) or die(mysql_error(). $sql);
					$followstring="<H4>Known followers of $sname</H4><table><tr><th>uid</th><th>Name</th><th>Score</th><th>Troll?</th><th>Colour</th></tr>";
					$result = mysql_query($sql) or die(mysql_error(). $sql);
					while ($row = mysql_fetch_assoc($result)) {
						$followstring .= "<tr><td>" . $row['uid'] ."</td><td>". $row['screenName'] ."</td><td>". $row['score'] ."</td><td>". $row['isTroll'] ."</td><td>". $row['color'] ."</td></tr>";
					}
					$followstring.="</table>";
				}
			}	
		}
	}	
	$query = "select * from SM_score where uid='" .$userXML->id . "'"; 
	$result = mysql_query($query) or die(mysql_error());
	$score = "Undefined";
	$isTroll = "Undefined";
	$color = "Undefined";
	$inDb = "False";
	while ($row = mysql_fetch_assoc($result)) {
		$score= $row['score'];
		$isTroll= $row['isTroll'];
		$color = $row['color'];
		$inDb = "True";
	}
?>

<head>
<link rel="stylesheet" type="text/css" href="smc.css" />
<script type="text/javascript" src="./library/jquery-1.4.4.js"></script>
<script type="application/x-javascript"> 
<?php
	$pheading="Reputation Calculation ";
	$sql="select count(*) as count from SM_scoringProfile where name='$profile'";
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$count= $row['count'];
	} 
	if ($count == 0 ) {
		$profile = 'default';
		$pheading .= " not found - using default";
	}

	$sql="select w,m from SM_scoringProfile where name='$profile' limit 1";
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$w= $row['w'];
		$m= $row['m'];
	} 

	echo "var arrweight =[ $w ];\n";
	echo "var arrmax    =[ $m ];\n";
?>

function popupFollowers(){
	var newwindow=window.open('','Followers','height=200,width=400,scrollbars=1,location=0,menubar=0,status=0,toolbar=0');
	var tmp = newwindow.document;
	tmp.write ('<head><title>Known Followers</title>');
	tmp.write ('<style type="text/css">body {font-family: "gill sans", "new baskerville", sans-serif;}');
	tmp.write ('td {border:1px solid white;background-color:#F7F7F7;font-size: 10pt;}');
	tmp.write ('th {background-color:green;color:white;}</style></head><body>');
	<?php echo "tmp.write('$followstring');"; ?>
	tmp.write ('</body>');
	tmp.close();
}
function changeWeight(i){
	var sTable = document.getElementById('scoreTable');
	var row = sTable.getElementsByTagName("tr")[i+1];
	var newWeight = prompt("Enter New Weighting for " + row.childNodes.item(0).innerText, row.childNodes.item(2).innerHTML);
	if (IsNumeric(newWeight)){
		arrweight[i]=newWeight;
		showCalc();
	}
}
function changeMax(i){
	var sTable = document.getElementById('scoreTable');
	var row = sTable.getElementsByTagName("tr")[i+1];
	var newMax = prompt("Enter New Max for " + row.childNodes.item(0).innerText, row.childNodes.item(3).innerHTML);
	if (IsNumeric(newMax)){
		arrmax[i]=newMax;
		showCalc();
	}
}
function saveReputationProfile(){
	var Pname = prompt("Under what new name would you like to save this profile?", Pname);
	if (Pname == '' || Pname == null) { 
			return;
	}
	<?php	echo "var url = '$path" . "apisaveScoringProfile.php?name=';\n";?>
	url = url + encodeURIComponent(Pname) + '&w=';
	for (i=0; i<arrweight.length;i++){
		url=url+arrweight[i]+',';
	}
	url = url.substr(0,url.length -1) + '&m=';
	for (i=0; i<arrmax.length;i++){
		url=url+arrmax[i]+',';
	}
	url = url.substr(0,url.length -1);
	$.get(url,{async: false},function(result){ 
		alert(result);
		var profile = document.getElementById('profile').options;
		profile[profile.length] = new Option(Pname, Pname);
		profile[profile.length-1].selected=true;
	});
	
}
function showCalc(){
	var sTable = document.getElementById('scoreTable');
	var tmax = 0;
	var tscore=0;
	for( var i=0; i<  sTable.getElementsByTagName("tr").length -3; i++){
		var row = sTable.getElementsByTagName("tr")[i+1];
		var val = row.childNodes.item(1).innerHTML;
		row.childNodes.item(2).innerHTML = arrweight[i];
		if (arrweight[i] != 0){
			row.childNodes.item(3).innerHTML = arrmax[i];
			var score = Math.floor((val * arrweight[i]) + 0.5);
			if (score > arrmax[i]){
				score = arrmax[i]
			}
			row.childNodes.item(4).innerHTML = score;
			tscore = parseInt(score) + tscore;
			tmax = parseInt(arrmax[i]) + tmax;
		} else {
			row.childNodes.item(3).innerHTML = '-';
			row.childNodes.item(4).innerHTML = "0";
		}	
	}
	var row = sTable.getElementsByTagName("tr")[i+1];
	row.childNodes.item(3).innerHTML = tscore;
	row.childNodes.item(2).innerHTML = tmax;
	var repscore= parseInt(0);
	if (tmax > 0){repscore = parseInt(Math.floor(tscore/tmax * 100 + 0.5));}
	row = sTable.getElementsByTagName("tr")[i+2];
	var sb = document.getElementById('newscore');
	if (sb){ sb.value=repscore;}
	row.childNodes.item(2).innerHTML = repscore + '%';
}
function handleKeyPress(e,form){
	var key=e.keyCode || e.which;
	if (key==13){
		go();
	}
}
function go(){
	var url="./rateuser.php?sname=";
	var profile = document.getElementById('profile');
	url += document.getElementById('twitid').value;
	for (i=0; i<profile.options.length; i++){
		if (profile.options[i].selected){
			url += "&p=" + escape(profile.options[i].value);
			i=profile.options.length + 1;
		}
	}
	//alert(url);
	window.location.href=url;
}
function newProfile(){
	var profile = document.getElementById('profile');
	var url = './apiGetProfile.php?';
	for (i=0; i<profile.options.length; i++){
		if (profile.options[i].selected){
			url += "&p=" + escape(profile.options[i].value);
			i=profile.options.length + 1;
		}
	}
	//alert (url);
	$.get(url, function(data) {
		//alert(data);
		var arr1 = data.split('|');
		arrweight = arr1[0].split(',');
		arrmax = arr1[1].split(',');
		showCalc();
    });	
}
function IsNumeric(input){
   return (input - 0) == input && input.length > 0;
}
function addToDb(){
	<?php	echo "var url = '$path" . "apiAddUser.php?sname=$sname". "&id=" . $userXML->id . "&score=';\n";  ?>
	url += document.getElementById("newscore").value;	
	//alert(url);
	$.get(url,{async: false},function(result){ 
		//alert(result);
	});
}
function getHelp(){
	alert("Enter the user's screen name in the input box and press enter.\n\nThe page will use the weighting profile specified in the dropdown next to the 'Reputation Calculation' dropdown.\n\nTo change a weighting or max click on its value.\n\nTo save the current profile as a new profile click save and enter the profile name.");
}
</script>
</head>
<body onLoad='showCalc();'>
<div style="float: left: width: 100%; text-align: Left; color: white; background: green; font-size: 10pt;">Snarl<BR/><small>Reputation Management Console</small></div>
<div style="float: left; width: 50%; text-align:center; background: #f0fff0">

<H3>Tweeter Profile</H3>
<?php
/*	if ($userXML->profile_image_url == ''){
		$myFile = "profile.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $raw);
		fclose($fh);
		echo "Error Message from Twitter: $raw <BR>Press F5 to reload page</BR>";
	}*/
	echo "<table width='95%' align='center'>";
	echo "<tr><th width=150px>Heading</th><th>Value</th></tr>";
	if ($userXML->profile_image_url ==''){
		echo "<tr><td rowspan='2'></td><td>". $userXML->id."</td></tr>";
	} else {
		echo "<tr><td rowspan='2'><img src='". $userXML->profile_image_url ."'></td><td>". $userXML->id."</td></tr>";
	}
	echo "<tr><td>". $userXML->name."</td></tr>";
	echo "<tr><td>Screen Name</td><td><input type='text' id='twitid' maxlength='20' size='20' onkeypress='handleKeyPress(event,this.form)' value='". $userXML->screen_name."'/><button onclick='go();'>Find</button></td></tr>";
	echo "<tr><td>location</td><td>". $userXML->location."</td></tr>";
	echo "<tr><td>description</td><td>". $userXML->description."</td></tr>";
	echo "<tr><td>protected</td><td>". $userXML->protected."</td></tr>";
	echo "<tr><td>Followers</td><td>". $userXML->followers_count."</td></tr>";
	echo "<tr><td>Friends</td><td>". $userXML->friends_count."</td></tr>";
	if ($userXML->profile_image_url ==''){
		$userXML->created_at = date('d M Y');
		echo "<tr><td>Created</td><td>". $userXML->created_at. "</td></tr>";
	} else {
		echo "<tr><td>Created</td><td>". $userXML->created_at. "[". 
			date('d/m/Y', strtotime($userXML->created_at)). "]</td></tr>";
	}
	echo "<tr><td>Favourites</td><td>". $userXML->favourites_count."</td></tr>";
	echo "<tr><td>Statuses</td><td>". $userXML->statuses_count."</td></tr>";
	echo "<tr><td>Listed</td><td>". $userXML->listed_count."</td></tr>";
	echo "<tr><td>Known Followers</td><td><a href='#' onclick='popupFollowers();return false;'>". $knownfol . "</a></td></tr>";
	echo "<tr><td>Max Follower Score</td><td>". floor($maxfol + .5) . "</td></tr>";
	echo "<tr><td>Min Follower Score</td><td>". floor($minfol + .5) . "</td></tr>";
	echo "<tr><td>Average Follower Score</td><td>". floor($avgfol + .5) . "</td></tr>";
	echo "<tr><td>In Database?</td><td>". $inDb. "</td></tr>";
	if ($inDb != "True" && $userXML->profile_image_url != ''){
		echo "<tr><td>Add To Database?</td><td><button onclick='addToDb();'>Add</button>&nbsp;Reputation Score (0-100):<input type='text' id='newscore' maxlength='3' size='3' value='$avgfol'/></td></tr>";
	} else {
	echo "<tr><td>Score</td><td>". $score . "</td></tr>";
	echo "<tr><td>Is Troll</td><td>". $isTroll . "</td></tr>";
	echo "<tr><td>Screen Color</td><td>". $color . "</td></tr>";
	}
	echo "</table>";
	echo "<HR/>";
	//echo "<button>Good verified message</button><button>Misinformation</button><button>Noise</button><button>Troll</button>";
} else {
	echo "Screen Name: [$sname] not found in twitter";
}
?>
</div>
<div style="float: right; width: 50%; background:#f0fff0;  text-align:center;empty-cells: hide">
<H3><?php echo "$pheading<select id='profile' onchange='newProfile();'>$selstring</select>"; ?><button onclick='saveReputationProfile();'>New Profile</button>
<button onclick='getHelp();'>Help</button></H3>
<table width='95%' align='center' id="scoreTable">
<tr><th>Category</th><th align="right">value</th><th align="right">weighting</th><th align="right">max</th><th align="right">score</th></tr>
<?php
$myscore=0;
$mymax=0;
$rowid =0;
function showrow($name, $value, $weighting, $max){
	global $myscore, $mymax, $rowid;
	if ($weighting == 0){
		echo "<tr><td>$name</td><td align='right'>$value</td><td align='right' onclick='changeWeight($rowid)'>0</td><td align='right' onclick='changeMax($rowid)'>-</td><td align='right'>0</td></tr>\n";
	} else {
		$v = floor($weighting * $value + 0.5);
		if ($v > $max){$v=$max;}
		$mymax += $max;
		echo "<tr><td>$name</td><td align='right'>$value</td><td onclick='changeWeight($rowid)' align='right'>$weighting</td><td align='right'  onclick='changeMax($rowid)'>$max</td><td align='right'>$v</td></tr>\n";
		$myscore += $v;
	}
	$rowid++;
	return;
}
showrow("Followers", $userXML->followers_count, .1, 5);
showrow("Friends", $userXML->friends_count, 0.01, 1);
if (($userXML->friends_count) > 0){
	showrow("Followers/Friends %", floor($userXML->followers_count/$userXML->friends_count * 100 +0.5), 2, 100);
} else {
	showrow("Followers/Friends %", 0, 2, 100);
}	
$days=floor((time()-strtotime($userXML->created_at))/86400);
showrow("Longevity (days)", $days , 0.2, 100);

showrow("Known Followers", $knownfol , 0, 10);
showrow("Max Known Follower score", floor($maxfol + .5), 0, 10);
showrow("Min Known Follower score", floor($minfol +.5), 0, 10);
showrow("Average known Follower score", floor($avgfol + .5), 0, 10);
if ($userXML->followers_count > 0){
	showrow("Known Followers/Followers %", floor($knownfol/$userXML->followers_count * 100 +0.5), 0, 10);
	showrow("Tweets per follower", floor($userXML->statuses_count/$userXML->followers_count + 0.5), 1, 10);
} else {
	showrow("Known Followers/Followers %", 0, 0, 10);
	showrow("Tweets per follower", 0, 1, 10);
}
if ($days > 0){
	showrow("Tweets per 100 days", floor($userXML->statuses_count/ $days * 100 +0.5), 1, 10);
} else{	
	showrow("Tweets per 100 days", 0, 1, 10);
}
echo "<tr><td colspan=2></td><td>Totals</td><td align='right'>$mymax</td><td align='right'>$myscore</td></tr>\n";
if ($mymax != 0){
	echo "<tr><td colspan=2></td><td colspan='2'>Overall Score</td><td align='right'>" .floor($myscore/$mymax * 100 +.5) ."%</td></tr>\n";
} else {
	echo "<tr><td colspan=2></td><td colspan='2'>Overall Score</td><td align='right'>0%</td></tr>\n";
}
?>
</table>
<HR/>
</div>
<div id="logodiv" style="position:absolute; display:inline; top: 0px; right: 0px; background-color:transparent ;background-image: url(./images/wp_small.png); height: 102px; width: 100px; border: none;"/>
</body>
</html>
