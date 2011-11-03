<?php
$sizes = array('none','xx-small','x-small','small','medium','large','x-large','xx-large');
$xml = simplexml_load_file("smcsettings.xml");
if ($xml->removehashtags['value'] == 'on')	{$rht = " checked ";} else {$rht='';}
if ($xml->URLstoLinks['value'] == 'on')	{$utl = " checked ";} else {$utl='';}
$msgcount=$xml->msgperscreen['value'];
$badthresh = $xml->badthresh['value'];
$goodthresh = $xml->goodthresh['value'];
$badcolor = $xml->badcolor['value'];
$medcolor =$xml->medcolor['value'];
$goodcolor = $xml->goodcolor['value'];
$keywords = $xml->keywords['value'];
$picsperpage = $xml->picsperpage['value'];
$picsajaxtime = $xml->picsajaxtime['value'];

	
?>
<html>
<head>
<title>Sukey 2eb - Message Management Console Options</title>
<link rel="stylesheet" type="text/css" href="smc.css" />
<script type="text/javascript" src="./library/jquery-1.4.4.js"></script>
<script type="text/javascript">
function saveconfig(){
	var table = document.getElementById("table");
	var xml = '<' + '?xml version="1.0" encoding="UTF-8"?' + '>\n<config>\n';
	for (i=1; i< table.rows.length; i++){
		var key =table.rows[i].cells[1].childNodes[0].name;
		var val =table.rows[i].cells[1].childNodes[0].value;
		xml += "   <" + key + " value='" + val + "'/>\n";
	}
	xml += '</config>\n';
	$.post('./apismcSaveOptions.php', { xml: xml }, 
		function(resp){
			alert (resp);
			location.reload(true);
		}	 
	);
	
}
</script>
</head>
<body>
<div style="float: left: width: 100%; text-align: Left; color: white; background: green; font-size: 10pt;">
	Sukey II electric boogaloo<BR/><small>Reputation Management Console Options</small>
	<div id="buttondiv" style="position:relative; float:right; top: -14px; right: 100px; background-color:transparent; border: none;">
		<button onclick="document.location.href='./smc.php';">Message Console</button>
		<button onclick="document.location.href='./smcImages.php';">Reports Console</button>
	</div>	
</div>
<div style="float: left; width: 100%; text-align:center; background: #f0fff0">
<H3>Sukey Message Center Options</H3>
<p style="text-size:small;">Note, none of these values are validated. <br/>For colours use either HTML color names (eg DarkGreen) or 6 digit hex values (eg #00FE00).</p>
<table align='center' id='table'>
	<tr><th>Criteria</th><th>Value</th></tr>
<?php
echo "<tr><td>Remove Hashtags from Message Body</td><td><input type=checkbox name='removehashtags' $rht></td></tr>";
echo "<tr><td>Number of Messages To Show in Console</td><td><input type=text name='msgperscreen' value=$msgcount></td></tr>";
echo "<tr><td>Show URLs in Messages as Links</td><td><input type=checkbox name='URLstoLinks' $utl></td></tr>";

echo "<tr><td>Tags Column Text</td><td><select name='TagsColText'>";
$thissize=$xml->TagsColText['value'];
foreach($sizes as $size)
{
	echo "<option value='$size' ";
	if ($size == $thissize) echo " selected ";  
	echo ">$size</option>";
}
echo "</select></td></tr>";

echo "<tr><td>Hashtags Column Text</td><td><select name='HashColText'>";
$thissize=$xml->HashColText['value'];
foreach($sizes as $size)
{
	echo "<option value='$size' ";
	if ($size == $thissize) echo " selected ";  
	echo ">$size</option>";
}
echo "</select></td></tr>";

echo "<tr><td>Messages Column Text</td><td><select name='MsgColText'>";
$thissize=$xml->MsgColText['value'];
foreach($sizes as $size)
{
	echo "<option value='$size' ";
	if ($size == $thissize) echo " selected ";  
	echo ">$size</option>";
}
echo "</select></td></tr>";
echo "<tr><td>Reputation Threshold for Neutral</td><td><input type=text name='badthresh' value=$badthresh></td></tr>";
echo "<tr><td>Reputation Threshold for Reliable</td><td><input type=text name='goodthresh' value=$goodthresh></td></tr>";
echo "<tr><td>Good Reputation - colour</td><td bgcolor = $goodcolor><input type=text name='goodcolor' value=$goodcolor></td></tr>";
echo "<tr><td>Medium Reputation - colour</td><td bgcolor=$medcolor><input type=text name='medcolor' value=$medcolor></td></tr>";
echo "<tr><td>Bad Reputation - colour</td><td bgcolor=$badcolor><input type=text name='badcolor' value=$badcolor></td></tr>";
echo "<tr><td>Keywords (Separate with space)</td><td><TEXTAREA NAME='keywords' ROWS=5 COLS=40>$keywords</TEXTAREA></td></tr>";
echo "<tr><td>Report - Pics Per page</td><td><input type=text name='picsperpage' value=$picsperpage></td></tr>"; 
echo "<tr><td>Report - Update Frequency (ms)</td><td><input type=text name='picsajaxtime' value=$picsajaxtime></td></tr>"; 
?>
</table>
<button onclick='saveconfig();'>Save Config</button>
</div>
<div id='logodiv' style="position:absolute; display:inline; top: 0px; right: 0px; background-color:transparent ;background-image: url(./images/wp_small.png); height: 102px; width: 100px; border: none;"/>

</body>
</html>
