<html>
<head>
<title>Sukey 2eb - Images Console</title>
<link rel="stylesheet" type="text/css" href="smc.css" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="./library/jquery-1.4.4.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> 
<style type="text/css">
	
html,body {
height:100%;
margin:0 0 0 0;
padding:0 0 0 0;
}

.imagediv {
position:relative;
/*display:inline;*/
display:none;
float:left;
height:200px;
border:3px double #999;
margin:0 0 0 0;
}

.imagediv img {
    height:100%;
    width:auto;
}
h2 {
   position: absolute;
   float:left;
   bottom: 2px;
   left: 2px;
   width: 100%;
   text-align:left;
}
h2 span {
   color: white;
   font: bold 10px Helvetica, Sans-Serif;
   letter-spacing: -1px;
   background: rgb(0, 0, 0); /* fallback color */
   background: rgba(0, 0, 0, 0.5);
   padding: 1px;
}
</style>
<script type="application/x-javascript"> 
<?php
$xml = simplexml_load_file("smcsettings.xml");
echo "function Hilite(msg){\n";
$keywords = explode(" ", $xml->keywords['value']);
foreach($keywords as $keyword){
	if (strlen($keyword) > 0){ 
		echo "    msg=msg.replace(/$keyword/gi,\"<strong style='background:aqua;color:black'>$keyword</strong>\");\n";
	}
}
echo "    return msg;\n";	
echo "}\n";
echo "var maxpics=".$xml->picsperpage['value'].";\n";
echo "var ajaxTime=".$xml->picsajaxtime['value'].";\n";
?>
var since='';
var ajaxTimer;
var maindiv;

function moveAlong(pic, thumb, text){
	var kids= maindiv.childElementCount;
	if (kids >= maxpics){
		maindiv.removeChild(maindiv.childNodes(kids - 1));
	}
	var divname = pic.split('.');
	var h=Hilite(text);
	$('#maindiv').prepend(
	"<div style='display:inline;'>" + 
	"<div class='imagediv' id='" + divname[0] + "'>" + 
		"<a href= './reports/" + pic + "' target='_blank'>" + 
//		"<a href='' onclick=\'bigPic(\""+ divname[0] + "\", \"" +text + "\");'>" + 
		   "<img src='./reports/" + thumb + "'>" +
		"</a>" + 
		"<h2><span>" + Hilite(text) + "</span></h2>" + 
	"</div></div>");
	$('#' + divname[0]).fadeIn('slow');
//	setTimeout(function(){$('#' + divname[0]).slideDown('slow')},200);
}


function getData(){
	$.ajax({
	url: './apireportsRSS.php?since=' + since,
	dataType: 'atom',
	success: 
		function(data){
			$(data).find('report').each(
				function(report) {
					if (this.id > since ) since=this.id;
					//alert(this.id); //.attr('id'));
					var banner=  $(this).find("when").text() + ": " 
					           + $(this).find('what').text() + " (" 
					           + $(this).find("where").text() + ")<BR/>" 
					           + $(this).find("GMT").text();
					for (i=6; i< this.childElementCount;i++){
						var thumb = this.children[i].outerHTML.split('"')[1];
						moveAlong (this.children[i].innerText, thumb, banner);
					}
				}
			);	
			ajaxTimer=setTimeout(function() { getData(); }, ajaxTime);
		}
	});	
}

$(document).ready(function () {
maindiv = document.getElementById('maindiv');
getData();
});

</script>
</head>
<body>
<div style="float: left: width: 100%; text-align: Left; color: white; background: green; font-size: 10pt;">
	Sukey II electric boogaloo<BR/><small>Images Console</small>
	<div id="buttondiv" style="position:relative; float:right; top: -14px; right: 100px; background-color:transparent; border: none;">
		<button onclick="document.location.href='./rateuser.php';">Reputation Management</button>
		<button onclick="document.location.href='./smcOptions.php';">Options</button>
		<button onclick="document.location.href='./smc.php';">Message Console</button>
	</div>	
</div>
<div id="maindiv" style="width: 100%; text-align:center; background: #f0fff0; display:inline;">
</div>

<div id="logodiv" style="position:absolute; display:inline; top: 0px; right: 0px; background-color:transparent ;background-image: url(./images/wp_small.png); height: 102px; width: 100px; border: none;"/>
</body>
</html>
