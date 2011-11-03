<?php
	$token=$_GET['token'];
	if(!($token=="fG5pZKSf6BwcjM8cerw9ll7vMsDpxDasdqwd23r23sdasdasd")) {
		echo "You are not authenticated.</p>";
		die;
	}
?>
<html>
<head>
<title>Snarl 2eb - Message Management Console</title>
<link rel="stylesheet" type="text/css" href="smc.css" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="./library/jquery-1.4.4.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> 
<script type="application/x-javascript"> 
<?php
$xml = simplexml_load_file("smcsettings.xml");
if ($xml->removehashtags['value'] != 'on')	{$rht = 'true';} else {$rht='false';}
echo "var showhashinmsg=$rht;\n";

if ($xml->URLstoLinks['value'] == 'on')	{$utl = 'true';} else {$utl='false';}
echo "var makeurlsclickable=$utl;\n";
echo "var numMsgs = " .$xml->msgperscreen['value'].";\n";
echo "var tagsize='" . $xml->TagsColText['value'] ."';\n";
echo "var hashtagsize='" .$xml->HashColText['value'] ."';\n";
echo "var msgsize='".$xml->MsgColText['value'] ."';\n";
echo "var badthresh = " . $xml->badthresh['value'] .";\n";
echo "var goodthresh = " . $xml->goodthresh['value'].";\n";
echo "var badcolor = '" . $xml->badcolor['value']."';\n";
echo "var medcolor = '" . $xml->medcolor['value']."';\n";
echo "var goodcolor = '" . $xml->goodcolor['value']."';\n";

echo "function Hilite(msg){\n";
$keywords = explode(" ", $xml->keywords['value']);
foreach($keywords as $keyword){
	if (strlen($keyword) > 0){ 
		echo "    msg=msg.replace(/$keyword/gi,\"<strong style='background: aqua'>$keyword</strong>\");\n";
	}
}
echo "    return msg;\n";	
echo "}\n";

?>

var msgdata;
var table;
var ogmsgbox;
function sendMsg(msg){
	$("#dialog").dialog("close");
	//alert (ogmsgbox.value);
	$("#dialog").dialog("destroy");
}

function sendMsgDlg(){
  ogmsgbox.value='';
  $("#dialog").dialog({ title: 'Broadcast Message', width: 330 });
}
function showRow(m){
	document.getElementById('showme').innerHTML=m;
	
}
function showData(){
	//assumes empty table
	var hashreg = new RegExp("/#[^ \n]+/", "i");
	for (i=0; i<msgdata.length;i++){
		
		//alert ("loading row: " + i + ": " + msgdata[i].msg_id);
		var row = table.insertRow(i);
		var id = msgdata[i].msg_id;
		
		//onmouseover=showRowData()
		
		
		var m = msgdata[i].msg_plain;
		row.setAttribute('rowno', i);
		row.onmouseover = function() { 
			var r= this.getAttribute('rowno');
			showRow(msgdata[r].uid + "<BR/>" + msgdata[r].published + "<BR/>"+msgdata[r].msg_id); }
		
		var cell = row.insertCell(-1);
		var color = medcolor;
		if (msgdata[i].score < badthresh) color=badcolor;
		if (msgdata[i].score > goodthresh) color=goodcolor;
		var element1 = document.createElement("input");
		element1.type = "checkbox";
		cell.setAttribute("bgColor",color);
		cell.appendChild(element1);

		//var div=document.createElement("div");
		//div.style.border = "2px solid " + color;
		//div.appendChild(element1);
		//cell.appendChild(div);
		
		if (tagsize != 'none'){
			var cell = row.insertCell(-1);
			cell.width="10%";
			cell.innerHTML =  "<p style='font-size:" + tagsize + "'>" + Hilite(msgdata[i].tags)  +"</p>";
		}

		if (hashtagsize != 'none'){
			var cell = row.insertCell(-1);
			cell.width="10%";
			var reg = /#[^ \n]+/g;
			var ar = m.match(reg);
			if (ar != null){
				var str = ar.join(' ');
				cell.innerHTML = "<p style='font-size:" + Hilite(hashtagsize) +"'>" + str +"</p>";
			}
		}
		
		if (msgsize != 'none'){
			var cell3 = row.insertCell(-1);
			if (makeurlsclickable)
				m=m.replace(/((www|http:\/\/)[^ \n]+)/, '<a href="$1" target="_blank">$1</a>');
			if (!showhashinmsg){
				while (m.indexOf('#')>0)
					m=m.replace(/#[^ \n]+/, '');
			}		
			cell3.innerHTML = "<p style='font-size:" + msgsize +"'>" + Hilite(m) +"</p>";
		}
	}
}
function amendReputation(repvalue){

	for (i=table.rows.length -1; i>= 0; i--){
		if (table.rows[i].cells[0].childNodes[0].checked){
			$.get('./apiIncrementUserReputation.php?sname=' +  msgdata[i].uid 
			+ '&delta=' + repvalue + '&type=p&msgid=' + msgdata[i].msg_id, 
			    {async:false}, function(result){
				if (result.substring(0,2) != 'OK'){
					alert(result);
				}	
			});	
		}
	}
	getData();
}
function clearTable(){
	for (i=table.rows.length -1; i>= 0; i--){
		table.deleteRow(i);
		msgdata.splice(i, 1);	
	}
}
function flagNoise(){
	var url = '';
	for (i=table.rows.length -1; i>= 0; i--){
		if (table.rows[i].cells[0].childNodes[0].checked){
			url += msgdata[i].msg_id +",";
			table.deleteRow(i);
			msgdata.splice(i, 1);
		}
	}
	if (url.length > 0){
		url = url.substr(0,url.length -1);
		$.get('./apiFlagNoise.php?msg=' + url,{async: false},function(result){ 
		    getData();
	    });
	}
}
function setTroll(){
	var url = '';
	for (i=table.rows.length -1; i>= 0; i--){
		if (table.rows[i].cells[0].childNodes[0].checked){
			$.get('./apiSetUserTrollFlag.php?sname='  + msgdata[i].uid + '&to=Y', {async:false}, function(result){
				if (result.substring(0,2) != 'OK'){
					alert(result);
				}	
			});	
		}
	}
	getData();
}

function getData(){
	clearTable();
	$.ajax({
	  url: './apiGetinq.php?r=' + numMsgs,
	  dataType: 'json',
	  success: 
	  function(msg){
		  msgdata=eval(msg);
		  showData();
	  }
	});
}
function checkAll(value){
	for (i=0; i< table.rows.length; i++){
		table.rows[i].cells[0].childNodes[0].checked=value;
	}	
}
function checkReverse(){
	for (i=0; i< table.rows.length; i++){
		var cb =table.rows[i].cells[0].childNodes[0];
		cb.checked = !cb.checked;
	}	
}
function checkWord(){
	var matchString= document.getElementById("checkword").value.toLowerCase();
	for (i=0; i< table.rows.length; i++){
		if (msgdata[i].msg_plain.toLowerCase().indexOf(matchString) != -1){
			var cb =table.rows[i].cells[0].childNodes[0];
			cb.checked=true;
		} 
	}		
}
function initialise(){
	table = document.getElementById("msgtable");
    ogmsgbox=document.getElementById('txtmsg');
	getData();
}
</script>
</head>
<body onLoad='initialise();'>
<div style="float: left; width: 10%; top:60px; background:#fffff0;  text-align:center;empty-cells: hide; font-size:small;position:fixed">
	<div style="position:relative; width: 100%; height:200px; background:#f0fff0;">
		Select:
		<button style="width:120px;height:25px" onclick='checkAll(true);'>Check All</button>
		<button style="width:120px;height:25px" onclick='checkAll(false);'>Uncheck All</button>
		<button style="width:120px;height:25px" onclick='checkReverse();'>Reverse Selection</button>
		<input type='text' id='checkword' style="width:90px"/>
		<button style="width:25px;height:25px" onclick='checkWord();'>&#8226;</button>
		<HR/>
		Flag As:
		<button style="width:120px;height:25px" onclick='flagNoise();'>Noise</button>
		<button style="width:120px;height:25px" onclick='amendReputation(15);'>Good Message</button>
		<button style="width:120px;height:25px" onclick='amendReputation(-50);'>Misinformation</button>
		<button style="width:120px;height:25px" onclick='setTroll();'>Troll</button>
		<HR/>
		Outgoing Message
		<button style="width:120px;height:25px" onclick='sendMsgDlg(true,false);'>Snarl Data</button>
		<button style="width:120px;height:25px" onclick='sendMsgDlg(true, true);'>Data &amp; SMS</button>
		<button style="width:120px;height:25px" onclick='sendMsgDlg(false, true);'>SMS Only</button>
		<HR/>
		<span id='showme' style='font-size: x-small;'></span>

	</div>	
</div>
<div style="float: right; width: 90%; top:50px; text-align:center; background: #f0fff0;position:relative">
<Table id="msgtable"></table>
<?php
include 'params.php';
/*	$color = "#F7F7F7";
	if ($score < 33.33){
		$color = "F1100";
	}
	if ($score > 66.66){
		$color = "10F100";
	}
	echo "<tr><td bgcolor=$color><INPUT TYPE=CHECKBOX id='row$rowid'></td><td><small>".$row['tags']."</small></td><td><small>".$row['msg_html']."</small></td></tr>";
*/
?>
</div>
<div style="float: left; width: 100%; text-align: Left; color: white; background: green; font-size: 10pt;position:fixed;">
	Snarl<BR/><small>Message Management Console</small>
	<div id="buttondiv" style="position:relative; float:right; top: -14px; right: 100px; background-color:transparent; border: none;">
		<button onclick="document.location.href='./rateuser.php';">Reputation Management</button>
		<button onclick="document.location.href='./smcOptions.php';">Options</button>
		<button onclick="document.location.href='./smcImages.php';">Images</button>
	</div>	
</div>
<div id="logodiv" style="position:fixed; display:inline; top: 0px; right: 0px; background-color:transparent ;background-image: url(./images/wp_small.png); height: 102px; width: 100px; border: none;"/>
<div id="dialog" title="Dialog Title" style="display:none;" >
<textarea id='txtmsg' rows="10" cols="30" style='align:center;'>
New Message here</textarea>
<button onclick='sendMsg();'>Send</button><button onclick='$("#dialog").dialog("destroy");'>Cancel</button>
</div>

</body>
</html>
